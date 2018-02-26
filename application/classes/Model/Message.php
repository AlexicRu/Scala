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
            ->where("note_type = ".$params['note_type'])
            ->orderBy('date_time desc')
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
            $user = User::current();
        }

        $data = [
            'p_note_guid' 		=> !empty($params['note_guid']) ? $params['note_guid'] : null,
            'p_new_status' 	    => self::MESSAGE_STATUS_READ,
            'p_note_type' 		=> !empty($params['note_type']) ? $params['note_type'] : self::MESSAGE_TYPE_COMMON,
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('notification_change_status', $data);

        if($res != Oracle::CODE_SUCCESS){
            return false;
        }

        return true;
    }

    /**
     * парсим BB коды
     *
     * @param $messages
     * @param $addMarkReadLink
     * @return mixed
     */
    public static function parseBBCodes($messages, $addMarkReadLink = true)
    {
        /*
        [client|:client_id]client_name[/client] => ссылка на клиента
        [contract|:contract_id]contract_name[/contract] => ссылка на конкретный договор клиента
        [manager|:manager_id]manager_name[/manager] => ссылка на менеджера (пока нет прямой ссылки)
        [supplier|:supplier_id]supplier_name[/supplier] => ссылка на поставщика
        [supplier_contract|:supplier_contract_id]supplier_contract_name[/supplier_contract] => ссылка на договор поставки (пока нет прямой ссылки)
         */
        foreach ($messages as &$message) {

            $markReadLink = '';

            if ($addMarkReadLink /*&& $message['STATUS'] == Model_Message::MESSAGE_STATUS_NOTREAD*/) {
                $markReadLink = '&read=' . $message['NOTE_GUID'];
            }

            $message['NOTIFICATION_BODY'] = preg_replace("/\[contract\|(.*)\|(.*)\](.*)\[\/contract\]/", "<a href='/clients/client/$2?contract_id=$1".$markReadLink."'>$3</a>", $message['NOTIFICATION_BODY']);
            $message['NOTIFICATION_BODY'] = preg_replace("/\[client\|(.*)\](.*)\[\/client\]/", "<a href='/clients/client/$1?".$markReadLink."'>$2</a>", $message['NOTIFICATION_BODY']);
            $message['NOTIFICATION_BODY'] = preg_replace("/\[supplier\|(.*)\](.*)\[\/supplier\]/", "<a href='/suppliers/$1?".$markReadLink."'>$2</a>", $message['NOTIFICATION_BODY']);
        }

        return $messages;
    }

    /**
     * Удаляем BB коды
     *
     * @param $messages
     * @return mixed
     */
    public static function clearBBCodes($messages)
    {
        foreach ($messages as &$message) {
            $message['NOTIFICATION_BODY'] = preg_replace("/\[contract\|(.*)\|(.*)\](.*)\[\/contract\]/", "$3", $message['NOTIFICATION_BODY']);
            $message['NOTIFICATION_BODY'] = preg_replace("/\[client\|(.*)\](.*)\[\/client\]/", "$2", $message['NOTIFICATION_BODY']);
            $message['NOTIFICATION_BODY'] = preg_replace("/\[supplier\|(.*)\](.*)\[\/supplier\]/", "$2", $message['NOTIFICATION_BODY']);
        }

        return $messages;
    }
}