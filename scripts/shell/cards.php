<?php

//for loading
define('SYSPATH', 'fake');

include (__DIR__ . '/../../application/classes/Shell.php');

$config = include(__DIR__ . '/../../application/config/config.php');

$params = [
    'config' => $config['shell'], // ['login' => '', 'password' => '']
];

echo (new Shell($params))->getAllCards();