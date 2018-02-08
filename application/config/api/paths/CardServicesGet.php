<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 900,
    'url'   => '/card-services',
    'method' => 'get',
    'tags' => ['cards'],
    'summary' => 'Получение списка сервисов по карте',
    'operationId' => 'transactions',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'card_id',
            'in' => 'query',
            'type' => 'integer',
            'required' => true
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
                            '$ref' => '#/definitions/ServiceModel'
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