<?php defined('SYSPATH') or die('No direct script access');

/**
 * список действий и список ролей, у которых есть доступ
 *
 * у запрета приоритет
 *
 * controller_action - автоматом обработается до выполнения основного кода
 *
 * руту всегда можно
 */

return [
    'allow' => [ //для всех остальных ролей будет запрещено
        // functions
        'clients_card_toggle' => [
            Access::ROLE_MANAGER,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_MANAGER_SALE_SUPPORT,
        ],
        'client_cabinet_create' => [
            Access::ROLE_ADMIN,
            Access::ROLE_MANAGER,
            Access::ROLE_SUPERVISOR,
        ],
        'control_index' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            'u_7',
        ],

        'support_index' => [
            Access::ROLE_ROOT
        ],
        'clients_card_withdraw' => [
            Access::ROLE_MANAGER,
            Access::ROLE_MANAGER_SALE_SUPPORT,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR
        ],
        'clients_bill_add' => [
            'a_1',
            'a_2',
            'a_6',
            'a_10',
        ],
        'news_news_edit' => [
            Access::ROLE_ADMIN,
        ],
        'clients_bill_print' => [
            'a_1',
            'a_2',
            'a_6',
            'a_10',
        ],
        'control_tariffs' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            'u_7',
        ],
        'control_dots' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            'u_7',
        ],
        'control_connect_1c' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,

        ],
        'control_cards_groups' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'administration_index' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_index' => [
            Access::ROLE_ROOT,
        ],
        // custom
        'view_contract_balances' => [
            Access::ROLE_ROOT
        ],
        'view_penalties' => [
            Access::ROLE_ROOT,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_MANAGER_SALE_SUPPORT,
            Access::ROLE_MANAGER,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'view_balance_sheet' => [
            Access::ROLE_ROOT
        ],
        'download_bill_as_xls' => [
            Access::ROLE_ROOT,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_MANAGER,
            Access::ROLE_MANAGER_SALE_SUPPORT,
        ],
        'root' => [
            Access::ROLE_ROOT
        ]
    ],
    'deny' => [ //для всех остальных ролей будет разрешено
        // functions
        'control_managers' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_MANAGER_SALE_SUPPORT,
        ],
        'clients_client_add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_MANAGER,
        ],
        'clients_contract_add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_contract_edit' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_card_add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_card_edit_limits' => [
            Access::ROLE_USER_SECOND,
        ],
        'clients_payment_add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_payment_del' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'reports_index' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        // custom
        'view_tariffs' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        'edit_client_full' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        'view_penalties_overdrafts' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ]
    ]
];