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
}
