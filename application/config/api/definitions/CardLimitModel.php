<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'limit_id' => [
            'type' => 'integer'
        ],
        'card_id' => [
            'type' => 'string'
        ],
        'services' => [
            'type' => 'array',
            'items' => [
                'type' => 'integer'
            ]
        ],
        'limit_value' => [
            'type' => 'integer'
        ],
        'trn_count' => [
            'type' => 'integer'
        ],
        'days_week_type' => [
            'type' => 'integer'
        ],
        'days_week' => [
            'type' => 'string'
        ],
        'time_from' => [
            'type' => 'integer'
        ],
        'time_to' => [
            'type' => 'integer'
        ],
        'duration_type' => [
            'type' => 'integer',
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsTypesFull),
        ],
        'duration_value' => [
            'type' => 'integer'
        ],
        'unit_type' => [
            'type' => 'integer',
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsParams),
        ],
        'unit_currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR
        ]
    ]
];