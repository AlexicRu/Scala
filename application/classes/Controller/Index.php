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
        if (!empty($_FILES['file'])) {
            $name = explode('.', $_FILES['file']['name']);
            $filename = md5(time().'salt'.$_FILES['file']['name']).'.'.end($name);
            $component = $this->request->query('component') ?: 'file';

            $directory = Upload::generateFileDirectory($filename, $component);

            if(Upload::save($_FILES['file'], $filename, $_SERVER["DOCUMENT_ROOT"].$directory)){
                $this->jsonResult(true, ['file' => $directory.$filename]);
            }
        }

        $this->jsonResult(false);
    }

} // End Welcome
