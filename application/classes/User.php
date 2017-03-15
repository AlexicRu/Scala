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
}