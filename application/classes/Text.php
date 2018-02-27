<?php defined('SYSPATH') OR die('No direct script access.');

class Text extends Kohana_Text
{
    const RUR = '₽';

    /**
     * echo plural_form(42, array('арбуз', 'арбуза', 'арбузов'));
     *
     * @param $n
     * @param $forms
     * @return mixed
     */
    public static function plural($n, $forms) {
        return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
    }

    /**
     * переводим из camelCase
     *
     * @param $str
     * @param string $delimiter
     * @return string
     */
    public static function camelCaseToDashed($str, $delimiter = '-')
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1' . $delimiter, $str));
    }

    /**
     * переводим в camelCase
     *
     * @param $str
     * @param bool $lcfirst
     * @return mixed
     */
    public static function dashesToCamelCase($str, $lcfirst = TRUE)
    {
        $pos = strpos($str, '-');
        if ( $pos !== FALSE && $pos !== 0 )
        {
            $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
            if ( $lcfirst )
            {
                $str[0] = strtolower($str[0]);
            }
        }
        return $str;
    }
}
