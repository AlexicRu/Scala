<?php defined('SYSPATH') or die('No direct script access.');

class User
{
    /**
     * получение текущего пользователя
     *
     * @return mixed
     */
    public static function current()
    {
        return Auth::instance()->get_user();
    }

    /**
     * проверяем доступность логина из под одного пользователя в другого
     *
     * @param $userFrom
     * @param $userTo
     * @return bool
     */
    public static function checkForceLogin($userFrom, $userTo)
    {
        return true;
    }

    /**
     * Получаем имя для вывода
     *
     * @param $user
     * @return string
     */
    public static function getName($user)
    {
        if(!empty($user['M_NAME'])){
            $name = $user['M_NAME'];
        }elseif(!empty($user['MANAGER_NAME']) && !empty($user['MANAGER_SURNAME']) && !empty($user['MANAGER_MIDDLENAME'])){
            $name = $user['MANAGER_NAME'].' '.$user['MANAGER_SURNAME'].' '.$user['MANAGER_MIDDLENAME'];
        }elseif(!empty($user['FIRM_NAME'])){
            $name = $user['FIRM_NAME'];
        }else{
            $name = $user['LOGIN'];
        }

        return $name;
    }
}