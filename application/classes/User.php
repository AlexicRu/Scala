<?php defined('SYSPATH') or die('No direct script access.');

class User
{
    /**
     * получение текущего пользователя
     *
     * @return mixed
     */
    public static function current()
    {
        return Auth::instance()->get_user();
    }

    /**
     * залогинен ли?
     *
     * @return mixed
     */
    public static function loggedIn()
    {
        return Auth::instance()->logged_in();
    }

    /**
     * проверяем доступность логина из под одного пользователя в другого
     *
     * @param $userFrom
     * @param $userTo
     * @return bool
     */
    public static function checkForceLogin($userFrom, $userTo)
    {
        //todo
        return true;
    }

    /**
     * Получаем имя для вывода
     *
     * @param $user
     * @return string
     */
    public static function getName($user)
    {
        if (is_numeric($user)) {
            $user = Model_Manager::getManager(['MANAGER_ID' => (int)$user]);
        }

        if(!empty($user['M_NAME'])){
            $name = $user['M_NAME'];
        }elseif(!empty($user['MANAGER_NAME']) && !empty($user['MANAGER_SURNAME']) && !empty($user['MANAGER_MIDDLENAME'])){
            $name = $user['MANAGER_NAME'].' '.$user['MANAGER_SURNAME'].' '.$user['MANAGER_MIDDLENAME'];
        }elseif(!empty($user['FIRM_NAME'])){
            $name = $user['FIRM_NAME'];
        }else{
            $name = $user['LOGIN'];
        }

        return $name;
    }

    /**
     * связываем юзера и чат в телеграме
     *
     * @param $phone
     * @param $chatId
     * @return bool
     */
    public static function connectToTelegram($phone, $chatId)
    {
        if (empty($phone) || empty($chatId)) {
            return false;
        }

        $res = Oracle::init()->procedure('ctrl_manager_telegram_access', [
            'p_phone_number'       => $phone,
            'p_telegram_chat_id'   => $chatId,
            'p_error_code'         => 'out',
        ]);

        if ($res == Oracle::CODE_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * Получаем список доступных для прямой авторизации манеджеров
     */
    public static function getManagersBinds($managerId)
    {
        $sql = (new Builder())->select()
            ->from('v_web_manager_binds')
            ->where('MANAGER_FROM = ' . $managerId)
            ->orderBy('WEB_NAME_TO')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * получаем список посещенных пользователем туров
     */
    public static function getWebTours()
    {
        $user = User::current();

        $sql = (new Builder())->select(['scenario_id'])
            ->from('v_web_webtour')
            ->where('MANAGER_ID = ' . $user['MANAGER_ID'])
        ;

        return Oracle::init()->column($sql, 'scenario_id');
    }

    /**
     * пользователь просмотрел сценарий вебтура
     */
    public static function seeWebTour($scenario)
    {
        $user = User::current();

        Oracle::init()->procedure('web_manager_site_tour', [
            'p_manager_id'    => $user['MANAGER_ID'],
            'p_scenario_id'   => $scenario,
        ]);

        $user['tours'][] = $scenario;

        Auth::instance()->saveSession($user);

        return true;
    }

    /**
     * id текущего пользователя
     *
     * @return mixed
     */
    public static function id()
    {
        return self::current()['MANAGER_ID'];
    }
}