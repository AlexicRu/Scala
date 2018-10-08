<?php

//for loading
define('SYSPATH', 'fake');

include (__DIR__ . '/../../application/classes/Shell.php');
include (__DIR__ . '/../../application/classes/Common.php');

$config = include(__DIR__ . '/../../application/config/config.php');
$database = include(__DIR__ . '/../../application/config/database.php');

$params = [
    'config'    => $config['shell'], // ['login' => '', 'password' => '']
    'db'        => $database, // ['db' => '', 'name' => '', 'password' => '']
    'currency'  => Common::CURRENCY_RUR, // 643
    'debug'     => true
];

$agentId = !empty($argv[1]) ? $argv[1] : 4;
$tubeId = !empty($argv[2]) ? $argv[2] : 70183602;
$dateStart = !empty($argv[3]) ? $argv[3] : date('Y-m-01');
$dateEnd = !empty($argv[4]) ? $argv[4] : date('Y-m-d');

echo (new Shell($params))->loadTransactions($agentId, $tubeId, $dateStart, $dateEnd);