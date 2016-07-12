<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Messages extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Сообщения';
	}

	public function action_index()
	{
		if($this->_isPost()) {
			$params = [
				'offset' => $this->request->post('offset'),
				'pagination' => true
			];

			list($messages, $more) = Model_Message::collect($params);

			if(empty($messages)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $messages, 'more' => $more]);
		}
	}

	/**
	 * отмечаем все сообщения пользователя прочитанными
	 */
	public function action_make_read()
	{
		$res = Model_Message::makeRead(['note_guid' => null]);
		
		if(empty($res)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true);
	}
}
