<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'services' => [
            'type' => 'array',
            'items' => [
                'type' => 'integer'
            ]
        ],
        'description' => [
            'type' => 'string'
        ],
        'limit_group' => [
            'type' => 'integer'
        ],
        'limit_param' => [
            'type' => 'integer'
        ],
        'limit_type' => [
            'type' => 'integer'
        ],
        'limit_value' => [
            'type' => 'integer'
        ],
        'limit_currency' => [
            'type' => 'string',
            'default' => Common::CURRENCY_RUR
        ]
    ]
];