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

} // End Welcome
