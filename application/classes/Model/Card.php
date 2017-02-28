<?php defined('SYSPATH') or die('No direct script access.');

class Model_Card extends Model
{
	const CARD_STATE_IN_WORK 	= 1;
	const CARD_STATE_BLOCKED 	= 4;

	const CARD_ACTION_DELETE 	= 0;
	const CARD_ACTION_ADD 		= 1;
	const CARD_ACTION_EDIT		= 2;

	const CARD_LIMIT_PARAM_VOLUME 	= 1;
	const CARD_LIMIT_PARAM_RUR 		= 2;

	const CARD_LIMIT_TYPE_DAY		= 1;
	const CARD_LIMIT_TYPE_WEEK		= 2;
	const CARD_LIMIT_TYPE_MONTH		= 3;
	const CARD_LIMIT_TYPE_ONCE		= 4;

	public static $cardLimitsParams = [
		self::CARD_LIMIT_PARAM_VOLUME 	=> 'л.',
		self::CARD_LIMIT_PARAM_RUR 		=> Text::RUR,
	];

	public static $cardLimitsTypes = [
		self::CARD_LIMIT_TYPE_DAY 	=> 'в сутки',
		self::CARD_LIMIT_TYPE_WEEK 	=> 'в неделю',
		self::CARD_LIMIT_TYPE_MONTH => 'в месяц',
		//self::CARD_LIMIT_TYPE_ONCE 	=> 'единовременно',
	];

	/**
	 * получаем список доступный карт по контракту
	 *
	 * @param $contractId
	 * @param $cardId
	 * @param $params
	 * @return array|int
	 */
	public static function getCards($contractId = false, $cardId = false, $params = false)
	{
		if(empty($contractId) && empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LIST
			where 1=1
		";

		if(!empty($contractId)){
			$sql .= " and contract_id = ".Oracle::quote($contractId);
		}

		if(!empty($cardId)){
			$sql .= " and card_id = ".Oracle::quote($cardId);
		}

		if(!empty($params['query'])){
			$sql .= " and (card_id like ".Oracle::quote('%'.$params['query'].'%')." or upper(holder) like ".mb_strtoupper(Oracle::quote('%'.$params['query'].'%')).")";
		}

        if(!empty($params['status'])){
            if($params['status'] == 'work'){
                $sql .= ' and CARD_STATE != '.Model_Card::CARD_STATE_BLOCKED;
            } else {
                $sql .= ' and CARD_STATE = '.Model_Card::CARD_STATE_BLOCKED;
            }
        }

		$cards = $db->query($sql);

		return $cards;
	}

	/**
	 * вытягиваем одну карту
	 */
	public static function getCard($cardId, $contractId = false)
	{
		$card = self::getCards($contractId, $cardId);

		if(!empty($cardId)){
			return reset($card);
		}

		return false;
	}

	/**
	 * получаем данные по ограничениям по топливу
	 *
	 * @param $cardId
	 */
	public static function getOilRestrictions($cardId)
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LIMITS
			where card_id = ".Oracle::quote($cardId)
		;

		$restrictions = $db->tree($sql, 'LIMIT_GROUP');

		return $restrictions;
	}

	/**
	 * данные по последней заправке
	 *
	 * @param $cardId
	 */
	public static function getLastFilling($cardId)
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$card = Model_Card::getCard($cardId);

		$where = ["card_id = ".Oracle::quote($cardId)];

		if(!empty($card['CONTRACT_ID'])){
			$where[] = "contract_id = ".Oracle::quote($card['CONTRACT_ID']);
		}

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LAST_SERV
			where ".implode(" and ", $where)
		;

		$restrictions = $db->row($sql);

		return $restrictions;
	}

	/**
	 * добавление новой карты
	 *
	 * @param $params
	 */
	public static function editCard($params, $action)
	{
		if(empty($params['contract_id']) || empty($params['card_id'])){
			return false;
		}

		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		if($action == self::CARD_ACTION_EDIT){
			$card = self::getCard($params['card_id']);

			if(empty($params['expire_date'])){
				$params['expire_date'] = $card['EXPIRE_DATE'];
			}
		}

		$data = [
			'p_contract_id' 	=> $params['contract_id'],
			'p_card_id' 		=> $params['card_id'],
			'p_card_type' 		=> 1, //1-EMV, 2 - PayFlex, 3 - Loyalty
			'p_holder' 			=> empty($params['holder']) ? '' : $params['holder'],
			'p_expire_date' 	=> !empty($params['expire_date']) ? date('m/Y', strtotime($params['expire_date'])) : '',
			'v_action' 			=> $action,
			'p_manager_id' 		=> $user['MANAGER_ID'],
			'p_error_code' 		=> 'out',
		];

		$res = $db->procedure('client_contract_card', $data);

		if(!empty($res)){
			return $res;
		}

		if($action != self::CARD_ACTION_ADD) {
            //редактируем лимитов если таковые пришли в запросе
            self::editCardLimits($params['card_id'], empty($params['limits']) ? false : $params['limits']);
        }

		return true;
	}

	/**
	 * получаем историю операция по карте
	 *
	 * @param $cardId
	 * @param $limit
	 */
	public static function getOperationsHistory($cardId, $params = [])
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

        $contractId = !empty($params['CONTRACT_ID']) ? $params['CONTRACT_ID'] : false;

		$card = Model_Card::getCard($cardId, $contractId);
        $user = Auth::instance()->get_user();

		$where = [
		    "card_id = ".Oracle::quote($cardId),
		    "agent_id = ".$user['AGENT_ID'],
        ];

		if(!empty($card['CONTRACT_ID'])){
			$where[] = "contract_id = ".Oracle::quote($card['CONTRACT_ID']);
		}

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_HISTORY
			where ".implode(" and ", $where)."
			order by HISTORY_DATE desc
		";

		if(!empty($params['pagination'])) {
			return $db->pagination($sql, $params);
		}

		return $db->query($sql);
	}

	/**
	 * блокировка/разблокировка карты
	 *
	 * @param $cardId
	 */
	public static function toggleStatus($params)
	{
		if(empty($params['card_id']) || empty($params['contract_id'])){
			return false;
		}

		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		//получаем карты и смотрим текущий статус у нее
		$card = self::getCard($params['card_id'], $params['contract_id']);

		if(empty($card['CARD_ID'])){
			return false;
		}

		switch($card['CARD_STATE']){
			case self::CARD_STATE_BLOCKED:
				$status = self::CARD_STATE_IN_WORK;
				break;
			default:
				$status = self::CARD_STATE_BLOCKED;
		}

		$data = [
			'p_card_id' 		=> $params['card_id'],
			'p_contract_id' 	=> $params['contract_id'],
			'p_new_state' 		=> $status,
			'p_comment' 		=> $params['comment'],
			'p_manager_id' 		=> $user['MANAGER_ID'],
			'p_error_code' 		=> 'out',
		];

		$res = $db->procedure('card_change_state', $data);

		if(empty($res)){
			return true;
		}

		return false;
	}

	/**
	 * редактирование карты и лимитов
	 *
	 * @param $params
	 * @return bool
	 */
	public static function editCardLimits($cardId, $limits)
	{
		if(empty($cardId)){
			return false;
		}

        $user = Auth::instance()->get_user();

		if(in_array($user['role'], array_keys(Access::$clientRoles))) {
            $currentLimits = self::getOilRestrictions($cardId);

            foreach($limits as $limit){
                if(
                    ($limit['param'] == 1 && $limit['value'] > 1000) ||
                    ($limit['param'] == 2 && $limit['value'] > 30000)
                ){
                    if(empty($currentLimits)){
                        Messages::put('Изменение лимитов не произошло. Превышен допустимый лимит! Обратитесь к вашему менеджеру');
                        return false;
                    }
                    foreach($limit['services'] as $service){
                        foreach ($currentLimits as $currentLimit){
                            foreach ($currentLimit as $currentL) {
                                if ($currentL['SERVICE_ID'] == $service && $limit['value'] != $currentL['LIMIT_VALUE']) {
                                    Messages::put('Изменение лимитов не произошло. Превышен допустимый лимит! Обратитесь к вашему менеджеру');
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }

		$db = Oracle::init();

		$db->procedure('card_service_refresh', ['p_card_id' => $cardId]);

		if(empty($limits)){
            $db->procedure('card_queue_limit_add', ['p_card_id' => $cardId]);
			return true;
		}

		foreach($limits as $group => $limit){
			foreach($limit['services'] as $service){
				$data = [
						'p_card_id'			=> $cardId,
						'p_service_id'		=> $service,
						'p_limit_group'		=> $group,
						'p_limit_param'		=> $limit['param'],
						'p_limit_type'		=> $limit['type'],
						'p_limit_value'		=> str_replace(",", ".", $limit['value']),
						'p_limit_currency'	=> Model_Contract::CURRENCY_RUR,
						'p_limit_pcs'		=> 0, //default
						'p_manager_id' 		=> $user['MANAGER_ID'],
						'p_error_code' 		=> 'out',
				];

				$res = $db->procedure('card_service_edit', $data);

				if(!empty($res)){
					return false;
				}
			}
		}

		$db->procedure('card_queue_limit_add', ['p_card_id' => $cardId]);

		return true;
	}

	/**
	 * изъятие карты
	 *
	 * @param $params
	 * @return bool|int
	 */
	public static function withdrawCard($params)
	{
		if(empty($params['contract_id']) || empty($params['card_id'])){
			return false;
		}

		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		$data = [
			'p_card_id' 		=> $params['card_id'],
			'p_contract_id' 	=> $params['contract_id'],
			'p_manager_id' 		=> $user['MANAGER_ID'],
			'p_error_code' 		=> 'out',
		];

		$res = $db->procedure('card_contract_withdraw', $data);

		if(!empty($res)){
			return $res;
		}

		return true;
	}
}