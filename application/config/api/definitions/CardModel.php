<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'card_id' => [
            'type' => 'string'
        ],
        'holder' => [
            'type' => 'string'
        ],
        'date_holder' => [
            'type' => 'string'
        ],
        'card_status' => [
            'type' => 'integer'
        ],
        'block_available' => [
            'type' => 'integer'
        ],
        'change_limit_available' => [
            'type' => 'integer'
        ],
        'card_comment' => [
            'type' => 'string'
        ]
    ]
];