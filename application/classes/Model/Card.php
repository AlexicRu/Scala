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

	const CARD_TYPE_EMV_CAN 		= 1; //Карта EMV с возможностью смены лимитов/блокировки в сторонней системе
	const CARD_TYPE_EMV_CANT 		= 2; //Карта EMV без возможности смены лимитов/блокировки в сторонней системе
	const CARD_TYPE_PAYFLEX_CAN 	= 3; //Карта PayFlex с возможностью блокировки в сторонней системе
	const CARD_TYPE_PAYFLEX_CANT 	= 4; //Карта PayFlex без возможности блокировки в сторонней системе

	public static $cardLimitsParams = [
		self::CARD_LIMIT_PARAM_VOLUME 	=> 'л.',
		self::CARD_LIMIT_PARAM_RUR 		=> Text::RUR,
	];

	public static $cardLimitsTypes = [
		self::CARD_LIMIT_TYPE_DAY 	=> 'в сутки',
		self::CARD_LIMIT_TYPE_WEEK 	=> 'в неделю',
		self::CARD_LIMIT_TYPE_MONTH => 'в месяц',
		self::CARD_LIMIT_TYPE_ONCE 	=> 'единовременно',
	];

	/**
	 * получаем список доступный карт по контракту
	 *
	 * @param $contractId
	 * @param $cardId
	 * @param $query
	 * @return array|int
	 */
	public static function getCards($contractId = false, $cardId = false, $query = false)
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

		if(!empty($query)){
			$sql .= " and card_id like '%".Oracle::quote($query)."%'";
		}

		$cards = $db->query($sql);

		return $cards;
	}

	/**
	 * вытягиваем одну карту
	 */
	public static function getCard($cardId)
	{
		$card = self::getCards(false, $cardId);

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

			if(empty($params['holder'])){
				$params['holder'] = $card['HOLDER'];
			}
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

		//редактируем лимиту если таковые пришли в запросе
		if(!empty($params['limits'])){
			self::editCardLimits($params['card_id'], $params['limits']);
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

		$card = Model_Card::getCard($cardId);

		$where = ["card_id = ".Oracle::quote($cardId)];

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
		if(empty($params['card_id'])){
			return false;
		}

		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		//получаем карты и смотрим текущий статус у нее
		$card = self::getCard($params['card_id']);

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
		if(empty($cardId) || empty($limits)){
			return false;
		}

		$db = Oracle::init();

		$db->procedure('card_service_refresh', ['p_card_id' => $cardId]);

		$user = Auth::instance()->get_user();

		foreach($limits as $group => $limit){
			foreach($limit['services'] as $service){
				$data = [
						'p_card_id'			=> $cardId,
						'p_service_id'		=> $service,
						'p_limit_group'		=> $group,
						'p_limit_param'		=> $limit['param'],
						'p_limit_type'		=> $limit['type'],
						'p_limit_value'		=> $limit['value'],
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


		return true;
	}

	/**
	 * получаем список доступных видов сервиса
	 *
	 * @return array|int
	 */
	public static function getServicesList()
	{
		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		$sql = "
			select *
			from ".Oracle::$prefix."v_web_service_list
			where agent_id = ".$user['AGENT_ID']
		;

		return $db->query($sql);
	}
}