<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 500,
    'url'   => '/cards',
    'get'   => [
        'tags' => ['cards'],
        'summary' => 'Получение списка карт',
        'operationId' => 'cards',
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
    ]
];