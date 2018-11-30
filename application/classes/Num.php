<?php defined('SYSPATH') OR die('No direct script access.');

class Num extends Kohana_Num
{
    /**
     * меняем запятую на точку, это для корректной записи в базу
     *
     * @param $number
     * @return mixed
     */
    public static function toFloat($number)
    {
        $number = preg_replace("/[^\d\.-]+/", "", str_replace([',', ' ', " "], ['.', '', ""], $number));

        if (!is_numeric($number)) {
            throw new HTTP_Exception_500('Wrong number');
        }

        return $number;
    }

    /**
     * возвращает инт
     *
     * @param $number
     * @return int
     */
    public static function toInt($number)
    {
        return (int)$number;
    }
}
