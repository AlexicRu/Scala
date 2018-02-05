<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'CONTRACT_ID' => [
            'type' => 'integer'
        ],
        'CONTRACT_NAME' => [
            'type' => 'string'
        ],
        'DATE_BEGIN' => [
            'type' => 'string',
            'description' => 'd.m.Y'
        ],
        'DATE_END' => [
            'type' => 'string',
            'description' => 'd.m.Y'
        ],
        'CURRENCY' => [
            'type' => 'integer',
            'default' => 643
        ],
        'CONTRACT_STATUS' => [
            'type' => 'integer'
        ]
    ]
];