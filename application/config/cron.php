<?php defined('SYSPATH') or die('No direct script access');
return [
    ['* * * * *', '/cron/sender'],
    ['*/10 * * * *', '/cron/unlock-queue'],
    ['0 * * * *', '/cron/check-sms-status'],
    //['20,50 * * * *', '/scripts/shell/transactions.php'],
];