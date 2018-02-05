<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Common {

	public function action_index()
	{
	}

	/**
	 * функция авторизации
	 */
	public function action_login()
	{
		$post = Request::current()->post();
		
		if(empty($post['login']) || empty($post['password'])){
			Messages::put('Не заполнен логин или пароль', 'error');
			$this->redirect('/');
		}

		if(Auth::instance()->login($post['login'], $post['password'], FALSE)){
			$this->redirect('/clients');
		}

        $this->redirect('/');
	}

	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('/');
	}

    /**
     * грузим файлы аяксово
     */
	public function action_upload_file()
    {
        $component = $this->request->query('component') ?: 'file';

        if ($file = Upload::uploadFile($component)) {
            $this->jsonResult(true, ['file' => $file]);
        }

        $this->jsonResult(false);
    }

    /**
     * подгружаем конфиг api
     */
    public function action_get_json()
    {
        $api = Api::getStructure();

        $html = View::factory('api/json')
            ->bind('api', $api)
        ;

        $this->html($html);
    }
} // End Welcome
