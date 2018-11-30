<?php defined('SYSPATH') or die('No direct script access.');

class Model_Manager extends Model
{
    const STATE_MANAGER_ACTIVE      = 1;
    const STATE_MANAGER_BLOCKED     = 4;

    /**
     * радактируем данные юзверя
     *
     * @param $params
     * @param $manager
     * @param bool|false $userId
     */
    public static function edit($managerId, $params)
    {
        if(empty($managerId)){
            return false;
        }

        $user = User::current();

        $db = Oracle::init();

        $manager = Model_Manager::getManager($managerId);

        if (!($manager['SENDER_SMS'] || Access::allow('root'))) {
            $params['manager_sms_is_on'] = 0;
        }

        $data = [
            'p_manager_for_id' 	    => $managerId,
            'p_role_id' 	        => !isset($params['manager_settings_role'])              ? $manager['ROLE_ID'] : $params['manager_settings_role'],
            'p_name' 	            => !isset($params['manager_settings_name'])              ? $manager['MANAGER_NAME'] : $params['manager_settings_name'],
            'p_surname' 	        => !isset($params['manager_settings_surname'])           ? $manager['MANAGER_SURNAME'] : $params['manager_settings_surname'],
            'p_middlename' 	        => !isset($params['manager_settings_middlename'])        ? $manager['MANAGER_MIDDLENAME'] : $params['manager_settings_middlename'],
            'p_phone' 		        => !isset($params['manager_settings_phone'])             ? $manager['CELLPHONE'] : $params['manager_settings_phone'],
            'p_email' 		        => !isset($params['manager_settings_email'])             ? $manager['EMAIL'] :
                (!empty($params['manager_settings_email']) ? Text::checkEmailMulti($params['manager_settings_email']) : ''),
            'p_limit_restriction' 	=> !isset($params['manager_settings_limit'])             ? $manager['LIMIT_RESTRICTION'] : $params['manager_settings_limit'],
            'p_sms_is_on' 	        => !isset($params['manager_sms_is_on'])                  ? $manager['SMS_IS_ON'] : $params['manager_sms_is_on'],
            'p_telegram_is_on' 	    => !isset($params['manager_telegram_is_on'])             ? $manager['TELEGRAM_IS_ON'] : $params['manager_telegram_is_on'],
            'p_manager_who_id' 	    => $user['MANAGER_ID'],
            'p_error_code' 	        => 'out',
        ];

        $res = $db->procedure('ctrl_manager_edit', $data);

        if($res == Oracle::CODE_ERROR){
            return false;
        }

        if(
            !empty($params['manager_settings_password']) && !empty($params['manager_settings_password_again']) &&
            $params['manager_settings_password'] == $params['manager_settings_password_again'] &&
            $managerId != Access::USER_TEST
        ){
            //обновление паролей

            $data = [
                'p_manager_id' 	    => $managerId,
                'p_new_password'    => empty($params['manager_settings_password'])      ? '' : $params['manager_settings_password'],
                'p_manager_who_id'  => $user['MANAGER_ID'],
                'p_error_code' 	    => 'out',
            ];

            $res = $db->procedure('ctrl_manager_change_password', $data);

            if(!empty($res)){
                return false;
            }
        }

        if ($managerId == User::id()) {
            Auth::instance()->regenerate_user_profile();
        }

        return true;
    }

    /**
     * список менеджеров
     *
    1) Рут (role_id = 1) видит всех менеджеров агента
    2) Администратор (role_id = 2) видит всех менеджеров агента, кроме администраторов (2), рута (1) и супервайзера агентов (7)
    (сейчас не видит главного менеджера role_id = 3)
    3) Супервайзер агентов (7) видит всех менеджеров, кроме администраторов (2), рута (1)
    4) Супервайзер менеджеров (3) видит всех менеджеров агента, кроме администраторов (2), рута (1) и супервайзера агентов (7)
    5) Менеджер по сопровождению (4) и менеджер по продажам и сопровождению (6) видят только клиентские учетные записи (97, 98, 99), менеджерские учетные записи не видят.
     *
     * @param $params
     * @return array|bool|int
     */
    public static function getManagersList($params = [])
    {
        $db = Oracle::init();

        $sql = (new Builder())->select()
            ->from('V_WEB_MANAGERS')
            ->orderBy('M_NAME')
        ;

        if(!empty($params['search'])){
            $params['search'] = Oracle::quoteLike('%'.mb_strtoupper($params['search']).'%');

            $sql
                ->whereStart()
                ->whereOr("upper(LOGIN) like " . $params['search'])
                ->whereOr("upper(MANAGER_NAME) like " . $params['search'])
                ->whereOr("upper(MANAGER_SURNAME) like " . $params['search'])
                ->whereOr("upper(MANAGER_MIDDLENAME) like " . $params['search'])
                ->whereOr("upper(M_NAME) like " . $params['search'])
                ->whereEnd()
            ;
        }
        unset($params['search']);

        $params['role_id'] = empty($params['role_id']) ? [] : (array)$params['role_id'];
        $params['roles_exclude'] = empty($params['roles_exclude']) ? [] : (array)$params['roles_exclude'];

        /*
         * система доступов
         */
        if (empty($params['skip_role_check'])) {
            $user = User::current();
            switch ($user['ROLE_ID']) {
                case Access::ROLE_ROOT:
                    break;
                case Access::ROLE_ADMIN:
                    $params['roles_exclude'] = array_merge($params['roles_exclude'], [Access::ROLE_ADMIN, Access::ROLE_ROOT, Access::ROLE_ADMIN_READONLY]);
                    break;
                case Access::ROLE_ADMIN_READONLY:
                    $params['roles_exclude'] = array_merge($params['roles_exclude'], [Access::ROLE_ADMIN, Access::ROLE_ROOT]);
                    break;
                case Access::ROLE_SUPERVISOR:
                    $params['roles_exclude'] = array_merge($params['roles_exclude'], [Access::ROLE_ADMIN, Access::ROLE_ROOT, Access::ROLE_ADMIN_READONLY]);
                    break;
                case Access::ROLE_MANAGER:
                case Access::ROLE_MANAGER_SALE_SUPPORT:
                    $params['role_id'] = array_intersect($params['role_id'], array_keys(Access::$clientRoles));
                    break;
            }
        }
        unset($params['skip_role_check']);

        if(!empty($params['only_managers'])){
            $sql->whereNotIn("ROLE_ID", array_keys(Access::$clientRoles));
        }
        unset($params['only_managers']);

        if(!empty($params['roles_exclude'])){
            $sql->whereNotIn("ROLE_ID", $params['roles_exclude']);
        }
        unset($params['roles_exclude']);

        foreach($params as $key => $value){
            if (empty($value)) {
                continue;
            }

            if(is_array($value)){
                $sql->whereIn(strtoupper($key), $value);
            }else {
                $sql->where(strtoupper($key) . ' = ' . Oracle::quote($value));
            }
        }

        if(!empty($params['limit'])) {
            $sql->limit($params['limit']);
        }

        return $db->query($sql);
    }

    /**
     * получаем менеджера
     */
    public static function getManager($params)
    {
        if(empty($params)){
            return false;
        }

        if(!is_array($params)){
            $params = ['manager_id' => (int)$params];
        }

        $params['skip_role_check'] = true;

        $managers = self::getManagersList($params);

        if(empty($managers)){
            return false;
        }

        return reset($managers);
    }

    /**
     * блокировка/разблокировка
     *
     * @param $cardId
     */
    public static function toggleStatus($params)
    {
        if(empty($params['manager_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        //получаем карты и смотрим текущий статус у нее
        $manager = self::getManager($params['manager_id']);

        if(empty($manager['MANAGER_ID'])){
            return false;
        }

        switch($manager['STATE_ID']){
            case self::STATE_MANAGER_ACTIVE:
                $status = self::STATE_MANAGER_BLOCKED;
                break;
            default:
                $status = self::STATE_MANAGER_ACTIVE;
        }

        $data = [
            'p_manager_for_id' 	=> $manager['MANAGER_ID'],
            'p_new_status' 		=> $status,
            'p_manager_who_id' 	=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('ctrl_manager_change_status', $data);

        if($res == Oracle::CODE_SUCCESS){
            return true;
        }

        return false;
    }

    /**
     * у выбранного менеджера удаляем клиента
     *
     * @param $managerId
     * @param $clientId
     */
    public static function delClient($managerId, $clientId)
    {
        if(empty($managerId) || empty($clientId)){
            return Oracle::CODE_ERROR;
        }

        $user = Auth::instance()->get_user();

        $db = Oracle::init();

        $data = [
            'p_manager_for_id' 	=> $managerId,
            'p_client_id' 		=> $clientId,
            'p_manager_who_id' 	=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $res = $db->procedure('ctrl_manager_client_del', $data);

        if($res == Oracle::CODE_ERROR){
            return Oracle::CODE_ERROR;
        }

        return Oracle::CODE_SUCCESS;
    }

    /**
     * добавляем менеджера
     *
     * @param $params
     */
    public static function addManager($params)
    {
        if(empty($params['role']) || empty($params['login']) || empty($params['password'])){
            return false;
        }
        if($params['password'] != $params['password_again']){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_manager_role_id' 	=> $params['role'],
            'p_manager_name' 	    => empty($params['name']) ? '' : $params['name'],
            'p_manager_surname' 	=> empty($params['surname']) ? '' : $params['surname'],
            'p_manager_midname' 	=> empty($params['middlename']) ? '' : $params['middlename'],
            'p_login' 	            => $params['login'],
            'p_password' 	        => $params['password'],
            'p_phone' 	            => empty($params['phone']) ? '' : $params['phone'],
            'p_email' 	            => empty($params['email']) ? '' : Text::checkEmailMulti($params['email']),
            'p_manager_id' 		    => $user['MANAGER_ID'],
            'p_new_manager_id' 		=> 'out',
            'p_error_code' 		    => 'out',
        ];

        $res = $db->procedure('ctrl_manager_add', $data, true);

        if($res['p_error_code'] == Oracle::CODE_ERROR){
            return false;
        }
        return $res['p_new_manager_id'];
    }

    /**
     * добавляем клиентов
     *
     * @param $params
     */
    public static function addClients($params)
    {
        if(empty($params['ids']) || empty($params['manager_id'])){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        foreach($params['ids'] as $id){
            $data = [
                'p_manager_for_id' 	=> $params['manager_id'],
                'p_client_id' 	    => $id,
                'p_manager_who_id' 	=> $user['MANAGER_ID'],
                'p_error_code' 		=> 'out',
            ];

            $res = $db->procedure('ctrl_manager_client_add', $data);

            if($res != Oracle::CODE_SUCCESS){
                return $res;
            }
        }

        return Oracle::CODE_SUCCESS;
    }

    /**
     * добавляем отчеты
     *
     * @param $params
     * @param $action 1-add 2-remove
     */
    public static function editReports($params, $action = 1)
    {
        if(empty($params['ids']) || empty($params['manager_id'])){
            return Oracle::CODE_ERROR;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        foreach($params['ids'] as $id){
            $data = [
                'p_manager_for_id' 	=> $params['manager_id'],
                'p_report_id' 	    => $id,
                'p_action' 	        => $action,
                'p_manager_who_id' 	=> $user['MANAGER_ID'],
                'p_error_code' 		=> 'out',
            ];

            $res = $db->procedure('ctrl_manager_report', $data);

            if($res != Oracle::CODE_SUCCESS){
                return $res;
            }
        }

        return Oracle::CODE_SUCCESS;
    }

    /**
     * у выбранного менеджера удаляем отчет
     *
     * @param $managerId
     * @param $clientId
     */
    public static function delReport($managerId, $clientId)
    {
        return self::editReports([
            'manager_id' => $managerId,
            'ids' => [$clientId],
        ], 2);
    }

    /**
     * получаем список доступный клиентов по манагеру
     *
     * @param array $params
     * @param array $columns
     * @return array
     */
    public static function getClientsList($params = [], $columns = [])
    {
        $user = User::current();

        if (empty($params['manager_id'])) {
            $managerId = $user['MANAGER_ID'];
        } else {
            $managerId = $params['manager_id'];
        }

        $sql = (new Builder())->select()->distinct()
            ->from('V_WEB_CLIENTS_LIST t')
            ->where('t.agent_id = ' . (int)$user['AGENT_ID'])
            ->orderBy('t.client_id desc')
        ;

        if (!empty($params['ids'])) {
            $sql->where('t.client_id in ('. implode(',', (array)$params['ids']) .')');
        }

        if (!empty($params['only_available_to_add'])) {
            $subSql = (new Builder())->select('1')
                ->from('V_WEB_MANAGER_CLIENTS vwc')
                ->where('vwc.client_id = t.client_id')
                ->where('vwc.agent_id = t.agent_id')
                ->where('vwc.manager_id = ' . (int)$managerId)
            ;
            $sql
                ->where('not exists (' . $subSql . ')')
                //небольшой костыль чтобы сработал дистинкт
                ->columns([
                    'CLIENT_ID', 'CLIENT_NAME', 'LONG_NAME', 'CLIENT_STATE'
                ]);
        } else {
            $sql
                ->where('t.manager_id = ' . (int)$managerId)
            ;
        }

        if(!empty($params['search'])){
            $sql->where("upper(t.CLIENT_NAME) like " . mb_strtoupper(Oracle::quoteLike('%'.$params['search'].'%')));
        }

        if (!empty($columns)) {
            $sql->columns($columns);
        }

        if (!empty($params['limit'])) {
            $sql->limit((int)$params['limit']);
        }

        return Oracle::init()->query($sql);
    }

    /**
     * получаем список доступный отчетов по манагеру
     *
     * @param array $params
     * @return array
     */
    public static function getReportsList($params = [])
    {
        $db = Oracle::init();

        $user = User::current();

        if (empty($params['manager_id'])) {
            $manager = Model_Manager::getManager($user['MANAGER_ID']);
        } else {
            $manager = Model_Manager::getManager($params['manager_id']);
        }

        $sql = (new Builder())->select()
            ->from('V_WEB_REPORTS_LIST r')
            ->orderBy('r.REPORT_TYPE_ID')
        ;

        if (in_array($manager['ROLE_ID'], array_keys(Access::$clientRoles))) {

            $subSql = (new Builder())->select(1)
                ->from('V_WEB_REPORTS_AVAILABLE t')
                ->whereIn('t.agent_id', [0, $user['AGENT_ID']])
                ->whereIn('t.manager_id', [0, $manager['MANAGER_ID']])
                ->where('t.report_id = r.report_id')
            ;

            $sql
                ->where('r.REPORT_TYPE_ID = ' . Model_Report::REPORT_GROUP_CLIENT)
                ->where('exists ('. $subSql->build() .')')
            ;
        } else {
            $subSql = (new Builder())->select(1)
                ->from('V_WEB_REPORTS_AVAILABLE t')
                ->whereIn('t.agent_id', [0, $user['AGENT_ID']])
                ->where('t.report_id = r.report_id')
            ;

            $sql
                ->where('exists ('. $subSql->build() .')')
            ;
        }

        if(!empty($params['search'])){
            $sql->where("upper(r.WEB_NAME) like " . mb_strtoupper(Oracle::quoteLike('%'.$params['search'].'%')));
        }

        return $db->query($sql);
    }

    /**
     * редактируем логин пальзователя
     *
     * @param $managerId
     * @param $login
     */
    public static function editLogin($managerId, $login)
    {
        if (empty($managerId) || empty($login)) {
            return ['error' => 'Некорректные данные'];
        }

        $login = str_replace(' ', '', $login);

        if (empty($login)) {
            return ['error' => 'Пустой логин'];
        }

        $db = Oracle::init();
        $user = User::current();

        $data = [
            'p_manager_id' 	    => $managerId,
            'p_new_login' 	    => $login,
            'p_manager_who_id' 	=> $user['MANAGER_ID'],
            'p_error_code' 		=> 'out',
        ];

        $code = $db->procedure('ctrl_manager_change_login', $data);

        $result = ['login' => $login];

        switch ($code) {
            case Oracle::CODE_ERROR:
                $result = ['error' => 'Логин не обновлен'];
                break;
            case Oracle::CODE_ERROR_EXISTS:
                $result = ['error' => 'Логин уже занят'];
                break;
        }

        return $result;
    }

    /**
     * редактируем доступы менеджера к контрактам конкретного клиента
     *
     * @param $managerId
     * @param $clientId
     * @param $binds
     */
    public static function editContractBinds($managerId, $clientId, $binds = [])
    {
        if (empty($clientId) || empty($managerId)) {
            return false;
        }

        $user = User::current();

        $data = [
            'p_manager_for_id' 	    => $managerId,
            'p_client_id' 	        => $clientId,
            'p_contract_collection'	=> [($binds ?: [-1]), SQLT_INT],
            'p_manager_who_id' 	    => $user['MANAGER_ID'],
            'p_error_code' 		    => 'out',
        ];

        $res = Oracle::init()->procedure('ctrl_manager_client_contracts', $data);

        return $res == Oracle::CODE_SUCCESS;
    }

    /**
     * дерево доступных контрактов
     *
     * @param $managerId
     */
    public static function getContractsTree($managerId)
    {
        if (empty($managerId)) {
            return [];
        }

        $sql = (new Builder())->select()->distinct()
            ->from('v_web_manager_contracts')
            ->where('MANAGER_ID = '.(int)$managerId)
            ->where('CONTRACT_ID is not null')
        ;

        return Oracle::init()->tree($sql, 'CLIENT_ID', false, 'CONTRACT_ID');
    }

    /**
     * редактирование информирования менеджера
     *
     * @param $managerId
     * @param string $phone
     * @return bool
     */
    public static function enableInform($managerId, $phone = '')
    {
        $data = [
            'p_manager_id' 	    => $managerId,
            'p_manager_phone' 	=> $phone,
            'p_error_code' 		=> 'out',
        ];

        $res = Oracle::init()->procedure('ctrl_manager_switch_inform', $data);

        if ($res == Oracle::CODE_SUCCESS) {
            if ($managerId == User::id()) {
                Auth::instance()->regenerate_user_profile();
            }
            return true;
        }

        return false;
    }

    /**
     * выключение информирования
     *
     * @param $managerId
     * @return bool
     */
    public static function disableInform($managerId)
    {
        return self::enableInform($managerId);
    }
}