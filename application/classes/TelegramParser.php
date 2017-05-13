<?php defined('SYSPATH') or die('No direct script access.');

class TelegramParser
{
    private $_cache;
    private $_config;
    private $_params;
    private $_command;
    private $_postData;
    private $_telegramUser;
    private $_chatId;

    private $_debug = false;
    private $_cacheKey = 'telegram_auth_';
    private $_cacheTime = 60*60*24; //на день
    private $_answer = [];

    private $_commandsWithoutAuth = [
        '/help',
        'login',
        'logout',
    ];

    /*
Array
(
[update_id] => 964539797
[message] => Array
    (
        [message_id] => 33
        [from] => Array
            (
                [id] => 115462629
                [first_name] => Pavel
                [last_name] => Nikitin
                [username] => paShamanZ
            )
        [chat] => Array
            (
                [id] => 115462629
                [first_name] => Pavel
                [last_name] => Nikitin
                [username] => paShamanZ
                [type] => private
            )
        [date] => 1494686163
        [text] => /help
        [entities] => Array
            (
                [0] => Array
                    (
                        [type] => bot_command
                        [offset] => 0
                        [length] => 5
                    )
            )
    )
)
     */

    public function __construct($postData)
    {
        //разбираем пришедшие запросы
        if (!empty($postData['message']['text'])) {

            $data = explode(' ', $postData['message']['text']);

            $this->_postData = $postData;
            $this->_command = array_shift($data);
            $this->_params = !empty($data) ? $data : [];

            $this->_telegramUser = !empty($postData['message']['from']['username']) ? $postData['message']['from']['username'] : '';
            $this->_cacheKey .= $this->_telegramUser;

            $this->_chatId = !empty($postData['message']['chat']['id']) ? $postData['message']['chat']['id'] : false;

            $this->_cache = Cache::instance();

            $this->_config = Kohana::$config->load('config');
        }
    }

    /**
     * установим режим тестирования
     */
    public function debug()
    {
        $this->_debug = true;

        $this->_answer[] = '<b>command:</b> ' . $this->_command;
        $this->_answer[] = '<b>params:</b> ' . (empty($this->_params) ? 'empty' : print_r($this->_params, 1));
        $this->_answer[] = '<b>debug:</b> ' . print_r($this->_postData, 1);
    }

    /**
     * выполняем команду
     */
    public function execute()
    {
        try {
            if (empty($this->_command) || empty($this->_telegramUser) || $this->_chatId != $this->_config['telegram_chat_id']) {
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
     * возвращаем ответ
     *
     * @return string
     */
    public function getResponse()
    {
        return implode(PHP_EOL, $this->_answer);
    }

    /**
     * проверяем авторизовался ли пользователь
     */
    private function _checkAuth()
    {
        $user = $this->_cache->get($this->_cacheKey);

        if (!$user) {
            return false;
        }

        $user = explode(md5($this->_config['cookie_salt']), $user);
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
    private function _commandLogin()
    {
        $login = !empty($this->_params[0]) ? $this->_params[0] : false;
        $password = !empty($this->_params[1]) ? $this->_params[1] : false;

        if (empty($login) || empty($password)) {
            throw new Exception('Некорректные логин и(или) пароль');
        }

        if (Auth::instance()->login($login, $password, FALSE)) {
            $value = $login.md5($this->_config['cookie_salt']).Auth::instance()->hash($password);

            $this->_cache->set($this->_cacheKey, $value, $this->_cacheTime);

            $this->_answer[] = 'Авторизация <b>на сутки</b> прошла успешно';
        } else {
            throw new Exception('Ошибка авторизации');
        }
    }

    /**
     * выход
     */
    private function _commandLogout()
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
    private function _buildHelpAnswer()
    {
        $this->_answer[] = '<i>Доступные команды:</i>';
        $this->_answer[] = '<b>/help</b> - вывод помощи';
        $this->_answer[] = '<b>login</b> <i>login</i> <i>password</i> - авторизация на сутки';
        $this->_answer[] = '<b>logout</b> - разлогинивание';
    }
}