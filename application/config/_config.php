<?php defined('SYSPATH') or die('No direct script access');

return [
    'salt'              => '__REPLACE__',
    'sentry_dsn'        => '__REPLACE__',
    'sentry_release'    => '__REPLACE__',
    'redmine_key'       => '__REPLACE__',
    'recaptcha_public'  => '__REPLACE__',
    'recaptcha_secret'  => '__REPLACE__',
    'opengraph'         => '__REPLACE__',
    'sms'               => [
        '__PROVIDER__'  => ['__PARAMS__'],
    ],
    'telegram'          => [
        '__REPLACE__'   => [ //bot name
            'name'      => '__REPLACE__',
            'token'     => '__REPLACE__',
            'web_hook'  => '__REPLACE__',
            'debug'     => false
        ]
    ],
];