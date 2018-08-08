<?php defined('SYSPATH') or die('No direct script access');

return  [
    'sort'  => 700,
    'url'   => '/card-limits',
    'method' => 'get',
    'tags' => [
        'cards'
    ],
	'description' => 'Данный метод позволяет получить список лимитов, установленных на карте',
    'summary' => 'Получение лимитов карты',
    'operationId' => 'card_limits',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
        ],
        [
            'name' => 'card_id',
            'in' => 'query',
            'type' => 'string',
            'required' => true,
			'description' => 'Номер карты',
        ],
        [
            'name' => 'contract_id',
            'in' => 'query',
            'type' => 'integer',
            'required' => true,
			'description' => 'Идентификатор договора'
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
                            '$ref' => '#/definitions/CardLimitModel'
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