<?php defined('SYSPATH') or die('No direct script access.');

class Model_News extends Model
{
    /**
     * грузим новости
     *
     * @param array $params
     * @param bool $user
     * @return array
     */
    public static function load($params = [], $user = false)
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        //todo

        /*if(!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }*/
        
        $notice = [];

        if($user['ROLE_ID'] == Access::ROLE_ROOT) {
            $news = [
                50 => ['title', 'text'],
            ];
        }

        if(!empty($params['pagination'])) {
            return [$news, true];
        }

        return $news;
    }


    /**
     * отмечаем сообщения прочитанными
     *
     * @param bool $user
     */
    public static function makeRead($user = false)
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        //todo

        return true;
    }
}