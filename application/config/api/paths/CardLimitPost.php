<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 740,
    'url'   => '/card-limits',
    'method' => 'post',
    'tags' => ['cards'],
    'summary' => 'Добавление лимита карты',
    'operationId' => 'card_limit_post',
    'consumes' => [
        'application/x-www-form-urlencoded'
    ],
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'card_id',
            'in' => 'formData',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'contract_id',
            'in' => 'formData',
            'type' => 'integer',
            'required' => true
        ],
        [
            'name' => 'value',
            'in' => 'formData',
            'type' => 'integer',
            'required' => true
        ],
        [
            'name' => 'unit_type',
            'in' => 'formData',
            'type' => 'string',
            'enum' => array_keys(Model_Card::$cardLimitsParams),
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsParams),
            'required' => true
        ],
        [
            'name' => 'duration_type',
            'in' => 'formData',
            'type' => 'string',
            'enum' => array_keys(Model_Card::$cardLimitsTypesFull),
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsTypesFull),
            'required' => true
        ],
        [
            'name' => 'services',
            'in' => 'formData',
            'type' => 'array',
            'required' => true,
            'items' => [
                'type' => 'integer'
            ]
        ],
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
                        '$ref' => '#/definitions/CardLimitModel'
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