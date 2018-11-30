<?php defined('SYSPATH') or die('No direct script access.');

class Controller_References extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Справочники';
	}

	public function action_index()
	{
        $this->redirect('/references/sources');
	}

    /**
     * Источники данных
     */
	public function action_sources()
    {
        $this->title[] = 'Источники данных';

        $this->_initJsGrid();

        $tubesList = Model_Tube::getTubes();

        $this->tpl
            ->bind('tubesList', $tubesList)
        ;
    }

    /**
     * Адресный справочник
     */
    public function action_addresses()
    {
        $this->title[] = 'Адресный справочник';
    }

    /**
     * Валюты
     */
    public function action_currency()
    {
        $this->title[] = 'Валюты';
    }

    /**
     * Курсы валют
     */
    public function action_services()
    {
        $this->title[] = 'Услуги';

        $this->_initJsGrid();

        $tubesList = Model_Tube::getTubes(['is_owner' => 1]);
        $servicesList = Listing::getServices();

        $this->tpl
            ->bind('tubesList', $tubesList)
            ->bind('servicesList', $servicesList)
        ;
    }

    /**
     * грузим список услуг
     */
    public function action_serviceListLoad()
    {
        $tubeId = $this->request->post('tube_id');

        $servicesList = Model_Reference::getConverterServices(['tube_id' => $tubeId]);

        $this->jsonResult(1, $servicesList ?: []);
    }

    /**
     * добавление новой услуги
     */
    public function action_addConvertService()
    {
        $serviceId = $this->request->post('service_id');
        $tubeId = $this->request->post('tube_id');
        $name = $this->request->post('name');

        list($result, $error) = Model_Reference::addConvertService($serviceId, $tubeId, $name);

        if (empty($result)) {
            $this->jsonResult(0, $error);
        }
        $this->jsonResult(1);
    }

    /**
     * старт загрузки получения списка карт
     */
    public function action_cardListLoad()
    {
        $tubeId = $this->request->post('tube_id');

        list($result, $error) = Model_Tube::startCardListLoad($tubeId);

        if (empty($result)) {
            $this->jsonResult(0, $error);
        }
        $this->jsonResult(1);
    }

    /**
     * редактирование трубы
     */
    public function action_tubeNameEdit()
    {
        $tubeId = $this->request->post('tube_id');
        $name = $this->request->post('name');

        list($result, $error) = Model_Tube::editTubeName($tubeId, $name);

        if (empty($result)) {
            $this->jsonResult(0, $error);
        }
        $this->jsonResult(1);
    }

    /**
     * Список карт
     */
    public function action_cards()
    {
        $this->title[] = 'Список карт';

        $this->_initJsGrid();

        $cardsList = Model_Card::getDictionary();

        $this->tpl
            ->bind('cardsList', $cardsList)
        ;
    }

    /**
     * страницы точек
     */
    public function action_dots()
    {
        $this->title[] = 'Точки обслуживания';
    }
}
