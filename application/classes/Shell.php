<?php defined('SYSPATH') or die('No direct script access.');

class Shell
{
    const CARD_STATUS_ACTIVE = 'Active';

    private $_url = 'https://api-emea.prod.emea.wexinc.co.uk';
    private $_token = null;
    private $_configShell = null;
    private $_configDb = null;
    private $_actions = [
        'getToken'                      => '/oauth/token',
        'getCustomers'                  => '/customers',
        'getCustomer'                   => '/customer/__CUSTOMER__',
        'getCustomerCards'              => '/customer/__CUSTOMER__/cards',
        'getCustomerCard'               => '/customer/__CUSTOMER__/card/__CARD__',
        'getCustomerCardTransactions'   => '/customer/__CUSTOMER__/card/__CARD__/transactions',
        'getCustomerCardTransaction'    => '/customer/__CUSTOMER__/card/__CARD__/transaction/__TRANSACTION__',
        'setCustomerCardStatus'         => '/customer/__CUSTOMER__/card/__CARD__/status/__STATUS__',
    ];
    private $_currency = null;
    private $_connect = null;
    private $_skipTransactionStatuses = [
        'In Flight',
        'Declined'
    ];

    public function __construct($params = [])
    {
        /*
         * init variables START
         */
        $this->_configShell = !empty($params['config']) ? $params['config'] : Kohana::$config->load('config')['shell'];
        $this->_configDb = !empty($params['db']) ? $params['db'] : Kohana::$config->load('database');
        $this->_currency = !empty($params['currency']) ? $params['currency'] : Common::CURRENCY_RUR;
        /*
         * init variables END
         */

        $response = $this->_request($this->_actions['getToken'], 'post');

        if (empty($response['access_token'])) {
            throw new Exception('empty token');
        }

        $this->_token = $response['access_token'];
    }

    /**
     * execute query to db
     *
     * @param $sql
     * @return bool
     */
    private function _dbExecute($sql)
    {
        if (is_null($this->_connect)) {
            $this->_connect = oci_connect($this->_configDb['name'], $this->_configDb['password'], $this->_configDb['db'], 'UTF8');
        }

        $query = oci_parse($this->_connect, $sql);

        return oci_execute($query);
    }

    /**
     * квотируем
     *
     * @param $value
     * @return string
     */
    private function _quote($value)
    {
        return class_exists('Oracle') ? Oracle::quote($value) : ("'".str_replace(["'"], ["''"], trim($value))."'");
    }

    /**
     * форматируем дату
     *
     * @param $value
     * @return string
     */
    private function _date($value)
    {
        return class_exists('Oracle') ? Oracle::toDateOracle($value) : "to_date('".date('d.m.Y', $value)."', 'dd.mm.yyyy')";
    }

    /**
     * шлем запрос в shell
     *
     * @param $url
     * @param string $method
     */
    private function _request($url, $method = 'get')
    {
        $options = array(
            CURLOPT_HTTPHEADER => ['Content-Type:application/json'],
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_SSL_VERIFYPEER => false,    // Disabled SSL Cert checks,
        );

        if ($url == $this->_actions['getToken']) {
            $options[CURLOPT_POSTFIELDS] = json_encode([
                "grant_type" => "password",
                "username"=> $this->_configShell['login'],
                "password"=> $this->_configShell['password']
            ]);
        } else {
            $authorization = "Authorization: Bearer " . $this->_token;
            $options[CURLOPT_HTTPHEADER][] = $authorization;
        }

        $ch      = curl_init( $this->_url . $url );

        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        curl_close( $ch );

        $response = json_decode($content, true);

        if (isset($response['errorMessage'])) {
            if (in_array($response['errorMessage'], [
                'Customer not found.',
                'No Transactions found.',
                'Transaction Details not found.',
            ])) {
                return [];
            }
            throw new Exception($response['errorMessage']);
        }

        return $response;
    }

    /**
     * получаем полный список карт по всем клиентам
     */
    public function getAllCards()
    {
        $customers = $this->getCustomers();

        $cards = [];

        foreach ($customers as $customer) {
            $customerCards = $this->getCustomerCards($customer['customerNumber']);

            if (!empty($customerCards)) {
                $cards = array_merge($cards, $customerCards);
            }
        }

        foreach ($cards as $card) {
            echo $card['cardNumber'] . ":" . ($card['status'] == self::CARD_STATUS_ACTIVE ? 1 : 0) . "\n";
        }
    }

    /**
     * получаем полный список клиентов
     *
     * @return array
     */
    public function getCustomers()
    {
        return $this->_request($this->_actions['getCustomers']) ?: [];
    }

    /**
     * получаем список карт клиента
     *
     * @param $customerNumber
     * @return array
     */
    public function getCustomerCards($customerNumber)
    {
        if (empty($customerNumber)) {
            return [];
        }

        $url = str_replace('__CUSTOMER__', $customerNumber, $this->_actions['getCustomerCards']);

        return $this->_request($url) ?: [];
    }

    /**
     * получение списка транзакций
     *
     * @param $customerNumber
     * @param $cardNumber
     * @return array
     */
    public function getCustomerCardTransactions($customerNumber, $cardNumber)
    {
        if (empty($customerNumber) || empty($cardNumber)) {
            return [];
        }

        $url = str_replace(['__CUSTOMER__', '__CARD__'], [$customerNumber, $cardNumber], $this->_actions['getCustomerCardTransactions']);

        return $this->_request($url) ?: [];
    }

    /**
     * получение детальной инфы по транзакции
     *
     * @param $customerNumber
     * @param $cardNumber
     * @param $transactionId
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getCustomerCardTransaction($customerNumber, $cardNumber, $transactionId, $dateStart = false, $dateEnd = false)
    {
        if (empty($customerNumber) || empty($cardNumber) || empty($transactionId)) {
            return [];
        }

        $url = str_replace(['__CUSTOMER__', '__CARD__', '__TRANSACTION__'], [$customerNumber, $cardNumber, $transactionId], $this->_actions['getCustomerCardTransaction']);

        if (!empty($dateStart) || !empty($dateEnd)) {
            $url .= '?';
        }

        $params = [];

        if (!empty($dateStart)) {
            $params[] = 'datStart=' . $dateStart;
        }

        if (!empty($dateEnd)) {
            $params[] = 'datStart=' . $dateEnd;
        }

        return $this->_request($url . implode('&', $params)) ?: [];
    }

    /**
     * загружаем транзакции
     *
     * @param $agentId
     * @param $tubeId
     * @param $dateStart
     * @param $dateEnd
     */
    public function loadTransactions($agentId, $tubeId, $dateStart = false, $dateEnd = false)
    {
        if (empty($agentId) || empty($tubeId)) {
            die('empty params');
        }

        //unlim
        set_time_limit(0);

        $customers = $this->getCustomers();

        //customers
        foreach ($customers as $customer) {
            $customerCards = $this->getCustomerCards($customer['customerNumber']);

            //cards
            foreach ($customerCards as $card) {
                $cardTransactions = $this->getCustomerCardTransactions($card['customerNumber'], $card['cardNumber']);

                //transactions
                foreach ($cardTransactions as $transaction) {
                    $transactionDetail = $this->getCustomerCardTransaction($card['customerNumber'], $card['cardNumber'], $transaction['transactionId'], $dateStart, $dateEnd);

                    if (in_array($transactionDetail['transactionStatus'], $this->_skipTransactionStatuses)) {
                        continue;
                    }

                    $product = reset($transactionDetail['transactionLineItems']);

                    $data = [
                        'agent_id'              => $agentId, //number -- (по умолчанию 4)
                        'tube_id'               => $tubeId, //number -- (по умолчанию 70183602)
                        'account_number'        => $this->_quote(''), //varchar2(50) -- номер аккаунта (не обязательно)
                        'sub_account_number'    => $this->_quote($transactionDetail['accountNumber']), //varchar2(50) -- номер субаккаунта (не обязательно)
                        'invoice_id'            => $this->_quote(''), //varchar2(50) -- номер инвойса (не обязательно)
                        'invoice_date'          => $this->_quote(''), //varchar2(50) -- дата инвойса (не обязательно)
                        'card_group'            => $this->_quote(''), //varchar2(500) -- группа карт (не обязательно)
                        'card_number'           => $this->_quote($transactionDetail['cardNumber']), //varchar2(20) -- номер карты
                        'date_trn'              => $this->_date($transactionDetail['effectiveAt']['value'] / 1000), //date -- дата транзакции
                        'time_trn'              => $this->_quote(date('H:i:s', $transactionDetail['effectiveAt']['value'] / 1000)), //varchar2(10) -- время транзакции (формат hh24:mi:ss)
                        'holder'                => $this->_quote($transactionDetail['embossingName']), //varchar2(500) -- держатель карты (не обязательно)
                        'vrn'                   => $this->_quote(''), //varchar2(10) -- (не обязательно)
                        'fleet_id'              => $this->_quote($transactionDetail['fleetId']), //varchar2(50) -- (не обязательно)
                        'supplier_terminal'     => $this->_quote($transactionDetail['terminalId']), //varchar2(50) -- ID терминала в системе shell
                        'pos_name'              => $this->_quote($transactionDetail['locationNumber']), //varchar2(255) -- название АЗС
                        'trn_type'              => $this->_quote($transactionDetail['transactionType']), //varchar2(255) -- тип транзакции
                        'receipt_number'        => $this->_quote($transactionDetail['orderNumber']), //varchar2(50) -- номер чека (не обязательно)
                        'odometer'              => $this->_quote($transactionDetail['odometer']), //varchar2(50) -- показание одометра (не обязательно)
                        'service_id'            => $this->_quote($product['product']), //varchar2(50) -- ID услуги @todo запросить у wex id услуги
                        'service_name'          => $this->_quote($product['product']), //varchar2(500) -- название услуги (если отличается от ID услуги)
                        'service_amount'        => $product['quantity'], //number -- количество литров
                        'units'                 => $this->_quote($product['isFuel']), //varchar2(50) -- размерность услуги (не обязательно)
                        'vat_rate'              => $product['taxRate'], //number -- размер НДС
                        'service_price'         => round($product['originalValue'] / $product['quantity'], 2), //number -- цена на АЗС
                        'price_buy'             => round($product['customerValue'] / $product['quantity'], 2), //number -- цена покупки на АЗС
                        'rebate_rate'           => 'null', //number -- (не обязательно)
                        'rebate_rate_type'      => 'null', //number -- (не обязательно)
                        'service_price_net'     => round(($product['customerValue'] - $product['customerTaxAmount']) / $product['quantity'], 2), //number -- цена на АЗС без НДС
                        'rebate_value'          => 'null', //number -- (не обязательно)
                        'vat_amount'            => $product['customerTaxAmount'], //number -- размер налога
                        'service_sumprice_net'  => $product['originalValue'] - $product['customerTaxAmount'], //number -- стоимость на АЗС без НДС
                        'service_sumprice'      => $product['originalValue'], //number -- стоимость на АЗС с НДС
                        'pos_currency'          => $this->_quote(''), //varchar2(3) -- валюта АЗС @todo нет данных
                        'currency_rate'         => 1, //number -- курс @todo нет данных
                        'sumprice_buy_net'      => $product['customerValue'] - $product['customerTaxAmount'], //number -- цена покупки у Шелл без НДС
                        'sumprice_buy'          => $product['customerValue'], //number -- цена покупки у Шелл с НДС
                        'client_currency'       => $this->_quote($this->_currency), //varchar2(3) -- валюта клиента (по умолчанию '643')
                        'rrn'                   => $this->_quote($transactionDetail['transactionId']), //varchar2(50) -- номер транзакции
                        //'date_insert'           => '', //date -- по умолчанию текущая дата (не нужно, подставляется сама)
                    ];

                    $sql = 'INSERT INTO s_dev.test_transaction_shell_v2 (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $data) . ')';

                    try {
                        //execute
                        if (class_exists('Oracle')) {
                            Oracle::init()->query($sql, 'insert');
                        } else {
                            $this->_dbExecute($sql);
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
        }
    }
}