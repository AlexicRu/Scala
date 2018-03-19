<?php defined('SYSPATH') OR die('No direct script access.');

use \Longman\TelegramBot\Request as TelegramRequest;
use \Longman\TelegramBot\Entities\KeyboardButton;
use \Longman\TelegramBot\Telegram;

abstract class Telegram_Common
{
    protected $_telegramUser;
    protected $_chatId;
    /**
     * @var \Longman\TelegramBot\Telegram
     */
    protected $_telegram;
    protected $_bot;
    protected $_params;
    protected $_command;

    protected $_debug       = false;
    protected $_answer      = [];
    protected $_postData    = [];
    protected $_config      = [];
    protected $_configBot   = [];

    /**
     * @param string $bot
     * @return self
     * @throws HTTP_Exception_500
     */
    public static function factory($bot)
    {
        $className = 'Telegram_' . $bot;

        if (!class_exists($className)) {
            throw new HTTP_Exception_500('Wrong bot');
        }

        $class = new $className();

        $class->start();

        return $class;
    }

    /**
     * общая функция обработки
     */
    abstract public function parse();

    /**
     * включаем работу с api
     */
    public function start()
    {
        $this->_config = Kohana::$config->load('config');

        if (empty($this->_config['telegram'][$this->_bot])) {
            throw new HTTP_Exception_500('Wrong bot');
        }

        $this->_configBot       = $this->_config['telegram'][$this->_bot];
        $this->_telegram        = new Telegram($this->_configBot['token'], $this->_configBot['name']);
    }

    /**
     * функция инициализации
     *
     * @param $postData
     */
    public function init($postData)
    {
        $this->_postData        = $postData;
        $this->_telegramUser    = !empty($postData['message']['from']['username']) ? $postData['message']['from']['username'] : '';
        $this->_chatId          = !empty($postData['message']['chat']['id']) ? $postData['message']['chat']['id'] : false;

        if ($this->_configBot['debug']) {
            $this->_debug = true;
        }
    }

    /**
     * установим режим тестирования
     */
    public function debug()
    {
        $this->_debug = true;
    }

    /**
     * возвращаем ответ
     *
     * @return string
     */
    public function getAnswer()
    {
        if ($this->_debug) {
            $this->_answer[] = '<b>debug:</b> ' . print_r($this->_postData, 1);
        }

        return implode(PHP_EOL, $this->_answer);
    }

    /**
     * возвращаем идентификатор чата
     *
     * @return bool
     */
    public function getChatId()
    {
        return $this->_chatId;
    }

    /**
     * установка вебхука
     */
    public function setWebHook()
    {
        $result = $this->_telegram->setWebhook('https://dev.lk.glopro.ru' . $this->_configBot['web_hook']);

        if ($result->isOk()) {
            echo $result->getDescription();
        }
    }

    /**
     * получаем информацию по текущим вебхукам
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function checkWebHook()
    {
        return TelegramRequest::getWebhookInfo();
    }

    /**
     * отправляем ответ в телеграм
     */
    public function answer()
    {
        $answer = $this->getAnswer();

        if (empty($answer)) {
            $answer = 'Нажмите /help для помощи';
        }

        $data = [
            'parse_mode' => 'HTML',
            'chat_id' => $this->_chatId,
            'text' => $answer,
        ];

        if (!empty($this->_postData['message']['contact'])) {
            $data['reply_markup'] = ['remove_keyboard' => true];
        } else {
            $data['reply_markup'] = [
                'keyboard' => [
                    [
                        (new KeyboardButton('Отправить контакт'))->setRequestContact(true)
                    ]
                ],
                'resize_keyboard' => true
            ];
        }

        TelegramRequest::sendMessage($data);
    }

    /**
     * создает ответ для /help
     */
    protected function _buildHelpAnswer()
    {
        $this->_answer[] = '<i>Доступные команды:</i>';
        $this->_answer[] = '<b>/help</b> - вывод помощи';
    }
}