<?php defined('SYSPATH') or die('No direct script access.');

use \Longman\TelegramBot\Request;

class Controller_Telegram extends Controller_Template
{
    private $_config;

    /**
     * @var \Longman\TelegramBot\Telegram
     */
    private $_telegram;

    public function before()
    {
        $this->auto_render = false;

        $this->_config = Kohana::$config->load('config');

        $this->_telegram = new \Longman\TelegramBot\Telegram($this->_config['telegram_token'], $this->_config['telegram_bot']);
    }

    /**
     * тут принимаем сообщения от бота
     */
    public function action_index()
    {
        $postData = json_decode(file_get_contents("php://input"), 1);

        $telegramParser = new TelegramParser($postData);
        //$telegramParser->debug();
        $telegramParser->execute();

        $response = $telegramParser->getResponse();

        Request::sendMessage([
            'parse_mode' => 'HTML',
            'chat_id' => $telegramParser->getChatId(),
            'text' => $response
        ]);
    }

    /**
     * устанавливаем webhook
     */
    public function action_set_webhook()
    {
        $result = $this->_telegram->setWebhook($this->_config['telegram_web_hook']);

        if ($result->isOk()) {
            echo $result->getDescription();
        }
        die;
    }

    /**
     * проверка состояния webhook
     */
    public function action_check()
    {
        $result = Request::getWebhookInfo();

        echo '<pre>';
        print_r($result);
        die;
    }
}
