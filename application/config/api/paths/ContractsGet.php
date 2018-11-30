<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 300,
    'url'   => '/contracts',
    'method' => 'get',
    'tags' => ['contracts'],
    'summary' => 'Получение списка контрактов клиента',
	'description' => 'Данный метод позволяет получить полный список контрактов, закрепленных за клиентом',
    'operationId' => 'contracts',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
        ],
        [
            'name' => 'client_id',
            'in' => 'query',
            'type' => 'integer',
            'required' => true,
			'description' => 'Идентификатор клиента, для которого необходимо получить список контрактов',
        ]
    ],
    'responses' => [
        '200' => [
            'description' => 'Результат',
            'schema' => [
                'type' => 'object',
                'required' => [
                    'success',
                    'data'
                ],
                'properties' => [
                    'success' => [
                        'type' => 'boolean',
                        'default' => true
                    ],
                    'data' => [
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/definitions/ContractModel'
                        ]
                    ]
                ]
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