<?php defined('SYSPATH') or die('No direct script access.');

class Shell
{
    const CARD_STATUS_ACTIVE = 'Active';

    private $_url = 'https://api-emea.prod.emea.wexinc.co.uk';
    private $_token = null;
    private $_config = null;
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

    public function __construct($config = [])
    {
        $this->_config = !empty($config) ? $config : Kohana::$config->load('config')['shell'];

        $response = $this->_request($this->_actions['getToken'], 'post');

        if (empty($response['access_token'])) {
            throw new Exception('empty token');
        }

        $this->_token = $response['access_token'];
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
                "username"=> $this->_config['login'],
                "password"=> $this->_config['password']
            ]);
        } else {
            $authorization = "Authorization: Bearer " . $this->_token;
            $options[CURLOPT_HTTPHEADER][] = $authorization;
        }

        $ch      = curl_init( $this->_url . $url );

        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        curl_close( $ch );

        return json_decode($content, true);
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
        return $this->_request($this->_actions['getCustomers']);
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
}