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

	const CARD_LIMIT_TYPE_DAY		    = 1;
	const CARD_LIMIT_TYPE_WEEK		    = 2;
	const CARD_LIMIT_TYPE_MONTH		    = 3;
	const CARD_LIMIT_TYPE_QUARTER	    = 4;
	const CARD_LIMIT_TYPE_YEAR	        = 5;
	const CARD_LIMIT_TYPE_TRANSACTION	= 10;

    const CARDS_GROUP_ACTION_EDIT = 1;
    const CARDS_GROUP_ACTION_DEL = 0;

    const CARD_GROUP_TYPE_USER      = 1;
    const CARD_GROUP_TYPE_SYSTEM    = 2;

    const CARD_ICON_WAY4_LUKOIL = 'Way4 Lukoil';
    const CARD_ICON_WAY4_GPN    = 'Way4 GPN';
    const CARD_ICON_PETROL_RN   = 'Petrol RN';
    const CARD_ICON_NEFTIKA     = 'Neftika';
    const CARD_ICON_BASHNEFT    = 'Petrol Bashneft';
    const CARD_ICON_SKON        = 'Petrol SKON';
    const CARD_ICON_CPK         = 'Petrol CPK';
    const CARD_ICON_TATNEFT     = 'Tatneft';
    const CARD_ICON_CLEVEROIL   = 'Cleveroil';

    const CARD_SYSTEM_GPN = 5;

    public static $cardIcons = [
        self::CARD_ICON_WAY4_LUKOIL => 'card_lukoil.png',
        self::CARD_ICON_WAY4_GPN    => 'card_gpn.png',
        self::CARD_ICON_PETROL_RN   => 'card_rn.png',
        self::CARD_ICON_NEFTIKA     => 'card_neftika.png',
        self::CARD_ICON_BASHNEFT    => 'card_bashneft.png',
        self::CARD_ICON_SKON        => 'card_skon.png',
        self::CARD_ICON_CPK         => 'cpk.jpg',
        self::CARD_ICON_TATNEFT     => 'tatneft.jpg',
        self::CARD_ICON_CLEVEROIL   => 'cleveroil.jpg',
    ];

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

    public static $cardLimitsTypesFull = [
        self::CARD_LIMIT_TYPE_DAY 	        => 'сутки',
        self::CARD_LIMIT_TYPE_WEEK 	        => 'неделя',
        self::CARD_LIMIT_TYPE_MONTH         => 'месяц',
        self::CARD_LIMIT_TYPE_QUARTER       => 'квартал',
        self::CARD_LIMIT_TYPE_YEAR          => 'год',
        self::CARD_LIMIT_TYPE_TRANSACTION   => 'транзакций'
    ];

	public static $editLimitsManagerNoLimit = [
	    1233, 1499, 1335, 1582, 929
    ];

	private static $_selectedCards = [];

	public static $cantDelCardLimitSystems = [
	    1, 3, 4
    ];

	public static $cardsGroupsTypes = [
	    self::CARD_GROUP_TYPE_USER      => 'Пользовательская группа',
	    self::CARD_GROUP_TYPE_SYSTEM    => 'Группа для служебных операций',
    ];

	/**
	 * получаем список доступный карт по контракту
	 *
	 * @param $contractId
	 * @param $cardId
	 * @param $params
	 * @return array|int
	 */
	public static function getCards($contractId = false, $cardId = false, $params = false, $select = [])
	{
		if(empty($contractId) && empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = (new Builder())->select()
            ->from('V_WEB_CRD_LIST')
        ;

		if(!empty($contractId)){
			$sql->where("contract_id = ".Oracle::quote($contractId));
		}

		if(!empty($cardId)){
			$sql->where("card_id = ".Oracle::quote($cardId));
		}

		if(!empty($params['query'])){
		    $sql->whereStart();
			$sql->where("card_id like ".Oracle::quote('%'.$params['query'].'%'));
			$sql->whereOr("upper(holder) like ".mb_strtoupper(Oracle::quote('%'.$params['query'].'%')));
            $sql->whereEnd();
		}

        if(!empty($params['status'])){
            if($params['status'] == 'work'){
                $sql->where('CARD_STATE != '.self::CARD_STATE_BLOCKED);
            } else {
                $sql->where('CARD_STATE = '.self::CARD_STATE_BLOCKED);
            }
        }

        if(!empty($params['contract_id'])){
            $params['contract_id'] = (array)$params['contract_id'];
            $sql->where("contract_id in (".implode(',', array_map('intval', $params['contract_id']))).")";
        }

        if (!empty($select)) {
		    $sql->select($select);
        }

        if(!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }

		$cards = $db->query($sql);

		return $cards;
	}

	/**
	 * вытягиваем одну карту
     *
     * используем статический массив, так как карту иногда надо достать много раз внутри этого класса в рамках одной функции
	 */
	public static function getCard($cardId, $contractId = false)
	{
	    $key = $cardId . '_' . (int)$contractId;

	    if (empty(self::$_selectedCards[$key])) {
            $cards = self::getCards($contractId, $cardId);

            self::$_selectedCards[$key] = !empty($cards) ? reset($cards) : false;
        }

        return self::$_selectedCards[$key];
	}

    /**
     * получаем список доступных сервисов по карте
     *
     * @param $cardId
     * @param $select
     */
	public static function getServices($cardId, $select = [])
    {
        if (empty($cardId)) {
            return false;
        }

        $sql = (new Builder())->select()
            ->from('V_WEB_CARDS_SERVICE_AVAILABLE t')
            ->where('t.card_id = ' . Oracle::quote($cardId))
        ;

        if (!empty($select)) {
            $sql->select($select);
        }

        return Oracle::init()->query($sql);
    }

    /**
     * получаем данные по ограничениям по топливу
     *
     * @param $cardId
     */
    public static function getOilRestrictions($cardId, $limitId = false, $select = [])
    {
        if(empty($cardId) && empty($limitId)){
            return [];
        }

        $db = Oracle::init();

        $sql = (new Builder())->select()
            ->from('V_WEB_CARDS_LIMITS')
        ;

        if (!empty($cardId)) {
            $sql->where('card_id = ' . Oracle::quote($cardId));
        }

        if (!empty($limitId)) {
            $sql->where('limit_id = ' . (int)$limitId);
        }

        if (!empty($select)) {
            $sql->select($select);
        }

        $restrictions = $db->tree($sql, 'LIMIT_ID');

        $result = [];

        foreach ($restrictions as $services) {

            $restrictionServices = [];

            $restriction = reset($services);

            foreach ($services as $service) {
                $restrictionServices[] = [
                    'id'    => $service['SERVICE_ID'],
                    'name'  => $service['SERVICE_NAME'],
                ];
            }

            $restriction['services'] = $restrictionServices;

            $result[$restriction['LIMIT_ID']] = $restriction;
        }

        return $result;
    }

    /**
     * получаем инфу по конкретному лимиту
     *
     * @param $limitId
     * @return bool|mixed
     */
    public static function getLimit($limitId)
    {
        $result = self::getOilRestrictions(false, $limitId);

        return !empty($result[0]) ? $result[0] : false;
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

		$card = self::getCard($cardId);

		$where = ["card_id = ".Oracle::quote($cardId)];

		if (!empty($contractId)) {
            $where[] = "contract_id = ".Num::toInt($contractId);
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

		$card = self::getCard($cardId, $contractId);
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

		if (empty($params['status'])) {
            //получаем карты и смотрим текущий статус у нее
            $card = self::getCard($params['card_id'], $params['contract_id']);

            if (empty($card['CARD_ID'])) {
                return false;
            }

            switch ($card['CARD_STATE']) {
                case self::CARD_STATE_BLOCKED:
                    $status = self::CARD_STATE_IN_WORK;
                    break;
                default:
                    $status = self::CARD_STATE_BLOCKED;
            }
        } else {
            $status = $params['status'];
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

		if($res == Oracle::CODE_SUCCESS){
			return true;
		}

		return false;
	}

    /**
     * редактирование карты и лимитов
     *
     * @param $params
     * @return array
     */
    public static function editCardLimits($cardId, $contractId, $limits = [])
    {
        if(empty($cardId) || empty($contractId)){
            return [false, 'Ошибка'];
        }

        $currentLimits = self::getOilRestrictions($cardId);

        $checkResult = self::checkCardLimits($cardId, $contractId, $limits, $currentLimits);

        if (empty($checkResult[0])) {
            return $checkResult;
        }

        $limitsIds = !empty($limits) ? array_column($limits, 'limit_id') : [];

        foreach ($currentLimits as $limit) {
            if (!in_array($limit['LIMIT_ID'], $limitsIds)) {
                $result = self::delLimit($limit['LIMIT_ID'], $limit);

                if (empty($result[0])) {
                    return $result;
                }
            }
        }

        if(empty($limits)){
            return [true, ''];
        }

        return self::editCardLimitsSimple($cardId, $contractId, $limits);
    }

    /**
     * проверяем корректность лимитов
     */
    public static function checkCardLimits($cardId, $contractId, $limits, $currentLimits = [])
    {
        try {
            $settings = self::getCardLimitSettings($cardId);

            if ($settings['cntLimits'] < count($limits)) {
                throw new Exception('Превышено допустимое колво лимитов');
            }

            if ($settings['canSave'] == false) {
                throw new Exception('Отсутствует доступ на сохранение лимитов');
            }

            if (empty($currentLimits)) {
                $currentLimits = self::getOilRestrictions($cardId);
            }

            $user = User::current();

            if (
                (count($limits) > 9) ||
                (count($limits) == 1 && $limits[0]['limit_id'] == -1 && count($currentLimits) == 9)
            ) {
                throw new Exception('Изменение лимитов не произошло. Превышен лимит ограничений');
            }

            if (
                in_array($user['role'], array_keys(Access::$clientRoles)) &&
                !in_array($user['MANAGER_ID'], self::$editLimitsManagerNoLimit)
            ) {
                foreach ($limits as $limit) {
                    if (
                        ($limit['unit_type'] == 1 && $limit['value'] > 1000) ||
                        ($limit['unit_type'] == 2 && $limit['value'] > 30000)
                    ) {
                        if (empty($currentLimits)) {
                            throw new Exception('Изменение лимитов не произошло. Превышен допустимый лимит! Обратитесь к вашему менеджеру');
                        }
                        foreach ($limit['services'] as $service) {
                            foreach ($currentLimits as $currentLimitId => $currentLimit) {
                                foreach ($currentLimit['services'] as $currentService) {
                                    if ($currentService['id'] == $service && $limit['value'] != $currentLimit['LIMIT_VALUE']) {
                                        throw new Exception('Изменение лимитов не произошло. Превышен допустимый лимит! Обратитесь к вашему менеджеру');
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //проверка в зависимости от системы карт
            $firstFl = true;
            foreach($limits as $limit){
                $isNew = $limit['limit_id'] == -1;
                $value = str_replace(',', '.', $limit['value']);

                if (!$isNew && empty($currentLimits[$limit['limit_id']])) {
                    throw new Exception('Отсутствует запрашиваемый на редактировнаие лимит');
                }

                if (empty($limit['services'])) {
                    throw new Exception('Список сервисов для лимита должен быть заполнен');
                }

                if ($value < $settings['minValue']) {
                    throw new Exception('Минимальное доступное значение: ' . $settings['minValue']);
                }

                $servicesIds = $limit['services'];
                sort($servicesIds);
                if (!$isNew) {
                    $servicesCurrentIds = array_column($currentLimits[$limit['limit_id']]['services'], 'id');
                    sort($servicesCurrentIds);
                }

                //проверка доступа на добавление лимитов
                if ($isNew && $settings['canAddLimit'] == false) {
                    throw new Exception('Отсутствует доступ на добавление лимитов');
                }

                //проверка на добавление сервисов
                if ($settings['canAddService'] == false) {
                    if ($isNew) {
                        throw new Exception('Отсутствует доступ на добавление сервисов лимита');
                    } else {
                        if (!empty(array_diff($servicesIds, $servicesCurrentIds))) {
                            throw new Exception('Отсутствует доступ на добавление сервисов лимита');
                        }
                    }
                }

                //проверка на удаление сервисов
                if ($settings['canDelService'] == false && !$isNew) {
                    if (!empty(array_diff($servicesCurrentIds, $servicesIds))) {
                        throw new Exception('Отсутствует доступ на удаление сервисов лимита');
                    }
                }

                //проверка доступного колва сервисов
                if ($firstFl) {
                    $firstFl = false;
                    if (count($limit['services']) > $settings['cntServiceForFirstLimit']) {
                        throw new Exception('Превышено колво сервисов для первого лимита');
                    }
                } else {
                    if (count($limit['services']) > $settings['cntServiceForLimit']) {
                        throw new Exception('Превышено колво сервисов лимита');
                    }
                }

                //проверка доступных limitParams (unit_type)
                if (!in_array($limit['unit_type'], array_keys($settings['limitParams']))) {
                    throw new Exception('Использован недопустимый unit_type');
                }

                //проверка доступных limitTypes (duration_type)
                if (!in_array($limit['duration_type'], array_keys($settings['limitTypes']))) {
                    throw new Exception('Использован недопустимый duration_type');
                }

                //проверка возможности изменения сервисов
                if ($settings['editServiceSelect'] == false && !$isNew) {
                    if (
                        !empty(array_diff($servicesCurrentIds, $servicesIds))
                        || count($servicesCurrentIds) != count($servicesIds)
                    ) {
                        throw new Exception('Запрещено изменение сервисов');
                    }
                }

                //проверка возможности изменения limitParams (unit_type) и limitTypes (duration_type)
                if ($settings['editSelect'] == false && !$isNew) {
                    if ($limit['unit_type'] != $currentLimits[$limit['limit_id']]['UNIT_TYPE']) {
                        throw new Exception('Запрещено изменение unit_type');
                    }

                    if ($limit['duration_type'] != $currentLimits[$limit['limit_id']]['DURATION_TYPE']) {
                        throw new Exception('Запрещено изменение duration_type');
                    }
                }

                //проверка возможности изменения duration_value
                if ($settings['editDurationValue'] == false && !$isNew) {
                    if (isset($limit['duration_value']) && $limit['duration_value'] != $currentLimits[$limit['limit_id']]['DURATION_VALUE']) {
                        throw new Exception('Запрещено изменение duration_value');
                    }
                }

                //проверка возможности установки duration_value
                if ($settings['cntTypes'] == false && $isNew) {
                    if (!empty($limit['duration_value'])) {
                        throw new Exception('Запрещена установка duration_value');
                    }
                }

                //проверка возможности использования float
                if ($settings['canUseFloat'] == false){
                    if (is_float($value)) {
                        throw new Exception('Запрещено использование дробных значений');
                    }
                }
            }

        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, ''];
    }

    /**
     * простое редактирование лимитов
     *
     * @param $cardId
     * @param $contractId
     * @param array $limits
     */
    public static function editCardLimitsSimple($cardId, $contractId = -1, $limits = [])
    {
        try {
            if(empty($cardId) || !is_array($limits)){
                throw new Exception('Некорректные входные данные');
            }

            $user = User::current();
            $card = self::getCard($cardId);

            /*
    S1,S2,S3:DT1:DV1:UT1:UC1:V1:DWT1:DWV1:TiFr1:TiTo1:ID1

    S1,S2,S3 - набор услуг в ограничении
    DT1 - Duration type - периодичность услуги 1 - сутки, 2 недели, 3 - месяцы, 4 - кварталы, 5 - года, 10 - транзакция
    DV1 - Duration value - количество выбранных периодов
    UT1 - Unit type - параметр лимита 1 - литры, 2 - валюта
    UC1 - Unit currency - если в предыдущем параметре выбраны литры передаем "LIT", если валюта - код валюты (ISO 4217 number) пока по умолчанию передаем 643
    TC1 - Transaction count - ограничение на количество транзакций, если пустое, передаем -1 (по умолчанию передаем -1)
    V1 - Limit value - количественное значение лимита
    DWT1 - Limit of days week type - Ограничение по дням недели вкл/выкл (по умолчанию 0 - выкл)
    DWV1 - Limit of days week - Значение ограничения по дням недели, имеет вид строки из семи нулей и единиц: 0000000, 0 - выключен день, 1 - включен (по умолчанию передаем '0000000')
    TiFr1:TiTo1 - Time from / time to - Время в секундах с начала суток, показывающее период разрешенных заправок (по умолчанию передаем и там и там 0)
    ID1: Limit_id in DB. If '-1' - create new limit - Лимит полученный выборкой из БД. Если передаем значение '-1' создается новый лимит
     */
            $limitsArray = [];

            foreach($limits as $limit){

                if (empty($limit['duration_type']) || empty($limit['unit_type']) || (empty($limit['value']) && $card['SYSTEM_ID'] == self::CARD_SYSTEM_GPN)) {
                    throw new Exception('Недостаточно данных по лимиту');
                }

                $str =
                    /*dt*/ (int)$limit['duration_type'] . ':' .
                    /*dv*/ (int)(!empty($limit['duration_value']) ? $limit['duration_value'] : 1) . ':' .
                    /*ut*/ (int)$limit['unit_type'] . ':' .
                    /*uc*/ ($limit['unit_type'] == self::CARD_LIMIT_PARAM_VOLUME ? 'LIT' : Common::CURRENCY_RUR) . ':' .
                    /*tc*/ '-1:' .
                    /*v*/  $limit['value'] . ':' .
                    /*dwt*/'0:' .
                    /*dwv*/'0000000:' .
                    /*tf*/ '0:' .
                    /*tt*/ '0:' .
                    /*id*/ (!empty($limit['limit_id']) ? (int)$limit['limit_id'] : -1).
                    ';'
                ;

                $limitsArray[] =
                    /*s*/  implode(',', array_map('intval', $limit['services'])) . ':' .
                    str_replace(",", ".", $str)
                ;
            }

            $data = [
                'p_card_id'			=> $cardId,
                'p_contract_id'		=> $contractId,
                'p_limit_array'		=> [$limitsArray, SQLT_CHR],
                'p_json_str' 		=> -1,//json_encode($limits),
                'p_manager_id' 		=> $user['MANAGER_ID'],
                'p_error_code' 		=> 'out',
            ];

            $res = Oracle::init()->procedure('card_service_edit', $data);

            switch ($res) {
                case Oracle::CODE_ERROR:
                    throw new Exception('Ошибка');
                case 2:
                    throw new Exception('Превышен лимит ограничений');
                case 3:
                    throw new Exception('Задвоенные лимиты');
            }
        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, ''];
    }

    /**
     * удаляем лимит
     *
     * @param $limitId
     * @return array
     */
    public static function delLimit($limitId, $limit = [])
    {
        try {
            if (empty($limitId)) {
                throw new Exception('Некорректные входные параметры');
            }

            if (empty($limit)) {
                $limit = self::getLimit($limitId);
            }

            $card = self::getCard($limit['CARD_ID']);

            if (in_array($card['SYSTEM_ID'], self::$cantDelCardLimitSystems)) {
                throw new Exception('Отсутствует доступ на удаление лимитов');
            }

            $user = User::current();

            $data = [
                'p_limit_id' => $limitId,
                'p_manager_id' => $user['MANAGER_ID'],
                'p_error_code' => 'out',
            ];

            $res = Oracle::init()->procedure('card_limit_del', $data);

            if ($res != Oracle::CODE_SUCCESS) {
                throw new Exception('Ошибка');
            }
        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, ''];
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

		if($res == Oracle::CODE_SUCCESS){
			return true;
		}

		return false;
	}

    /**
     * получаем список групп карт
     *
     * @return array|int
     */
    public static function getGroups($filter = [])
    {
        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_CARDS_GROUPS t')
            ->where("t.manager_id = ".$user['MANAGER_ID'])
        ;

        if(!empty($filter['ids'])){
            $sql->where("t.group_id in (".implode(',', $filter['ids']).")");
        } else {
            if (!empty($filter['search'])) {
                $sql->where("upper(t.group_name) like " . mb_strtoupper(Oracle::quote('%' . $filter['search'] . '%')));
            }
        }

        if (!in_array($user['ROLE_ID'], Access::$adminRoles)) {
            $sql->where('t.GROUP_TYPE = ' . self::CARD_GROUP_TYPE_USER);
        }

        if (!empty($filter['group_type'])) {
            $sql->where('t.group_type = ' . (int)$filter['group_type']);
        }

        $db = Oracle::init();

        if(!empty($filter['limit'])){
            $sql->limit($filter['limit']);
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
            'p_group_name'      => $params['name'],
            'p_manager_id'      => $user['MANAGER_ID'],
            'p_group_type'      => !empty($params['type']) ? $params['type'] : self::CARD_GROUP_TYPE_USER,
            'p_group_id'        => 'out',
            'p_error_code' 	    => 'out',
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
            'p_group_type'        => !empty($params['type']) ? $params['type'] : self::CARD_GROUP_TYPE_USER,
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

        if (!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
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
                and exists
                   (
                        select 1 
                        from ".Oracle::$prefix."v_web_manager_cards vmc
                        where vmc.MANAGER_ID = {$user['MANAGER_ID']}
                         and vmc.card_id = vc.card_id
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

    /**
     * удаляем карты из группы
     *
     * @param $groupId
     * @param $cardsNumbers
     */
    public static function delCardsFromGroup($groupId, $cardsNumbers)
    {
        if (empty($groupId) || empty($cardsNumbers)) {
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_group_id'        => $groupId,
            'p_action'          => 2,
            'p_card_collection' => [$cardsNumbers, SQLT_CHR],
            'p_manager_id'      => $user['MANAGER_ID'],
            'p_error_code' 	    => 'out',
        ];

        $result = $db->procedure('ctrl_card_group_collection', $data);

        if ($result == Oracle::CODE_ERROR) {
            return false;
        }

        return true;
    }

    /**
     * редактируем держателя
     *
     * @param $cardId
     * @param $contractId
     * @param $holder
     * @param $date
     * @param $comment
     * @return bool
     */
    public static function editCardHolder($cardId, $contractId, $holder, $date = false, $comment = '')
    {
        if (empty($cardId) || empty($contractId)) {
            return false;
        }

        $user = User::current();

        $db = Oracle::init();

        $data = [
            'p_card_id'         => $cardId,
            'p_contract_id'     => $contractId,
            'p_new_holder'      => $holder ?: '',
            'p_card_comment'    => $comment,
            'p_date_from'       => $date ?: date('d.m.Y'),
            'p_manager_id'      => $user['MANAGER_ID'],
            'p_error_code' 	    => 'out',
        ];

        $result = $db->procedure('card_change_holder', $data);

        if ($result != Oracle::CODE_SUCCESS) {
            return false;
        }

        return true;
    }

    /**
     * получение справочника
     */
    public static function getDictionary()
    {
        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_CRD_DICTIONARY t')
            ->where('t.agent_id = '.$user['AGENT_ID'])
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * проверяем доступ юзера к карте
     *
     * @param $userId
     * @param $cardId
     * @param $contractId
     */
    public static function checkUserAccess($userId, $cardId, $contractId)
    {
        if (empty($userId) || empty($cardId) || empty($contractId)) {
            return false;
        }

        $data = [
            'p_manager_id' 		=> $userId,
            'p_contract_id'		=> $contractId,
            'p_card_id' 		=> $cardId,
        ];

        $res = Oracle::init()->func('check_manager_card', $data);

        if($res == Oracle::CODE_SUCCESS){
            return true;
        }
        return false;
    }

    /**
     * проверка доступа карты к сервису
     *
     * @param $cardId
     * @param $serviceId
     * @return bool
     */
    public static function checkServiceAccess($cardId, $serviceId)
    {
        if (empty($cardId) || empty($serviceId)) {
            return false;
        }

        $data = [
            'p_card_id' 		=> $cardId,
            'p_service_id'		=> $serviceId,
        ];

        $res = Oracle::init()->func('check_card_service', $data);

        if($res == Oracle::CODE_SUCCESS){
            return true;
        }
        return false;
    }

    /**
     * получаем настройки лимитов по карте
     *
     * @param $cardId
     */
    public static function getCardLimitSettings($cardId)
    {
        $card = Model_Card::getCard($cardId);
        $systemId = $card['SYSTEM_ID'];

        /*
         * + значит, что учтено в проверке
         */
        $settings = [
            /*+*/'canDelService'             => true,
            /*+*/'canAddService'             => true,
            /*+*/'canDelLimit'               => !in_array($systemId, Model_Card::$cantDelCardLimitSystems),
            /*+*/'canAddLimit'               => true,
            /*+*/'canSave'                   => true,
            /*+*/'editSelect'                => true,
            /*+*/'editServiceSelect'         => true,
            /*+*/'cntServiceForLimit'        => 1,
            /*+*/'cntServiceForFirstLimit'   => 1,
            /*+*/'limitParams'               => self::$cardLimitsParams,
            /*+*/'limitTypes'                => self::$cardLimitsTypes,
            /*+*/'cntTypes'                  => false,
            /*+*/'canUseFloat'               => true,
            /*+*/'cntLimits'                 => 999,
            /*+*/'editDurationValue'         => false,
            /*+*/'minValue'                  => 0,
        ];

        switch ($systemId) {
            case 1:
                $settings['canDelService']  = false;
                $settings['canAddService']  = false;
                $settings['canAddLimit']    = false;
                $settings['canSave']        = false;
                break;
            case 3:
                $settings['canAddLimit']        = false;
                $settings['canDelService']      = false;
                $settings['canAddService']      = false;
                $settings['editSelect']         = false;
                $settings['editServiceSelect']  = false;
                break;
            case 4:
                $settings['canDelService']  = false;
                $settings['canAddService']  = false;
                $settings['canAddLimit']    = false;
                $settings['canSave']        = false;
                break;
            case self::CARD_SYSTEM_GPN:
                $settings['cntServiceForFirstLimit'] = 999;
                $settings['limitTypes']     = Model_Card::$cardLimitsTypesFull;
                $settings['cntTypes']       = true;
                $settings['editSelect']     = false;
                $settings['canUseFloat']    = false;
                $settings['minValue']       = 1;
                break;
            case 6:
                $settings['cntServiceForLimit'] = 999;
                //можно все
                break;
            case 7:
                $settings['limitParams'] = [
                    self::CARD_LIMIT_PARAM_RUR => $settings['limitParams'][self::CARD_LIMIT_PARAM_RUR]
                ];
                $settings['limitTypes'] = [
                    self::CARD_LIMIT_TYPE_DAY => $settings['limitTypes'][self::CARD_LIMIT_TYPE_DAY]
                ];
                break;
            case 8:
                $settings['cntLimits'] = 1;
                $settings['cntServiceForFirstLimit'] = 999;
                $settings['limitParams'] = [
                    self::CARD_LIMIT_PARAM_RUR => $settings['limitParams'][self::CARD_LIMIT_PARAM_RUR]
                ];
                $settings['limitTypes'] = [
                    self::CARD_LIMIT_TYPE_DAY => $settings['limitTypes'][self::CARD_LIMIT_TYPE_DAY]
                ];
                break;
        }

        return $settings;
    }

    /**
     * перенос карт с одного договора на другой
     *
     * @param $oldContractId
     * @param $newContractId
     * @param $cards
     * @param $params
     * @return bool
     */
    public static function transferCards($oldContractId, $newContractId, $cards = [], $params = [])
    {
        if (empty($oldContractId) || empty($newContractId)) {
            return false;
        }

        if (empty($cards)) {
            $cards = [-1];
        }

        $user = User::current();

        $data = [
            'p_old_contract'        => $oldContractId,
            'p_new_contract'        => $newContractId,
            'p_cards_array'         => [(array)$cards, SQLT_CHR],
            'p_date_change'         => !empty($params['save_holder']) ? $params['save_holder'] : date('d.m.Y'),
            'p_date_last_trz'       => !empty($params['date_to']) ? $params['date_to'] : Model_Contract::DEFAULT_DATE_END,
            'p_fl_transfer_cards'   => !empty($params['transfer_cards']) ? 1 : 0,
            'p_fl_transfer_trn'     => !empty($params['transfer_transactions']) ? 1 : 0,
            'p_fl_save_holder'      => !empty($params['save_holder']) ? 1 : 0,
            'p_manager_id'          => $user['MANAGER_ID'],
            'p_error_code'          => 'out',
        ];

        $res = Oracle::init()->procedure('srv_ctr_cards_trz_change', $data);

        if($res == Oracle::CODE_SUCCESS){
            return true;
        }
        return false;
    }
}