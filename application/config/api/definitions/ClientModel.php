<?php defined('SYSPATH') or die('No direct script access');

return [
    'type' => 'object',
    'properties' => [
        'client_id' => [
            'type' => 'integer',
			'description' => 'Уникальный идентификатор клиента в системе'
        ],
        'client_name' => [
            'type' => 'string',
			'description' => 'Краткое наименование клиента'
        ],
        'long_name' => [
            'type' => 'string',
			'description' => 'Полное наименование клиента'
        ],
        'client_state' => [
            'type' => 'integer',
			'description' => 'Идентификатор текущего статуса клиента'
		]
    ]
];