<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 600,
    'url'   => '/cards/{card_id}',
    'method' => 'get',
    'tags' => [
        'cards'
    ],
    'summary' => 'Получение карты',
	'description' => 'Данный метод позволяет получить данные конкретной карты, закрепленной за договором клиента',
    'operationId' => 'card',
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
            'in' => 'path',
            'type' => 'string',
            'required' => true,
			'description' => 'Номер карты',
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
                        '$ref' => '#/definitions/CardModel'
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