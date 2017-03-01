<?php defined('SYSPATH') or die('No direct script access.');

class Model_Transaction extends Model
{
    /**
     * получаем плохие транзакции по заданным параметрам
     *
     * @param $params
     * @return mixed
     */
    public static function getTransactionsErrors($params)
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_TRANSACTION_ERROR where agent_id = ".$user['AGENT_ID']." 
        ";

        $sql .= " order by datetime_trn ";

        return $db->pagination($sql, $params);
    }

    /**
     * получаем историю транзакций по заданным параметрам
     *
     * @param $params
     * @return mixed
     */
    public static function getTransactionsHistory($params)
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_LOG_FILES_IMPORT where agent_id = ".$user['AGENT_ID']." 
        ";

        $sql .= " order by datetime_process desc ";

        return $db->pagination($sql, $params);
    }
}