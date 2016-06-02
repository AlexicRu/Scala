<?php defined('SYSPATH') or die('No direct script access.');

class Model_Message extends Model
{
    /**
     * собираем доступные пользовалю сообщения
     *
     * @param array $params
     * @param bool $user
     * @return array
     */
    public static function collect($params = [], $user = false)
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        //todo

        if(!empty($params['not_read'])){
            
        }

        /*if(!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }*/
        
        $notice = [];

        if($user['ROLE_ID'] == Access::ROLE_ROOT) {
            $notice = [
                50 => ['title', 'text'],
                51 => ['title title', 'text огромный текст'],
                52 => ['title', 'text а вот тут можно было и поэму какую-нить написать, вот как все длинно'],
                53 => ['title очень длинный заголовок', 'text'],
                54 => ['title', 'text'],
                55 => ['title ну прям совсем неприлично длинный заголовок', 'text'],
                60 => ['title', 'text'],
                70 => ['title', 'text'],
                83 => ['title', 'text'],
                100 => ['title', 'text'],
            ];
        }

        if(!empty($params['pagination'])) {
            return [$notice, true];
        }

        return $notice;
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