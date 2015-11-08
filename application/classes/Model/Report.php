<?php defined('SYSPATH') or die('No direct script access.');

require_once __DIR__."/../../../includes/Jasper/autoload.php";
use Jaspersoft\Client\Client;

class Model_Report extends Model
{
    const REPORT_TYPE_DAILY = 'daily';

    public static $reportTypes = [
        self::REPORT_TYPE_DAILY => 'kf_client_total_detail'
    ];

    public static $formatHeaders = [
        'xls' => [
            'Content-Type: application/vnd.ms-excel',
            'Content-Disposition: attachment;filename=__NAME__',
            'Cache-Control: max-age=0'
        ],
        'pdf' => [
            'Content-type:application/pdf',
            'Content-Disposition:attachment;filename=__NAME__'
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
            empty($params['contract_id']) ||
            empty($params['date_start']) ||
            empty($params['date_end'])
        ){
            return false;
        }

        $client = new Client(
                "http://185.6.172.165:18080/jasperserver",
                "glopro",
                "H-Geq_F4Gy"
        );

        $controls = array(
            'REPORT_START_TIME'     => [$params['date_start']." 00:00:00"],
            'REPORT_END_TIME'       => [$params['date_end']." 23:59:59"],
            'REPORT_CONTRACT_ID'    => [$params['contract_id']]
        );

        $format = empty($params['format']) ? 'xls' : $params['format'];

        $report = $client->reportService()->runReport('/reports/kf/'.self::$reportTypes[$params['type']], $format, null, null, $controls);

        $name = 'report_'.$params['type'].'_'.date('Y_m_d').'.'.$format;

        $headers = self::$formatHeaders[$format];
        foreach($headers as &$header){
            $header = str_replace('__NAME__', $name, $header);
        }

        return ['report' => $report, 'headers' => $headers];
    }
}