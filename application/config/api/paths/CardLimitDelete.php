<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 750,
    'url'   => '/card-limits/{limit_id}',
    'method' => 'delete',
    'tags' => ['cards'],
    'summary' => 'Удаление лимита карты',
	'description' => 'Данный метод позволяет удалить на карте предустановленный лимит',
    'operationId' => 'card_limit_delete',
    'consumes' => [
        'application/x-www-form-urlencoded'
    ],
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