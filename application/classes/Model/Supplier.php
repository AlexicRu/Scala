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

        $sql = (new Builder())->select()
            ->from('V_WEB_SUPPLIERS_LIST t')
            ->where('t.agent_id = '.$user['AGENT_ID'])
            ->orderBy('t.SUPPLIER_NAME')
        ;

        if (!empty($params['supplier_id'])) {
            $sql->where('t.id = '.Num::toInt($params['supplier_id']));
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

    /**
     * @param $supplierId
     * @param $params
     */
    public static function editSupplier($supplierId, $params)
    {
        if(
            empty($supplierId) ||
            empty($params)
        ){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_supplier_id'     => $supplierId,
            'p_supplier_name'   => $params['SUPPLIER_NAME'],
            'p_long_name'       => $params['LONG_NAME'] ?: $params['SUPPLIER_NAME'],
            'p_tin'             => $params['INN'],
            'p_iec'             => $params['KPP'],
            'p_psrn'            => $params['OGRN'],
            'p_okpo'            => $params['OKPO'],
            'p_y_address'       => $params['Y_ADDRESS'],
            'p_f_address'       => $params['F_ADDRESS'],
            'p_p_address'       => $params['P_ADDRESS'],
            'p_email'           => Text::checkEmailMulti($params['EMAIL']),
            'p_phone'           => $params['PHONE'],
            'p_comments'        => $params['COMMENTS'],
            'p_okonh'           => $params['OKONH'],
            'p_contact_person'  => $params['CONTACT_PERSON'],
            'p_icon_path' 	    => $params['ICON_PATH'] ?: -1,
            'p_manager_id'      => $user['MANAGER_ID'],
            'p_error_code'      => 'out',
        ];

        $res = $db->procedure('splrs_edit', $data);

        if(empty($res)){
            return true;
        }

        return false;
    }

    /**
     * добавление поставщика по имени
     *
     * @param $params
     */
    public static function add($params)
    {
        if(empty($params['name'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_name' 		=> $params['name'],
            'p_manager_id' 	=> $user['MANAGER_ID'],
            'p_client_id' 	=> 'out',
            'p_error_code' 	=> 'out',
        ];

        $res = $db->procedure('splrs_add', $data);

        if(empty($res)){
            return true;
        }

        return false;
    }
}
