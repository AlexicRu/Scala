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
        if ($this->request->is_ajax()) {
            $params = $this->request->post();

            $result = false;

            if(!empty($params)){
                //форма сабмититься обычным способом, и чек бокс приходит вот так
                if (isset($params['manager_settings_limit_restriction'])) {
                    $params['manager_settings_limit_restriction'] = $params['manager_settings_limit_restriction'] == 'on' ? 1 : 0;
                }
                if (isset($params['manager_sms_is_on'])) {
                    $params['manager_sms_is_on'] = $params['manager_sms_is_on'] == 'on' ? 1 : 0;
                }
                if (isset($params['manager_telegram_is_on'])) {
                    $params['manager_telegram_is_on'] = $params['manager_telegram_is_on'] == 'on' ? 1 : 0;
                }

                $result = Model_Manager::edit(!empty($params['manager_settings_id']) ? $params['manager_settings_id'] : User::id(), $params);
            }

            $this->jsonResult($result);
        } else {
            $this->title[] = 'Настройки';

            $this->_initPhoneInputWithFlags();
        }

        $user = User::current();

		$managerSettingsForm = View::factory('forms/manager/settings');
        $popupManagerInform = Form::popup('Подключение информирования', 'manager/inform', [
            'manager' => $user
        ]);

        $managerSettingsForm
            ->set('manager', $user)
            ->set('selfEdit', true)
            ->set('popupManagerInform', $popupManagerInform)
        ;

		$this->tpl
			->bind('managerSettingsForm', $managerSettingsForm)
		;
	}

    /**
     * блокируем/разблокируем
     */
    public function action_managerToggle()
    {
        $params = $this->request->post('params');

        $result = Model_Manager::toggleStatus($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * грузим список клиентов по менеджеру
     */
    public function action_loadClients()
    {
        $managerId = $this->request->post('manager_id');
        $params = $this->request->post('params');

        $search = !empty($params['search']) ? $params['search'] : null;

        $clients = Model_Manager::getClientsList(['search' => $search, 'manager_id' => $managerId]);

        $contractsTree = Model_Manager::getContractsTree($managerId);

        $html = View::factory('ajax/clients/contract/clients')
            ->bind('clients', $clients)
            ->bind('managerId', $managerId)
            ->bind('contractsTree', $contractsTree)
        ;

        $this->html($html);
    }


    /**
     * грузим список отчетов по менеджеру
     */
    public function action_loadReports()
    {
        $managerId = $this->request->post('manager_id');
        $params = $this->request->post('params');

        $params['manager_id'] = $managerId;

        $reports = Model_Report::getAvailableReports($params);

        $html = View::factory('ajax/managers/reports')
            ->bind('reports', $reports)
            ->bind('managerId', $managerId)
        ;

        $this->html($html);
    }

    /**
     * удаляем отчет у менеджера
     */
    public function action_delReport()
    {
        $managerId = $this->request->post('manager_id');
        $reportId = $this->request->post('report_id');

        $error = Model_Manager::delReport($managerId, $reportId);

        if(!empty($error)){
            $this->jsonResult(false, $error);
        }
        $this->jsonResult(true);
    }

    /**
     * удаляем клиента у менеджера
     */
    public function action_delClient()
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
    public function action_addManager()
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
    public function action_addClients()
    {
        $params = $this->request->post('params');

        $result = Model_Manager::addClients($params);

        if($result == Oracle::CODE_SUCCESS){
            $this->jsonResult(true);
        }
        $this->jsonResult(false, $result);
    }

    /**
     * добавление отчетов
     */
    public function action_addReports()
    {
        $params = $this->request->post('params');

        $result = Model_Manager::editReports($params);

        if($result == Oracle::CODE_SUCCESS){
            $this->jsonResult(true);
        }
        $this->jsonResult(false, $result);
    }

    /**
     * список доступный клиентов
     */
    public function action_managersClients()
    {
        $params = $this->request->post('params');

        $params['only_available_to_add'] = true;

        $clients = Model_Manager::getClientsList($params);

        $this->jsonResult(true, $clients);
    }

    /**
     * список доступный отчетов
     */
    public function action_managersReports()
    {
        $params = $this->request->post('params');

        $reportsExist = Model_Report::getAvailableReports($params);
        $reportsExistIds = array_column($reportsExist, 'REPORT_ID');

        $reports = Model_Manager::getReportsList($params);

        foreach ($reports as $key => &$report) {
            if (in_array($report['REPORT_ID'], $reportsExistIds)) {
                unset($reports[$key]);
                continue;
            }
            $report['global_type_label'] = Model_Report::$reportGlobalTypesNames[$report['REPORT_TYPE_ID']]['label'];
            $report['global_type_name'] = Model_Report::$reportGlobalTypesNames[$report['REPORT_TYPE_ID']]['name'];
        }

        $this->jsonResult(true, $reports);
    }

    /**
     * редактируем доступы менеджера к контрактам конкретного клиента
     */
    public function action_editManagerClientsContractBinds()
    {
        $clientId = $this->request->post('client_id');
        $managerId = $this->request->post('manager_id');
        $binds = $this->request->post('binds');

        $result = Model_Manager::editContractBinds($managerId, $clientId, $binds);

        if(!empty($result)){
            $this->jsonResult(true);
        }
        $this->jsonResult(false, $result);
    }
}
