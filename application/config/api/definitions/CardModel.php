<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'CARD_ID' => [
            'type' => 'string'
        ],
        'HOLDER' => [
            'type' => 'string'
        ],
        'DATE_HOLDER' => [
            'type' => 'string'
        ],
        'CARD_STATUS' => [
            'type' => 'integer'
        ],
        'BLOCK_AVAILABLE' => [
            'type' => 'integer'
        ],
        'CHANGE_LIMIT_AVAILABLE' => [
            'type' => 'integer'
        ],
        'CARD_COMMENT' => [
            'type' => 'string'
        ]
    ]
];