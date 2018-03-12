<?php defined('SYSPATH') or die('No direct script access.');

class Date extends Kohana_Date {

    public static function monthRu($month = false, $type = 1)
    {
		$months = array(
			1 => 'Январь',
			2 => 'Февраль',
			3 => 'Март',
			4 => 'Апрель',
			5 => 'Май',
			6 => 'Июнь',
			7 => 'Июль',
			8 => 'Август',
			9 => 'Сентябрь',
			10 => 'Октябрь',
			11 => 'Ноябрь',
			12 => 'Декабрь',
		);
		
		if($type == 2){
			$months = array(
				1 => 'января',
				2 => 'февраля',
				3 => 'марта',
				4 => 'апреля',
				5 => 'мая',
				6 => 'июня',
				7 => 'июля',
				8 => 'августа',
				9 => 'сентября',
				10 => 'октября',
				11 => 'ноября',
				12 => 'декабря',
			);	
		}		
		
        $month = $month ?: date("n", time());
		
        return $months[$month];
    }

    /**
     * немного махинаций с датами, пытаемся угадать, что же нам пришло, так как excel присылает что попало
     *
     * @param $dateStr
     * @param $exportFormat
     * @return string
     */
    public static function guessDate($dateStr, $exportFormat = 'Y-m-d')
    {
        $delimiter = strpos($dateStr, '-') !== false ? '-' : (strpos($dateStr, '/') !== false ? '/' : '.');
        $dateArr = explode($delimiter, $dateStr);
        $format = "d{$delimiter}m{$delimiter}y";

        if (count($dateArr) == 3) {
            if (strlen($dateArr[0]) == 4) {
                $format = "Y{$delimiter}m{$delimiter}d";
            } else if (strlen($dateArr[2]) == 4) {
                $format = "d{$delimiter}m{$delimiter}Y";
            } else {
                //y-m-d
                $year = date('y');
                if ($dateArr[0] == $year && $dateArr[2] != $year) {
                    $format = "y{$delimiter}m{$delimiter}d";
                }
            }
        }

        $date = DateTime::createFromFormat($format, $dateStr);

        return $date->format($exportFormat);
    }
}
