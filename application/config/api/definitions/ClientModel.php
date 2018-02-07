<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'client_id' => [
            'type' => 'integer'
        ],
        'client_name' => [
            'type' => 'string'
        ],
        'long_name' => [
            'type' => 'string'
        ],
        'client_state' => [
            'type' => 'integer'
        ]
    ]
];