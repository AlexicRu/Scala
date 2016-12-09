<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Help extends Controller_Common
{
    /**
     * получаем список точек для combobox
     */
    public function action_list_pos_group()
    {
        $search = $this->request->post('search');

        $res = Model_Dot::getGroups(['search' => $search]);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['GROUP_NAME'],
                'value' => $item['GROUP_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список стран для combobox
     */
    public function action_list_country()
    {
        $search = $this->request->post('search');

        $res = Listing::getCountries($search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['NAME_RU'],
                'value' => $item['ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список стран для combobox
     */
    public function action_list_service()
    {
        $search = $this->request->post('search');

        $res = Listing::getServices($search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['LONG_DESC'],
                'value' => $item['SERVICE_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список стран для combobox
     */
    public function action_list_card()
    {
        $search = $this->request->post('search');

        $res = Listing::getCards($search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['CARD_ID'],
                'value' => $item['CARD_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_list_client()
    {
        $search = $this->request->post('search');

        $res = Model_Client::getClientsList($search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['LONG_NAME'],
                'value' => $item['CLIENT_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_list_supplier()
    {
        $search = $this->request->post('search');

        $res = Listing::getSuppliers($search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['SUPPLIER_NAME'],
                'value' => $item['ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_list_manager()
    {
        $search = $this->request->post('search');

        $res = Model_Manager::getManagersList([
            'search' => $search,
            'only_managers' => true
        ]);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['M_NAME'],
                'value' => $item['MANAGER_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_list_manager_sale()
    {
        $search = $this->request->post('search');

        $res = Model_Manager::getManagersList([
            'search' => $search,
            'role_id' => [
                Access::ROLE_MANAGER_SALE,
                Access::ROLE_MANAGER_SALE_SUPPORT,
            ]
        ]);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['M_NAME'],
                'value' => $item['MANAGER_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }
}
