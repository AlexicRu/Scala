<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Customer extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Клиент';
	}

	public function action_index()
	{
		$this->redirect('/customer/settings');
	}

	public function action_settings()
	{
		$this->title[] = 'Настройки';

		$params = $this->request->post();
		if(!empty($params) && $params['form_type'] == 'settings'){
			if(!Model_Customer::edit($params)){
				$this->errors[] = 'Ошибка сохранения';
			}
		}
		if(!empty($params) && $params['form_type'] == 'settings_notices'){

		}

		$settingsForm = View::factory('/forms/customer/settings');
		$settingsNoticesForm = View::factory('/forms/customer/settings_notices');

		$this->tpl
			->bind('settingsForm', $settingsForm)
			->bind('settingsNoticesForm', $settingsNoticesForm)
		;
	}
}
