<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'datetime_trn' => [
            'type' => 'string'
        ],
        'card_id' => [
            'type' => 'string'
        ],
        'client_id' => [
            'type' => 'integer'
        ],
        'contract_id' => [
            'type' => 'integer'
        ],
        'operation_id' => [
            'type' => 'integer'
        ],
        'supplier_terminal' => [
            'type' => 'integer'
        ],
        'service_id' => [
            'type' => 'integer'
        ],
        'description' => [
            'type' => 'string'
        ],
        'service_amount' => [
            'type' => 'number'
        ],
        'service_price' => [
            'type' => 'number'
        ],
        'service_sumprice' => [
            'type' => 'number'
        ],
        'trn_currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR
        ],
        'price_discount' => [
            'type' => 'number'
        ],
        'sumprice_discount' => [
            'type' => 'number'
        ],
        'pos_address' => [
            'type' => 'string'
        ],
        'trn_key' => [
            'type' => 'string'
        ],
        'trn_comment' => [
            'type' => 'string'
        ]
    ]
];