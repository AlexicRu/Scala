<?php defined('SYSPATH') or die('No direct script access.');

class Telegram_GloPro extends Telegram_Common
{
    protected $_bot = 'GloPro';

    protected $_cache;

    protected $_cacheKey = 'telegram_glopro_auth_';
    protected $_cacheTime = 60*60*24; //на день

    protected $_commandsWithoutAuth = [
        '/start',
        '/help',
        'login',
        'logout',
    ];

    /**
     * функция инициализации
     *
     * @param $postData
     * @throws Cache_Exception
     */
    public function init($postData)
    {
        parent::init($postData);

        $this->_cacheKey .= $this->_telegramUser;

        $this->_cache = Cache::instance();
    }

    /**
     * общая функция обработки
     */
    public function parse()
    {
        //разбираем пришедшие запросы
        if (!empty($postData['message']['text'])) {

            $data = explode(' ', strtolower($postData['message']['text']));

            $this->_command = array_shift($data);
            $this->_params = !empty($data) ? $data : [];
        } else if(!empty($postData['message']['contact'])) {

        }

        $this->execute();
    }

    /**
     * выполняем команду
     */
    public function execute()
    {
        try {
            if (empty($this->_command) || empty($this->_telegramUser)) {
                throw new Exception('<i>Некорректный запрос</i>');
            }

            if (!in_array($this->_command, $this->_commandsWithoutAuth)) {
                if (!$this->_checkAuth()) {
                    throw new Exception('Необходима авторизация. см. /help');
                }

                if (Access::deny('telegram_'.$this->_command)) {
                    throw new Exception('У вас нет доступа на выполнение данной команды');
                }
            }

            switch ($this->_command) {
                case '/start':
                case '/help':
                    $this->_buildHelpAnswer();
                    break;
                case 'login':
                    $this->_commandLogin();
                    break;
                case 'logout':
                    $this->_commandLogout();
                    break;
                default:
                    throw new Exception('Команда <b>'.$this->_command.'</b> не найдена');
            }

        } catch (Exception $e) {
            $this->_answer[] = $e->getMessage();
        }
    }

    /**
     * проверяем авторизовался ли пользователь
     */
    protected function _checkAuth()
    {
        $user = $this->_cache->get($this->_cacheKey);

        if (!$user) {
            return false;
        }

        $user = explode(md5($this->_config['salt']), $user);
        $login = $user[0];
        $passwordHash = !empty($user[1]) ? $user[1] : false;

        if (empty($login) || empty($passwordHash)){
            return false;
        }

        if (!Auth::instance()->login($login, ['hash' => $passwordHash], FALSE)) {
            return false;
        }

        return true;
    }

    /**
     * авторизация
     */
    protected function _commandLogin()
    {
        $login = !empty($this->_params[0]) ? $this->_params[0] : false;
        $password = !empty($this->_params[1]) ? $this->_params[1] : false;

        if (empty($login) || empty($password)) {
            throw new Exception('Некорректные логин и(или) пароль');
        }

        if (Auth::instance()->login($login, $password, FALSE)) {
            $value = $login . md5($this->_config['salt']) . Auth::instance()->hash($password);

            $this->_cache->set($this->_cacheKey, $value, $this->_cacheTime);

            $this->_answer[] = 'Авторизация <b>на сутки</b> прошла успешно';
        } else {
            throw new Exception('Ошибка авторизации');
        }
    }

    /**
     * выход
     */
    protected function _commandLogout()
    {
        if ($this->_cache->delete($this->_cacheKey)) {
            $this->_answer[] = 'Разлогинивание прошло успешно';
        } else {
            throw new Exception('Ошибка разлогинивания');
        }
    }

    /**
     * создает ответ для /help
     */
    protected function _buildHelpAnswer()
    {
        parent::_buildHelpAnswer();

        $this->_answer[] = '<b>login</b> <i>login</i> <i>password</i> - авторизация на сутки';
        $this->_answer[] = '<b>logout</b> - разлогинивание';
    }

    /**
     * возвращаем ответ
     *
     * @return string
     */
    public function getAnswer()
    {
        if ($this->_debug) {
            $this->_answer[] = '<b>command:</b> ' . $this->_command;
            $this->_answer[] = '<b>params:</b> ' . (empty($this->_params) ? 'empty' : print_r($this->_params, 1));
        }

        return parent::getAnswer();
    }
}