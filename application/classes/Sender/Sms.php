<?php defined('SYSPATH') or die('No direct script access.');

class Sender_Sms extends Sender
{
    const STATUS_RECEIVED = Sender_Provider_SMSC::STATUS_RECEIVED;

    /**
     * @var Sender_Provider_SMSC
     */
    private $_provider = null;
    private $_sendFromDev = true;

    public function __construct()
    {
        $config = Kohana::$config->load('config');

        $provider = 'Sender_Provider_SMSC';

        $this->_provider = new $provider($config['SMSC']);
    }

    /**
     * отправка
     *
     * @param $message
     * @return bool
     */
    public function send($message)
    {
        if (empty($message)) {
            return false;
        }

        //не привязан телеграм
        if (empty($this->_manager['PHONE_FOR_SMS'])) {
            return false;
        }

        $this->_operatorTo = $this->_manager['PHONE_FOR_SMS'];

        if (Common::isProd() || $this->_sendFromDev) {
            $phone = $this->_manager['PHONE_FOR_SMS'];
            $result = $this->_provider->sendSms($phone, $message);
        } else {
            $result = [0, 0];
        }

        if (isset($result[1]) && $result[1] < 0) {
            /*
                1	Ошибка в параметрах.
                2	Неверный логин или пароль.
                3	Недостаточно средств на счете Клиента.
                4	IP-адрес временно заблокирован из-за частых ошибок в запросах.
                5	Неверный формат даты.
                6	Сообщение запрещено (по тексту или по имени отправителя).
                7	Неверный формат номера телефона.
                8	Сообщение на указанный номер не может быть доставлено.
                9	Отправка более одного одинакового запроса на передачу SMS-сообщения либо более пяти одинаковых запросов на получение стоимости сообщения в течение минуты.
             */
            $this->_error = $result[1]*-1;

            //проверка на force не обязательно
            self::editMessage($this->_messageId, [
                'type'          => self::TYPE_SMS,
                'to'            => $this->_operatorTo,
                'status'        => in_array($this->_error, [3,4,9]) ? self::STATUS_PENDING : self::STATUS_CANCEL,
                'error'         => $this->_error
            ]);

            return false;
        }

        /*
         * id, cnt, cost, balance
         */
        $this->_operatorId = $result[0];
        $this->_operatorPrice = $result[2];

        self::editMessage($this->_messageId, [
            'type'                  => self::TYPE_SMS,
            'to'                    => $this->_operatorTo,
            'operator_id'           => $this->_operatorId,
            'price'                 => $this->_operatorPrice,
            'status'                => self::STATUS_SENT,
        ]);

        return true;
    }

    /**
     * Проверка статуса
     */
    public function checkStatus($messageId, $data)
    {
        $result = $this->_provider->getStatus($messageId, $data['SENT_TO']);

        if (isset($result[1]) && $result[1] < 0) {
            /*
                1	Ошибка в параметрах.
                2	Неверный логин или пароль.
                4	IP-адрес временно заблокирован.
                5	Ошибка удаления сообщения.
                9	Попытка отправки более пяти запросов на получение статуса одного и того же сообщения в течение минуты.
             */
            $this->_error = $result[1]*-1;

            return false;
        }

        /*
    -3	Сообщение не найдено
            Возникает, если для указанного номера телефона и ID сообщение не найдено.
    -1	Ожидает отправки
            Если при отправке сообщения было задано время получения абонентом, то до этого времени сообщение будет находиться в данном статусе, в других случаях сообщение в этом статусе находится непродолжительное время перед отправкой на SMS-центр.
    0	Передано оператору
            Сообщение было передано на SMS-центр оператора для доставки.
    1	Доставлено
            Сообщение было успешно доставлено абоненту.
    2	Прочитано
            Сообщение было прочитано (открыто) абонентом. Данный статус возможен для e-mail-сообщений, имеющих формат html-документа.
    3	Просрочено
            Возникает, если время "жизни" сообщения истекло, а оно так и не было доставлено получателю, например, если абонент не был доступен в течение определенного времени или в его телефоне был переполнен буфер сообщений.
    20	Невозможно доставить
            Попытка доставить сообщение закончилась неудачно, это может быть вызвано разными причинами, например, абонент заблокирован, не существует, находится в роуминге без поддержки обмена SMS, или на его телефоне не поддерживается прием SMS-сообщений.
    22	Неверный номер
            Неправильный формат номера телефона.
    23	Запрещено
            Возникает при срабатывании ограничений на отправку дублей, на частые сообщения на один номер (флуд), на номера из черного списка, на запрещенные спам фильтром тексты или имена отправителей (Sender ID).
    24	Недостаточно средств
            На счете Клиента недостаточная сумма для отправки сообщения.
    25	Недоступный номер
            Телефонный номер не принимает SMS-сообщения, или на этого оператора нет рабочего маршрута.
*/
        /*
         * status, time, err
         */
        $this->_operatorStatus = $result[0];
        $this->_operatorDate = $result[1];

        self::editMessage($this->_messageId, [
            'operator_status'      => $this->_operatorStatus,
            'operator_status_date' => date(Date::$dateFormatRu . ' H:i:s', $this->_operatorDate)
        ]);

        return true;
    }
}