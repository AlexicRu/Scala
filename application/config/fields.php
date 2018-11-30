<?php defined('SYSPATH') or die('No direct script access');

return [
    'card_available_choose_single' => [
        'url'       => '/help/list-cards-available'
    ],
    'tube_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-tube'
    ],
    'tube_choose_single' => [
        'url'       => '/help/list-tube'
    ],
    'card_group_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-card-group'
    ],
    'card_group_srv_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-card-group?group_type=' . Model_Card::CARD_GROUP_TYPE_SYSTEM
    ],
    'client_choose_single' => [
        'url'       => '/help/list-client'
    ],
    'contract_tariffs' => [
        'url'       => '/help/list-contract-tariffs'
    ],
    'country_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-country'
    ],
    'manager_choose_single' => [
        'url'       => '/help/list-manager'
    ],
    'pos_group_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-pos-group'
    ],
    'pos_group_choose_single' => [
        'url'       => '/help/list-pos-group'
    ],
    'sale_manager_choose_single' => [
        'url'       => '/help/list-manager-sale'
    ],
    'service_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-service'
    ],
    'service_choose_single' => [
        'url'       => '/help/list-service'
    ],
    'supplier_choose_multi' => [
        'multi'     => true,
        'url'       => '/help/list-supplier'
    ],
    'supplier_choose_single' => [
        'url'       => '/help/list-supplier'
    ],
    'contract_choose_single' => [
        'url'               => '/help/list-clients-contracts',
        'placeholder'       => 'Договор',
        'depend_on'         => [
            'field'         => 'client_choose_single',
            'param'         => 'client_id',
            'placeholder'   => 'Клиент',
        ]
    ],
    'contract_choose_multi' => [
        'multi'             => true,
        'url'               => '/help/list-clients-contracts',
        'placeholder'       => 'Договор',
        'depend_on'         => [
            'field'         => 'client_choose_single',
            'param'         => 'client_id',
            'placeholder'   => 'Клиент',
        ]
    ],
    'card_choose_multi' => [
        'multi'             => true,
        'url'               => '/help/list-card',
        'placeholder'       => 'Карта',
        'depend_on'         => [
            'field'         => 'contract_choose_single',
            'param'         => 'contract_id',
            'placeholder'   => 'Договор',
        ]
    ],
    'card_choose_single' => [
        'url'               => '/help/list-card',
        'placeholder'       => 'Карта',
        'depend_on'         => [
            'field'         => 'contract_choose_single',
            'param'         => 'contract_id',
            'placeholder'   => 'Договор',
        ]
    ],
];