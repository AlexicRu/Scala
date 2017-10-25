<?php defined('SYSPATH') or die('No direct script access');

return array(
    'clients'           => ['title' => 'Фирмы', 'icon' => 'icon-clients'],
    'suppliers'         => ['title' => 'Поставщики', 'icon' => 'icon-drop'],
    'reports'           => ['title' => 'Отчетность', 'icon' => 'icon-reports'],
    'control'           => ['title' => 'Управление', 'icon' => 'icon-set', 'children' => [
        'managers'      => 'Менеджеры',
        'dots'          => 'Точки обслуживания',
        'tariffs'       => 'Тарифы',
        'connect_1c'    => 'Связь с 1С',
        'cards_groups'  => 'Группы карт',
        'firms_groups'  => 'Группы фирм',
    ]],
    'references'        => ['title' => 'Справочники', 'icon' => 'icon-contract', 'children' => [
        'sources'       => 'Источники данных',
        'addresses'     => 'Адресный справочник',
        'currency'      => 'Валюты',
        'services'      => 'Услуги',
        'cards'         => 'Список карт'
    ]],
    'administration'    => ['title' => 'Сервис', 'icon' => 'icon-service', 'children' => [
        'transactions'  => 'Транзакции',
        'calc_tariffs'  => 'Расчет тарифов'
    ]],
    'news'              => ['title' => 'Новости', 'icon' => 'icon-news'],
    'support'           => ['title' => 'Поддержка', 'icon' => 'icon-question'],
);