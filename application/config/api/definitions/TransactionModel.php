<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'DATETIME_TRN' => [
            'type' => 'string'
        ],
        'CARD_ID' => [
            'type' => 'string'
        ],
        'CLIENT_ID' => [
            'type' => 'integer'
        ],
        'CONTRACT_ID' => [
            'type' => 'integer'
        ],
        'OPERATION_ID' => [
            'type' => 'integer'
        ],
        'SUPPLIER_TERMINAL' => [
            'type' => 'integer'
        ],
        'SERVICE_ID' => [
            'type' => 'integer'
        ],
        'DESCRIPTION' => [
            'type' => 'string'
        ],
        'SERVICE_AMOUNT' => [
            'type' => 'number'
        ],
        'SERVICE_PRICE' => [
            'type' => 'number'
        ],
        'SERVICE_SUMPRICE' => [
            'type' => 'number'
        ],
        'TRN_CURRENCY' => [
            'type' => 'integer',
            'default' => 643
        ],
        'PRICE_DISCOUNT' => [
            'type' => 'number'
        ],
        'SUMPRICE_DISCOUNT' => [
            'type' => 'number'
        ],
        'POS_ADDRESS' => [
            'type' => 'string'
        ],
        'TRN_KEY' => [
            'type' => 'string'
        ],
        'TRN_COMMENT' => [
            'type' => 'string'
        ]
    ]
];