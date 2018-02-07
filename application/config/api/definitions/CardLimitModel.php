<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'array',
    'items' => [
        'type' => 'object',
        'properties' => [
            'SERVICE_ID' => [
                'type' => 'integer'
            ],
            'DESCRIPTION' => [
                'type' => 'string'
            ],
            'LIMIT_GROUP' => [
                'type' => 'integer'
            ],
            'LIMIT_PARAM' => [
                'type' => 'integer'
            ],
            'LIMIT_TYPE' => [
                'type' => 'integer'
            ],
            'LIMIT_VALUE' => [
                'type' => 'integer'
            ],
            'LIMIT_CURRENCY' => [
                'type' => 'string'
            ]
        ]
    ]
];