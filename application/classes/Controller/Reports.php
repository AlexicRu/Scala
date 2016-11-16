<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Reports extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Отчеты';
	}

	public function action_index()
	{
        $db = Oracle::init();

        /**
        [REPORT_ID] => 1
        [REPORT_NAME] => kf_client_total_detail_with_TO
        [WEB_NAME] => Транзакционный отчет с учетом скидки
        [REPORT_GROUP_ID] => 1 - поставщики / 2 - клиентские / 3 - аналитические
        [AGENT_ID] => 0 - для всех агентов / иначе, конкретному агенту
        [ROLE_ID] => 0 - для всех ролей / иначе, конкретной роли
        [MANAGER_ID] => 0 - для всех манагеров / иначе, конкретного манагера
         */

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_REPORTS_AVAILABLE
        ";

		$reports = $db->query($sql);

		//print_r($reports);die;
	}

	/**
	 * гшенерация отчетов
	 *
	 * @throws HTTP_Exception_404
	 */
	public function action_generate()
	{
		$params = $this->request->query();

		$report = Model_Report::generate($params);

		if(empty($report)){
			throw new HTTP_Exception_500('Отчет не сформировался');
		}

		foreach($report['headers'] as $header){
			header($header);
		}

		$this->html($report['report']);
	}
}
