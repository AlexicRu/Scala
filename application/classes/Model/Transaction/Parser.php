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

    /**
     * парсим входящие строки транзакций
     *
     * @param $rows
     */
    public function parse($rows)
    {
        $user = User::current();

        foreach($rows['ROWS'] as &$row){
            $row['CONTRACT_ID'] = $this->_getContractId($row);
        }

        $contracts = Model_Contract::getContracts(false, [
            'contract_id'   => array_unique(array_column($rows['ROWS'], 'CONTRACT_ID')),
            'agent_id'      => $user['AGENT_ID']
        ]);

        foreach($rows['ROWS'] as &$row){
            /*
             * Если значение запроса не определено, тогда на место договора в таблице макета выставляем надпись "Не определен", а в значение статус - "Неизвестно".
             * Если значение определено, тогда на место договора в таблице макета выставляем найденное имя договора, запомнив его ID (нужно будет в дальнейшем)
             */

            $row['OPERATION_NAME']  = $row['OPERATION'] == 50 ? 'Пополнение счета' : 'Списание со счета';
            $row['CAN_ADD']         = 0;
            $row['CONTRACT_NAME']   = 'Не определен';
            $row['STATE_ID']        = 'Неизвестно';
            $row['PAYMENT_STATUS']  = self::PAYMENT_STATUS_UNKNOWN;

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

        usort($rows['ROWS'], function($a, $b) {
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