<?php defined('SYSPATH') or die('No direct script access.');

require_once __DIR__."/../../../includes/Jasper/autoload.php";
use Jaspersoft\Client\Client;

class Model_Report extends Model
{
    const REPORT_TYPE_DAILY         = 'daily';
    const REPORT_TYPE_BALANCE_SHEET = 'balance_sheet';
    const REPORT_TYPE_BILL          = 'bill';

    public static $reportTypes = [
        self::REPORT_TYPE_DAILY         => 'kf/kf_client_total_detail',
        self::REPORT_TYPE_BALANCE_SHEET => 'kf/kf_manager_osv',
        self::REPORT_TYPE_BILL          => 'ru/invoice_client'
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
        if(
            empty($params['type']) || !in_array($params['type'], array_keys(Model_Report::$reportTypes)) ||
            empty($params['contract_id'])
        ){
            return false;
        }

        $config = Kohana::$config->load('jasper');

        $client = new Client(
            $config['host'],
            $config['login'],
            $config['password']
        );

        $controls = self::_prepareControls($params);

        $format = empty($params['format']) ? 'xls' : $params['format'];

        $report = $client->reportService()->runReport('/reports/'.self::$reportTypes[$params['type']], $format, null, null, $controls);

        $name = 'report_'.$params['type'].'_'.date('Y_m_d').'.'.$format;

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
        }

        return $controls;
    }
}