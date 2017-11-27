<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
    const USER_TEST 	= 6;

    const ROLE_GOD 	                    = 0;
    const ROLE_ROOT 	                = 1;
    const ROLE_ADMIN 	                = 2;
    const ROLE_SUPERVISOR               = 3;
    const ROLE_MANAGER                  = 4;
    const ROLE_USER		                = 99;
    const ROLE_USER_SECOND		        = 98;
    const ROLE_MANAGER_SALE		        = 5;
    const ROLE_MANAGER_SALE_SUPPORT		= 6;
    const ROLE_CLIENT		            = 97;

    public static $roles = [
        self::ROLE_MANAGER              => 'Менеджер сопровождения',
        self::ROLE_MANAGER_SALE         => 'Менеджер по продажам',
        self::ROLE_MANAGER_SALE_SUPPORT => 'Менеджер по продажам и сопровождению',
        self::ROLE_SUPERVISOR           => 'Главный менеджер',
        self::ROLE_USER                 => 'Клиент',
        self::ROLE_USER_SECOND          => 'Клиент (без редактирования лимитов)',
        self::ROLE_CLIENT               => 'Клиент (только просмотр)',
    ];

    public static $clientRoles = [
        self::ROLE_USER                 => 'Клиент',
        self::ROLE_USER_SECOND          => 'Клиент (без редактирования лимитов)',
        self::ROLE_CLIENT               => 'Клиент (только просмотр)',
    ];

    public static $adminRoles = [
        self::ROLE_ADMIN,
        self::ROLE_ROOT,
        self::ROLE_GOD
    ];

    public static $rolesForCardGroups = [
        self::ROLE_ADMIN,
        self::ROLE_ROOT,
        self::ROLE_GOD,
        self::ROLE_CLIENT,
    ];

    /**
     * функция проверки доступа
     */
    public static function allow($action, $onlySee = false)
    {
        if(empty($action)){
            return true;
        }

        $user = Auth_Oracle::instance()->get_user();

        if(in_array($user['role'], [self::ROLE_ROOT])){
            return true;
        }

        if(Kohana::$environment == Kohana::DEVELOPMENT && $user['MANAGER_ID'] == 7) return true;

        $access = Kohana::$config->load('access')->as_array();

        $allow = $access['allow'];
        $deny = $access['deny'];

        if(
            // если задано разрешение и нет роли/агента/юзера, то нельзя
            (isset($allow[$action]) && (
                !in_array($user['role'], $allow[$action]) &&
                !in_array('u_'.$user['MANAGER_ID'], $allow[$action]) &&
                !in_array('a_'.$user['AGENT_ID'], $allow[$action])
            )) ||
            // если задан запрет на действие и хоть где-то роль/агент/юзер, то нельзя
            (isset($deny[$action]) && (
                in_array($user['role'], $deny[$action]) ||
                in_array('u_'.$user['MANAGER_ID'], $deny[$action]) ||
                in_array('a_'.$user['AGENT_ID'], $deny[$action])
            ))
        ){
            return false;
        }

        //если нет явного запрета или наоборот, доступа только конкретной роли

        if(!$onlySee && in_array($user['role'], [self::ROLE_CLIENT])){
            return false;
        }

        return true;
    }

    /**
     * проверка запрета доступа
     *
     * @param $action
     * @return bool
     */
    public static function deny($action)
    {
        return !self::allow($action);
    }

    /**
     * проверка доступов
     *
     * @param $type
     * @param $id
     */
    public static function check($type, $id)
    {
        if (empty($type) || empty($id)) {
            throw new HTTP_Exception_404();
        }

        $allow = false;

        switch($type){
            case 'client':
                $clients = Model_Client::getClientsList(false, [
                    'ids' => $id
                ]);

                if(!empty($clients[$id])){
                    $allow = true;
                }
                break;
            case 'contract':
                $clients = Model_Client::getClientsList();

                $contracts = Model_Contract::getContracts(false, [
                    'client_id' => array_keys($clients),
                    'contract_id' => $id
                ]);

                if(!empty($contracts)){
                    $allow = true;
                }
                break;
            case 'card':
                $clients = Model_Client::getClientsList();

                $contracts = Model_Contract::getContracts(false, [
                    'client_id' => array_keys($clients),
                ]);

                $params = [
                    'contract_id' => array_column($contracts, 'CONTRACT_ID')
                ];

                $cards = Model_Card::getCards(false, $id, $params);

                if(!empty($cards)){
                    $allow = true;
                }
                break;
        }

        if(!$allow){
            throw new HTTP_Exception_404();
        }
    }
}