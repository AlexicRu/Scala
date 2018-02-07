<?php defined('SYSPATH') or die('No direct script access.');

class Api
{
    const DB_API_PACK = 'api_pack.';

    /**
     * @var Oracle
     */
    private $_db;
    private $_errors = [];

    public function __construct()
    {
        $this->_db = Oracle::init('api');
        //$this->_db->setPack(self::DB_API_PACK);
    }

    public function getErrors()
    {
        return $this->_errors;
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

        switch ($res['p_error_code']) {
            case 3:
                $this->_errors[] = 'API for current manager is forbidden';
                break;
            case 4:
                $this->_errors[] = 'Manager blocked';
                break;
            default:
                $this->_errors[] = 'Unknown error';
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

        switch ($res['p_error_code']) {
            case 2:
                $this->_errors[] = 'Invalid token';
                break;
            case 3:
                $this->_errors[] = 'API for current manager is forbidden';
                break;
            case 4:
                $this->_errors[] = 'Manager blocked';
                break;
            case 5:
                $this->_errors[] = 'Token expired';
                break;
            default:
                $this->_errors[] = 'Unknown error';
        }

        return false;
    }

    /**
     * получаем json структуру апи
     */
    public static function getStructure()
    {
        $config = Common::getEnvironmentConfig()['api'];
        $api = Kohana::$config->load('api')->as_array();

        $api  = array_merge($api, $config);

        $definitions = [];
        $paths = [];

        $apiConfigUrl = __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'config' .
            DIRECTORY_SEPARATOR . 'api' .
            DIRECTORY_SEPARATOR
        ;

        //definitions
        $definitionsFiles = scandir($apiConfigUrl . 'definitions');

        foreach ($definitionsFiles as $file) {
            if (is_file($apiConfigUrl . 'definitions' . DIRECTORY_SEPARATOR . $file)) {
                $definition = explode('.', $file)[0];
                $definitions[$definition] = Kohana::$config->load('api/definitions/' . $definition)->as_array();
            }
        }

        //paths
        $pathsFiles = scandir($apiConfigUrl . 'paths');

        foreach ($pathsFiles as $file) {
            if (is_file($apiConfigUrl . 'paths' . DIRECTORY_SEPARATOR . $file)) {
                $pathName = explode('.', $file)[0];
                $path = Kohana::$config->load('api/paths/' . $pathName)->as_array();
                $paths[$path['url']][$path['method']] = $path;

                if (empty($paths[$path['url']]['sort'])) {
                    $paths[$path['url']]['sort'] = $path['sort'];
                }
            }
        }

        uasort($paths, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return $a['sort'] > $b['sort'] ? 1 : -1;
        });

        //сортируем
        foreach ($paths as &$methods) {
            unset($methods['sort']);

            if (count($methods) > 1) {
                uasort($methods, function ($a, $b) {
                    if ($a['sort'] == $b['sort']) {
                        return 0;
                    }

                    return $a['sort'] > $b['sort'] ? 1 : -1;
                });
            }
        }

        $api['definitions'] = $definitions;
        $api['paths'] = $paths;

        return json_encode($api);
    }
}