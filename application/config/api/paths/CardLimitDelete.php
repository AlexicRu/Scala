<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 750,
    'url'   => '/card-limits/{limit_id}',
    'method' => 'delete',
    'tags' => ['cards'],
    'summary' => 'Удаление лимита карты',
    'operationId' => 'card_limit_delete',
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
                '$ref' => '#/definitions/ApiResponse'
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