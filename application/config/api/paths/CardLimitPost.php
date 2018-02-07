<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 740,
    'url'   => '/card-limits',
    'method' => 'post',
    'tags' => ['cards'],
    'summary' => 'Добавление лимита карты',
    'operationId' => 'card_limit_post',
    'consumes' => [
        'application/x-www-form-urlencoded'
    ],
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true
        ]
    ],
    'responses' => [
        '200' => [
            'description' => 'Результат',
            'schema' => [
                '$ref' => '#/definitions/CardLimitModel'
            ]
        ],
        '400' => [
            'description' => 'Ошибка',
            'schema' => [
                '$ref' => '#/definitions/ApiBadResponse'
            ]
        ]
    ]
];