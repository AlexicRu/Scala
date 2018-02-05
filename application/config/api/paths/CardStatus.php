<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 800,
    'url'   => '/card_status',
    'post'  => [
        'tags' => ['cards'],
        'summary' => 'Изменение статуса карты',
        'operationId' => 'card_status',
        'consumes' => [
            'application/x-www-form-urlencoded'
        ],
        'parameters' => [
            [
                'name' => 'token',
                'in' => 'header',
                'type' => 'string',
                'required' => true
            ],
            [
                'name' => 'card_id',
                'in' => 'formData',
                'type' => 'string',
                'required' => true
            ],
            [
                'name' => 'contract_id',
                'in' => 'formData',
                'type' => 'integer',
                'required' => true
            ],
            [
                'name' => 'comment',
                'in' => 'formData',
                'type' => 'string',
                'description' => 'Необходимо указывать при блокировке'
            ],
            [
                'name' => 'block',
                'in' => 'formData',
                'type' => 'integer',
                'description' => '1 / 0. Если параметр не передан, то происходит toggle статуса блокировки'
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
    ]
];