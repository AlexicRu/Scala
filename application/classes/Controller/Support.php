<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Support extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Поддержка';
	}

	public function action_index()
	{

	}
}
