<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 200,
    'url'   => '/clients',
    'method' => 'get',
    'tags' => ['clients'],
    'summary' => 'Получение списка клиентов',
	'description' => 'Данный метод позволяет получить полный список клиентов, закрепленных за данной учетной записью',
    'operationId' => 'clients',
    'parameters' => [
        [
            'name' => 'token',
            'in' => 'header',
            'type' => 'string',
            'required' => true,
			'description' => 'Полученный при авторизации token',
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
                            '$ref' => '#/definitions/ClientModel'
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