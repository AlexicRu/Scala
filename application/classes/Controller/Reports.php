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
