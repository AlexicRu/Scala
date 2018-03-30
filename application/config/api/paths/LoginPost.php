<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 100,
    'url'   => '/login',
    'method' => 'post',
    'tags' => ['main'],
    'summary' => 'Авторизация',
    'description' => User::loggedIn() ? 'В описании можели положительного ответа указан токен' : 'После авторизации обновите страницу для получения полной документации',
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
            'required' => true,
            'format' => 'password'
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
                                'default' => !empty(User::current()) ? (new Api)->getToken(User::current()['MANAGER_ID']) : false
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