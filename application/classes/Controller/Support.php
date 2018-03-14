<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Support extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Поддержка';
	}

	public function action_index()
	{
        $feedbackForm = View::factory('forms/support/feedback');

        $this->_initDropZone();

        $this->tpl
            ->bind('feedbackForm', $feedbackForm)
        ;
	}

    /**
     * отправка сообщения обратной связи
     */
	public function action_feedback()
    {
        $subject = $this->request->post('subject');
        $email = $this->request->post('email');
        $text = $this->request->post('text');
        $files = $this->request->post('files');

        $user = User::current();

        $subject = 'ЛК [Agent '. $user['AGENT_ID'] .' - '. $user['LOGIN'] .'] ' . $subject;
        $description = 'Email: ' . $email . "\n\n" . $text;

        $result = (new Redmine())->createIssue($subject, $description, (array)$files);

        $this->jsonResult($result);
    }
}
