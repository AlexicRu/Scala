<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 745,
    'url'   => '/card-limits/{limit_id}',
    'method' => 'put',
    'deprecated' => true,
    'tags' => ['cards'],
    'summary' => 'Изменение лимита карты',
    'operationId' => 'card_limit_put',
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