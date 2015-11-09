<?php
require_once __DIR__ . "/vendor/autoload.php";
use Jaspersoft\Client\Client;

$client = new Client(		"http://localhost:8080/jasperserver",
				"scanoil",
				"scanoil",
				"");
$client->setRequestTimeout(60);
$dl_format='xls';
$controls = array(	'REPORT_START_TIME'=>$p_start,
				'REPORT_END_TIME'=>$p_end,
				'REPORT_SUPPLIER_ID'=>$supplier_id);
$report = $client->reportService()->runReport('/reports/ScanOil/supplier/supplier_total_final',$dl_format,null,null,$controls);

      unset($controls);
  
?>
