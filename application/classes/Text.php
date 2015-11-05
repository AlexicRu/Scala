<?php defined('SYSPATH') OR die('No direct script access.');

class Text extends Kohana_Text
{
    const RUR = '&#x20bd;';

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
}
