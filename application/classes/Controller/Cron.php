<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cron extends Controller_Template
{
    public function before()
    {
        $this->auto_render = false;
    }

    /**
     * функция эмуляции крона
     *
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     * @throws Request_Exception
     */
    public function action_check()
    {
        $config = Kohana::$config->load('cron')->as_array();

        foreach ($config as $command) {
            $cron = \Cron\CronExpression::factory($command[0]);

            if ($cron->isDue()) {

                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $command[1];

                $request = Request::factory($url);
                $request->client()->options([
                    CURLOPT_TIMEOUT => 1
                ]);

                try {
                    $request->execute();
                } catch (Exception $e) {}
            }
        }
    }

    /**
     * рассылка уведомлений
     * последовательность: пуш, телеграм, смс
     *
     * @throws HTTP_Exception_500
     */
    public function action_sender()
    {
        $queue = Sender::getQueue([
            'limit'     => 100,
            'status'    => Sender::STATUS_NEW,
            '<attempts'  => 5
        ]);

        if (empty($queue)) {
            echo 'empty queue';
            die;
        }

        Sender::lockQueue(array_column($queue, 'MESSAGE_ID'));

        foreach ($queue as $message) {
            (new Sender())->sendMessage($message['MESSAGE_ID'], $message['MANAGER_ID'], $message['SEND_BODY'], $message['SEND_TYPE']);
        }
    }

    /**
     * разблокируем зависшие
     */
    public function action_unlockQueue()
    {
        $toUnlock = Sender::getQueue([
            'status'    => Sender::STATUS_PENDING,
            'locked'    => 10
        ]);

        if (empty($queue)) {
            echo 'empty queue';
            die;
        }

        Sender::unlockQueue(array_column($toUnlock, 'MESSAGE_ID'));
    }

    /**
     * проверяем статусы смс у оператора
     */
    public function action_checkSmsStatus()
    {
        $queue = Sender::getQueue([
            'type'              => Sender::TYPE_SMS,
            'status'            => Sender::STATUS_SENT,
            '!operator_status'  => Sender_Sms::STATUS_RECEIVED
        ]);

        if (empty($queue)) {
            echo 'empty queue';
            die;
        }

        foreach ($queue as $message) {
            (new Sender_Sms())->checkStatus($message['MESSAGE_ID'], $message);
        }
    }
}
