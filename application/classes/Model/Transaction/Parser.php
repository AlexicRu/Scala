<?php defined('SYSPATH') or die('No direct script access.');

class Model_Transaction_Parser extends Model
{
    const DEFAULT_VERSION = '1.0.0';

    private $_versions = [
        self::DEFAULT_VERSION,
        '1.0.1'
    ];

    const PAYMENT_STATUS_NEW        = 'Новая';
    const PAYMENT_STATUS_VERIFIED   = 'Проведено';
    const PAYMENT_STATUS_UNKNOWN    = 'Неизвестно';

    public static $sortArray = [
        self::PAYMENT_STATUS_NEW,
        self::PAYMENT_STATUS_UNKNOWN,
        self::PAYMENT_STATUS_VERIFIED,
    ];

    protected static $_xlsHeaders = [
        'CONTRACT_ID', 'ORDER_DATE', 'ORDER_NUM', 'COMMENT', 'SUMPAY', 'PURPOSE'
    ];

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
            $row['ORDER_DATE']      = Date::guessDate($row['ORDER_DATE']);
            $row['OPERATION']       = !empty($row['OPERATION']) ? $row['OPERATION'] : 50;
            $row['PAYMENT_DATE']    = !empty($row['PAYMENT_DATE']) ? Date::guessDate($row['PAYMENT_DATE']) : $row['ORDER_DATE'];
            $row['OPERATION_NAME']  = $row['OPERATION'] == 50 ? 'Пополнение счета' : 'Списание со счета';
            $row['CAN_ADD']         = 0;
            $row['CONTRACT_NAME']   = 'Не определен';
            $row['STATE_ID']        = 'Неизвестно';
            $row['PAYMENT_STATUS']  = self::PAYMENT_STATUS_UNKNOWN;
            $row['SUMPAY']          = Num::toFloat($row['SUMPAY']);

            foreach($contracts as $contract){
                if($row['CONTRACT_ID'] == $contract['CONTRACT_ID']){
                    $row['CONTRACT_NAME']   = $contract['CONTRACT_NAME'];
                    $row['STATE_ID']        = $contract['STATE_ID'];
                    $row['PAYMENT_STATUS']  = self::PAYMENT_STATUS_VERIFIED;

                    $pays = Model_Contract::getPaymentsHistory($row['CONTRACT_ID'], [
                        'order_date'    => [$row['ORDER_DATE'], $row['PAYMENT_DATE']],
                        'order_num'     => $row['ORDER_NUM'],
                        'sumpay'        => $row['SUMPAY'] * ($row['OPERATION'] == 50 ? 1 : -1),
                    ]);

                    if(empty($pays)){
                        $row['PAYMENT_STATUS'] = self::PAYMENT_STATUS_NEW;
                        $row['CAN_ADD']        = 1;
                    }

                    break;
                }
            }
        }

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