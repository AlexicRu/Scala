<?php

include ('shell.php');
include ('shell_config.php');

$agentId = 4;
$tubeId = 70183602;

$params = [
    'config'    => $config['shell'], // ['url' => '', 'login' => '', 'password' => '']
    'db'        => $database, // ['db' => '', 'name' => '', 'password' => '']
    'log_file'  => __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . '_shell_' . $agentId . '_' . $tubeId . '.log'
];

if (!empty($argv[1])) {
    $date = DateTime::createFromFormat('Ym', $argv[1]);
    $dateStart = $date->format('Y-m-01');
    $dateEnd = $date->format('Y-m-t');
} else {
    $cntDays = 5; //из конфига
    $date = new DateTime();
    $dateEnd = $date->format('Y-m-d');

    $date->modify('-'. $cntDays .' day');
    $dateStart = $date->format('Y-m-d');
}

echo (new Shell($params))->loadTransactions($agentId, $tubeId, $dateStart, $dateEnd);