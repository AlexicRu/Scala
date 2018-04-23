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
        'clients_card-toggle' => [
            Access::ROLE_MANAGER,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE_SUPPORT,
        ],
        'client_cabinet-create' => [
            Access::ROLE_ADMIN,
            Access::ROLE_MANAGER,
            Access::ROLE_SUPERVISOR,
        ],
        'control_index' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_CLIENT,
            Access::ROLE_USER_SECOND,
            Access::ROLE_USER,
            'u_7',
        ],
        'clients_card-withdraw' => [
            Access::ROLE_MANAGER,
            Access::ROLE_MANAGER_SALE_SUPPORT,
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR
        ],
        'clients_bill-add' => [
            'a_1',
            'a_2',
            'a_6',
            'a_10',
            'a_15',
            'a_4',
			'a_16',
			'a_22',
        ],
        'news_news-edit' => [
            Access::ROLE_ADMIN,
        ],
        'clients_bill-print' => [
            'a_1',
            'a_2',
            'a_6',
            'a_10',
            'a_15',
			'a_16',
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
        'control_connect-1c' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'administration_index' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_index' => [
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
            Access::ROLE_SUPERVISOR,
        ],
        'suppliers_supplier-add' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_supplier-edit' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_contract-add' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_contract-edit' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_supplier-etail' => [
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
            Access::ROLE_SUPERVISOR,
        ],
        'suppliers_agreement-add' => [
            Access::ROLE_ADMIN,
        ],
        'suppliers_agreement-edit' => [
            Access::ROLE_ADMIN,
        ],
        'clients_edit-login' => [
            Access::ROLE_ADMIN,
        ],
        'managers_edit-manager-clients-contract-binds' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'references_index' => [
            Access::ROLE_ADMIN,
        ],
        'references_sources' => [
            Access::ROLE_ADMIN,
        ],
        'references_addresses' => [
            Access::ROLE_ROOT
        ],
        'references_currency' => [
            Access::ROLE_ROOT
        ],
        'references_converter' => [
            Access::ROLE_ROOT
        ],
        'clients_contract-limits-edit' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_MANAGER,
        ],
        'control_firms-groups' => [
            Access::ROLE_ROOT
        ],
        'administration_calc-tariffs' => [
            Access::ROLE_ROOT
        ],
        'control_dots-groups' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'control_1c-connect' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
        ],
        'control_1c-export' => [
            'a_1',
            'a_2',
            'a_4',
            'a_6',
            'a_14',
            'a_16',
            'a_18',
        ],
        'managers_load-reports' => [
            Access::ROLE_ROOT
        ],
        'administration_cards-transfer' => [
            Access::ROLE_ADMIN
        ],
        'system_index' => [
            Access::ROLE_ROOT,
        ],
        'system_deploy' => [
            Access::ROLE_ROOT,
        ],
        'system_db' => [
            Access::ROLE_ROOT,
        ],
        'system_query' => [
            Access::ROLE_ROOT,
        ],
        'system_version-refresh' => [
            Access::ROLE_ROOT,
        ],
        'system_gulp' => [
            Access::ROLE_ROOT,
        ],
        'system_git' => [
            Access::ROLE_ROOT,
        ],
        'system_full' => [
            Access::ROLE_ROOT,
        ],
        // custom
        'view_contract_balances' => [
			Access::ROLE_SUPERVISOR,
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
			Access::ROLE_MANAGER,
        ],
        'view_penalties' => [
            Access::ROLE_ROOT,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_MANAGER_SALE_SUPPORT,
            Access::ROLE_MANAGER,
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
            Access::ROLE_SUPERVISOR,
        ],
        'view_balance_sheet' => [
            Access::ROLE_ROOT
        ],
        'download_bill_as_xls' => [
            Access::ROLE_ROOT,
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_MANAGER,
            Access::ROLE_MANAGER_SALE_SUPPORT,
        ],
        'view_goods_receiver' => [
            'a_14',
            'a_16',
            'a_17',
            'a_10',
        ],
        'edit_client_full' => [
            Access::ROLE_ADMIN,
            Access::ROLE_SUPERVISOR,
            Access::ROLE_MANAGER_SALE_SUPPORT
        ],
        'view_supplier_contract_group_dots' => [
            Access::ROLE_ADMIN,
            Access::ROLE_ADMIN_READONLY,
            Access::ROLE_SUPERVISOR,
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
            Access::ROLE_CLIENT,
        ],
        'clients_client-add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
            Access::ROLE_CLIENT
        ],
        'clients_contract-add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_contract-edit' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_card-add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_card-edit-limits' => [
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_payment-add' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_payment-del' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_MANAGER_SALE,
        ],
        'reports_index' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_CLIENT,
        ],
        'manager_setting' => [
            Access::ROLE_MANAGER_SALE,
        ],
        'clients_client-edit' => [
            Access::ROLE_CLIENT,
        ],
        'support_index' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
            Access::ROLE_CLIENT,
        ],
        'suppliers_supplier-add' => [
            'a_10',
        ],
        // custom
        'clients_card_toggle_full' => [
            Access::ROLE_CLIENT,
        ],
        'view_tariffs' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        'view_penalties_overdrafts' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        'client_add_bill_add_product' => [
            Access::ROLE_USER,
            Access::ROLE_USER_SECOND,
        ],
        'view_payment_block' => [
            Access::ROLE_CLIENT
        ]
    ],
    /*
     * функции без авторизации
     */
    'without_auth' => [
        'index_login',
        'index_logout',
        'index_force-login',
        'index_get-json',
    ],
    /*
     * данные роли имеют доступ только на чтение, но указанные процедуры выполнять можно
     */
    'skip_readonly' => [
        Access::ROLE_CLIENT => [
            'auth_user',
            'notification_change_status',
            'ctrl_card_group_add',
            'ctrl_card_group_collection',
            'ctrl_card_group_edit',
            'client_contract_notify_config',
            'ctrl_manager_change_password',
            'ctrl_manager_edit',
        ],
        Access::ROLE_ADMIN_READONLY => [
            'auth_user',
            'notification_change_status',
            'ctrl_manager_change_password',
            'ctrl_manager_edit',
        ]
    ],
    /*
     * доступы к скачиванию файлов
     */
    'files' => [
        'Инструкция_по_работе_с_ЛК_системы_Администратор.docx' => [
            Access::ROLE_ADMIN
        ]
    ]
];