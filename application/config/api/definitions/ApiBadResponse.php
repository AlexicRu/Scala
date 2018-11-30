<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'required' => [
        'success',
        'data'
    ],
    'properties' => [
        'success' => [
            'type' => 'boolean',
            'default' => false
        ],
        'data' => [
            'type' => 'array',
            'items' => [
                'type' => 'string',
                'description' => 'Текстовое описание ошибки'
            ]
        ]
    ]
];