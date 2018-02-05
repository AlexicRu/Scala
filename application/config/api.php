<?php defined('SYSPATH') or die('No direct script access');

return [
    'swagger' => '2.0',
    'info' => [
        'version' => '0.1.1',
        'title' => 'GloPro API'
    ],
    'host' => '', //из конфига
    'basePath' => '/api',
    'schemes' => [
        'https'
    ],
    'consumes' => [
        'application/json'
    ]
];