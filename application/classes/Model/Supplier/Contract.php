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
}
