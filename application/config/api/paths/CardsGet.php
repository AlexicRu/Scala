<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 500,
    'url'   => '/cards',
    'method' => 'get',
    'tags' => ['cards'],
    'summary' => 'Получение списка карт',
    'operationId' => 'cards',
	'description' => 'Данный метод позволяет получить список карт, закрепленных за договором клиента',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
        ],
        [
            'name' => 'contract_id',
            'in' => 'query',
            'type' => 'integer',
            'required' => true,
			'description' => 'Идентификатор договора',
        ]
    ],
    'responses' => [
        '200' => [
            'description' => 'Результат',
            'schema' => [
                'type' => 'object',
                'required' => [
                    'success',
                    'data'
                ],
                'properties' => [
                    'success' => [
                        'type' => 'boolean',
                        'default' => true
                    ],
                    'data' => [
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/definitions/CardModel'
                        ]
                    ]
                ]
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