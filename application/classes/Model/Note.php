<?php defined('SYSPATH') or die('No direct script access.');

class Model_Note extends Model
{
    const NOTE_TYPE_NEWS    = 1;
    const NOTE_TYPE_MESSAGE = 2;
    const NOTE_TYPE_POPUP   = 3;

    const NOTE_STATUS_NOTREAD = 0;
    const NOTE_STATUS_READ = 1;

    /**
     * добавляем заметку
     *
     * @param $params
     */
    public static function editNote($params)
    {
        if(empty($params)){
            return false;
        }

        $db = Oracle::init();
        $user = User::current();

        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        $image = (!empty($params['image']) && is_file($path.$params['image'])) ? $params['image'] : '';

        if(!empty($params['id'])) {
            $data = [
                'p_note_id' 		    => $params['id'],
                'p_note_date' 		    => $params['date'],
                'p_note_title' 		    => $params['title'],
                'p_note_body' 		    => $params['body'],
                'p_picture' 	        => $image,
                'p_manager_who_id' 	    => $user['MANAGER_ID'],
                'p_error_code' 	        => 'out',
            ];

            $res = $db->procedure('note_edit', $data);
        } else {
            $data = [
                'p_agent_id' 	        => $user['AGENT_ID'],
                'p_agent_group_id' 	    => !isset($params['subscribe']) || $params['subscribe'] == 'all' ? -1 : $params['subscribe_agent'],
                'p_manager_id' 		    => null,
                'p_note_date' 		    => $params['date'],
                'p_note_type' 		    => $params['type'],
                'p_note_title' 		    => $params['title'],
                'p_note_body' 		    => $params['body'],
                'p_picture' 	        => $image,
                'p_manager_who_create' 	=> $user['MANAGER_ID'],
                'p_error_code' 	        => 'out',
            ];

            $res = $db->procedure('note_add', $data);
        }

        if($res == Oracle::CODE_ERROR){
            return false;
        }
        return true;
    }

    /**
     * грузим записи
     *
     * @param array $params
     * @return array
     */
    public static function getList($params = [])
    {
        $db = Oracle::init();

        $sql = (new Builder())->select()
            ->from('V_WEB_NOTES t')
            ->where('t.manager_id = ' . User::id())
            ->orderBy([
                'note_date_sort desc',
                'note_id desc'
            ])
        ;

        if(!empty($params['search'])){
            $search = mb_strtoupper(Oracle::quote('%'.$params['search'].'%'));
            $sql->where("(upper(t.NOTE_BODY) like ".$search." or subject like ".$search.")");
        }

        if (!empty($params['note_type'])) {
            $sql->where('t.note_type = '. (int)$params['note_type']);
        }

        if (!empty($params['note_id'])) {
            $sql->where('t.note_id = '. (int)$params['note_id']);
        }

        if (isset($params['status'])) {
            $sql->where('t.status = '. (int)$params['status']);
        }

        if(!empty($params['pagination'])) {
            list($news, $more) = $db->pagination($sql, $params);

            foreach($news as &$newsDetail){
                $newsDetail['announce'] = strip_tags($newsDetail['NOTE_BODY']);

                if(strlen($newsDetail['announce']) > 500){
                    $newsDetail['announce'] = mb_strcut($newsDetail['announce'], 0, 500);
                }
            }

            return [$news, $more];
        }

        $news = $db->tree($sql, 'NOTE_ID', true);

        foreach($news as &$newsDetail){
            $newsDetail['announce'] = strip_tags($newsDetail['NOTE_BODY']);

            if(strlen($newsDetail['announce']) > 500){
                $newsDetail['announce'] = mb_strcut($newsDetail['announce'], 0, 500);
            }
        }

        return $news;
    }

    /**
     * получаем конкретную запись
     *
     * @param $noteId
     */
    public static function getNoteById($noteId)
    {
        if(empty($noteId)){
            return false;
        }

        $detail = self::getList([
            'pagination'    => false,
            'note_id'       => $noteId
        ]);

        if(!empty($detail[$noteId])){
            return $detail[$noteId];
        }

        return false;
    }

    /**
     * отмечаем сообщения прочитанными
     *
     * @param array $params
     */
    public static function makeRead($params = [])
    {
        $db = Oracle::init();

        $user = User::current();

        $data = [
            'p_note_id' 		=> !empty($params['note_id']) ? $params['note_id'] : null,
            'p_new_status' 	    => self::NOTE_STATUS_READ,
            'p_note_type' 		=> !empty($params['note_type']) ? $params['note_type'] : self::NOTE_TYPE_MESSAGE,
            'p_manager_id' 		=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('note_status_change', $data);

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

            if ($addMarkReadLink /*&& $message['STATUS'] == Model_Note::NOTE_STATUS_NOTREAD*/) {
                $markReadLink = '&read=' . $message['NOTE_ID'];
            }

            $message['NOTE_BODY'] = preg_replace("/\[contract\|(.*)\|(.*)\](.*)\[\/contract\]/", "<a href='/clients/client/$2?contract_id=$1".$markReadLink."'>$3</a>", $message['NOTE_BODY']);
            $message['NOTE_BODY'] = preg_replace("/\[client\|(.*)\](.*)\[\/client\]/", "<a href='/clients/client/$1?".$markReadLink."'>$2</a>", $message['NOTE_BODY']);
            $message['NOTE_BODY'] = preg_replace("/\[supplier\|(.*)\](.*)\[\/supplier\]/", "<a href='/suppliers/$1?".$markReadLink."'>$2</a>", $message['NOTE_BODY']);
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
            $message['NOTE_BODY'] = preg_replace("/\[contract\|(.*)\|(.*)\](.*)\[\/contract\]/", "$3", $message['NOTE_BODY']);
            $message['NOTE_BODY'] = preg_replace("/\[client\|(.*)\](.*)\[\/client\]/", "$2", $message['NOTE_BODY']);
            $message['NOTE_BODY'] = preg_replace("/\[supplier\|(.*)\](.*)\[\/supplier\]/", "$2", $message['NOTE_BODY']);
        }

        return $messages;
    }
}