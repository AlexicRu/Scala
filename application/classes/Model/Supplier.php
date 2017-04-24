<?php defined('SYSPATH') or die('No direct script access.');

class Model_Supplier extends Model
{
    /**
     * грузим список поставщиков
     *
     * @param array $params
     */
    public static function getList($params = [])
    {
        if(!isset($params['pagination']) && empty($params)){
            return false;
        }

        $db = Oracle::init();

        $user = User::current();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SUPPLIERS_LIST t where t.agent_id = ".$user['AGENT_ID'];

        if (!empty($params['supplier_id'])) {
            $sql .= ' and t.id = '.Oracle::toInt($params['supplier_id']);
        }

        if (!empty($params['pagination'])) {
            $params['limit'] = 15;

            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
    }

    /**
     * обертка для getList
     *
     * @param $supplierId
     */
    public static function getSupplier($supplierId)
    {
        if (empty($supplierId)) {
            return false;
        }

        $suppliers = self::getList(['supplier_id' => $supplierId]);

        if (empty($suppliers)) {
            return false;
        }

        return $suppliers[0];
    }
}
