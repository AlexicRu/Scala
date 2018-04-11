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
            $sql .= " and t.contract_id = " . Num::toInt($params['contract_id']);
        }
        if (!empty($params['agreement_id'])) {
            $sql .= " and t.agreement_id = " . Num::toInt($params['agreement_id']);
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
            'agreement_id' => $agreementId,
            'contract_id' => $contractId
        ]);

        if (empty($agreements)) {
            return false;
        }
        return reset($agreements);
    }

    /**
     * редактирование соглашения
     *
     * @param $params
     */
    public static function edit($params)
    {
        if(empty($params['contract_id']) || empty($params['agreement_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_contract_id' 	=> $params['contract_id'],
            'p_agreement_id' 	=> $params['agreement_id'],
            'p_agreement_name' 	=> $params['agreement_name'],
            'p_date_begin' 		=> $params['date_begin'],
            'p_date_end' 	    => !empty($params['date_end']) ? $params['date_end'] : Model_Contract::DEFAULT_DATE_END,
            'p_discount_type' 	=> $params['discount_type'],
            'p_tarif_id' 		=> $params['tarif_id'] ?: -1,
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('splrs_cntr_agreement_edit', $data);

        switch ($res) {
            case Oracle::CODE_SUCCESS:
                $result = true;
                break;
            case Oracle::CODE_ERROR_EXISTS:
                Messages::put('Уже есть соглашение на данный период');
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * добавление соглашения
     *
     * @param $params
     */
    public static function add($params)
    {
        if(empty($params['contract_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_contract_id' 	=> $params['contract_id'],
            'p_agreement_name' 	=> $params['agreement_name'],
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_agreement_id' 	=> 'out',
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('splrs_cntr_agreement_add', $data);

        if($res == Oracle::CODE_ERROR){
            return false;
        }

        return true;
    }
}