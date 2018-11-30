<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Oracle extends Auth {

    /**
     * Do username/password check here
     *
     * @param $user
     * @param $password
     * @param $remember
     * @return bool
     * @throws Kohana_Exception
     */
    protected function _login($user, $password, $remember)
    {
        if ( ! is_array($user))
        {
            $user = Model_Manager::getManager(['login' => strtoupper($user)]);
        }

        if (is_string($password))
        {
            // Create a hashed password
            $password = $this->hash($password);
        } else if (!empty($password['hash'])) {

            $request = Request::current();

            $controllersPasswordHashAvailable = [
                'Telegram'  => true,
                'Api'       => true
            ];
            $actionsPasswordHashAvailable = [
                'forceLogin'  => true,
            ];

            if (
                isset($controllersPasswordHashAvailable[$request->controller()]) ||
                isset($actionsPasswordHashAvailable[$request->action()])
            ) {
                $password = $password['hash'];
            }
        }

        // If the passwords match, perform a login
        if (empty($user['PASSWORD']) || $user['PASSWORD'] !== $password){
            Messages::put('Неправильный логин или пароль', 'error');
            return false;
        }

        if ($user['AGENT_STATE'] != 1){
            Messages::put('Доступ запрещен', 'error');
            return false;
        }

        if($user['STATE_ID'] != 1){
            Messages::put('Доступ запрещен', 'error');
            return false;
        }

        // Finish the login
        $this->complete_login($user);

        if (!empty($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $data = [
                'p_manager_id' => $user['MANAGER_ID'],
                'p_params' => $_SERVER['HTTP_USER_AGENT'] . ';' . $_SERVER['REMOTE_ADDR']
            ];

            $db = Oracle::init();
            $db->procedure('auth_user', $data);
        }

        return true;
    }

    /**
     * Return the password for the username
     *
     * @param $user
     * @return mixed
     */
    public function password($user)
    {
        if ( ! is_array($user))
        {
            $db = Oracle::init();

            $user = $db->row("select * from ".Oracle::$prefix."V_WEB_MANAGERS where LOGIN = '".strtoupper($user)."'");
        }

        return $user['PASSWORD'];
    }

    /**
     * Check to see if the logged in user has the given password
     *
     * @param $password
     * @return bool
     * @throws Kohana_Exception
     */
    public function check_password($password)
    {
        $user = $this->get_user();

        if ( ! $user)
            return FALSE;

        return ($this->hash($password) === $user['PASSWORD']);
    }

    /**
     * Check to see if the user is logged in, and if $role is set, has all roles
     *
     * @param null $role
     * @return bool
     */
    public function logged_in($role = NULL)
    {
        // Get the user from the session
        $user = $this->get_user();

        if ( ! $user)
            return FALSE;

        if (is_array($user) && isset($user['LOGIN']) && isset($user['PASSWORD']) && $this->password($user['LOGIN']) === $user['PASSWORD'])
        {
            // If we don't have a roll no further checking is needed
            if ( ! $role)
                return TRUE;

            return isset($user['ROLE_ID']) && $user['ROLE_ID'] == (int)$role;
        }
    }

    /**
     * функция хеширования пароля
     *
     * @param string $password
     * @return string
     * @throws Kohana_Exception
     */
    public function hash($password)
    {
        if ( ! $this->_config['hash_key'])
            throw new Kohana_Exception('A valid hash key must be set in your auth config.');

        return strtoupper(md5($this->_config['hash_key'].strtoupper($password)));
    }

    /**
     * перед завершением авторизации получаем дополнительные данные по клиенту
     *
     * @param $user
     */
    protected function complete_login($user)
    {
        $db = Oracle::init();

        $user['clients'] = $db->column("select CLIENT_ID from ".Oracle::$prefix."V_WEB_MANAGER_CLIENTS where MANAGER_ID = ".$user['MANAGER_ID'], 'CLIENT_ID');
        $user['contracts'] = Model_Manager::getContractsTree($user['MANAGER_ID']);
        $user['managers_binds'] = User::getManagersBinds($user['MANAGER_ID']);

        parent::complete_login($user);
    }

    /**
     * при регенерации данных пользователя подтягиваем его роль и вызываем завершение авторизации
     */
	public function regenerate_user_profile()
	{
		$user = Auth::instance()->get_user();
        $db = Oracle::init();

		$user = $db->row("select * from ".Oracle::$prefix."V_WEB_MANAGERS where MANAGER_ID = ".$user['MANAGER_ID']);

        self::complete_login($user);
	}

    /**
     * если надо в сессии пользователя сохранить доп данные
     *
     * @param $user
     */
	public function saveSession($user)
    {
        parent::complete_login($user);
    }
}