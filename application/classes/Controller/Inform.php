<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Inform extends Controller_Common
{
    /**
     * аяксово грузим поставщиков
     */
    public function action_sendSmsConfirmCode()
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
     * подключаем информирование
     */
    public function action_enableInform()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');

        /**
         * @var Sender_Sms $sender
         */
        $sender = Sender::factory('sms');

        $managerId = $sender->checkConfirmCode($phone, $code);

        if (empty($managerId)) {
            $this->jsonResult(false, "Неверный код");
        }

        $result = Model_Manager::enableInform($managerId, $phone);

        if (!empty($result)) {
            $this->jsonResult(true);
        }
        $this->jsonResult(false);
    }

    /**
     * отключаем sms информирование
     */
    public function action_disableInform()
    {
        $user = User::current();

        $result = Model_Manager::disableInform($user['MANAGER_ID']);

        if (!empty($result)) {
            $this->jsonResult(true);
        }
        $this->jsonResult(false);
    }
}
