<?php defined('SYSPATH') or die('No direct script access.');

class Sender_Telegram extends Sender
{
    /**
     * отправка сообщения
     *
     * @param $message
     * @return bool
     * @throws HTTP_Exception_500
     */
    public function send($message)
    {
        if (empty($this->_manager['TELEGRAM_IS_ON'])) {
            return false;
        }

        if (empty($message)) {
            return false;
        }

        //не привязан телеграм
        if (empty($this->_manager['TELEGRAM_CHAT_ID'])) {
            return false;
        }

        $this->_operatorTo = $this->_manager['TELEGRAM_CHAT_ID'];

        /** @var Telegram_GloProInfo $bot */
        $bot = Telegram_Common::factory('GloProInfo');

        $result = $bot->sendInfo($this->_manager['TELEGRAM_CHAT_ID'], $message);

        if (!$result->isOk()) {
            $this->_error = $result->printError();

            /*
             * если задан принудительный способ отправки и есть ошибка, то фигу
             */
            if ($this->_forceType == 'telegram') {
                self::editMessage($this->_messageId, [
                    'type'          => self::TYPE_TELEGRAM,
                    'to'            => $this->_operatorTo,
                    'status'        => self::STATUS_CANCEL,
                    'error'         => $this->_error,
                ]);
            }

            return false;
        }

        $result = $result->getResult();

        $this->_operatorId = $result->message_id;
        $this->_operatorDate = date(Date::$dateFormatRu . ' H:i:s', $result->date);

        self::editMessage($this->_messageId, [
            'type'                  => self::TYPE_TELEGRAM,
            'to'                    => $this->_operatorTo,
            'operator_id'           => $this->_operatorId,
            'operator_status_date'  => $this->_operatorDate,
            'status'                => self::STATUS_SENT,
        ]);

        return true;
    }
}