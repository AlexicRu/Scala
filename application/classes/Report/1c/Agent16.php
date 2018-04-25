<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * OptTrade
 */
class Report_1c_Agent16 extends Report_1c_Common
{
    protected static $_agentId = 16;

    /**
     * достаем данные для экспорта в 1с
     *
     * @param $params
     * @return mixed
     */
    public function getDataForExport($params)
    {
        if (empty($params['date_from']) || empty($params['date_to'])) {
            return false;
        }

        $sql = (new Builder())
            ->select([
                'v.contract_id as id',
                'replace(v.client_name,\'"\', \'\') as name',
                'pi.country_id as territory_id',
                'v.supplier_contract as supplier_id',
                'v.service_id as unit_type',
                'decode(v.supplier_contract, 33, 18, decode(pi.country_id, 643, 18, 0)) as vat_rate',
                '0 as recharge_vat',
                'sum(v.service_amount) as volume',
                'sum(v.sumprice_buy) as cost',
                'sum(v.sumprice_discount) as sale'
            ])
            ->from('v_rep_transaction v')
            ->join('v_rep_transaction v2', 'v2.trn_key = v.link_key')
            ->joinLeft('v_web_pos_list pi', 'v.supplier_terminal = pi.pos_id and pi.agent_id = v.agent_id')
            ->where('v.date_trn >= ' . Oracle::toDateOracle($params['date_from'], 'd.m.Y'))
            ->where('v.date_trn <= ' . Oracle::toDateOracle($params['date_to'], 'd.m.Y'))
            ->where('v.agent_id = ' . self::$_agentId)
            ->groupBy([
                'v.contract_id',
                'v.client_name',
                'v.supplier_contract',
                'v.service_id',
                'pi.country_id',
            ])
            ->having('sum(v.service_amount) > 0')
            ->orderBy('v.client_name')
        ;

        if (!empty($params['contracts'])) {
            $contracts = array_map('intval', $params['contracts']);

            $sql->where('v.contract_id in (' . implode(',', $contracts) . ')');
        }

        return Oracle::init()->query($sql);
    }
}