<?php defined('SYSPATH') or die('No direct script access.');

class Sender
{
    const STATUS_NEW        = 0;
    const STATUS_SENT       = 1;
    const STATUS_PENDING    = 2;
    const STATUS_CANCEL     = 3;

    const TYPE_PUSH         = 1;
    const TYPE_TELEGRAM     = 2;
    const TYPE_SMS          = 3;

    protected $_messageId       = false;
    protected $_managerId       = false;
    protected $_manager         = false;
    protected $_operatorId      = false;
    protected $_operatorTo      = false;
    protected $_operatorDate    = false;
    protected $_operatorStatus  = false;
    protected $_operatorPrice   = 0;
    protected $_error           = '';
    protected $_forceType       = false;

    /**
     * @param string $driver
     * @return self
     * @throws HTTP_Exception_500
     */
    public static function factory($driver)
    {
        $className = 'Sender_' . ucfirst($driver);

        if (!class_exists($className)) {
            throw new HTTP_Exception_500('Wrong driver: ' . $driver);
        }

        $class = new $className();

        return $class;
    }

    /**
     * рассылка уведомлений
     * последовательность: пуш, телеграм, sms
     */
    public function sendMessage($messageId, $managerId, $message, $forceType = false)
    {
        if (empty($messageId) || empty($managerId) || empty($message)) {
            return false;
        }

        $manager = Model_Manager::getManager($managerId);

        $types = !empty($forceType) ? [self::_getForceTypeName($forceType)] : ['push', 'telegram', 'sms'];

        if (empty($manager['SMS_IS_ON'])) {
            unset($types['sms']);
        }
        if (empty($manager['TELEGRAM_IS_ON'])) {
            unset($types['telegram']);
        }

        foreach ($types as $type) {
            $sender = self::factory($type);
            $sender->_messageId = $messageId;
            $sender->_managerId = $managerId;
            $sender->_manager   = $manager;
            $sender->_forceType = $forceType;

            if ($sender->send($message)) {
                return true;
            }
        }

        return false;
    }

    protected static function _getForceTypeName($forceType)
    {
        return $forceType == self::TYPE_PUSH ? 'push' :
            ($forceType == self::TYPE_TELEGRAM ? 'telegram' : 'sms')
        ;
    }

    /**
     * получаем очередь на отправку
     */
    public static function getQueue($params = [])
    {
        $sql = (new Builder())->select()
            ->from('V_QUEUE_MESSAGES')
            ->orderBy('DATE_CREATE desc')
        ;

        if (!empty($params['limit'])) {
            $sql->limit($params['limit']);
        }

        if (isset($params['status'])) {
            $sql->where('send_status = ' . $params['status']);
        }

        if (isset($params['type'])) {
            $sql->where('send_type = ' . $params['type']);
        }

        if (isset($params['operator_status'])) {
            $sql->where('operator_status = ' . $params['operator_status']);
        }

        if (isset($params['!operator_status'])) {
            $sql->where('coalesce(operator_status, 0) != ' . $params['!operator_status']);
        }

        if (isset($params['<attempts'])) {
            $sql->where('attempt < ' . (int)$params['<attempts']);
        }

        //заброкированные более 10 минут назад
        if (!empty($params['locked'])) {
            $sql->where('(sysdate - DATE_UPDATE_OUR) * 24 * 60 > ' . (int)$params['locked']);
        }

        if (isset($params['message_id'])) {
            $sql->where('message_id = ' . (int)$params['message_id']);
        }

        return Oracle::init()->query($sql);
    }

    /**
     * ставим статус в обработке
     */
    public static function lockQueue($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $data = [
            'p_new_status'  => self::STATUS_PENDING,
            'p_notes_array' => [$ids, SQLT_INT]
        ];

        Oracle::init()->procedure('queue_message_change_status', $data);

        $data = [
            'p_notes_array' => [$ids, SQLT_INT]
        ];

        Oracle::init()->procedure('queue_message_counter', $data);

        return true;
    }

    /**
     * снимаем статус в обработке
     */
    public static function unlockQueue($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $data = [
            'p_new_status'  => self::STATUS_NEW,
            'p_notes_array' => [$ids, SQLT_INT]
        ];

        Oracle::init()->procedure('queue_message_change_status', $data);

        return true;
    }

    /**
     * добавляем сообщение в очередь на отправку
     */
    public static function pushMessage($managerId, $message, $type = '')
    {
        if (empty($managerId) || empty($message)) {
            return false;
        }

        $data = [
            'p_manager_to'          => $managerId,
            'p_message_type'        => $type,
            'p_message_body'        => $message,
            'p_message_id'          => 'out',
            'p_error_code'          => 'out',
        ];

        $res = Oracle::init()->procedure('queue_message_add', $data);

        if ($res == Oracle::CODE_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * редактируем сообщение, например в sms при изменении статуса
     *
     * @param $messageId
     * @param $params
     */
    public static function editMessage($messageId, $params)
    {
        if (empty($messageId) || empty($params)) {
            return false;
        }

        $message = self::getMessage($messageId);

        $data = [
            'p_message_id'          => $messageId,
            'p_send_type'           => !empty($params['type']) ? $params['type'] : $message['SEND_TYPE'],
            'p_send_to'             => !empty($params['to']) ? $params['to'] : $message['SENT_TO'],
            'p_send_operator_id'    => !empty($params['operator_id']) ? $params['operator_id'] : $message['SEND_OPERATOR_ID'],
            'p_operator_status'     => !empty($params['operator_status']) ? $params['operator_status'] : $message['OPERATOR_STATUS'],
            'p_date_update_status'  => !empty($params['operator_status_date']) ? $params['operator_status_date'] : $message['DATE_UPDATE_STATUS'],
            'p_send_status'         => !empty($params['status']) ? $params['status'] : $message['SEND_STATUS'],
            'p_send_price'          => !empty($params['price']) ? $params['price'] : $message['SEND_PRICE'],
            'p_error_str'           => !empty($params['error']) ? $params['error'] : $message['ERROR_STR'],
            'p_error_code'          => 'out',
        ];

        $res = Oracle::init()->procedure('queue_message_params_edit', $data);

        if ($res == Oracle::CODE_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * получаем сообщение
     *
     * @param $messageId
     */
    public static function getMessage($messageId)
    {
        $messages = self::getQueue(['message_id' => $messageId]);

        if (!empty($messages)) {
            return reset($messages);
        }

        return false;
    }

    /**
     * установка id сообщения с которым идет работа
     *
     * @param $messageId
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;

        return $this;
    }
}