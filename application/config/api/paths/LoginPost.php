<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 100,
    'url'   => '/login',
    'method' => 'post',
    'tags' => ['main'],
    'summary' => 'Авторизация',
    'operationId' => 'login',
    'consumes' => [
        'application/x-www-form-urlencoded'
    ],
    'parameters' => [
        [
            'name' => 'login',
            'in' => 'formData',
            'type' => 'string',
            'required' => true
        ],
        [
            'name' => 'password',
            'in' => 'formData',
            'type' => 'string',
            'required' => true
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
                        'type' => 'object',
                        'properties' => [
                            'token' => [
                                'type' => 'string',
                                'default' => (new Api)->getToken(User::current()['MANAGER_ID'])
                            ]
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