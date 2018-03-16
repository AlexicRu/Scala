<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Telegram extends Controller_Template
{
    /**
     * @var Telegram_Common
     */
    private $_bot;

    public function before()
    {
        $this->auto_render = false;
        $bot = $this->request->param('bot');

        $this->_bot = Telegram_Common::factory($bot);
    }

    /**
     * тут принимаем сообщения от бота
     */
    public function action_index()
    {
        $postData = json_decode(file_get_contents("php://input"), true);

        $this->_bot->init($postData);

        $this->_bot->parse();

        $this->_bot->answer();
    }

    /**
     * устанавливаем webhook
     */
    public function action_setWebhook()
    {
        $this->_bot->setWebHook();
    }

    /**
     * проверка состояния webhook
     */
    public function action_checkWebhook()
    {
        $result = $this->_bot->checkWebHook();

        echo '<pre>';
        print_r($result);
    }
}
