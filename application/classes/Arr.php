<?php defined('SYSPATH') OR die('No direct script access.');

class Arr extends Kohana_Arr
{
    /**
     * рекурсивно применяем array_change_key_case
     *
     * @param $arr
     * @return array
     */
    public static function arrayChangeKeyCaseRecursive($arr)
    {
        return array_map(function($item){
            if(is_array($item))
                $item = self::arrayChangeKeyCaseRecursive($item);
            return $item;
        }, array_change_key_case($arr));
    }
}
