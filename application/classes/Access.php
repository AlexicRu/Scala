<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
    const USER_TEST 	= 6;

    const ROLE_ROOT 	                = 1;
    const ROLE_ADMIN 	                = 2;
    const ROLE_SUPERVISOR               = 3;
    const ROLE_MANAGER                  = 4;
    const ROLE_USER		                = 99;
    const ROLE_MANAGER_SALE		        = 5;
    const ROLE_MANAGER_SALE_SUPPORT		= 6;

    /**
     * функция проверки доступа
     */
    public static function allow($action)
    {
        if(empty($action)){
            return true;
        }

        $user = Auth_Oracle::instance()->get_user();

        if(in_array($user['role'], [self::ROLE_ROOT])){
            return true;
        }

        $access = Kohana::$config->load('access')->as_array();

        $allow = $access['allow'];
        $deny = $access['deny'];

        if(
            (isset($allow[$action]) && !in_array($user['role'], $allow[$action])) ||
            (isset($deny[$action]) && in_array($user['role'], $deny[$action]))
        ){
            return false;
        }

        //если нет явного запрета или наоборот, доступа только конкретной роли

        return true;
    }

    /**
     * проверка запрета доступа
     *
     * @param $action
     * @return bool
     */
    public static function deny($action)
    {
        return !self::allow($action);
    }

    /**
     * проверка доступов
     *
     * @param $type
     * @param $id
     */
    public static function check($type, $id)
    {
        $allow = false;

        if(!empty($type)){
            switch($type){
                case 'client':
                    if(!empty($id)){
                        $clients = Model_Client::getClientsList();

                        if(!empty($clients[$id])){
                            $allow = true;
                        }
                    }
                    break;
            }
        }

        if(!$allow){
            throw new HTTP_Exception_404();
        }
    }
}