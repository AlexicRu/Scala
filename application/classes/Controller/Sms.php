<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sms extends Controller_Common
{
    /**
     * аяксово грузим поставщиков
     */
    public function action_sendConfirmCode()
    {
        $phone = $this->request->post('phone');

        /**
         * @var Sender_Sms $sender
         */
        $sender = Sender::factory('sms');

        $result = $sender->sendConfirmCode($phone);

        if (empty($result)) {
            $this->jsonResult(false, 'Не удалось отправить код');
        }

        $this->jsonResult(true, $result);
    }

    /**
     * подключаем смс информирование
     */
    public function action_enableSmsInform()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');

        /**
         * @var Sender_Sms $sender
         */
        $sender = Sender::factory('sms');

        if (!$sender->checkConfirmCode($phone, $code)) {
            $this->jsonResult(false, "Неверный код");
        }

        $manager = Model_Manager::getManager(['PHONE_FOR_INFORM' => $phone]);

        //$result = Model_Manager::disableSmsInform($manager['MANAGER_ID']); //todo

        if (!empty($result)) {
            $this->jsonResult(true);
        }
        $this->jsonResult(false);
    }

    /**
     * отключаем смс информирование
     */
    public function action_disableSmsInform()
    {
        $user = User::current();

        //$result = Model_Manager::disableSmsInform($user['MANAGER_ID']); //todo

        if (!empty($result)) {
            $this->jsonResult(true);
        }
        $this->jsonResult(false);
    }
}
