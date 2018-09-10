<?php defined('SYSPATH') or die('No direct script access.');

class Telegram_GloProInfo extends Telegram_Common
{
    protected $_bot = 'GloProInfo';
    protected $_requestContact = true;

    /**
     * выполняем команду
     */
    public function execute()
    {
        try {
            if (empty($this->_command) || empty($this->_telegramUser)) {
                throw new Exception('<i>Некорректный запрос</i>');
            }

            switch ($this->_command) {
                case '/start':
                case '/help':
                    $this->_buildHelpAnswer();
                    break;
                default:
                    throw new Exception('Команда <b>'.$this->_command.'</b> не найдена');
            }

        } catch (Exception $e) {
            $this->_answer[] = $e->getMessage();
        }
    }

    /**
     * отправляем сообщение в привязанный чат
     *
     * @param $chatId
     * @param $message
     */
    public function sendInfo($chatId, $message)
    {
        if (empty($chatId)) {
            return false;
        }

        $this->start();

        $this->_chatId   = $chatId;

        $this->_answer[] = $message;

        return $this->answer();
    }

    /**
     * создает ответ для /help
     */
    protected function _buildHelpAnswer()
    {
        parent::_buildHelpAnswer();

        $this->_answer[] = '';
        $this->_answer[] = 'Для работы необходимо отправить контакт. По номеру телефона мы найдем менеджера и будем отправлять ему полезную информацию.';
    }
}