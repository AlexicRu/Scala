<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
    const USER_TEST 	= 6;

    const ROLE_ADMIN 	        = 3;
    const ROLE_USER		        = 99;
    const ROLE_MANAGER_SALE		= 5;

    /**
     * функция проверки доступа
     */
    public static function allow($action)
    {
        if(empty($action)){
            return true;
        }

        $user = Auth_Oracle::instance()->get_user();

        if($user['role'] == self::ROLE_ADMIN){
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
}