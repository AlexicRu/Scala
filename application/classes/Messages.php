<?php defined('SYSPATH') or die('No direct script access.');

class Messages
{
    /**
     * получаем список сообщений, которые надо вывести
     * 
     * @return array|mixed
     * @throws Cache_Exception
     */
    public static function get()
    {
        $cache = Cache::instance();

        $user = User::current();

        $key = 'messages_user'.(!empty($user['MANAGER_ID']) ? $user['MANAGER_ID'] : Session::instance()->id());

        $messages = $cache->get($key);

        if (!empty($messages)) {
            $cache->delete($key);
        }
        
        return $messages ?: [];
    }

    /**
     * в массив сообщений добавляем еще одно
     *
     * @param $text
     * @param $type
     */
    public static function put($text, $type = 'error')
    {
        $messages = self::get();

        $messages[] = ['type' => $type, 'text' => $text];

        $cache = $cache = Cache::instance();

        $user = User::current();

        $key = 'messages_user'.(!empty($user['MANAGER_ID']) ? $user['MANAGER_ID'] : Session::instance()->id());

        return $cache->set($key, $messages);
    }
}