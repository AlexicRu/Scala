<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'datetime_trn' => [
            'type' => 'string',
			'description' => 'Дата и время транзакции в формате d.m.Y H:i:s'
        ],
        'card_id' => [
            'type' => 'string',
			'description' => 'Номер карты',
        ],
        'client_id' => [
            'type' => 'integer',
			'description' => 'Идентификатор клиента',
        ],
        'contract_id' => [
            'type' => 'integer',
			'description' => 'Идентификатор договора',
        ],
        'operation_id' => [
            'type' => 'integer',
			'description' => 'Идентификатор операции',
        ],
        'supplier_terminal' => [
            'type' => 'integer',
			'description' => 'Идентификатор номера терминала поставщика',
        ],
        'service_id' => [
            'type' => 'integer',
			'description' => 'Идентификатор услуги',
        ],
        'description' => [
            'type' => 'string',
			'description' => 'Наименование услуги',
        ],
        'service_amount' => [
            'type' => 'number',
			'description' => 'Количество услуги',
        ],
        'service_price' => [
            'type' => 'number',
			'description' => 'Цена услуги на АЗС без учета скидки',
        ],
        'service_sumprice' => [
            'type' => 'number',
			'description' => 'Стоимость услуги на АЗС без учета скидки',
        ],
        'trn_currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR,
			'description' => 'Код палюты транзакции ISO 4217',
        ],
        'price_discount' => [
            'type' => 'number',
			'description' => 'Цена услуги на АЗС с учетом скидки',
        ],
        'sumprice_discount' => [
            'type' => 'number',
			'description' => 'Стоимость услуги на АЗС с учетом скидки',
        ],
        'pos_name' => [
            'type' => 'string',
            'description' => 'Название АЗС',
        ],
        'pos_address' => [
            'type' => 'string',
			'description' => 'Адрес АЗС',
        ],
        'trn_key' => [
            'type' => 'string',
			'description' => 'Уникальный идентификатор транзакции',
        ],
        'trn_comment' => [
            'type' => 'string',
			'description' => 'Комментарии к транзакции',
        ]
    ]
];