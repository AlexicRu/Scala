<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Oracle extends Auth {
    private static $_prefix = 'usketch.';

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

            return isset($user['role']) && $user['role'] == (int)$role;
        }
    }

    protected function _login($user, $password, $remember)
    {
        if ( ! is_array($user))
        {
            $db = Oracle::getInstance();

            $user = $db->row("select * from ".self::$_prefix."MANAGER t where t.login = '".strtoupper($user)."'");
            $user['role'] = $user['ACCESS_TYPE'];
		}

        if (is_string($password))
        {
            // Create a hashed password
            $password = $this->hash($password);
		}
		
        // If the passwords match, perform a login
        if (!empty($user['PASSWORD']) && $user['PASSWORD'] === $password)
        {
	
            // Finish the login
            $this->complete_login($user);

            return TRUE;
        }

        // Login failed
        return FALSE;
    }

    public function password($user)
    {
        if ( ! is_array($user))
        {
            $db = Oracle::getInstance();

            $user = $db->row("select * from ".self::$_prefix."MANAGER t where t.login = '".strtoupper($user)."'");
        }

        return $user['PASSWORD'];
    }

    public function check_password($password)
    {
        $user = $this->get_user();

        if ( ! $user)
            return FALSE;

        return ($this->hash($password) === $user['PASSWORD']);
    }

    public function hash($password)
    {
        if ( ! $this->_config['hash_key'])
            throw new Kohana_Exception('A valid hash key must be set in your auth config.');

        return strtoupper(sha1($this->_config['hash_key'].strtoupper($password)));
    }


    protected function complete_login($user)
    {
        $db = Oracle::getInstance();

        $user['clients'] = $db->column("select t.manager_client_id from ".self::$_prefix."MANAGER_CLIENTS t where t.manager_id = ".$user['MANAGER_ID'], 'manager_client_id');

        parent::complete_login($user);
    }
	
    public function regenerate_session()
    {
        $user = Auth::instance()->get_user();
        $db = Oracle::getInstance();

        $user['clients'] = $db->column("select t.manager_client_id from ".self::$_prefix."MANAGER_CLIENTS t where t.manager_id = ".$user['MANAGER_ID'], 'manager_client_id');

        parent::complete_login($user);
    }	
	
	public function regenerate_user_profile()
	{
		$user = Auth::instance()->get_user();
        $db = Oracle::getInstance();

		$user = $db->row("select * from ".self::$_prefix."MANAGER t where t.manager_id = ".$user['MANAGER_ID']);
        $user['role'] = $user['ACCESS_TYPE'];

        self::complete_login($user);
	}
}