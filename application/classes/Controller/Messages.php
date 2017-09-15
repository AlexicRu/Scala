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
				'offset'        => $this->request->post('offset'),
				'search'        => $this->request->post('search'),
				'pagination'    => true
			];

			list($messages, $more) = Model_Message::getList($params);

			if(empty($messages)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $messages, 'more' => $more]);
		}

        $search = $this->request->query('m_search');

        $this->tpl
            ->bind('mSearch', $search)
        ;
	}

	/**
	 * отмечаем все сообщения пользователя прочитанными
	 */
	public function action_make_read()
	{
        $noteType = $this->request->post('type') ?: Model_Message::MESSAGE_TYPE_COMMON;

		$res = Model_Message::makeRead(['note_type' => $noteType]);
		
		if(empty($res)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true);
	}
}
