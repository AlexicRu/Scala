<?php defined('SYSPATH') or die('No direct script access.');

class Telegram_GloProInfo extends Telegram_Common
{
    protected $_bot = 'GloProInfo';

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
        } else if (!empty($this->_postData['message']['contact'])) {
            $phone = $this->_postData['message']['contact']['phone_number'];

            //связываем аккаунт
            $res = Model_Manager::connectToTelegram($phone, $this->_chatId);

            if (!empty($res)) {
                $this->_answer[] = 'Доступ получен';
            } else {
                $this->_answer[] = 'Ошибка получения доступа';
            }
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
     * @param $message
     */
    public function sendInfo($message)
    {
        $user = User::current();

        if (empty($user['TELEGRAM_CHAT_ID'])) {
            return false;
        }

        $this->start();

        $this->_chatId   = $user['TELEGRAM_CHAT_ID'];

        $this->_answer[] = $message;

        $this->answer();

        return true;
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