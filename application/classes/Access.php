<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
    const USER_TEST 	= 6;

    const ROLE_GOD 	                    = 0;
    const ROLE_ROOT 	                = 1;
    const ROLE_ADMIN 	                = 2;
    const ROLE_SUPERVISOR               = 3;
    const ROLE_MANAGER                  = 4;
    const ROLE_MANAGER_SALE		        = 5;
    const ROLE_MANAGER_SALE_SUPPORT		= 6;
    const ROLE_ADMIN_READONLY		    = 7;
    const ROLE_CLIENT		            = 97;
    const ROLE_USER_SECOND		        = 98;
    const ROLE_USER		                = 99;

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
        self::ROLE_GOD,
        self::ROLE_SUPERVISOR,
    ];

    public static $readonlyRoles = [
        self::ROLE_CLIENT,
        self::ROLE_ADMIN_READONLY,
    ];

    /**
     * функция проверки доступа
     */
    public static function allow($action, $readOnly = false)
    {
        if(empty($action)){
            return true;
        }

        $user = Auth_Oracle::instance()->get_user();

        if(in_array($user['role'], [self::ROLE_ROOT])){
            return true;
        }

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

        if(!$readOnly && in_array($user['role'], self::$readonlyRoles)){
            return false;
        }

        return true;
    }

    /**
     * функция проверки доступа к скачиванию файлов
     * по умолчанию запрет
     */
    public static function file($file)
    {
        if(empty($file)){
            return false;
        }

        $user = User::current();

        if(in_array($user['role'], [self::ROLE_ROOT])){
            return true;
        }

        $access = Kohana::$config->load('access')['files'];

        if(
            // если задано разрешение и есть роль/агент/юзер, то можно
            isset($access[$file]) && (
                    in_array($user['role'], $access[$file]) ||
                    in_array('u_'.$user['MANAGER_ID'], $access[$file]) ||
                    in_array('a_'.$user['AGENT_ID'], $access[$file])
                )
        ){
            return true;
        }

        return false;
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
     * @param $additional
     */
    public static function check($type, $id, $additional = false)
    {
        if (empty($type) || empty($id)) {
            throw new HTTP_Exception_404();
        }

        $allow = false;

        $user = User::current();

        switch($type){
            case 'client':
                $clients = Model_Manager::getClientsList([
                    'ids' => [$id]
                ]);

                $clientsIds = !empty($clients) ? array_column($clients, 'CLIENT_ID') : [];

                if(in_array($id, $clientsIds)){
                    $allow = true;
                }
                break;

            case 'contract':
                $allow = Model_Contract::checkUserAccess($user['MANAGER_ID'], $id);
                break;

            case 'card':
                $allow = Model_Card::checkUserAccess($user['MANAGER_ID'], $id, $additional);
                break;

            case 'service':
                $allow = Model_Card::checkServiceAccess($id, $additional);
        }

        if(!$allow){
            throw new HTTP_Exception_404();
        }
    }

    /**
     * проверка доступа к процедуре
     *
     * @param $procedure
     * @param $role
     * @return bool
     */
    public static function checkReadOnly($procedure, $role)
    {
        if (empty($role) || empty($procedure)) {
            return false;
        }

        $access = Kohana::$config->load('access')['skip_readonly'];

        if (isset($access[$role]) && !in_array($procedure, $access[$role])) {
            return true;
        }

        return false;
    }

}