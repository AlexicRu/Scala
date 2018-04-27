<?php defined('SYSPATH') or die('No direct script access.');

use Jaspersoft\Client\Client;

class Model_Report extends Model
{
    const REPORT_GROUP_SUPPLIER = 1;
    const REPORT_GROUP_CLIENT   = 2;
    const REPORT_GROUP_ANALYTIC = 3;
    const REPORT_GROUP_CARDS    = 4;
    const REPORT_GROUP_OTHERS   = 5;

    const REPORT_TYPE_DAILY         = 'daily';
    const REPORT_TYPE_BALANCE_SHEET = 'balance_sheet';
    const REPORT_TYPE_BILL          = 'bill';

    const REPORT_GLOBAL_TYPE_CONTRACT = 1;
    const REPORT_GLOBAL_TYPE_REPORT = 2;

    const REPORT_CONSTRUCTOR_TYPE_PERIOD     = 'period';
    const REPORT_CONSTRUCTOR_TYPE_ADDITIONAL = 'additional';
    const REPORT_CONSTRUCTOR_TYPE_FORMAT     = 'format';

    const REPORT_TYPE_DB_ALL = 1;
    const REPORT_TYPE_DB_CLIENT = 2;

    public static $reportTypes = [
        self::REPORT_TYPE_DAILY         => 'kf/kf_client_total_detail',
        self::REPORT_TYPE_BALANCE_SHEET => 'kf/kf_manager_osv',
        self::REPORT_TYPE_BILL          => 'Ru/aN_invoice_client'
    ];

    public static $reportGroups = [
        self::REPORT_GROUP_ANALYTIC => ['name' => 'Аналитические', 'icon' => 'icon-analytics'],
        self::REPORT_GROUP_CARDS    => ['name' => 'Карты', 'icon' => 'icon-dailes'],
        self::REPORT_GROUP_CLIENT   => ['name' => 'Клиентские', 'icon' => 'icon-dailes'],
        self::REPORT_GROUP_SUPPLIER => ['name' => 'Поставщики', 'icon' => 'icon-dailes'],
        self::REPORT_GROUP_OTHERS   => ['name' => 'Прочие', 'icon' => 'icon-summary'],
    ];

    public static $reportGlobalTypesNames = [
        self::REPORT_GLOBAL_TYPE_CONTRACT   => ['name' => 'Отчетность', 'label' => 'label_info'],
        self::REPORT_GLOBAL_TYPE_REPORT     => ['name' => 'В договоре', 'label' => 'label_success'],
    ];

    public static $formatHeaders = [
        'xlsx' => [
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition: attachment;filename=__NAME__',
            'Cache-Control: must-revalidate',
            'Content-Transfer-Encoding: binary',
            'Pragma: public',
        ],
        'xls' => [
            'Content-Type: application/vnd.ms-excel',
            'Content-Disposition: attachment;filename=__NAME__',
            'Cache-Control: max-age=0'
        ],
        'pdf' => [
            'Cache-Control: must-revalidate',
            'Pragma: public',
            'Content-Disposition: attachment;filename=__NAME__',
            'Content-Transfer-Encoding: binary',
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
        set_time_limit(0);

        $config = Kohana::$config->load('jasper');

        $client = new Client(
            $config['host'],
            $config['login'],
            $config['password']
        );
        $client->setRequestTimeout(180);

        $controls = self::_prepareControls($params);

        $format = empty($params['format']) ? 'xls' : $params['format'];

        $type = !empty(self::$reportTypes[$params['type']]) ? self::$reportTypes[$params['type']] : $params['type'];

        if($params['type'] == self::REPORT_TYPE_BILL){
            $user = Auth_Oracle::instance()->get_user();
            $type = str_replace('Ru/aN', 'Ru/a'.$user['AGENT_ID'], $type);
        }

        try {
            $report = $client->reportService()->runReport('/reports/' . str_replace('\\', '/', $type), $format, null, null, $controls);
        } catch (Exception $e){
            throw new HTTP_Exception_500('Отчет не сформировался. Code: '.$e->getCode().'. Message: '.$e->getMessage());
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
    public static function getAvailableReports($params = [])
    {
        $db = Oracle::init();

        $user = User::current();

        if (empty($params['manager_id'])) {
            $manager = $user;
        } else {
            $manager = Model_Manager::getManager($params['manager_id']);
        }

        if (empty($params['report_type_id'])) {
            if (in_array($manager['role'], array_keys(Access::$clientRoles))) {
                $params['report_type_id'] = [self::REPORT_TYPE_DB_CLIENT];
            }
            if (in_array($manager['role'], array_keys(Access::$adminRoles))) {
                $params['report_type_id'] = [
                    self::REPORT_TYPE_DB_CLIENT,
                    self::REPORT_TYPE_DB_ALL,
                ];
            }
        }

        if (empty($params['report_type_id'])) {
            $params['report_type_id'] = [self::REPORT_TYPE_DB_ALL];
        }

        $sql = (new Builder())->select([
            'REPORT_ID',
            'REPORT_NAME',
            'WEB_NAME',
            'DESCRIPTION',
            'REPORT_GROUP_ID',
            'REPORT_TYPE_ID',
        ])->distinct()
            ->from('V_WEB_REPORTS_AVAILABLE t')
            ->where("t.agent_id in (0, {$manager['AGENT_ID']})")
            ->where("t.role_id in (0, {$manager['role']})")
            ->where("t.manager_id in (0, {$manager['MANAGER_ID']})")
            ->where("t.report_type_id in (0, ". implode(', ', array_map('intval', (array)$params['report_type_id'])) .")")
        ;

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
                if (
                    !empty($additional['value'])
                    && $additional['value'] != -1
                    && !(is_array($additional['value']) && count($additional['value']) == 1 && $additional['value'][0] == -1)
                ) {
                    $weight += $additional['weight'];
                }
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
                    if (!empty($params['additional'])) {
                        foreach ($params['additional'] as $additional) {
                            if ($additional['name'] == $param['PARAM_NAME']) {

                                if (!isset($additional['value'])) {
                                    continue;
                                }

                                if (is_array($additional['value'])) {
                                    $additional['value'] = array_filter($additional['value']);
                                    if (!in_array(-1, $additional['value'])) {
                                        array_unshift($additional['value'], -1); //привет джасперу
                                    }
                                }

                                $value = $additional['value'];
                                break;
                            }
                        }
                    }
            }

            $settings[$param['PARAM_VARIABLE_NAME']] = $value;

            switch($param['PARAM_NAME']){
                case 'contract_id':
                    if (!empty($params['contract_id'])) {
                        $settings['REPORT_CONTRACT_ID'] = $params['contract_id'];
                    }
                    break;
                case 'contract_id_multi':
                    if (!empty($params['contract_id'])) {
                        $settings['REPORT_CONTRACT_ID'] = [-1, $params['contract_id']];
                    }
                    break;
            }
        }

        return $settings;
    }

    /**
     * разбиваем отчеты по группам
     *
     * @param $reportsList
     * @return array
     */
    public static function separateBuyGroups($reportsList)
    {
        $reports = [];

        if (empty($reportsList)) {
            return $reports;
        }

        foreach(Model_Report::$reportGroups as $reportGroupId => $reportGroup){
            foreach($reportsList as $report){
                if($report['REPORT_GROUP_ID'] == $reportGroupId){
                    $reports[$reportGroupId][] = $report;
                }
            }
        }

        return $reports;
    }
}
