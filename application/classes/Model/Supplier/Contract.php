<?php defined('SYSPATH') or die('No direct script access.');

class Model_Supplier_Contract extends Model
{
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
}
