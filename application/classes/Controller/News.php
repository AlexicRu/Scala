<?php defined('SYSPATH') or die('No direct script access.');

class Controller_News extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Новости';
	}

	public function action_index()
	{
		if($this->isPost()) {
			$params = [
				'offset' => $this->request->post('offset'),
				'pagination' => true
			];

			list($news, $more) = Model_News::getList($params);

			if(empty($news)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $news, 'more' => $more]);
		}

        $popupNewsAdd = Form::popup('Добавление новости', 'news/edit');

        $this->_initWYSIWYG();
        $this->_initDropZone();

        $this->tpl
            ->bind('popupNewsAdd', $popupNewsAdd)
        ;
	}

    /**
     * страница новости детально
     *
     * @throws HTTP_Exception_404
     */
	public function action_newsDetail()
    {
        $newsId = $this->request->param('id');

        $newsDetail = Model_News::getNewsById($newsId);

        if(empty($newsDetail)){
            throw new HTTP_Exception_404();
        }

        $popupNewsEdit = Form::popup('Редактирование новости', 'news/edit', ['detail' => $newsDetail]);
        $this->_initWYSIWYG();
        $this->_initDropZone();

        $this->tpl
            ->bind('detail', $newsDetail)
            ->bind('popupNewsEdit', $popupNewsEdit)
        ;
    }

    /**
     * добавление новости
     */
    public function action_newsEdit()
    {
        $params = $this->request->post('params');

        $result = Model_News::editNews($params);

        if(empty($result)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true);
    }
}
