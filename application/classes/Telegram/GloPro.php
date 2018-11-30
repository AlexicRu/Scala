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
        '/login',
        '/logout',
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
                case '/login':
                    $this->_commandLogin();
                    break;
                case '/logout':
                    $this->_commandLogout();
                    break;
                case '/clients':
                    $this->_commandClients();
                    break;
                case '/contracts':
                    $this->_commandContracts();
                    break;
                case '/cards':
                    $this->_commandCards();
                    break;
                case '/balance':
                    $this->_commandBalance();
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

        $this->_answer[] = '/login <i>LOGIN</i> <i>PASSWORD</i> - авторизация на сутки';
        $this->_answer[] = '/logout - разлогинивание';
        $this->_answer[] = '/clients <i>CLIENT_NAME_PART</i> - получение списка клиентов';
        $this->_answer[] = '/contracts <i>CLIENT_ID</i> - получение списка контактов клиента';
        $this->_answer[] = '/cards <i>CONTRACT_ID</i> <i>CARD_ID_PART</i> - получение списка карт договора';
        $this->_answer[] = '/balance <i>CONTRACT_ID</i> - получение баланса контракта';
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

    /**
     * Получаем список клиентов
     */
    protected function _commandClients()
    {
        $clientNamePart = !empty($this->_params[0]) ? $this->_params[0] : null;

        $clients = Model_Manager::getClientsList(['search' => $clientNamePart]);

        if (empty($clients)) {
            $this->_answer[] = '<b>Клиенты не найдены</b>';
        }

        foreach ($clients as $client) {
            $this->_answer[] = '/contracts'. $client['CLIENT_ID'] . ' ' . (!empty($client['LONG_NAME']) ? $client['LONG_NAME'] : $client['CLIENT_NAME']);
        }
    }

    /**
     * получаем список контрактов по конкретномпу клиенту
     */
    protected function _commandContracts()
    {
        $clientId = !empty($this->_params[0]) ? $this->_params[0] : $this->_id;

        if (empty($clientId)) {
            $this->_answer[] = '<i>Не передан ID клиента</i>';
            return;
        }

        $contracts = Model_Contract::getContracts($clientId);

        if (empty($contracts)) {
            $this->_answer[] = '<b>Контракты не найдены</b>';
        }

        foreach ($contracts as $contract) {
            $this->_answer[] =
                '/cards'. $contract['CONTRACT_ID'] . ' ' .
                '/balance'. $contract['CONTRACT_ID'] . ' ' .
                $contract['CONTRACT_NAME'];
        }
    }

    /**
     * получаем список карт по конкретномпу договору
     */
    protected function _commandCards()
    {
        $contractId = !empty($this->_params[0]) ? $this->_params[0] : $this->_id;
        $cardIdPart = !empty($this->_params[0]) ? $this->_params[0] : false;

        if (empty($contractId)) {
            $this->_answer[] = '<i>Не передан ID контракта</i>';
            return;
        }

        $cards = Model_Card::getCards($contractId, false, ['query' => $cardIdPart]);

        if (empty($cards)) {
            $this->_answer[] = '<b>Карты не найдены</b>';
        }

        foreach ($cards as $card) {
            $this->_answer[] = $card['CARD_ID'];
        }
    }

    /**
     * получаем список карт по конкретномпу договору
     */
    protected function _commandBalance()
    {
        $contractId = !empty($this->_params[0]) ? $this->_params[0] : $this->_id;

        if (empty($contractId)) {
            $this->_answer[] = '<i>Не передан ID контракта</i>';
            return;
        }

        $balance = Model_Contract::getContractBalance($contractId);

        if (empty($balance)) {
            $this->_answer[] = '<b>Данные не найдены</b>';
        }

        $this->_answer[] = 'Баланс контракта: <b>'. number_format($balance['BALANCE'], 2, '.', ' ') .' '. Text::getCurrency($balance['CURRENCY']) .'</b>';
    }
}