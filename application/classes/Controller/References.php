<?php defined('SYSPATH') or die('No direct script access.');

class Controller_References extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Справочники';
	}

	public function action_index()
	{
        $this->redirect('/references/sources');
	}

    /**
     * Источники данных
     */
	public function action_sources()
    {
        $this->title[] = 'Источники данных';
    }

    /**
     * Адресный справочник
     */
    public function action_addresses()
    {
        $this->title[] = 'Адресный справочник';
    }

    /**
     * Валюты
     */
    public function action_currency()
    {
        $this->title[] = 'Валюты';
    }

    /**
     * Курсы валют
     */
    public function action_converter()
    {
        $this->title[] = 'Курсы валют';
    }
}
