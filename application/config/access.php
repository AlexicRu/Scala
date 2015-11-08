<?php defined('SYSPATH') or die('No direct script access');

/**
 * список действий и список ролей, у которых есть доступ
 *
 * у запрета приоритет
 */

return [
    'allow' => [ //для всех остальных ролей будер запрещено

    ],
    'deny'  => [ //для всех остальных ролей будет разрешено
        'menu_reports' => [
            Access::ROLE_USER,
        ],
        'view_tariffs' => [
            Access::ROLE_USER,
        ],
        'add_client' => [
            Access::ROLE_USER,
        ],
        'edit_client_full' => [
            Access::ROLE_USER,
        ],
        'add_contract' => [
            Access::ROLE_USER,
        ],
        'edit_contract' => [
            Access::ROLE_USER,
        ],
        'add_card' => [
            Access::ROLE_USER,
        ],
        'edit_card' => [
            Access::ROLE_USER,
        ],
        'add_payment' => [
            Access::ROLE_USER,
        ],
        'del_payment' => [
            Access::ROLE_USER,
        ],
    ]
];