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

echo (new Shell($params))->loadTransactions(4, 70183602, '2018-09-01', '2018-09-02');