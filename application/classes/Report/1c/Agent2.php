<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modul
 */
class Report_1c_Agent2 extends Report_1c_Common
{
    protected static $_agentId = 2;

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
                '(case
  when v.contract_id in (50, 51, 52, 83, 90) then 49 
  when v.contract_id in (1835, 1836) then 1834
  when v.contract_id in (2283, 2160, 2803, 2802, 2801, 3269, 3267, 3266) then 2127
  else v.contract_id end) as id',
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