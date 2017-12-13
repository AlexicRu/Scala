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

        if (!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
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

        $sql = (new Builder())->select()
            ->from('V_WEB_LOG_FILES_IMPORT t')
            ->where("t.agent_id = ".$user['AGENT_ID'])
            ->orderBy('t.datetime_process desc')
        ;

        if (!empty($params['rnum'])) {
            $sql->where('t.RNUM in ('. implode(',', $params['rnum']) .')');
        }

        if (!empty($params['filter'])) {
            foreach ($params['filter'] as $key => $value) {
                if (!empty($value)) {
                    $sql->where("upper(t." . $key . ") like " . mb_strtoupper(Oracle::quote('%'.$value.'%')));
                }
            }
        }

        if (!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
    }

    /**
     * получение списка транзакций
     *
     * @param $contractId
     * @param array $select
     * @return array|bool
     */
    public static function getTransactions($contractId, $params = [], $select = [])
    {
        if (empty($contractId)) {
            return false;
        }

        $sql = (new Builder())->select()
            ->from('V_API_TRANSACTION')
            ->where('contract_id = ' . (int)$contractId)
        ;

        if (!empty($params['date_from'])) {
            $sql->where('DATE_TRN >= '. Oracle::toDateOracle($params['date_from'], 'd.m.Y'));
        }

        if (!empty($params['date_to'])) {
            $sql->where('DATE_TRN <= '. Oracle::toDateOracle($params['date_to'], 'd.m.Y'));
        }

        if (!empty($select)) {
            $sql->select($select);
        }

        return Oracle::init()->query($sql);
    }
}