<?php defined('SYSPATH') or die('No direct script access.');

class Model_Transaction_Parser extends Model
{
    const DEFAULT_VERSION = '1.0.0';

    private $_versions = [
        self::DEFAULT_VERSION,
        '1.0.1'
    ];

    const ACTION_PUSH = 50;
    const ACTION_PULL = 51;

    const PAYMENT_STATUS_NEW        = 'Новая';
    const PAYMENT_STATUS_VERIFIED   = 'Проведено';
    const PAYMENT_STATUS_UNKNOWN    = 'Неизвестно';
    const PAYMENT_STATUS_ERROR      = 'Ошибка';

    public static $sortArray = [
        self::PAYMENT_STATUS_ERROR,
        self::PAYMENT_STATUS_NEW,
        self::PAYMENT_STATUS_UNKNOWN,
        self::PAYMENT_STATUS_VERIFIED,
    ];

    protected static $_xlsHeaders = [
        'ACTION', 'CONTRACT_ID', 'ORDER_DATE', 'ORDER_NUM', 'COMMENT', 'SUMPAY', 'PURPOSE'
    ];

    protected $_dateFormat = false;
    protected $_summary = [
        'all' => 0,
        'old' => 0,
        'new' => 0,
        'error' => 0,
    ];

    public function __construct()
    {
        $this->_dateFormat = Date::$dateFormatRu;
    }

    /**
     * установка формата даты с которым рабоатет с парсер
     *
     * @param $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->_dateFormat = $dateFormat;
    }

    /**
     * приводим данные в порядок
     *
     * @param $rows
     * @param $mimeType
     * @return array|mixed
     */
    private function _prepareRows($rows, $mimeType)
    {
        switch ($mimeType) {
            case Upload::MIME_TYPE_OFFICE:
            case Upload::MIME_TYPE_XLS:
            case Upload::MIME_TYPE_XLSX:
            case Upload::MIME_TYPE_TXT:
                $data = self::_prepareXls($rows);
                break;
            default:
                $data = $rows;
        }

        if (empty($data)) {
            return [];
        }

        return $data;
    }

    /**
     * парсим xls
     *
     * @param $file
     * @return array
     */
    private static function _prepareXls($data)
    {
        if (empty($data)) {
            return [];
        }

        $headers = array_shift($data);
        $return = [];

        if (
            empty(array_diff($headers, self::$_xlsHeaders)) &&
            count(array_intersect($headers, self::$_xlsHeaders)) == count(self::$_xlsHeaders)
        ) {
            foreach ($data as $row) {
                $array = [];

                foreach ($row as $colId => $colValue) {
                    foreach (self::$_xlsHeaders as $header) {
                        if ($header == $headers[$colId]) {
                            $array[$header] = $colValue;
                            break;
                        }
                    }
                }

                if (!empty($array)) {
                    $return[] = $array;
                }
            }
        }

        return $return;
    }

    /**
     * проверка корректности даты
     *
     * @param $date
     */
    protected function _checkDate($date)
    {
        /*
        1) Дата платежа не может быть больше текущей даты (в поле "Статус" указать "Ошибка. Неверная дата" и запретить к добавлению)
        2) Дата платежа не может быть меньше текущей даты минус 2 месяца (в поле "Статус" указать "Ошибка. Неверная дата" и запретить к добавлению)
        */

        $error = '';

        try {
            $now = (new DateTime())->getTimestamp();
            $date = DateTime::createFromFormat($this->_dateFormat, $date);

            if (empty($date)) {
                throw new Exception();
            }

            if ($date->getTimestamp() > $now) {
                $error = '<br><small class="red date_error"><b>Дата платежа не может быть больше текущей даты</b></small>';
            }
            if ($date->getTimestamp() < $now - 60*60*24*60) {
                $error = '<br><small class="red date_error"><b>Дата платежа не может быть меньше текущей даты минус 2 месяца</b></small>';
            }
        } catch (Exception $e) {
            return '<b class="red date_error">Ошибка чтения даты</b>';
        }

        return $date->format(Date::$dateFormatRu) . $error;
    }

    /**
     * получаем суммарную инфу по считанным данным
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * парсим входящие строки транзакций
     *
     * @param $rows
     * @param $mimeType
     */
    public function parse($rows, $mimeType)
    {
        $rows = $this->_prepareRows($rows, $mimeType);

        $user = User::current();

        foreach($rows as &$row){
            $row['CONTRACT_ID'] = $this->_getContractId($row);
        }

        $contracts = Model_Contract::getContracts(false, [
            'contract_id'   => array_unique(array_column($rows, 'CONTRACT_ID')),
            'agent_id'      => $user['AGENT_ID']
        ]);

        foreach($rows as &$row){
            /*
             * Если значение запроса не определено, тогда на место договора в таблице макета выставляем надпись "Не определен", а в значение статус - "Неизвестно".
             * Если значение определено, тогда на место договора в таблице макета выставляем найденное имя договора, запомнив его ID (нужно будет в дальнейшем)
             */
            $row['ORDER_DATE']      = $this->_checkDate($row['ORDER_DATE']);
            $row['OPERATION']       = !empty($row['ACTION']) ? $row['ACTION'] : self::ACTION_PUSH;
            $row['PAYMENT_DATE']    = !empty($row['PAYMENT_DATE']) ? $this->_checkDate($row['PAYMENT_DATE']) : $row['ORDER_DATE'];
            $row['OPERATION_NAME']  = $row['ACTION'] == self::ACTION_PUSH ? 'Пополнение счета' : 'Списание со счета';
            $row['CAN_ADD']         = 0;
            $row['CONTRACT_NAME']   = 'Не определен';
            $row['STATE_ID']        = 'Неизвестно';
            $row['PAYMENT_STATUS']  = self::PAYMENT_STATUS_UNKNOWN;
            $row['SUMPAY']          = Num::toFloat($row['SUMPAY']);

            //мини проверка что даты корректно распознались
            if (strpos($row['ORDER_DATE'], 'date_error') === false && strpos($row['PAYMENT_DATE'], 'date_error') === false ) {
                foreach ($contracts as $contract) {
                    if ($row['CONTRACT_ID'] == $contract['CONTRACT_ID']) {
                        $row['CONTRACT_NAME'] = $contract['CONTRACT_NAME'];
                        $row['STATE_ID'] = $contract['STATE_ID'];
                        $row['PAYMENT_STATUS'] = self::PAYMENT_STATUS_VERIFIED;

                        $pays = Model_Contract::getPaymentsHistory($row['CONTRACT_ID'], [
                            'order_date' => [$row['ORDER_DATE'], $row['PAYMENT_DATE']],
                            'order_num' => $row['ORDER_NUM'],
                            'sumpay' => $row['SUMPAY'] * ($row['ACTION'] == self::ACTION_PUSH ? 1 : -1),
                        ]);

                        if (empty($pays)) {
                            $row['PAYMENT_STATUS'] = self::PAYMENT_STATUS_NEW;
                            $row['CAN_ADD'] = 1;
                            $this->_summary['new']++;
                        }

                        break;
                    }
                }
            } else {
                $row['PAYMENT_STATUS'] = self::PAYMENT_STATUS_ERROR;
                $this->_summary['error']++;
            }
        }

        $this->_summary['all'] = count($rows);
        $this->_summary['old'] = $this->_summary['all'] - $this->_summary['new'] - $this->_summary['error'];

        return $this->_sort($rows);
    }

    /**
     * сортировка
     *
     * @param $rows
     * @return mixed
     */
    private function _sort($rows)
    {
        /*
1) Вначале отображать новые платежи
2) Затем неопределенные
3) И уже в конце списка "Проведенные"
         */

        usort($rows, function($a, $b) {
            if ($a['PAYMENT_STATUS'] == $b['PAYMENT_STATUS']) {
                return 0;
            }

            $sortArray = Model_Transaction_Parser::$sortArray;

            $sortIndexA = array_search($a['PAYMENT_STATUS'], $sortArray);
            $sortIndexB = array_search($b['PAYMENT_STATUS'], $sortArray);

            return $sortIndexA > $sortIndexB ? 1 : -1;
        });

        return $rows;
    }

    /**
     * узнаем версию парсера строки
     *
     * @param $row
     * @return string
     */
    private function _getVersion($row)
    {
        return !empty($row['VERSION']) && in_array($row['VERSION'], $this->_versions) ? $row['VERSION'] : self::DEFAULT_VERSION;
    }

    /**
     * парсим номер контракта
     *
     * @param $row
     * @return bool
     */
    private function _getContractId($row)
    {
        $version = $this->_getVersion($row);

        switch ($version) {
            case '1.0.1':
                preg_match_all("/\[(.*?)\]/", $row['PURPOSE'], $output);
                $contractId = !empty($output[1][0]) ? $output[1][0] : false;
                break;
            default:
                $contractId = !empty($row['CONTRACT_ID']) ? $row['CONTRACT_ID'] : false;
        }

        return $contractId;
    }
}