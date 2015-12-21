<?php defined('SYSPATH') or die('No direct script access');

/**
 * список действий и список ролей, у которых есть доступ
 *
 * у запрета приоритет
 */

return [
    'allow' => [ //для всех остальных ролей будер запрещено
        // functions
        // custom
    ],
    'deny' => [ //для всех остальных ролей будет разрешено
        // functions
        'reports_index' => [
            Access::ROLE_USER,
        ],
        'clients_client_add' => [
            Access::ROLE_USER,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_contract_add' => [
            Access::ROLE_USER,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_contract_edit' => [
            Access::ROLE_USER,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_card_add' => [
            Access::ROLE_USER,
        ],
        'clients_card_edit' => [
            Access::ROLE_USER,
        ],
        'clients_payment_add' => [
            Access::ROLE_USER,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_payment_del' => [
            Access::ROLE_USER,
            Access::ROLE_MANAGER_SALE,
        ],
        'customer_settings' => [
            //Access::ROLE_USER,
        ],
        // custom
        'view_tariffs' => [
            Access::ROLE_USER,
        ],
        'edit_client_full' => [
            Access::ROLE_USER,
        ],
        'view_penalties_overdrafts' => [
            Access::ROLE_USER,
        ],
        'view_balance_sheet' => [
            Access::ROLE_USER
        ]
    ]
];