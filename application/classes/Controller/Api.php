<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_Template
{
    private $_token;

    /**
     * @var Api
     */
    private $_api;

    protected function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonResult($result, $data = [])
    {
        self::json(['success' => $result, 'data' => $data]);
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
                $this->jsonResult(0, ['error' => 'empty token']);
            } else {
                $managerId = $this->_api->getUserIdByToken($this->_token);

                if (empty($managerId)) {
                    $this->jsonResult(0, ['error' => 'invalid token']);
                } else {
                    $user = Model_Manager::getManager($managerId);

                    $resultAuth = Auth::instance()->login($user, ['hash' => $user['PASSWORD']], false);

                    if (empty($resultAuth)) {
                        $messages = Messages::get();
                        $this->jsonResult(0, ['errors' => $messages]);
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
            $messages = Messages::get();
            $this->jsonResult(0, ['errors' => $messages]);
        }

        $user = User::current();

        $this->_token = $this->_api->getToken($user['MANAGER_ID']);

        if ($this->_token) {
            $this->jsonResult(1, ['token' => $this->_token]);
        }

        $this->jsonResult(0, ['errors' => ['get token error']]);
    }

    /**
     * test
     */
    public function action_test()
    {
        $this->jsonResult(1, ['test' => true]);
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
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * GET
     * грузим историю операций
     */
    public function action_transactions_history()
    {
        $this->jsonResult(false);
    }

    /**
     * GET
     * получаем лимиты по карте
     */
    public function action_card_limits()
    {
        $cardId = $this->request->query('card_id');

        try {
            Access::check('card', $cardId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false);
        }

        $limits = Model_Card::getOilRestrictions($cardId);

        if(empty($limits)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, $limits);
    }

    /**
     * GET
     * получаем список карт по договору
     */
    public function action_cards_list()
    {
        $cardId = $this->request->query('card_id');
        $contractId = $this->request->query('contract_id');

        try {
            Access::check('contract', $contractId);
        } catch (HTTP_Exception_404 $e) {
            $this->jsonResult(false);
        }

        $cards = Model_Card::getCards($cardId, $contractId);

        if(empty($limits)){
            $this->jsonResult(false);
        }

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
            $this->jsonResult(false);
        }

        $user = User::current();

        $params = [
            'agent_id' => $user['AGENT_ID']
        ];

        $contracts = Model_Contract::getContracts($clientId, $params);

        if(empty($limits)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, $contracts);
    }
}
