<?php defined('SYSPATH') or die('No direct script access.');

class Dashboard
{
    /**
     * Реализация по агентам
     */
    public static function realizationByAgents($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select()
            ->from('V_WEB_DASH_AGENTS t')
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->whereIn('t.agent_id', $sqlSub)
            ->orderBy('upper(t.web_name)')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по агентам (график)
     */
    public static function realizationByAgentsGraph()
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            't.agent_id',
            't.web_name',
            't.date_month',
            't.date_year',
            'sum(t.service_amount) as summ',
        ])
            ->from('V_WEB_DASH_REALIZ t')
            ->whereIn('t.agent_id', $sqlSub)
            ->groupBy([
                't.agent_id',
                't.web_name',
                't.date_month',
                't.date_year',
                't.month_of_date',
            ])
            ->orderBy('t.month_of_date')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по клиентам
     */
    public static function realizationByClients($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.contract_id'
        ])
            ->from('v_web_manager_contracts v')
            ->where('v.manager_id = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            'count(distinct t.client_id) as cnt_clients',
            'count(distinct t.contract_id) as cnt_contracts',
            'sum(t.litres) as service_amount',
            'sum(t.sale) as sumprice_discount'
        ])
            ->from('V_WEB_DASH_CLIENTS t')
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->whereIn('t.contract_id', $sqlSub)
        ;

        return Oracle::init()->row($sql);
    }

    /**
     * Реализация по клиентам (график)
     */
    public static function realizationByClientsGraph()
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.contract_id'
        ])
            ->from('v_web_manager_contracts v')
            ->where('v.manager_id = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            't.date_month',
            't.date_year',
            'sum(t.litres) as litres',
            'sum(t.sale) as sale',
        ])
            ->from('V_WEB_DASH_CLIENTS t')
            ->whereIn('t.contract_id', $sqlSub)
            ->groupBy([
                't.month_of_date',
                't.date_month',
                't.date_year',
            ])
            ->orderBy('t.month_of_date')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по агентам в разрезе номенклатур
     */
    public static function realizationByAgentsNomenclature($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            't.long_desc',
            'sum(t.service_amount) as service_amount',
            'sum(t.service_sumprice) as service_sumprice',
            'sum(t.sumprice_discount) as sumprice_discount',
        ])
            ->from('V_WEB_DASH_REALIZ t')
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->whereIn('t.agent_id ', $sqlSub)
            ->groupBy('t.long_desc')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по клиентам в разрезе номенклатур
     */
    public static function realizationByClientsNomenclature($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.contract_id'
        ])
            ->from('v_web_manager_contracts v')
            ->where('v.manager_id = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            't.long_desc',
            'sum(t.service_amount) as service_amount',
            'sum(t.sumprice_discount) as sumprice_discount',
        ])
            ->from('V_WEB_DASH_REALIZ t')
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->whereIn('t.contract_id ', $sqlSub)
            ->groupBy('t.long_desc')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по агентам средняя скидка (график)
     */
    public static function realizationByAgentsAvgDiscountGraph()
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            't.agent_id',
            't.web_name',
            't.date_month',
            't.date_year',
            'round(avg((1 - t.sumprice_discount / t.service_sumprice) * 100),5) as avg_discount',
        ])
            ->from('V_WEB_DASH_REALIZ t')
            ->whereIn('t.agent_id', $sqlSub)
            ->where('t.service_sumprice <> 0')
            ->groupBy([
                't.agent_id',
                't.web_name',
                't.date_month',
                't.date_year',
                't.month_of_date',
            ])
            ->orderBy('t.month_of_date')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по клиентам оплаты
     */
    public static function realizationByClientsPayments($date)
    {
        $user = User::current();

        $sql = (new Builder())->select([
            'sum(t.sumpay) as sumpay',
        ])
            ->from('V_WEB_DASH_CLIENTS_PAYS t')
            ->where('t.manager_id = ' . (int)$user['MANAGER_ID'])
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
        ;

        return Oracle::init()->row($sql);
    }

    /**
     * Реализация по клиентам сводная таблица
     */
    public static function realizationByClientsSummary($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.contract_id'
        ])
            ->from('v_web_manager_contracts v')
            ->where('v.manager_id = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select([
            'sum(t.service_amount) as service_amount',
            'sum(t.service_sumprice) as service_sumprice',
            'sum(t.sumprice_discount) as sumprice_discount',
            'sum(t.sumprice_buy) as sumprice_buy',
            'sum(t.sumprice_discount - t.sumprice_buy) as marginality',
            'round(avg((1 - t.sumprice_discount / t.service_sumprice) * 100), 5) as avg_discount',
        ])
            ->from('V_WEB_DASH_REALIZ t')
            ->where('t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->where('t.service_sumprice <> 0')
            ->whereIn('t.contract_id ', $sqlSub)
        ;

        return Oracle::init()->row($sql);
    }

    /**
     * Реализация по агентам (Количество карт)
     */
    public static function realizationByAgentsCardsCount()
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sql = (new Builder())->select()
            ->from('V_WEB_DASH_CARDS_COUNT t')
            ->whereIn('t.agent_id', $sqlSub)
            ->orderBy('upper(t.web_name)')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * Реализация по агентам (полные данные)
     */
    public static function realizationByAgentsFull($date)
    {
        $user = User::current();

        $sqlSub = (new Builder())->select([
            'v.agent_id'
        ])
            ->from('V_WEB_MANAGER_BINDS v')
            ->where('v.manager_to = ' . (int)$user['MANAGER_ID'])
            ->whereOr('v.manager_from = ' . (int)$user['MANAGER_ID'])
        ;

        $sqlJoin = (new Builder())->select([
            'c.agent_id',
            'count(1) as cl_count'
        ])
            ->from('V_WEB_CLIENTS_LIST c')
            ->groupBy('c.agent_id')
        ;

        $sql = (new Builder())->select([
            'a.agent_id',
            'a.web_name',
            'nvl(t.service_amount,0) as service_amount',
            'nvl(t.count_trz,0) as count_trz',
            'nvl(crc.all_cards,0) as all_cards',
            'nvl(crc.cards_in_work,0) as cards_in_work',
            'nvl(cl.cl_count,0) as cl_count',
        ])
            ->from('v_web_agents_list a')
            ->joinLeft(['cl' => $sqlJoin], 'a.agent_id = cl.agent_id')
            ->joinLeft('V_WEB_DASH_CARDS_COUNT crc', 'a.agent_id = crc.agent_id')
            ->joinLeft('V_WEB_DASH_AGENTS t', 'a.agent_id = t.agent_id and t.month_of_date = ' . Oracle::toDateOracle($date, Date::$dateFormatRu))
            ->whereIn('a.agent_id', $sqlSub)
            ->orderBy('upper(a.web_name)')
        ;

        return Oracle::init()->query($sql);
    }
}