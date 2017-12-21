<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_Template
{
    private $_token;

    /**
     * @var Api
     */
    private $_api;
    private $_errors = [];

    protected function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonResult($result, $data = [])
    {
        if (empty($result)) {
            $apiErrors = $this->_api->getErrors();

            $data = array_merge($this->_errors, (array)$data, $apiErrors);

            if (empty($data)) {
                $data[] = 'unknown error';
            }
        }

        self::json(['success' => $result, 'data' => (array)$data]);
    }

    public function before()
    {
        $this->_api = new Api();
        $this->_token = $this->request->headers('token') ?: ($this->request->post('token') ?: $this->request->query('token'));

        $action = $this->request->action();

        $withoutToken = [
            'login' => true
        ];

        if (!isset($withoutToken[$action])) {
            if (empty($this->_token)) {
                $this->jsonResult(false, 'empty token');
            } else {
                $managerId = $this->_api->getUserIdByToken($this->_token);

                if (empty($managerId)) {
                    $this->jsonResult(false, 'invalid token');
                } else {
                    $user = Model_Manager::getManager($managerId);

                    $resultAuth = Auth::instance()->login($user, ['hash' => $user['PASSWORD']], false);

                    if (empty($resultAuth)) {
                        foreach (Messages::get() as $item) {
                            if ($item['type'] == 'error') {
                                $this->_errors[] = $item['text'];
                            }
                        }

                        $this->jsonResult(false);
                    }
                }
            }
        }
    }

    /**
     * POST
     * авторизация
     */
    public function action_login()
    {
        $login = $this->request->post('login');
        $password = $this->request->post('password');

        $resultAuth = Auth::instance()->login($login, $password, FALSE);

        if (empty($resultAuth)) {
            foreach (Messages::get() as $item) {
                if ($item['type'] == 'error') {
                    $this->_errors[] = $item['text'];
                }
            }

            $this->jsonResult(false);
        }

        $user = User::current();

        $this->_token = $this->_api->getToken($user['MANAGER_ID']);

        if ($this->_token) {
            $this->jsonResult(true, ['token' => $this->_token]);
        }

        $this->jsonResult(false);
    }

    /**
     * test
     */
    public function action_test()
    {
        $this->jsonResult(true, ['test' => true]);
    }

    /**
     * POST
     * ищменение статуса карты
     */
    public function action_card_status()
    {
        $params = [
            'card_id'       => $this->request->post('card_id'),
            'contract_id'   => $this->request->post('contract_id'),
            'comment'       => $this->request->post('comment')
        ];

        $result = Model_Card::toggleStatus($params);

        if(empty($result)){
            $this->jsonResult(false, 'Статус не изменился');
        }

        $this->jsonResult(true);
    }

    /**
     * GET
     * грузим историю операций
     */
    public function action_transactions()
    {
        $contractId = $this->request->query('contract_id');
        $dateFrom = $this->request->query('date_from') ?: date('01.m.Y');
        $dateTo = $this->request->query('date_to') ?: date('d.m.Y');

        try {
            Access::check('contract', $contractId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'no access to contract');
        }

        $transactions = Model_Transaction::getTransactions($contractId, [
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ], [
            "DATETIME_TRN",
            "CARD_ID",
            "CLIENT_ID",
            "CONTRACT_ID",
            "OPERATION_ID",
            "SUPPLIER_TERMINAL",
            "SERVICE_ID",
            "DESCRIPTION",
            "SERVICE_AMOUNT",
            "SERVICE_PRICE",
            "SERVICE_SUMPRICE",
            "TRN_CURRENCY",
            "PRICE_DISCOUNT",
            "SUMPRICE_DISCOUNT",
            "POS_ADDRESS",
            "TRN_KEY",
            "TRZ_COMMENT"
        ]);

        $this->jsonResult(true, $transactions);
    }

    /**
     * GET
     * получаем лимиты по карте
     */
    public function action_card_limits()
    {
        $cardId = $this->request->query('card_id');
        $contractId = $this->request->query('contract_id');

        try {
            Access::check('card', $cardId, $contractId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'no access to card');
        }

        $limits = Model_Card::getOilRestrictions($cardId, [
            "SERVICE_ID",
            "DESCRIPTION",
            "LIMIT_GROUP",
            "LIMIT_PARAM",
            "LIMIT_TYPE",
            "LIMIT_VALUE",
            "LIMIT_CURRENCY",
        ]);

        $this->jsonResult(true, $limits);
    }

    /**
     * GET
     * получаем список карт по договору
     */
    public function action_cards_list()
    {
        $contractId = $this->request->query('contract_id');

        try {
            Access::check('contract', $contractId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'no access to contract');
        }

        $cards = Model_Card::getCards($contractId, false, false, [
            "CARD_ID",
            "HOLDER",
            "DATE_HOLDER",
            "CARD_STATE",
            "BLOCK_AVAILABLE",
            "CHANGE_LIMIT_AVAILABLE",
            "CARD_COMMENT"
        ]);

        $this->jsonResult(true, $cards);
    }

    /**
     * GET
     * получаем список контрактов
     */
    public function action_contracts_list()
    {
        $clientId = $this->request->query('client_id');

        try {
            Access::check('client', $clientId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'no access to client');
        }

        $user = User::current();

        $params = [
            'agent_id' => $user['AGENT_ID']
        ];

        $contracts = Model_Contract::getContracts($clientId, $params, [
            "CONTRACT_ID",
            "CONTRACT_NAME",
            "DATE_BEGIN",
            "DATE_END",
            "CURRENCY",
            "STATE_ID",
        ]);

        $this->jsonResult(true, $contracts);
    }

    /**
     * GET
     * получаем список контрактов
     */
    public function action_clients_list()
    {
        $user = User::current();

        $clients = Model_Client::getClientsList(null, [
            'manager_id' => $user['MANAGER_ID']
        ], [
            'CLIENT_ID',
            'CLIENT_NAME',
            'LONG_NAME',
            'CLIENT_STATE',
        ]);

        $this->jsonResult(true, $clients);
    }
}
