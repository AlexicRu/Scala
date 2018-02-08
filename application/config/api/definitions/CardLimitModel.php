<?php defined('SYSPATH') or die('No direct script access');

                 /*   "DURATION_TYPE",
                    "DURATION_VALUE",
                    "UNIT_TYPE",
                    "UNIT_CURRENCY",
                    "LIMIT_VALUE",
                    "TRN_COUNT",
                    "DAYS_WEEK_TYPE",
                    "DAYS_WEEK",
                    "TIME_FROM",
                    "TIME_TO",
    {
      "limit_id": "65924",
      "service_id": "3",
      "service_name": "Дизельное топливо",
      "card_id": "7824861090005155788",
      "duration_type": "1",
      "duration_value": "1",
      "unit_type": "2",
      "unit_currency": "643",
      "limit_value": "1000",
      "trn_count": "-1",
      "days_week_type": "0",
      "days_week": "0000000",
      "time_from": "0",
      "time_to": "0",
      "services": [
        "3"
      ]
    },
                 */

return [
    'type' => 'object',
    'properties' => [
        'limit_id' => [
            'type' => 'integer'
        ],
        'card_id' => [
            'type' => 'string'
        ],
        'services' => [
            'type' => 'array',
            'items' => [
                'type' => 'integer'
            ]
        ],
        'limit_value' => [
            'type' => 'integer'
        ],
        'trn_count' => [
            'type' => 'integer'
        ],
        'days_week_type' => [
            'type' => 'integer'
        ],
        'days_week' => [
            'type' => 'string'
        ],
        'time_from' => [
            'type' => 'integer'
        ],
        'time_to' => [
            'type' => 'integer'
        ],
        'duration_type' => [
            'type' => 'integer',
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsTypesFull),
        ],
        'duration_value' => [
            'type' => 'integer'
        ],
        'unit_type' => [
            'type' => 'integer',
            'description' => Common::stringFromKeyValueFromArray(Model_Card::$cardLimitsParams),
        ],
        'unit_currency' => [
            'type' => 'integer',
            'default' => Common::CURRENCY_RUR
        ]
    ]
];