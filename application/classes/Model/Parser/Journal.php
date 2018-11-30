<?php defined('SYSPATH') or die('No direct script access.');

class Model_Parser_Journal extends Model_Parser_Transaction
{
    protected static $_xlsHeaders = [
        'Дата', 'Время', 'Номер карты', 'Операция', 'Услуга', 'Количество', 'Цена АЗС', 'Сумма по цене АЗС', 'Цена со скидкой', 'Сумма по цене со скидкой', 'Название АЗС', 'Адрес АЗС', 'RRN', 'Del'
    ];

    /**
     * парсим входящие строки
     *
     * @param $rows
     * @param $mimeType
     */
    public function parse($rows, $mimeType)
    {
        $rows = $this->_prepareRows($rows, $mimeType);

        foreach($rows as &$row) {
            /*
             * Если значение запроса не определено, тогда на место договора в таблице макета выставляем надпись "Не определен", а в значение статус - "Неизвестно".
             * Если значение определено, тогда на место договора в таблице макета выставляем найденное имя договора, запомнив его ID (нужно будет в дальнейшем)
             */
            $row['Дата'] = $this->_checkDate($row['Дата']);

            if (!empty($row['Del'])) {
                $row['Del'] = 'Удаление';
            } else {
                if (Model_Transaction::checkTransaction($row['RRN']) === 0) {
                    $row['Del'] = 'Новая';
                } else {
                    $row['Del'] = 'Корректировка';
                }
            }
        }

        return $rows;
    }

    protected function _checkDateAdditional($date)
    {
        return '';
    }
}