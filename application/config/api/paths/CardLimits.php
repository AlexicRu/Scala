<?php defined('SYSPATH') or die('No direct script access');

return  [
    'sort'  => 700,
    'url'   => '/card_limits',
    'get'   => [
        'deprecated' => true,
        'tags' => [
            'cards'
        ],
        'summary' => 'Получение лимитов карты',
        'operationId' => 'card_limits'
    ]
];