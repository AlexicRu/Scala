<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 300,
    'url'   => '/contracts',
    'get'   => [
        'tags' => ['contracts'],
        'summary' => 'Получение списка контрактов',
        'operationId' => 'contracts',
        'parameters' => [
            [
                'name' => 'token',
                'in' => 'header',
                'type' => 'string',
                'required' => true
            ],
            [
                'name' => 'client_id',
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
                                '$ref' => '#/definitions/ContractModel'
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