<?php defined('SYSPATH') or die('No direct script access.');

class Model_Supplier_Contract extends Model
{
    const STATE_CONTRACT_WORK = 1;
    const STATE_CONTRACT_EXPIRED = 5;
    const STATE_CONTRACT_BLOCKED = 10;

    const DATA_SOURCE_INSIDE = 1;
    const DATA_SOURCE_OUTSIDE = 2;

    public static $statusContractNames = [
        self::STATE_CONTRACT_WORK 			=> 'В работе',
        self::STATE_CONTRACT_BLOCKED 		=> 'Заблокирован',
        self::STATE_CONTRACT_EXPIRED 		=> 'Завершен',
    ];

    public static $statusContractClasses = [
        self::STATE_CONTRACT_WORK 			=> 'label_success',
        self::STATE_CONTRACT_BLOCKED 		=> 'label_error',
        self::STATE_CONTRACT_EXPIRED 		=> 'label_warning',
    ];

    /**
     * грузим список контрактов поставщиков
     *
     * @param array $params
     */
    public static function getList($params = [])
    {
        if(empty($params)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SUPPLIERS_CONTRACTS t where 1=1 ";

        if(!empty($params['supplier_id'])){
            $sql .= " and t.supplier_id = ".Oracle::toInt($params['supplier_id']);
        }
        if(!empty($params['contract_id'])){
            $sql .= " and t.contract_id = ".Oracle::toInt($params['contract_id']);
        }

        return $db->query($sql);
    }

    /**
     * обертка для getList
     *
     * @param $contractId
     */
    public static function get($contractId)
    {
        if (empty($contractId)) {
            return false;
        }

        $contracts = self::getList(['contract_id' => $contractId]);

        if (empty($contracts)) {
            return false;
        }

        return reset($contracts);
    }

    /**
     * получаем список труб
     *
     * @return array|bool
     */
    public static function getTubes()
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = "select * from ".Oracle::$prefix."V_WEB_TUBES_LIST t where t.is_owner = 1 and t.agent_id=".$user['AGENT_ID'];

        return $db->query($sql);
    }


    /**
     * @param $contractId
     * @param $params
     */
    public static function edit($contractId, $params)
    {
        if(
            empty($contractId) ||
            empty($params['CONTRACT_NAME'])
        ){
            return [false, ''];
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_contract_id'         => $contractId,
            'p_contract_state'      => $params['CONTRACT_STATE'],
            'p_contract_name'       => $params['CONTRACT_NAME'],
            'p_date_begin'          => $params['DATE_BEGIN'],
            'p_date_end'            => $params['DATE_END'],
            'p_contract_cur'        => Model_Contract::CURRENCY_RUR,
            'p_contract_source'     => $params['DATA_SOURCE'],
            'p_contract_tube'       => $params['TUBE_ID'],
            'p_contract_service'    => [$params['CONTRACT_SERVICES'], SQLT_INT],
            'p_contract_pos_groups' => [$params['CONTRACT_POS_GROUPS'], SQLT_INT],
            'p_manager_id'          => $user['MANAGER_ID'],
            'p_error_code'          => 'out',
        ];

        $res = $db->procedure('splrs_contract_edit', $data, true);

        $return = [true, ''];

        if($res['p_error_code'] != Oracle::CODE_SUCCESS){
            switch ($res['p_error_code']) {
                case 2:
                    $error = 'Данный источник используется';
                    break;
                case 3:
                    $error = 'Для услуг есть действующий договор';
                    break;
                case 4:
                    $error = 'Для точек есть действующий договор';
                    break;
                default:
                    $error = 'Ошибка';
            }
            $return = [false, $error];
        }

        return $return;
    }
}
