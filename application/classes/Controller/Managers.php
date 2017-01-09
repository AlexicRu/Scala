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
		    $user = false;

            if(!empty($params['manager_settings_id'])){
                $user = [
                    'MANAGER_ID' => $params['manager_settings_id'],
                    'role' => $params['manager_settings_role'],
                ];
            }
			$result = Model_Manager::edit($params, $user);

            $this->jsonResult($result);
		}

		if(!empty($params) && $params['form_type'] == 'settings_notices'){

		}

		$managerSettingsForm = View::factory('forms/manager/settings');

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

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * грузим список клиентов по менеджеру
     */
    public function action_load_clients()
    {
        $managerId = $this->request->post('manager_id');

        $clients = Model_Client::getClientsList(null, ['manager_id' => $managerId]);

        if($clients === false){
            $this->jsonResult(0);
        }
        $this->jsonResult(1, $clients);
    }

    /**
     * удаляем кдинта у менеджера
     */
    public function action_del_client()
    {
        $managerId = $this->request->post('manager_id');
        $clientId = $this->request->post('client_id');

        $error = Model_Manager::delClient($managerId, $clientId);

        if(!empty($error)){
            $this->jsonResult(false, $error);
        }
        $this->jsonResult(true);
    }

    /**
     * добавление менеджера
     */
    public function action_add_manager()
    {
        $params = $this->request->post('params');

        $newManagerId = Model_Manager::addManager($params);

        if(empty($newManagerId)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true, Model_Manager::getManager($newManagerId));
    }

    /**
     * добавление клиентов
     */
    public function action_add_clients()
    {
        $params = $this->request->post('params');

        $result = Model_Manager::addClients($params);

        if($result == Oracle::CODE_SUCCESS){
            $this->jsonResult(true);
        }
        $this->jsonResult(false, $result);
    }

    /**
     * список доступный клиентов
     */
    public function action_managers_clients()
    {
        $params = $this->request->post('params');

        $clients = Model_Manager::getClientsList($params);

        $this->jsonResult(true, $clients);
    }
}
