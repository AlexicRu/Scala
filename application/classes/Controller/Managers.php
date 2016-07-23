<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Managers extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Клиент';
	}

	public function action_index()
	{
		$this->redirect('/managers/settings');
	}

    /**
     * страница настроек
     */
	public function action_settings()
	{
		$this->title[] = 'Настройки';

		$params = $this->request->post();
		if(!empty($params) && $params['form_type'] == 'settings'){
			$result = Model_Manager::edit($params);

            $this->jsonResult($result);
		}

		if(!empty($params) && $params['form_type'] == 'settings_notices'){

		}

		$managerSettingsForm = View::factory('/forms/manager/settings');

		$this->tpl
			->bind('managerSettingsForm', $managerSettingsForm)
		;
	}

    /**
     * блокируем/разблокируем
     */
    public function action_manager_toggle()
    {
        $params = $this->request->post('params');

        $result = Model_Manager::toggleStatus($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }
}
