<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Help extends Controller_Common
{
    protected $_search;
    protected $_ids;
    protected $_user;

    public function before()
    {
        parent::before();

        $this->_search = $this->request->post('search');
        $this->_ids = $this->request->post('ids');

        if(!empty($this->_ids)){
            $this->_ids = explode(',', $this->_ids);
        }

        $this->_user = Auth::instance()->get_user();
    }

    /**
     * получаем список точек для combobox
     */
    public function action_list_pos_group()
    {
        $res = Model_Dot::getGroups(['search' => $this->_search, 'ids' => $this->_ids]);

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
        $res = Listing::getCountries($this->_search, $this->_ids);

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
        $res = Listing::getServices(['search' => $this->_search, 'ids' => $this->_ids]);

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
        $res = Listing::getCards($this->_search, $this->_ids);

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
        $res = Model_Client::getClientsList($this->_search, ['ids' => $this->_ids]);

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
        $res = Listing::getSuppliers($this->_search, $this->_ids);

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
        $res = Model_Manager::getManagersList([
            'search' => $this->_search,
            'only_managers' => true,
            'agent_id' => $this->_user['AGENT_ID'],
            'manager_id' => $this->_ids
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
        $res = Model_Manager::getManagersList([
            'search' => $this->_search,
            'role_id' => [
                Access::ROLE_MANAGER_SALE,
                Access::ROLE_MANAGER_SALE_SUPPORT,
            ],
            'agent_id' => $this->_user['AGENT_ID'],
            'manager_id' => $this->_ids
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