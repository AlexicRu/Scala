<?php

//for loading
define('SYSPATH', 'fake');

include (__DIR__ . '/../../application/classes/Shell.php');

$config = include(__DIR__ . '/../../application/config/config.php');

echo (new Shell($config['shell']))->getAllCards();