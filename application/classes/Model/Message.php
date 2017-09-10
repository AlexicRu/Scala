<?php defined('SYSPATH') or die('No direct script access.');

class Model_Message extends Model
{
    const MESSAGE_STATUS_NOTREAD = 0;
    const MESSAGE_STATUS_READ = 1;

    const MESSAGE_TYPE_COMMON = 1;
    const MESSAGE_TYPE_GLOBAL = 2;

    /**
     * собираем доступные пользовалю сообщения
     *
     * @param array $params
     * @return array
     */
    public static function getList($params = [])
    {
        $user = User::current();

        $db = Oracle::init();

        if (empty($params['note_type'])) {
            $params['note_type'] = self::MESSAGE_TYPE_COMMON;
        }

        $sql = (new Builder())->select()
            ->from('V_WEB_NOTIFICATION')
            ->where("manager_id = ".$user['MANAGER_ID'])
            ->orderBy('date_time desc')
            ->where("note_type = ".$params['note_type'])
        ;

        if(isset($params['status'])){
            $sql->where("status = ".$params['status']);
        }
        if(!empty($params['search'])){
            $search = mb_strtoupper(Oracle::quote('%'.$params['search'].'%'));
            $sql->where("(upper(NOTIFICATION_BODY) like ".$search." or subject like ".$search.")");
        }

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
            'p_note_guid' 		=> null,
            'p_new_status' 	    => self::MESSAGE_STATUS_READ,
            'p_note_type' 		=> $params['note_type'],
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('notification_change_status', $data);

        if($res != Oracle::CODE_SUCCESS){
            return false;
        }

        return true;
    }
}