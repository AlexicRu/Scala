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

        $reports = [];

        foreach(Model_Report::$reportGroups as $reportGroupId => $reportGroup){
            foreach($reportsList as $report){
                if($report['REPORT_GROUP_ID'] == $reportGroupId){
                    $reports[$reportGroupId][] = $report;
                }
            }
        }

        $this->tpl
            ->bind('reports', $reports)
        ;
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
