<?php

if (!preg_match('/shell_(\d+)_(\d+)_report.php/', $argv[0], $m)) {
    do_log("invalid filename: ".$argv[0]);
    die("valid filename: shell_{agentId}_{tubeId}_report.php\n");
}
$tubeConfig = "shell_".$m[1]."_".$m[2]."_config.php";

include ('shell.php');
include ('shell_config.php');
include ($tubeConfig);

do_log("loaded config: ".$tubeConfig);

$params = [
    'agent_id'  => $agentId,
    'tube_id'   => $tubeId,
    'config'    => ['url' => $shellUrl, 'login' => $shellConfig['login'], 'password' => $shellConfig['password']],
    'db'        => $database, // ['db' => '', 'name' => '', 'password' => '']
    'log_file'  => __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . '_shell_' . $agentId . '_' . $tubeId . '.log'
];

if (!empty($argv[1])) {
    $date = DateTime::createFromFormat('Ym', $argv[1]);
    $dateStart = $date->format('Y-m-01');
    $dateEnd = $date->format('Y-m-t');
} else {
    $cntDays = isset($cntDays) ? $cntDays : 5; //из конфига
    $date = new DateTime();
    $dateEnd = $date->format('Y-m-d');

    $date->modify('-'. $cntDays .' day');
    $dateStart = $date->format('Y-m-d');
}

echo (new Shell($params))->loadTransactions($dateStart, $dateEnd);