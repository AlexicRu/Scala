<?php defined('SYSPATH') or die('No direct script access.');

class Lng
{
    public static function phrase($phrase)
    {
        $config = Kohana::$config->load('lng')->as_array();

        $cache = Cache::instance();

        $lng = $cache->get('lng', 'ru');

        if(empty($config[$lng][$phrase])){
            return '';
        }

        return $config[$lng][$phrase];
    }
}