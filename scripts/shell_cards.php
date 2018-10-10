<?php

include ('shell.php');
include ('shell_config.php');

$params = [
    'config'    => $config['shell'], // ['url' => '', 'login' => '', 'password' => '']
];

echo (new Shell($params))->getAllCards();