<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Reports extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Отчеты';
	}

	public function action_index()
	{

	}
}
