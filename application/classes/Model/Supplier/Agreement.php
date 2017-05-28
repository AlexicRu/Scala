<?php defined('SYSPATH') or die('No direct script access.');

class Model_Supplier_Agreement extends Model
{
    const DISCOUNT_TYPE_FROM_LOAD = 1;
    const DISCOUNT_TYPE_FROM_TARIFF = 2;
    /**
     * грузим список соглашений по контракту поставщиков
     *
     * @param array $params
     */
    public static function getList($params = [])
    {
        if (empty($params)) {
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from " . Oracle::$prefix . "V_WEB_SUPLS_AGREEMENTS  t where 1=1 ";

        if (!empty($params['contract_id'])) {
            $sql .= " and t.contract_id = " . Oracle::toInt($params['contract_id']);
        }
        if (!empty($params['agreement_id'])) {
            $sql .= " and t.agreement_id = " . Oracle::toInt($params['agreement_id']);
        }

        $sql .= ' order by t.DATE_BEGIN desc';

        return $db->query($sql);
    }

    /**
     * обертка для getList
     *
     * @param $agreementId
     * @param $contractId
     */
    public static function get($agreementId, $contractId)
    {
        if (empty($agreementId) || empty($contractId)) {
            return false;
        }

        $agreements = self::getList([
            'agreement_id ' => $agreementId,
            'contract_id' => $contractId
        ]);

        if (empty($agreements)) {
            return false;
        }
        return reset($agreements);
    }
}