<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Reports extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Отчеты';
	}

	public function action_index()
	{
        $reportsList = Model_Report::getAvailableReports();

        $reports = Model_Report::separateBuyGroups($reportsList);

        $this->tpl
            ->bind('reports', $reports)
        ;
	}

	/**
	 * генерация отчетов
	 */
	public function action_generate()
	{
		$params = $this->request->query();

		if(!empty($params['build'])){
		    $params = Model_Report::prepare($params);
        }

		$report = Model_Report::generate($params);

		foreach($report['headers'] as $header){
			header($header);
		}

		$this->html($report['report']);
	}

    /**
     * подгружаем шаблон отчета
     */
	public function action_load_report_template()
    {
        $reportId = $this->request->param('id');

        $templateSettings = Model_Report::getReportTemplateSettings($reportId);

        if(empty($templateSettings)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $this->html(Model_Report::buildTemplate($templateSettings));
    }
}
