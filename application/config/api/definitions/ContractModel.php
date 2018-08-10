<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'contract_id' => [
            'type' => 'integer',
			'description' => 'Уникальный идентификатор договора'
        ],
        'contract_name' => [
            'type' => 'string',
			'description' => 'Наименование договора'
        ],
        'date_begin' => [
            'type' => 'string',
            'description' => 'Дата начала действия договора в формате d.m.Y'
        ],
        'date_end' => [
            'type' => 'string',
            'description' => 'Дата окончания действия договора в формате d.m.Y (в случае даты равной "31.12.2099" договор является бессрочным)'
        ],
        'currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR,
			'description' => 'Код валюты договора ISO 4217'
        ],
        'contract_status' => [
            'type' => 'integer',
			'description' => 'Идентификатор статуса договора договора'
        ],
        'balance' => [
            'type' => 'object',
            'description' => 'Баланс по договору',
            'properties' => [
                'balance' => [
                    'type' => 'integer',
                ],
                'month_realiz' => [
                    'type' => 'integer',
                ],
                'month_realiz_cur' => [
                    'type' => 'integer',
                ],
                'last_month_realiz' => [
                    'type' => 'integer',
                ],
                'last_month_realiz_cur' => [
                    'type' => 'integer',
                ],
                'date_last_change' => [
                    'type' => 'string',
                ],
            ]
        ],
    ]
];