<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 600,
    'url'   => '/cards/[card_id]',
    'get'   => [
        'tags' => [
            'cards'
        ],
        'summary' => 'Получение карты',
        'operationId' => 'card',
        'parameters' => [
            [
                'name' => 'token',
                'in' => 'header',
                'type' => 'string',
                'required' => true
            ],
            [
                'name' => 'card_id',
                'in' => 'path',
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
    ]
];