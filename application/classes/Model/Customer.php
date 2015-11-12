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

        $proc = 'begin '.Oracle::$prefix.'web_pack.manager_edit(
			:p_manager_id,
			:p_role_id,
			:p_name,
			:p_surname,
			:p_middlename,
			:p_phone,
			:p_email,
			:p_error_code
        ); end;';

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

        $res = $db->ora_proced($proc, $data);

        if($res['p_error_code'] == Oracle::CODE_ERROR){
            return false;
        }

        if(
            !empty($params['customer_settings_password']) && !empty($params['customer_settings_password_again']) &&
            $params['customer_settings_password'] == $params['customer_settings_password_again']
        ){
            //обновление паролей
            $proc = 'begin '.Oracle::$prefix.'web_pack.manager_change_password(
                :p_manager_id,
                :p_new_password,
                :p_error_code
            ); end;';

            $data = [
                'p_manager_id' 	    => $user['MANAGER_ID'],
                'p_new_password'    => empty($params['customer_settings_password'])      ? '' : $params['customer_settings_password'],
                'p_error_code' 	    => 'out',
            ];

            $res = $db->ora_proced($proc, $data);

            if(!empty($res['p_error_code'])){
                return false;
            }
        }

        Auth::instance()->regenerate_user_profile();

        return true;
    }
}