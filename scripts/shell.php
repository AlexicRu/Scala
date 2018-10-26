<?php

include '_log.php';
include '_debug.php';

class Shell
{
    use Log;
    use Debug;

    const CARD_STATUS_ACTIVE    = 'Active';
    const CARD_STATUS_DECLINED  = 'Declined';
    const CARD_STATUS_IN_FLIGHT = 'In Flight';

    private $_token         = null;
    private $_configShell   = null;
    private $_configDb      = null;
    private $_actions       = [
        'getToken'                      => '/oauth/token',
        'getCustomers'                  => '/customers',
        'getCustomer'                   => '/customer/__CUSTOMER__',
        'getCustomerCards'              => '/customer/__CUSTOMER__/cards',
        'getCustomerCard'               => '/customer/__CUSTOMER__/card/__CARD__',
        'getCustomerCardTransactions'   => '/customer/__CUSTOMER__/card/__CARD__/transactions',
        'getCustomerCardTransaction'    => '/customer/__CUSTOMER__/card/__CARD__/transaction/__TRANSACTION__',
        'setCustomerCardStatus'         => '/customer/__CUSTOMER__/card/__CARD__/status/__STATUS__',
    ];
    private $_currency      = 643; //руб
    private $_connectDb     = null;
    private $_skipTransactionStatuses   = [];
    private $_loadedTransactions        = [];

    public function __construct($params = [])
    {
        if (empty($params['agent_id']) || empty($params['tube_id'])) {
            die($this->_logErrorArguments);
        }
        /*
         * init variables START
         */
        $this->_agentId = $params['agent_id'];
        $this->_tubeId  = $params['tube_id'];
        $this->_configShell = !empty($params['config']) ? $params['config'] : null;
        $this->_configDb = !empty($params['db']) ? $params['db'] : null;
        $this->_debug = !empty($params['debug']) ? $params['debug'] : false;
        $this->_logFile = !empty($params['log_file']) ? $params['log_file'] : false;
        /*
         * init variables END
         */

        $this->_debugStart();

        $response = $this->_request($this->_actions['getToken'], 'post');

        if (empty($response['access_token'])) {
            die($this->_logErrorAuth);
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
            $this->_connectDb = oci_connect($this->_configDb['name'], $this->_configDb['password'], $this->_configDb['db'], 'UTF8');
        }

        $query = oci_parse($this->_connectDb, $sql);

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
        return "'".str_replace(["'"], ["''"], trim($value))."'";
    }

    /**
     * форматируем дату
     *
     * @param $value
     * @return string
     */
    private function _date($value)
    {
        return "to_date('".date('d.m.Y', $value)."', 'dd.mm.yyyy')";
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

        $ch      = curl_init( $this->_configShell['url'] . $url );

        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        curl_close( $ch );

        $response = json_decode($content, true);

        $this->_debug($method . ': ' . $url);

        if (isset($response['errorMessage'])) {
            if (in_array($response['errorMessage'], [
                'Customer not found.',
                'No Transactions found.',
                'Transaction Details not found.',
            ])) {
                $this->_log($response['errorMessage']);

                return [];
            }
            die($this->_logErrorExecute);
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
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getCustomerCardTransactions($customerNumber, $cardNumber, $dateStart = false, $dateEnd = false)
    {
        if (empty($customerNumber) || empty($cardNumber)) {
            return [];
        }

        $url = str_replace(['__CUSTOMER__', '__CARD__'], [$customerNumber, $cardNumber], $this->_actions['getCustomerCardTransactions']);

        if (!empty($dateStart) || !empty($dateEnd)) {
            $url .= '?';
        }

        $params = [];

        if (!empty($dateStart)) {
            $params[] = 'startDate=' . $dateStart;
        }

        if (!empty($dateEnd)) {
            $params[] = 'endDate=' . $dateEnd;
        }

        return $this->_request($url . implode('&', $params)) ?: [];
    }

    /**
     * получение детальной инфы по транзакции
     *
     * @param $customerNumber
     * @param $cardNumber
     * @param $transactionId
     * @return array
     */
    public function getCustomerCardTransaction($customerNumber, $cardNumber, $transactionId)
    {
        if (empty($customerNumber) || empty($cardNumber) || empty($transactionId)) {
            return [];
        }

        $url = str_replace(['__CUSTOMER__', '__CARD__', '__TRANSACTION__'], [$customerNumber, $cardNumber, $transactionId], $this->_actions['getCustomerCardTransaction']);

        return $this->_request($url) ?: [];
    }

    /**
     * загружаем транзакции
     *
     * @param $dateStart
     * @param $dateEnd
     */
    public function loadTransactions($dateStart = false, $dateEnd = false)
    {
        $this->_log('loadTransactions start');

        //unlim
        set_time_limit(0);
        $cnt = 0;

        $customers = $this->getCustomers();

        //customers
        foreach ($customers as $customer) {
            $this->_customerId = $customer['customerNumber'];
            $customerCards = $this->getCustomerCards($customer['customerNumber']);

            //cards
            foreach ($customerCards as $card) {
                $this->_cardId = $card['cardNumber'];
                $cardTransactions = $this->getCustomerCardTransactions($card['customerNumber'], $card['cardNumber'], $dateStart, $dateEnd);

                //transactions
                foreach ($cardTransactions as $transaction) {
                    if (in_array($transaction['transactionId'], $this->_loadedTransactions)) {
                        continue;
                    }

                    //wrong statuses
                    if (empty($transaction['status']) || in_array($transaction['status'], $this->_skipTransactionStatuses)) {
                        $this->_log($transaction['transactionId'] . ' - ' . $transaction['status']);
                        continue;
                    }

                    //GET transaction detail
                    $transactionDetail = $this->getCustomerCardTransaction($card['customerNumber'], $card['cardNumber'], $transaction['transactionId']);

                    //empty detail
                    if (empty($transactionDetail)) {
                        $this->_log('empty detail transaction ' . $transaction['transactionId']);
                        die($this->_logErrorExecute);
                    }

                    //wrong statuses, малоли по какой-то причине первая проверка пропустила...мб данные различаются
                    if (empty($transactionDetail['transactionStatus']) || in_array($transactionDetail['transactionStatus'], $this->_skipTransactionStatuses)) {
                        $this->_log($transaction['transactionId'] . ' - ' . $transactionDetail['transactionStatus']);
                        continue;
                    }

                    //empty params
                    if (
                        empty($transactionDetail['effectiveAt']['value']) ||
                        empty($transactionDetail['transactionLineItems'])
                    ) {
                        $this->_log("empty params: " .
                            ' effectiveAt - ' . (!empty($transactionDetail['effectiveAt']['value']) ? $transactionDetail['effectiveAt']['value'] : 'empty') .
                            ' transactionLineItems - ' . (!empty($transactionDetail['transactionLineItems']) ? 'exists' : 'empty')
                        );
                        die($this->_logErrorExecute);
                    }

                    //почему-то иногда пусто
                    $transactionDetail['terminalId']        = !isset($transactionDetail['terminalId']) ? '' : $transactionDetail['terminalId'];
                    $transactionDetail['transactionType']   = !isset($transactionDetail['transactionType']) ? '' : $transactionDetail['transactionType'];
                    $transactionDetail['cardNumber']        = !isset($transactionDetail['cardNumber']) ? '' : $transactionDetail['cardNumber'];
                    $transactionDetail['locationNumber']    = !isset($transactionDetail['locationNumber']) ? '' : $transactionDetail['locationNumber'];
                    $transactionDetail['transactionId']     = !isset($transactionDetail['transactionId']) ? '' : $transactionDetail['transactionId'];

                    //GET product
                    $product = reset($transactionDetail['transactionLineItems']);

                    //почему-то иногда пусто
                    $product['product']             = !isset($product['product']) ? '' : $product['product'];
                    $product['customerValue']       = !isset($product['customerValue']) ? 0 : $product['customerValue'];
                    $product['customerTaxAmount']   = !isset($product['customerTaxAmount']) ? 0 : $product['customerTaxAmount'];
                    $product['quantity']            = !isset($product['quantity']) ? 0 : $product['quantity'];
                    $product['taxRate']             = !isset($product['taxRate']) ? 'null' : $product['taxRate'];
                    $product['originalValue']       = !isset($product['originalValue']) ? 0 : $product['originalValue'];

                    $data = [
                        'agent_id'              => $this->_agentId, //number -- (по умолчанию 4)
                        'tube_id'               => $this->_tubeId, //number -- (по умолчанию 70183602)
                        'account_number'        => $this->_quote(''), //varchar2(50) -- номер аккаунта (не обязательно)
                        'sub_account_number'    => $this->_quote(isset($transactionDetail['accountNumber']) ? $transactionDetail['accountNumber'] : ''), //varchar2(50) -- номер субаккаунта (не обязательно)
                        'invoice_id'            => $this->_quote(''), //varchar2(50) -- номер инвойса (не обязательно)
                        'invoice_date'          => $this->_quote(''), //varchar2(50) -- дата инвойса (не обязательно)
                        'card_group'            => $this->_quote(''), //varchar2(500) -- группа карт (не обязательно)
                        'card_number'           => $this->_quote($transactionDetail['cardNumber']), //varchar2(20) -- номер карты
                        'date_trn'              => $this->_date($transactionDetail['effectiveAt']['value'] / 1000), //date -- дата транзакции
                        'time_trn'              => $this->_quote(date('H:i:s', $transactionDetail['effectiveAt']['value'] / 1000)), //varchar2(10) -- время транзакции (формат hh24:mi:ss)
                        'holder'                => $this->_quote(isset($transactionDetail['embossingName']) ? $transactionDetail['embossingName'] : ''), //varchar2(500) -- держатель карты (не обязательно)
                        'vrn'                   => $this->_quote(''), //varchar2(10) -- (не обязательно)
                        'fleet_id'              => $this->_quote(isset($transactionDetail['fleetId']) ? $transactionDetail['fleetId'] : ''), //varchar2(50) -- (не обязательно)
                        'supplier_terminal'     => $this->_quote($transactionDetail['terminalId']), //varchar2(50) -- ID терминала в системе shell
                        'pos_name'              => $this->_quote($transactionDetail['locationNumber']), //varchar2(255) -- название АЗС
                        'transaction_type'      => $this->_quote($transactionDetail['transactionType']), //varchar2(255) -- тип транзакции
                        'receipt_number'        => $this->_quote(isset($transactionDetail['orderNumber']) ? $transactionDetail['orderNumber'] : ''), //varchar2(50) -- номер чека (не обязательно)
                        'odometer'              => $this->_quote(isset($transactionDetail['odometer']) ? $transactionDetail['odometer'] : ''), //varchar2(50) -- показание одометра (не обязательно)
                        'service_id'            => $this->_quote($product['product']), //varchar2(50) -- ID услуги @todo запросить у wex id услуги
                        'service_name'          => $this->_quote($product['product']), //varchar2(500) -- название услуги (если отличается от ID услуги)
                        'service_amount'        => $product['quantity'], //number -- количество литров
                        'units'                 => $this->_quote(isset($product['isFuel']) ? $product['isFuel'] : ''), //varchar2(50) -- размерность услуги (не обязательно)
                        'vat_rate'              => $product['taxRate'], //number -- размер НДС
                        'service_price'         => empty($product['quantity']) ? 'null' : round($product['originalValue'] / $product['quantity'], 2), //number -- цена на АЗС
                        'price_buy'             => empty($product['quantity']) ? 'null' : round($product['customerValue'] / $product['quantity'], 2), //number -- цена покупки на АЗС
                        'rebate_rate'           => 'null', //number -- (не обязательно)
                        'rebate_rate_type'      => 'null', //number -- (не обязательно)
                        'service_price_net'     => empty($product['quantity']) ? 'null' : round(($product['customerValue'] - $product['customerTaxAmount']) / $product['quantity'], 2), //number -- цена на АЗС без НДС
                        'rebate_value'          => 'null', //number -- (не обязательно)
                        'vat_amount'            => $product['customerTaxAmount'], //number -- размер налога
                        'service_sumprice_net'  => $product['originalValue'] - $product['customerTaxAmount'], //number -- стоимость на АЗС без НДС
                        'service_sumprice'      => $product['originalValue'], //number -- стоимость на АЗС с НДС
                        'pos_currency'          => $this->_quote(''), //varchar2(3) -- валюта АЗС @todo нет данных
                        'currency_rate'         => 1, //number -- курс @todo нет данных
                        'sumprice_buy_net'      => $product['customerValue'] - $product['customerTaxAmount'], //number -- цена покупки у Шелл без НДС
                        'sumprice_buy'          => $product['customerValue'], //number -- цена покупки у Шелл с НДС
                        'client_currency'       => $this->_quote($this->_currency), //varchar2(3) -- валюта клиента (по умолчанию '643')
                        'transaction_id'        => $this->_quote($transactionDetail['transactionId']), //varchar2(50) -- номер транзакции
                        'transaction_status'    => $this->_quote($transactionDetail['transactionStatus']), //varchar2(50) -- стутус транзакции
                        //'date_insert'           => '', //date -- по умолчанию текущая дата (не нужно, подставляется сама)
                    ];

                    $sql = 'INSERT INTO s_dev.test_transaction_shell_v2 (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $data) . ')';

                    try {
                        //execute
                        $this->_dbExecute($sql);

                        //for checking loaded
                        $this->_loadedTransactions[] = $transaction['transactionId'];

                        $this->_log('inserted transaction ' . $transaction['transactionId']);
                    } catch (Exception $e) {
                        die($this->_logErrorExecute);
                    }
                }
            }
        }

        $this->_log('loadTransactions finished: ' . $cnt);
    }
}