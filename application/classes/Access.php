<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
    const ROLE_ADMIN 	= 3;
    const ROLE_USER		= 99;

    /**
     * функция проверки доступа
     */
    public static function allow($action)
    {
        if(empty($action)){
            return false;
        }

        $user = Auth_Oracle::instance()->get_user();

        if($user['role'] == self::ROLE_ADMIN){
            return true;
        }

        $access = Kohana::$config->load('access')->as_array();

        $allow = $access['allow'];
        $deny = $access['deny'];

        if(
            !array_key_exists($action, $allow) &&
            !array_key_exists($action, $deny)
        ){
            return true; //все что не разрешено, то запрещено
        }

        if(
            (isset($allow[$action]) && !in_array($user['role'], $allow[$action])) ||
            (isset($deny[$action]) && in_array($user['role'], $deny[$action]))
        ){
            return false;
        }

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