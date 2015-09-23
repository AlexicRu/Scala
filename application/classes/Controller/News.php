<?php defined('SYSPATH') or die('No direct script access.');

class Controller_News extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Новости';
	}

	public function action_index()
	{

	}
}
