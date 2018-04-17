<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Help extends Controller_Common
{
    protected $_search;
    protected $_ids;
    protected $_params;
    protected $_user;

    public function before()
    {
        parent::before();

        $this->_params = $this->request->post('params');
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
    public function action_listCardGroup()
    {
        $params = [
            'group_type'    => $this->request->query('group_type'),
            'search'        => $this->_search,
            'ids'           => $this->_ids,
            'limit'         => 10,
        ];
        $res = Model_Card::getGroups($params);

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
     * получаем список точек для combobox
     */
    public function action_listPosGroup()
    {
        $params = [
            'search'        => $this->_search,
            'ids'           => $this->_ids,
            'limit'         => 10,
            'group_type'    => !empty($this->_params['group_type']) ? $this->_params['group_type'] : false
        ];
        $res = Model_Dot::getGroups($params);

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
    public function action_listCountry()
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
     * получаем список услуг для combobox
     */
    public function action_listService()
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
     * получаем список карт для combobox
     */
    public function action_listCard()
    {
        $contractId = $this->request->post('contract_id');

        $res = Listing::getCards([
            'search'        => $this->_search,
            'contract_id'   => $contractId
        ], $this->_ids);

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
     * получаем список доступных карт для combobox
     */
    public function action_listCardsAvailable()
    {
        $res = Listing::getCardsAvailable($this->_search, $this->_ids);

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
    public function action_listClient()
    {
        $res = Model_Manager::getClientsList(['search' => $this->_search, 'ids' => $this->_ids, 'limit' => 10]);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['CLIENT_NAME'],
                'value' => $item['CLIENT_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_listSupplier()
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
    public function action_listManager()
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
    public function action_listManagerSale()
    {
        $res = Model_Manager::getManagersList([
            'search' => $this->_search,
            'role_id' => [
                Access::ROLE_MANAGER_SALE,
                Access::ROLE_MANAGER_SALE_SUPPORT,
            ],
            'agent_id' => $this->_user['AGENT_ID'],
            'manager_id' => $this->_ids,
            'limit' => 10
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
     * получаем список контрактов клиентов для combobox
     * _depend
     */
    public function action_listClientsContracts()
    {
        $clientId = $this->request->post('client_id');

        if (!empty($this->_params['client_id'])) {
            $clientId = $this->_params['client_id'];
        }

        if(empty($clientId) && empty($this->_ids)){
            $this->jsonResult(false);
        }

        $res = Model_Contract::getContracts(
            $clientId,
            [
                'search' => $this->_search,
                'contract_id' => $this->_ids,
                'limit' => 10,
            ]
        );

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['CONTRACT_NAME'],
                'value' => $item['CONTRACT_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список контрактов поставщика для combobox
     * _depend
     */
    public function action_listSuppliersContracts()
    {
        $supplierId = $this->request->post('supplier_id');

        $res = Listing::getSuppliersContracts($supplierId, $this->_search);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['CONTRACT_NAME'],
                'value' => $item['CONTRACT_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список доступных тарифов
     */
    public function action_listContractTariffs()
    {
        $contractTariffs = Model_Contract::getTariffs([
            'tarif_name' => $this->_search,
            'ids'        => $this->_ids,
            'limit'      => 10
        ]);

        if (empty($contractTariffs)) {
            $this->jsonResult(false);
        }

        $return = [];

        foreach($contractTariffs as $item){
            $return[] = [
                'name' => $item['TARIF_NAME'],
                'value' => $item['ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }

    /**
     * получаем список клиентов для combobox
     */
    public function action_listTube()
    {
        $res = Listing::getTubes($this->_search, $this->_ids);

        if(empty($res)){
            $this->jsonResult(false);
        }

        $return = [];

        foreach($res as $item){
            $return[] = [
                'name' => $item['TUBE_NAME'],
                'value' => $item['TUBE_ID'],
            ];
        }

        $this->jsonResult(true, $return);
    }
}
