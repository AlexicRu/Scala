<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'card_id' => [
            'type' => 'string',
			'description' => 'Номер карты',
        ],
        'holder' => [
            'type' => 'string',
			'description' => 'Текущий владелец карты (держатель)',
        ],
        'date_holder' => [
            'type' => 'string',
			'description' => 'Дата закрепления карты за текущим владелецем (держателем)',
        ],
        'card_status' => [
            'type' => 'integer',
			'description' => 'Идентификатор статуса карты',
        ],
        'block_available' => [
            'type' => 'integer',
			'description' => 'Возможность управления статусом карты <br> 0 - управление статусом невозможно <br> 1 - Возможны блокировка и разблокировка карты <br> 2 - Возможна только блокировка карты',
        ],
        'change_limit_available' => [
            'type' => 'integer',
			'description' => 'Возможность управления лимитами карты <br> 0 - управление лимитами невозможно <br> 1 - Управление климитами карты возможно',
        ],
        'card_comment' => [
            'type' => 'string',
			'description' => 'Комментарий к карте',
        ]
    ]
];