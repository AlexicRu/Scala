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
        $cache = $cache = Cache::instance();
        
        $messages = $cache->get('messages');
        
        $cache->delete('messages');
        
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

        return $cache->set('messages', $messages);
    }
}