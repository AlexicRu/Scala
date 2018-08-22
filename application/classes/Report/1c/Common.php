<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Report_1c_Common
{
    protected static $_agentId = false;

    /**
     * @param integer $company
     * @return self
     * @throws HTTP_Exception_500
     */
    public static function factory($company)
    {
        $className = 'Report_1c_Agent' . ucfirst($company);

        if (!class_exists($className)) {
            throw new HTTP_Exception_500('Wrong report company');
        }

        return new $className();
    }

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
            ->joinLeft('v_web_pos_list pi', 'v.supplier_terminal = pi.pos_id and pi.agent_id = v.agent_id')
            ->where('v.date_trn >= ' . Oracle::toDateOracle($params['date_from'], 'd.m.Y'))
            ->where('v.date_trn <= ' . Oracle::toDateOracle($params['date_to'], 'd.m.Y'))
            ->where('v.agent_id = ' . static::$_agentId)
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

    /**
     * генерим xml для экспорта
     *
     * @param $data
     */
    public function generateXmlForExport($data)
    {
        //render xml
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></root>');

        $oldClient = false;
        foreach ($data as $item) {
            if ($oldClient != $item['ID']) {
                $oldClient = $item['ID'];
                $client = $xml->addChild('client');
                $client->addAttribute('id', $item['ID']);
                $client->addAttribute('name', $item['NAME']);

                if (!empty($item['CONTRACT_COMMENT'])) {
                    $client->addAttribute('contract_comment', $item['CONTRACT_COMMENT']);
                }
            }

            $delivery = $client->addChild('delivery');
            $delivery->addChild('territory_id', $item['TERRITORY_ID']);
            $delivery->addChild('supplier_id', $item['SUPPLIER_ID']);
            $delivery->addChild('unit_type', $item['UNIT_TYPE']);
            $delivery->addChild('vat_rate', $item['VAT_RATE']);
            $delivery->addChild('recharge_vat', $item['RECHARGE_VAT']);
            $delivery->addChild('volume', $item['VOLUME']);
            $delivery->addChild('cost', $item['COST']);
            $delivery->addChild('sale', $item['SALE']);
        }

        return $xml->asXML();
    }
}
