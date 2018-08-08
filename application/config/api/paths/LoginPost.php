<?php defined('SYSPATH') or die('No direct script access');

return [
    'sort'  => 100,
    'url'   => '/login',
    'method' => 'post',
    'tags' => ['main'],
    'summary' => 'Авторизация пользователя',
    'description' => User::loggedIn() ? 'В описании модели положительного ответа указан действующий токен' : 'Для получения доступа к остальному функционалу нужна авторизация <br> После успешной аавторизации обновите страницу <br> После успешной авторизации будет указан токен, требуемый для последующих методов',
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