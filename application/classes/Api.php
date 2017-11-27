<?php defined('SYSPATH') or die('No direct script access.');

class Api
{
    const DB_API_PACK = 'api_pack.';

    private $_db;

    public function __construct()
    {
        $this->_db = Oracle::init();
        //$this->_db->setPack(self::DB_API_PACK);
    }

    /**
     * получение токена
     */
    public function getToken($userId)
    {
        if (empty($userId)) {
            return false;
        }

        $data = [
            'p_manager_id'  => $userId,
            'p_token'       => 'out',
            'p_error_code'  => 'out'
        ];

        $res = $this->_db->procedure('api_get_token', $data, true);

        if ($res['p_error_code'] == Oracle::CODE_SUCCESS) {
            return $res['p_token'];
        }

        return false;
    }

    /**
     * проверка токена
     *
     * @param $token
     */
    public function getUserIdByToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $data = [
            'p_token'       => $token,
            'p_manager_id'  => 'out',
            'p_error_code'  => 'out'
        ];

        $res = $this->_db->procedure('api_check_token', $data, true);

        if ($res['p_error_code'] == Oracle::CODE_SUCCESS) {
            return $res['p_manager_id'];
        }

        return false;
    }
}