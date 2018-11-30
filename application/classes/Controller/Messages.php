<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Messages extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Сообщения';
	}

	public function action_index()
	{
		if($this->isPost()) {
			$params = [
			    'note_type'     => Model_Note::NOTE_TYPE_MESSAGE,
				'offset'        => $this->request->post('offset'),
				'search'        => $this->request->post('search'),
				'pagination'    => true
			];

			list($messages, $more) = Model_Note::getList($params);

			if(empty($messages)){
				$this->jsonResult(false);
			}

			$messages = Model_Note::parseBBCodes($messages);

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
	public function action_makeRead()
	{
        $noteType = $this->request->post('type') ?: Model_Note::NOTE_TYPE_MESSAGE;

		$res = Model_Note::makeRead(['note_type' => $noteType]);
		
		if(empty($res)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true);
	}
}
