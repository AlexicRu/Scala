<?php defined('SYSPATH') or die('No direct script access.');

class Model_Transaction extends Model
{
    /**
     * получаем список транзакций
     *
     * @param $cardId
     * @param $contractId
     * @param array $params
     * @return array|bool
     */
    public static function getList($cardId, $contractId, $params = [])
    {
        $sql = (new Builder())->select()
            ->from('v_rep_transaction')
            ->where('card_id = ' . Oracle::quote($cardId))
            ->where('contract_id = ' . (int)$contractId)
            ->columns([
                "to_char(date_trn, 'dd.mm.yyyy') as date_trn_formatted",
                "date_trn",
                "time_trn",
                "pos_petrol_name",
                "pos_address",
                "long_desc",
                "service_amount",
                "sumprice_discount",
            ])
            ->orderBy([
                'date_trn desc',
                'time_trn desc'
            ])
        ;

        if (!empty($params['limit'])) {
            $sql->limit($params['limit']);
        }

        return Oracle::init()->query($sql);
    }

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
     * получаем транзакции (в процессе) по заданным параметрам
     *
     * @param $params
     * @return mixed
     */
    public static function getTransactionsProcess($params)
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('v_web_transaction_process')
            ->where("agent_id = ".$user['AGENT_ID'])
            ->orderBy('datetime_trn')
        ;

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