<?php defined('SYSPATH') or die('No direct script access.');

use Longman\TelegramBot\Telegram;

class Controller_Telegram extends Controller_Template
{
    private $_telegram;

    public function before()
    {
        $this->auto_render = false;

        $config = Kohana::$config->load('config');

        $this->_telegram = new Telegram($config['telegram_token'], $config['telegram_bot']);
    }

    public function action_index()
    {

    }

    /**
     * webhook
     */
    public function action_hook()
    {}
}
