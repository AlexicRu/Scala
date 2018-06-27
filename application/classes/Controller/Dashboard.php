<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dashboard extends Controller_Common {

	public function action_index()
	{
	    //график
        $this->_initChart();
	}

    public function action_agent()
    {
        //график
        $this->_initChart();
    }

    /**
     * Реализация по агентам
     */
	public function action_getRealizationByAgents()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByAgentsFull($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_agents')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по агентам (Количество карт)
     */
    public function action_getRealizationByAgentsCardsCount()
    {
        $data = Dashboard::realizationByAgentsCardsCount();

        $html = View::factory('ajax/dashboard/realization_by_agents_cards_count')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по агентам (график)
     */
    public function action_getRealizationByAgentsGraph()
    {
        $graph = Dashboard::realizationByAgentsGraph();

        $data = [];
        $agents = [];

        foreach ($graph as $item) {
            $dt = $item['DATE_MONTH'].'.'.$item['DATE_YEAR'];

            if(empty($data[$dt])) {
                $data[$dt] = [
                    'date' => $dt
                ];
            }
            $data[$dt]['agent' . $item['AGENT_ID']] = $item['SUMM'];
            $agents[$item['AGENT_ID']] = [
                'agent_id'  => $item['AGENT_ID'],
                'label'     => $item['WEB_NAME'],
            ];
        }

        $this->jsonResult(true, [
            'agents' => array_values($agents),
            'data'   => array_values($data)
        ]);
    }

    /**
     * Реализация по клиентам
     */
    public function action_getRealizationByClients()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByClients($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_clients')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по клиентам (график)
     */
    public function action_getRealizationByClientsGraph()
    {
        $graph = Dashboard::realizationByClientsGraph();

        foreach ($graph as &$item) {
            $item['date'] = $item['DATE_MONTH'].'.'.$item['DATE_YEAR'];
        }

        $this->jsonResult(true, $graph);
    }

    /**
     * Реализация по агентам в разрезе номенклатур
     */
    public function action_getRealizationByAgentsNomenclature()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByAgentsNomenclature($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_agents_nomenclature')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по агентам в разрезе номенклатур (график)
     */
    public function action_getRealizationByAgentsNomenclatureGraph()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByAgentsNomenclature($date->format('01.m.Y'));

        $this->jsonResult(true, $data);
    }

    /**
     * Реализация по агентам средняя скидка (график)
     */
    public function action_getRealizationByAgentsAvgDiscountGraph()
    {
        $graph = Dashboard::realizationByAgentsAvgDiscountGraph();

        $data = [];
        $agents = [];

        foreach ($graph as $item) {
            $dt = $item['DATE_MONTH'].'.'.$item['DATE_YEAR'];

            if(empty($data[$dt])) {
                $data[$dt] = [
                    'date' => $dt
                ];
            }
            $data[$dt]['agent' . $item['AGENT_ID']] = $item['AVG_DISCOUNT'];
            $agents[$item['AGENT_ID']] = [
                'agent_id'  => $item['AGENT_ID'],
                'label'     => $item['WEB_NAME'],
            ];
        }

        $this->jsonResult(true, [
            'agents' => array_values($agents),
            'data'   => array_values($data)
        ]);
    }

    /**
     * Реализация по клиентам в разрезе номенклатур (график)
     */
    public function action_getRealizationByClientsNomenclatureGraph()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByClientsNomenclature($date->format('01.m.Y'));

        $this->jsonResult(true, $data);
    }

    /**
     * Реализация по клиентам в разрезе номенклатур
     */
    public function action_getRealizationByClientsNomenclature()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByClientsNomenclature($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_clients_nomenclature')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по слиентам оплаты
     */
    public function action_getRealizationByClientsPayments()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByClientsPayments($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_clients_payments')
            ->bind('data', $data)
        ;

        $this->html($html);
    }

    /**
     * Реализация по слиентам сводная таблица
     */
    public function action_getRealizationByClientsSummary()
    {
        $date = $this->request->post('date');

        $date = DateTime::createFromFormat('d.m.Y', $date);

        $data = Dashboard::realizationByClientsSummary($date->format('01.m.Y'));

        $html = View::factory('ajax/dashboard/realization_by_clients_summary')
            ->bind('data', $data)
        ;

        $this->html($html);
    }
}
