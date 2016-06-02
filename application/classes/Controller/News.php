<?php defined('SYSPATH') or die('No direct script access.');

class Controller_News extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Новости';
	}

	public function action_index()
	{
		if($this->_isPost()) {
			$params = [
				'offset' => $this->request->post('offset'),
				'pagination' => true
			];

			list($news, $more) = Model_News::load($params);

			if(empty($news)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $news, 'more' => $more]);
		}
	}
}
