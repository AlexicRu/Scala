<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Common {

	public function action_index()
	{
		$content = View::factory('/pages/index');

		$this->template->content = $content;
	}

	/**
	 * функция авторизации
	 */
	public function action_login()
	{
		$post = Request::current()->post();

		if(empty($post['login']) || empty($post['password'])){
			$this->redirect('/');
		}

		if(Auth::instance()->login($post['login'], $post['password'], FALSE)){
			$this->redirect('/cabinet');
		}
	}

	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('/');
	}

} // End Welcome
