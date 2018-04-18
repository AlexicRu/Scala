<?php defined('SYSPATH') or die('No direct script access');

return array(
    'clients'           => ['title' => 'Фирмы', 'icon' => 'icon-clients'],
    'suppliers'         => ['title' => 'Поставщики', 'icon' => 'icon-drop'],
    'reports'           => ['title' => 'Отчетность', 'icon' => 'icon-reports'],
    'control'           => ['title' => 'Управление', 'icon' => 'icon-set', 'children' => [
        'managers'      => 'Менеджеры',
        'tariffs'       => 'Тарифы',
        '1c-connect'    => 'Связь с 1С',
        'cards-groups'  => 'Группы карт',
        'firms-groups'  => 'Группы фирм',
        'dots-groups'   => 'Группы ТО',
    ]],
    'references'        => ['title' => 'Справочники', 'icon' => 'icon-contract', 'children' => [
        'sources'       => 'Источники данных',
        'addresses'     => 'Адреса',
        'currency'      => 'Валюты',
        'services'      => 'Услуги',
        'cards'         => 'Список карт',
        'dots'          => 'Точки обслуживания',
    ]],
    'administration'    => ['title' => 'Сервис', 'icon' => 'icon-service', 'children' => [
        'transactions'      => 'Транзакции',
        'calc-tariffs'      => 'Расчет тарифов',
        'cards-transfer'    => 'Перенос карт'
    ]],
    'news'              => ['title' => 'Новости', 'icon' => 'icon-news'],
    'support'           => ['title' => 'Поддержка', 'icon' => 'icon-question'],
    'system'            => ['title' => 'System', 'icon' => 'icon-loader', 'children' => [
        'deploy'        => 'Deploy',
        'db'            => 'DB',
    ]],
);