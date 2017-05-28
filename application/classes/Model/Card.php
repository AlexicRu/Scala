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

    const CARDS_GROUP_ACTION_EDIT = 1;
    const CARDS_GROUP_ACTION_DEL = 0;

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

		if(!empty($card)){
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
	 * @param $contractId
	 */
	public static function getLastFilling($cardId, $contractId = false)
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$card = Model_Card::getCard($cardId);

		$where = ["card_id = ".Oracle::quote($cardId)];

		if (!empty($contractId)) {
            $where[] = "contract_id = ".Oracle::toInt($contractId);
        } else if(!empty($card['CONTRACT_ID'])){
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

		if($action != self::CARD_ACTION_ADD && Access::allow('clients_card_edit_limits')) {
            //редактируем лимитов если таковые пришли в запросе
            self::editCardLimits($params['card_id'], $params['contract_id'], empty($params['limits']) ? [] : $params['limits']);
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
	public static function editCardLimits($cardId, $contractId, $limits = [])
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

        /*
        S1,S2,S3:P1:T1:V1:PCS1;
        S4,S5,S6:P2:T2:V2:PCS2;

        где S1,S2,S3 - ID услуг через "запятую" в рамках указанной группы ограничения
        P1 - параметр лимита для указанной группы ограничения (1 - в литрах, 2 - в рублях)
        T1 - тип лимита для указанной группы ограничения (1 - суточный, 2 - недельный, 3 - месячный)
        V1 - размер лимита для указанной группы ограничения (дробная часть через "точку")
        PCS1 - лимит на количество операций для указанной группы ограничения (по умолчанию пока "0" - без ограничений)
         */
        $limitsArray = [];

		foreach($limits as $group => $limit){

		    $limitsArray[] =
                implode(',', $limit['services']) . ':' .
                $limit['param'] . ':' .
                $limit['type'] . ':' .
                str_replace(",", ".", $limit['value']) . ':' .
                0 . ';'
            ;
		}

        $data = [
            'p_card_id'			=> $cardId,
            'p_contract_id'		=> $contractId,
            'p_limit_array'		=> [$limitsArray, SQLT_CHR],
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('card_service_edit_ar', $data);

        if(!empty($res)){
            return false;
        }

		$db->procedure('card_queue_limit_add', ['p_card_id' => $cardId]);

		return true;
	}

    /**
     * редактирование карты и лимитов
     *
     * @param $params
     * @return bool
     * @deprecated
     */
    public static function OLD_editCardLimits($cardId, $limits = [])
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

    /**
     * получаем список групп карт
     *
     * @return array|int
     */
    public static function getGroups($filter = [])
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_CARD_GROUPS t where t.agent_id = ".$user['AGENT_ID']
        ;

        if(!empty($filter['search'])){
            $sql .= " and upper(t.group_name) like ".mb_strtoupper(Oracle::quote('%'.$filter['search'].'%'));
        }

        if(!empty($filter['ids'])){
            $sql .= " and t.group_id in (".implode(',', $filter['ids']).")";
        }

        $sql .= ' order by group_name ';

        if(!empty($filter['limit'])){
            return $db->query($db->limit($sql, 0, $filter['limit']));
        }
        return $db->query($sql);
    }

    /**
     * добавляем группу карт
     *
     * @param $params
     */
    public static function addCardsGroup($params)
    {
        if(empty($params['name'])){
            return Oracle::CODE_ERROR;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_pos_group_name'    => $params['name'],
            'p_manager_id'        => $user['MANAGER_ID'],
            'p_group_id'          => 'out',
            'p_error_code' 		  => 'out',
        ];

        return $db->procedure('ctrl_card_group_add', $data);
    }

    /**
     * редактируем группу точек
     *
     * @param $params
     */
    public static function editCardsGroup($params, $action = self::CARDS_GROUP_ACTION_EDIT)
    {
        if(empty($params['group_id'])){
            return Oracle::CODE_ERROR;
        }

        $db = Oracle::init();

        $user = User::current();

        $data = [
            'p_group_id'          => $params['group_id'],
            'p_action'            => $action,
            'p_group_name'        => !empty($params['name']) ? $params['name'] : '',
            'p_manager_id'        => $user['MANAGER_ID'],
            'p_error_code' 		  => 'out',
        ];

        return $db->procedure('ctrl_card_group_edit', $data);
    }

    /**
     * получаем список карт группы
     *
     * @param $params
     */
    public static function getGroupCards($params)
    {
        if(empty($params['group_id'])){
            return false;
        }

        $db = Oracle::init();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_CARDS_GROUP_ITEMS t where t.group_id = ".$params['group_id'];
        ;

        return $db->pagination($sql, $params);
    }

    /**
     * обертка для getGroups
     *
     * @param $groupId
     * @return bool
     */
    public static function getGroup($groupId)
    {
        if(empty($groupId)){
            return false;
        }

        $groups = self::getGroups(['ids' => [$groupId]]);

        if(empty($groups[0])){
            return false;
        }

        return $groups[0];
    }

    /**
     * получаем доступные для добавления карты
     *
     * @param $params
     * @return mixed
     */
    public static function getAvailableGroupCards($params)
    {
        if(empty($params['group_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = User::current();

        $sql = "
            select 
                vc.card_id, 
                vc.holder, 
                vc.description_ru 
            from ".Oracle::$prefix."v_web_cards_all vc
            where vc.agent_id = {$user['AGENT_ID']}
                and not exists 
                    (
                        select 1 
                        from ".Oracle::$prefix."v_web_cards_group_items vgi 
                        where vgi.card_id = vc.card_id
                         and vgi.group_id = ".(int)$params['group_id']."
                    )
            ";

        if(!empty($params['CARD_ID'])){
            $sql .= ' and vc.CARD_ID like '.Oracle::quote('%'.$params['CARD_ID'].'%');
        }

        if(!empty($params['HOLDER'])){
            $sql .= ' and vc.HOLDER like '.Oracle::quote('%'.$params['HOLDER'].'%');
        }

        if(!empty($params['DESCRIPTION_RU'])){
            $sql .= ' and vc.DESCRIPTION_RU like '.Oracle::quote('%'.$params['DESCRIPTION_RU'].'%');
        }

        return $db->pagination($sql, $params);
    }

    /**
     * добавляем карты к группе
     *
     * @param $groupId
     * @param $cardsIds
     */
    public static function addCardsToGroup($groupId, $cardsIds)
    {
        if(empty($groupId) || empty($cardsIds)){
            return Oracle::CODE_ERROR;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_group_id'        => $groupId,
            'p_action'          => 1,
            'p_card_collection' => [$cardsIds, SQLT_CHR],
            'p_manager_id'      => $user['MANAGER_ID'],
            'p_error_code' 	    => 'out',
        ];

        return $db->procedure('ctrl_card_group_collection', $data);
    }
}