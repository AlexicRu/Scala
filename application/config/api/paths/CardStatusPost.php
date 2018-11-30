<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 800,
    'url'   => '/card-status',
    'method' => 'post',
    'tags' => ['cards'],
    'summary' => 'Изменение статуса карты',
	'description' => 'Данный метод позволяет изменять статусы карт',
    'operationId' => 'card_status',
    'consumes' => [
        'application/x-www-form-urlencoded'
    ],
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
        ],
        [
            'name' => 'card_id',
            'in' => 'formData',
            'type' => 'string',
            'required' => true,
			'description' => 'Номер карты',
        ],
        [
            'name' => 'contract_id',
            'in' => 'formData',
            'type' => 'integer',
            'required' => true,
			'description' => 'Идентификатор договора',
        ],
        [
            'name' => 'comment',
            'in' => 'formData',
            'type' => 'string',
            'description' => 'Комментарий к операции. Необходимо указывать только при блокировке',
			
        ],
        [
            'name' => 'block',
            'in' => 'formData',
            'type' => 'integer',
            'description' => 'Идентификатор операции <br>1 - Блокировка карты <br>0 - Hазблокировка карты <br> Если параметр не передан, то происходит смена статуса с предыдущего'
        ]
    ],
    'responses' => [
        '200' => [
            'description' => 'Результат',
            'schema' => [
                '$ref' => '#/definitions/ApiResponse'
            ]
        ],
        '400' => [
            'description' => 'Ошибка',
            'schema' => [
                '$ref' => '#/definitions/ApiBadResponse'
            ]
        ]
    ]
];