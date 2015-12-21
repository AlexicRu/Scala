<?php defined('SYSPATH') or die('No direct script access.');

class Model_Customer extends Model
{
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
            'p_name' 	    => empty($params['customer_settings_name'])         ? '' : $params['customer_settings_name'],
            'p_surname' 	=> empty($params['customer_settings_surname'])      ? '' : $params['customer_settings_surname'],
            'p_middlename' 	=> empty($params['customer_settings_middlename'])   ? '' : $params['customer_settings_middlename'],
            'p_phone' 		=> empty($params['customer_settings_phone'])        ? '' : $params['customer_settings_phone'],
            'p_email' 		=> empty($params['customer_settings_email'])        ? '' : $params['customer_settings_email'],
            'p_error_code' 	=> 'out',
        ];

        $res = $db->procedure('manager_edit', $data);

        if($res == Oracle::CODE_ERROR){
            return false;
        }

        if(
            !empty($params['customer_settings_password']) && !empty($params['customer_settings_password_again']) &&
            $params['customer_settings_password'] == $params['customer_settings_password_again'] &&
            $user['MANAGER_ID'] != Access::USER_TEST
        ){
            //обновление паролей

            $data = [
                'p_manager_id' 	    => $user['MANAGER_ID'],
                'p_new_password'    => empty($params['customer_settings_password'])      ? '' : $params['customer_settings_password'],
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
}