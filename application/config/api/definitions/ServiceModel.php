<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'contract_id' => [
            'type' => 'integer'
        ],
        'contract_name' => [
            'type' => 'string'
        ],
        'date_begin' => [
            'type' => 'string',
            'description' => 'd.m.Y'
        ],
        'date_end' => [
            'type' => 'string',
            'description' => 'd.m.Y'
        ],
        'currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR
        ],
        'contract_status' => [
            'type' => 'integer'
        ]
    ]
];