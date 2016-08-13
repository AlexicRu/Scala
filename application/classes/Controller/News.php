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

			list($news, $more) = Model_News::getList($params);

			if(empty($news)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $news, 'more' => $more]);
		}

        $popupNewsAdd = Common::popupForm('Добавление новости', 'news/add');

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
	public function action_news_detail()
    {
        $newsId = $this->request->param('id');

        $newsDetail = Model_News::getNewsById($newsId);

        if(empty($newsDetail)){
            throw new HTTP_Exception_404();
        }

        $this->tpl
            ->bind('detail', $newsDetail)
        ;
    }

    /**
     * добавление новости
     */
    public function action_news_add()
    {
        $params = $this->request->post('params');

        $result = Model_News::addNews($params);

        if(empty($result)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true);
    }
}
