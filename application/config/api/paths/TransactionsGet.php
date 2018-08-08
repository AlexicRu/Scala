<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 400,
    'url'   => '/transactions',
    'method' => 'get',
    'tags' => ['contracts'],
    'summary' => 'Получение списка транзакций',
	'description' => 'Данный метод позволяет получить список транзакций по договору за определенный период',
    'operationId' => 'transactions',
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
			'description' => 'Уникальный идентификатор договора'
        ],
        [
            'name' => 'date_from',
            'in' => 'query',
            'type' => 'string',
            'description' => 'Дата начала периода <br> - Если не передан параметр, то подставляется первое число текущего месяца'
        ],
        [
            'name' => 'date_to',
            'in' => 'query',
            'type' => 'string',
            'description' => 'Дата окончания  периода <br> - Если не передан параметр, то подставляется текущая дата'
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
                            '$ref' => '#/definitions/TransactionModel'
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