<?php defined('SYSPATH') or die('No direct script access');

return [
    'swagger' => '2.0',
    'info' => [
        'version' => '0.8.2',
        'title' => 'GloPro API',
        'description' => 'Документ описывает интерфейсы взаимодействия/интеграции сторонних систем с информационными сервисом GloPro'
    ],
    'host' => '', //из конфига
    'basePath' => '/api',
    'schemes' => '', //из конфига
    'consumes' => [
        'application/json'
    ]
];