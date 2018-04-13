<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_Template
{
    private $_token;

    /**
     * @var Api
     */
    private $_api;
    private $_errors = [];
    private $_data = [];

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
                $data[] = 'Unknown error';
            }

            http_response_code(400);
        }


        self::json([
            'success' => $result,
            'data' => Arr::arrayChangeKeyCaseRecursive((array)$data)
        ]);
    }

    public function before()
    {
        $this->_api = new Api();
        $this->_token = $this->request->headers('token') ?: $this->request->post('token');

        $body = $this->request->body();
        $this->_data = $body ? json_decode($body, true) : [];

        $action = $this->request->action();

        $withoutToken = [
            'login' => true
        ];

        if (!isset($withoutToken[$action])) {
            if (empty($this->_token)) {
                $this->jsonResult(false, 'Empty token');
            } else {
                $managerId = $this->_api->getUserIdByToken($this->_token);

                if (empty($managerId)) {
                    $this->jsonResult(false);
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

        if (empty($login) || empty($password)) {
            $this->jsonResult(false, 'Not enough data');
        }

        $resultAuth = Auth::instance()->login($login, $password, FALSE);

        if (empty($resultAuth)) {
            foreach (Messages::get() as $item) {
                var_dump($item);
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
     * POST
     * изменение статуса карты
     */
    public function action_cardStatus()
    {
        $params = [
            'card_id'       => $this->request->post('card_id'),
            'contract_id'   => $this->request->post('contract_id'),
            'comment'       => $this->request->post('comment'),
            'status'        => is_null($this->request->post('block')) ? false : (
                $this->request->post('block') ? Model_Card::CARD_STATE_BLOCKED : Model_Card::CARD_STATE_IN_WORK
            ),
        ];

        if (empty($params['card_id']) || empty($params['contract_id'])) {
            $this->jsonResult(false, 'Not enough data');
        }

        try {
            Access::check('card', $params['card_id'], $params['contract_id']);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'No access to card');
        }

        $result = Model_Card::toggleStatus($params);

        if(empty($result)){
            $this->jsonResult(false, 'Status not changed');
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

        if (empty($contractId)) {
            $this->jsonResult(false, 'Not enough data');
        }

        try {
            Access::check('contract', $contractId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'No access to contract');
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

        foreach ($transactions as &$transaction) {
            $transaction['TRN_COMMENT'] = $transaction['TRZ_COMMENT'];
            unset($transaction['TRZ_COMMENT']);
        }

        $this->jsonResult(true, $transactions);
    }

    /**
     * GET
     * получаем лимиты по карте
     *
     * DELETE
     * удаляем лимит по карте
     *
     * POST
     * добавление лимита по карте
     *
     * PUT
     * редактирование лимита по карте
     */
    public function action_cardLimits()
    {
        $method = strtoupper($this->request->method());

        $result = true;
        $data = [];

        switch ($method) {
            case 'GET':
                $cardId = $this->request->query('card_id');
                $contractId = $this->request->query('contract_id');

                if (empty($contractId) || empty($cardId)) {
                    $this->jsonResult(false, 'Not enough data');
                }

                try {
                    Access::check('card', $cardId, $contractId);
                } catch (HTTP_Exception_404 $e) {
                    $this->jsonResult(false, 'No access to card');
                }

                $data = Model_Card::getOilRestrictions($cardId, false, [
                    "LIMIT_ID",
                    "SERVICE_ID",
                    "SERVICE_NAME",
                    "CARD_ID",
                    "DURATION_TYPE",
                    "DURATION_VALUE",
                    "UNIT_TYPE",
                    "UNIT_CURRENCY",
                    "LIMIT_VALUE",
                    "TRN_COUNT",
                    "DAYS_WEEK_TYPE",
                    "DAYS_WEEK",
                    "TIME_FROM",
                    "TIME_TO",
                ]);

                foreach ($data as &$limit) {
                    $limit['services'] = array_column($limit['services'], 'id');
                }

                break;

            case 'DELETE':
                $limitId = $this->request->param('id') ?: false;

                list($result, $data) = Model_Card::delLimit($limitId);

                break;

            case 'POST':

                $limitId        = -1;

            case 'PUT':
                $limitId        = !empty($limitId) ? $limitId : $this->request->param('id');
                $cardId         = $this->_data['card_id'] ?: false;
                $value          = $this->_data['limit_value'] ?: false;
                $unitType       = $this->_data['unit_type'] ?: false;
                $durationType   = $this->_data['duration_type'] ?: false;
                $services       = $this->_data['services'] ?: [];

                try {
                    foreach ($services as $service) {
                        Access::check('service', $cardId, $service);
                    }
                } catch (HTTP_Exception_404 $e) {
                    $this->jsonResult(false, 'No access to service');
                }

                $limits = [
                    [
                        'limit_id'      => $limitId,
                        'value'         => $value,
                        'unit_type'     => $unitType,
                        'duration_type' => $durationType,
                        'services'      => $services
                    ]
                ];

                list($result, $data) = Model_Card::checkCardLimits($cardId, false, $limits);

                if (!empty($result)) {
                    list($result, $data) = Model_Card::editCardLimitsSimple($cardId, -1, $limits);
                }

                break;
        }

        $this->jsonResult($result, $data);
    }

    /**
     * GET
     * получаем список карт по договору
     */
    public function action_cards()
    {
        $contractId = $this->request->query('contract_id');
        $cardId = $this->request->param('id') ?: false;

        if (empty($contractId)) {
            $this->jsonResult(false, 'Not enough data');
        }

        if (!empty($cardId)) {
            try {
                Access::check('card', $cardId, $contractId);
            } catch (HTTP_Exception_404 $e) {
                $this->jsonResult(false, 'No access to card');
            }
        } else {
            try {
                Access::check('contract', $contractId);
            } catch (HTTP_Exception_404 $e) {
                $this->jsonResult(false, 'No access to contract');
            }
        }

        $cards = Model_Card::getCards($contractId, $cardId, false, [
            "CARD_ID",
            "HOLDER",
            "DATE_HOLDER",
            "CARD_STATE",
            "BLOCK_AVAILABLE",
            "CHANGE_LIMIT_AVAILABLE",
            "CARD_COMMENT"
        ]);

        foreach ($cards as &$card) {
            $card['CARD_STATUS'] = $card['CARD_STATE'];
            unset($card['CARD_STATE']);
        }

        $this->jsonResult(true, !empty($cardId) ? reset($cards) : $cards);
    }

    /**
     * GET
     * получаем список контрактов
     */
    public function action_contracts()
    {
        $clientId = $this->request->query('client_id');

        if (empty($clientId)) {
            $this->jsonResult(false, 'Not enough data');
        }

        try {
            Access::check('client', $clientId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false, 'No access to client');
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

        foreach ($contracts as &$contract) {
            $contract['CONTRACT_STATUS'] = $contract['STATE_ID'];
            unset($contract['STATE_ID']);

            $contract['BALANCE'] = Model_Contract::getContractBalance($contract['CONTRACT_ID'], [], [
                "BALANCE",
                "MONTH_REALIZ",
                "MONTH_REALIZ_CUR",
                "LAST_MONTH_REALIZ",
                "LAST_MONTH_REALIZ_CUR",
                "DATE_LAST_CHANGE"
            ]);
        }

        $this->jsonResult(true, $contracts);
    }

    /**
     * GET
     * получаем список клиентов
     */
    public function action_clients()
    {
        $user = User::current();

        $clients = Model_Manager::getClientsList([
            'manager_id' => $user['MANAGER_ID']
        ], [
            'CLIENT_ID',
            'CLIENT_NAME',
            'LONG_NAME',
            'CLIENT_STATE',
        ]);

        $this->jsonResult(true, $clients);
    }

    /**
     * GET
     * получаем список сервисов по карте
     */
    public function action_cardServices()
    {
        $cardId = $this->request->query('card_id');

        $servicesList = Model_Card::getServices($cardId, [
            'SERVICE_ID',
            'SYSTEM_SERVICE_NAME'
        ]);

        $this->jsonResult(true, $servicesList ?: []);
    }
}
