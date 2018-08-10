<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 900,
    'url'   => '/transactions',
    'method' => 'get',
    'tags' => ['transactions'],
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
            'name' => 'client_id',
            'in' => 'query',
            'type' => 'integer',
            'description' => 'Уникальный идентификатор клиента'
        ],
        [
            'name' => 'contract_id',
            'in' => 'query',
            'type' => 'integer',
			'description' => 'Уникальный идентификатор договора'
        ],
        [
            'name' => 'card_id',
            'in' => 'query',
            'type' => 'integer',
            'description' => 'Уникальный идентификатор карты. При указании карты обязательно указывать contract_id'
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