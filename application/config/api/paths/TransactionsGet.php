<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 400,
    'url'   => '/transactions',
    'method' => 'get',
    'tags' => ['contracts'],
    'summary' => 'Получение списка транзакций',
    'operationId' => 'transactions',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'contract_id',
            'in' => 'query',
            'type' => 'integer',
            'required' => true
        ],
        [
            'name' => 'date_from',
            'in' => 'query',
            'type' => 'string',
            'description' => 'Если не передан параметр, то 01.m.Y'
        ],
        [
            'name' => 'date_to',
            'in' => 'query',
            'type' => 'string',
            'description' => 'Если не передан параметр, то d.m.Y'
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