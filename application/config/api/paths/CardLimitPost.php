<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 740,
    'url'   => '/card-limits',
    'method' => 'post',
    'tags' => ['cards'],
    'summary' => 'Добавление лимита карты',
	'description' => 'Данный метод позволяет добавить на карту требуемый лимит',
    'operationId' => 'card_limit_post',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'body',
            'in' => 'body',
            'required' => true,
            'schema' => [
                '$ref' => '#/definitions/CardLimitModel'
            ]
        ],
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