<?php defined('SYSPATH') or die('No direct script access.');

class Model_Message extends Model
{
    const MESSAGE_STATUS_NOTREAD = 0;
    const MESSAGE_STATUS_READ = 1;

    /**
     * собираем доступные пользовалю сообщения
     *
     * @param array $params
     * @return array
     */
    public static function collect($params = [])
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_NOTIFICATION where manager_id = ".$user['MANAGER_ID'];

        if(!empty($params['not_read'])){
            $sql .= ' and status = '.self::MESSAGE_STATUS_NOTREAD;
        }
        if(!empty($params['search'])){
            //todo
        }

        $sql .= ' order by date_time desc';

        if(!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }

        return $db->query($sql);
    }


    /**
     * отмечаем сообщения прочитанными
     *
     * @param bool $user
     */
    public static function makeRead($params = [], $user = false)
    {
        $db = Oracle::init();

        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        $data = [
            'p_note_guid' 		=> $params['note_guid'],
            'p_new_status' 	    => self::MESSAGE_STATUS_READ,
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('notification_change_status', $data);

        if(!empty($res)){
            return $res;
        }

        return true;
    }
}