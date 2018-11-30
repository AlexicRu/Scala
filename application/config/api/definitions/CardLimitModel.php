<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'limit_id' => [
            'type' => 'integer',
			'description' => 'Уникальный идентификатор лимита карты',
        ],
        'card_id' => [
            'type' => 'string',
			'description' => 'Номер карты',
        ],
        'services' => [
            'type' => 'array',
			'description' => 'Массив идентификаторов услуг, разрешенных по данному лимиту',
            'items' => [
                'type' => 'integer'
            ]
        ],
        'limit_value' => [
            'type' => 'integer',
			'description' => 'Количественный размер лимита',
        ],
        'trn_count' => [
            'type' => 'integer',
			'description' => 'Количество разрешенных транзакций',
        ],
        'days_week_type' => [
            'type' => 'integer',
			'description' => 'Флаг включения лимита по дням недели',
        ],
        'days_week' => [
            'type' => 'string',
			'description' => 'Строка из 7 нулей и единиц. 1 – ограничение применяется в этот день, 0 – нет'
        ],
        'time_from' => [
            'type' => 'integer',
			'description' => 'Ограничение по времени. Время обслуживания от <br> - Если указано 0, ограничения по времени нет',
        ],
        'time_to' => [
            'type' => 'integer',
			'description' => 'Ограничение по времени. Время обслуживания до <br> - Если указано 0, ограничения по времени нет',
        ],
        'duration_type' => [
            'type' => 'integer',
            'description' => 'Период действия лимита <br>'.Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsTypesFull),
        ],
        'duration_value' => [
            'type' => 'integer',
			'description' => 'Количество периодов действия лимита',
        ],
        'unit_type' => [
            'type' => 'integer',
            'description' => 'Единицы измерения лимита<br>'.Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsParams),
        ],
        'unit_currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR,
			'description' =>'Код валюты лимита ISO 4217 в случае единицы измерения лимита - 2 <br>'
        ]
    ]
];