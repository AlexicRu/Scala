<?php defined('SYSPATH') or die('No direct script access.');

class Model_Manager extends Model
{
    const STATE_MANAGER_ACTIVE      = 1;
    const STATE_MANAGER_BLOCKED     = 0;

    /**
     * радактируем данные юзверя
     *
     * @param $params
     * @param bool|false $userId
     */
    public static function edit($params, $user = false)
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        if(
            empty($user['MANAGER_ID']) ||
            empty($user['role'])
        ){
            return false;
        }

        $db = Oracle::init();

        $data = [
            'p_manager_id' 	=> $user['MANAGER_ID'],
            'p_role_id' 	=> $user['role'],
            'p_name' 	    => empty($params['manager_settings_name'])         ? '' : $params['manager_settings_name'],
            'p_surname' 	=> empty($params['manager_settings_surname'])      ? '' : $params['manager_settings_surname'],
            'p_middlename' 	=> empty($params['manager_settings_middlename'])   ? '' : $params['manager_settings_middlename'],
            'p_phone' 		=> empty($params['manager_settings_phone'])        ? '' : $params['manager_settings_phone'],
            'p_email' 		=> empty($params['manager_settings_email'])        ? '' : $params['manager_settings_email'],
            'p_error_code' 	=> 'out',
        ];

        $res = $db->procedure('manager_edit', $data);

        if($res == Oracle::CODE_ERROR){
            return false;
        }

        if(
            !empty($params['manager_settings_password']) && !empty($params['manager_settings_password_again']) &&
            $params['manager_settings_password'] == $params['manager_settings_password_again'] &&
            $user['MANAGER_ID'] != Access::USER_TEST
        ){
            //обновление паролей

            $data = [
                'p_manager_id' 	    => $user['MANAGER_ID'],
                'p_new_password'    => empty($params['manager_settings_password'])      ? '' : $params['manager_settings_password'],
                'p_error_code' 	    => 'out',
            ];

            $res = $db->procedure('manager_change_password', $data);

            if(!empty($res)){
                return false;
            }
        }

        Auth::instance()->regenerate_user_profile();

        return true;
    }

    /**
     * список менеджеров
     *
     * @param $params
     * @return array|bool|int
     */
    public static function getManagersList($params = [])
    {
        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_MANAGERS where 1 = 1 ";

        if(!empty($params['name'])){
            $params['name'] = mb_strtoupper($params['name']);
            $sql .= " and (
                upper(MANAGER_NAME) like '%". Oracle::quote($params['name'])."%' or 
                upper(MANAGER_SURNAME) like '%". Oracle::quote($params['name'])."%' or 
                upper(MANAGER_MIDDLENAME) like '%". Oracle::quote($params['name'])."%' 
            )";
        }
        unset($params['name']);

        foreach($params as $key => $value){
            $sql .= " and ".strtoupper($key)." = '". Oracle::quote($value)."' ";
        }

        $sql .= ' order by MANAGER_NAME';

        $users = $db->query($sql);

        if(empty($users)){
            return false;
        }

        foreach($users as &$user){
            $user['role'] = $user['ROLE_ID'];
        }

        return $users;
    }

    /**
     * получаем менеджера
     */
    public static function getManager($params)
    {
        if(empty($params)){
            return false;
        }

        if(!is_array($params)){
            $params = ['manager_id' => (int)$params];
        }

        $managers = self::getManagersList($params);

        if(empty($managers)){
            return false;
        }

        return reset($managers);
    }

    /**
     * блокировка/разблокировка
     *
     * @param $cardId
     */
    public static function toggleStatus($params)
    {
        if(empty($params['manager_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        //получаем карты и смотрим текущий статус у нее
        $manager = self::getManager($params['manager_id']);

        if(empty($manager['MANAGER_ID'])){
            return false;
        }

        switch($manager['STATE_ID']){
            case self::STATE_MANAGER_ACTIVE:
                $status = self::STATE_MANAGER_BLOCKED;
                break;
            default:
                $status = self::STATE_MANAGER_ACTIVE;
        }
        return true;
        $data = [
            'p_card_id' 		=> $params['card_id'],
            'p_new_state' 		=> $status,
            'p_comment' 		=> $params['comment'],
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        //todo
        //$res = $db->procedure('card_change_state', $data);

        if(empty($res)){
            return true;
        }

        return false;
    }
}