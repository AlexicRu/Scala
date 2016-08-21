<?php defined('SYSPATH') or die('No direct script access');

return array(
    'clients'   => ['title' => 'Фирмы', 'icon' => 'icon-clients'],
    'control'   => ['title' => 'Управление', 'icon' => 'icon-set', 'children' => [
        'managers' => 'Менеджеры',
        'dots' => 'Точки обслуживания',
    ]],
    'reports'   => ['title' => 'Отчетность', 'icon' => 'icon-reports'],
    'news'      => ['title' => 'Новости', 'icon' => 'icon-news'],
    'support'   => ['title' => 'Поддержка', 'icon' => 'icon-question'],
);