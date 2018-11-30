<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 745,
    'url'   => '/card-limits/{limit_id}',
    'method' => 'put',
    'tags' => ['cards'],
    'summary' => 'Изменение лимита карты',
	'description' => 'Данный метод позволяет изменить на карте ранее предустановленный лимит',
    'operationId' => 'card_limit_put',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
        ],
        [
            'name' => 'limit_id',
            'in' => 'path',
            'type' => 'integer',
            'required' => true,
			'description' => 'Идентификатор предустановленного лимита',
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