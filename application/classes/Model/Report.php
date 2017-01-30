<?php defined('SYSPATH') or die('No direct script access.');

require_once __DIR__."/../../../includes/Jasper/autoload.php";
use Jaspersoft\Client\Client;

class Model_Report extends Model
{
    const REPORT_GROUP_SUPPLIER = 1;
    const REPORT_GROUP_CLIENT   = 2;
    const REPORT_GROUP_ANALYTIC = 3;
    const REPORT_GROUP_OTHERS   = 4;

    const REPORT_TYPE_DAILY         = 'daily';
    const REPORT_TYPE_BALANCE_SHEET = 'balance_sheet';
    const REPORT_TYPE_BILL          = 'bill';

    const REPORT_CONSTRUCTOR_TYPE_PERIOD     = 'period';
    const REPORT_CONSTRUCTOR_TYPE_ADDITIONAL = 'additional';
    const REPORT_CONSTRUCTOR_TYPE_FORMAT     = 'format';

    public static $reportTypes = [
        self::REPORT_TYPE_DAILY         => 'kf/kf_client_total_detail',
        self::REPORT_TYPE_BALANCE_SHEET => 'kf/kf_manager_osv',
        self::REPORT_TYPE_BILL          => 'ru/aN_invoice_client'
    ];

    public static $reportGroups = [
        self::REPORT_GROUP_SUPPLIER => ['name' => 'Поставщики', 'icon' => 'icon-dailes'],
        self::REPORT_GROUP_CLIENT   => ['name' => 'Клиентские', 'icon' => 'icon-dailes'],
        self::REPORT_GROUP_ANALYTIC => ['name' => 'Аналитические', 'icon' => 'icon-analytics'],
        self::REPORT_GROUP_OTHERS   => ['name' => 'Прочие', 'icon' => 'icon-summary'],
    ];

    public static $formatHeaders = [
        'xls' => [
            'Content-Type: application/vnd.ms-excel',
            'Content-Disposition: attachment;filename=__NAME__',
            'Cache-Control: max-age=0'
        ],
        'pdf' => [
            'Cache-Control: must-revalidate',
            'Pragma: public',
            'Content-Description: File Transfer',
            'Content-Disposition: attachment;filename=__NAME__',
            'Content-Transfer-Encoding: binary',
            //'Content-Length: ' . strlen($report),
            'Content-Type: application/pdf',
        ]
    ];

    /**
     * генерация отчета
     *
     * @param $type
     * @param $params
     */
    public static function generate($params)
    {
        $config = Kohana::$config->load('jasper');

        $client = new Client(
            $config['host'],
            $config['login'],
            $config['password']
        );

        $controls = self::_prepareControls($params);

        $format = empty($params['format']) ? 'xls' : $params['format'];

        $type = !empty(self::$reportTypes[$params['type']]) ? self::$reportTypes[$params['type']] : $params['type'];

        if($params['type'] == self::REPORT_TYPE_BILL){
            $user = Auth_Oracle::instance()->get_user();
            $type = str_replace('ru/aN', 'ru/a'.$user['AGENT_ID'], $type);
        }

        try {
            $report = $client->reportService()->runReport('/reports/' . str_replace('\\', '/', $type), $format, null, null, $controls);
        } catch (Exception $e){
            throw new HTTP_Exception_500('Отчет не сформировался. '.$e->getMessage().$e->getCode());
        }

        $name = 'report_'.str_replace('\\', '_', $params['type']).'_'.date('Y_m_d').'.'.$format;

        $headers = self::$formatHeaders[$format];
        foreach($headers as &$header){
            $header = str_replace('__NAME__', $name, $header);
        }

        return ['report' => $report, 'headers' => $headers];
    }

    /**
     * собираем массив опций для отчета
     *
     * @param $params
     */
    private static function _prepareControls($params)
    {
        $controls = [];

        if(empty($params['type'])){
            return $controls;
        }

        switch($params['type']){
            case self::REPORT_TYPE_DAILY:
                $controls = [
                    'REPORT_START_TIME'     => [$params['date_start']." 00:00:00"],
                    'REPORT_END_TIME'       => [$params['date_end']." 23:59:59"],
                    'REPORT_CONTRACT_ID'    => [$params['contract_id']]
                ];
                break;
            case self::REPORT_TYPE_BALANCE_SHEET:
                $user = Auth::instance()->get_user();
                $controls = [
                    'REPORT_START_DATE'     => [$params['date_start']],
                    'REPORT_END_DATE'       => [$params['date_end']],
                    'REPORT_MANAGER_ID'     => [$user['MANAGER_ID']]
                ];
                break;
                break;
            case self::REPORT_TYPE_BILL:
                $controls = [
                    'INVOICE_CONTRACT_ID'  => [$params['contract_id']],
                    'INVOICE_NUMBER'       => [$params['invoice_number']],
                ];
                break;
            default:
                unset($params['type']);
                unset($params['format']);
                $controls = $params;
        }

        return $controls;
    }

    /**
     * получаем список доступных отчетов дл менеджера
     */
    public static function getAvailableReports()
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select *
            from ".Oracle::$prefix."V_WEB_REPORTS_AVAILABLE t 
            where t.agent_id in (0, {$user['AGENT_ID']}) 
            and t.role_id in (0, {$user['role']})
            and t.manager_id in (0, {$user['MANAGER_ID']})
        ";

		$reports = $db->query($sql);

		return $reports;
    }

    /**
     * получаем настройки для шаблона отчета
     *
     * @param $reportId
     */
    public static function getReportTemplateSettings($reportId)
    {
        if(empty($reportId)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_REPORTS_FORM t where t.report_id = ".Oracle::quote($reportId);

        $settings = $db->tree($sql, 'PROPERTY_TYPE');

        return $settings;
    }

    /**
     * создаем шаблон отчета
     *
     * @param $templateSettings
     */
    public static function buildTemplate($templateSettings)
    {
        $html = View::factory('forms/reports/constructor')
            ->bind('fields', $templateSettings)
        ;
        return $html;
    }

    /**
     * подготавливаем параметры отчета
     *
     * @param $params
     */
    public static function prepare($params)
    {
        if(empty($params)){
            return [];
        }

        $settings = [
            'format' => $params['format']
        ];

        $weight = 0;

        if(!empty($params['additional'])){
		foreach ($params['additional'] as $additional){
  			$weight += $additional['value'] ? $additional['weight'] : 0;
 		}
	}

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_REPORTS_PARAMS t where t.report_id = {$params['report_id']} and t.template_weight = {$weight}";

        $report = $db->query($sql);

        $row = reset($report);
        $settings['type'] = $row['FULL_PATH'];

        $user = Auth_Oracle::instance()->get_user();

        foreach($report as $param){
            $value = false;

            switch($param['PARAM_NAME']){
                case 'date_begin':
                    $value = $params['period_start'];
                    break;
                case 'date_end':
                    $value = $params['period_end'];
                    break;
                case 'date_begin_time':
                    $value = $params['period_start'].' 00:00:00';
                    break;
                case 'date_end_time':
                    $value = $params['period_end'].' 23:59:59';
                    break;
                case 'manager_id':
                    $value = $user['MANAGER_ID'];
                    break;
                case 'agent_id':
                    $value = $user['AGENT_ID'];
                    break;
                default:
                    foreach($params['additional'] as $additional){
                        if($additional['name'] == $param['PARAM_NAME']){
                            $value = $additional['value'];
                            break;
                        }
                    }
            }

            $settings[$param['PARAM_VARIABLE_NAME']] = $value;
        }

        return $settings;
    }
}
