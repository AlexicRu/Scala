<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Suppliers extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Поставщики';
	}

	public function action_index()
	{

	}

    /**
     * аяксово грузим поставщиков
     */
    public function action_suppliers_list()
    {
        $params = [
            'offset' 		    => $this->request->post('offset'),
            'pagination'        => true
        ];

        list($dots, $more) = Model_Supplier::getList($params);

        if(empty($dots)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $dots, 'more' => $more]);
    }

    /**
     * Детальная страница по поставщику
     */
    public function action_supplier_detail()
    {
        $supplierId = $this->request->param('id');

        $supplier = Model_Supplier::getSupplier($supplierId);

        if (empty($supplier)) {
            throw new HTTP_Exception_404();
        }

        $supplierContracts = Model_Supplier_Contract::getList(['supplier_id' => $supplierId]);

        $this->title[] = $supplier['SUPPLIER_NAME'];

        $this->_initDropZone();

        $popupSupplierContractAdd = Common::popupForm('Добавление нового договора', 'supplier/contract/add');

        $this->tpl
            ->bind('supplier', $supplier)
            ->bind('supplierContracts', $supplierContracts)
            ->bind('popupSupplierContractAdd', $popupSupplierContractAdd)
        ;
    }

    /**
     * редактирование поставщика
     */
    public function action_supplier_edit()
    {
        $supplierId = $this->request->param('id');
        $params = $this->request->post('params');

        $result = Model_Supplier::editSupplier($supplierId, $params);

        if(empty($result)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true, $result);
    }

    /**
     * грузим контракт
     */
    public function action_contract()
    {
        $contractId = $this->request->param('id');

        if($contractId == 0){
            $this->html('<div class="error_block">Контракты отсутствуют</div>');
        }

        $tab = $this->request->post('tab');
        $contract = Model_Supplier_Contract::get($contractId);
        $tubes = Model_Supplier_Contract::getTubes();

        switch($tab) {
            case 'contract':

                $content = View::factory('ajax/suppliers/contract/contract')
                    ->bind('contract', $contract)
                    ->bind('tubes', $tubes)
                ;
                break;
            case 'agreements':

                $content = View::factory('ajax/suppliers/contract/agreements')
                    ->bind('contract', $contract)
                ;
                break;
        }

        $tabs = [
            'contract' => [
                'name' => 'Договор',
                'icon' => 'icon-contract',
            ],
            'agreements'    => [
                'name' => 'Соглашения',
                'icon' => 'icon-reports',
            ]
        ];

        $html = View::factory('ajax/suppliers/contract/_tabs')
            ->bind('content', $content)
            ->bind('balance', $balance)
            ->bind('tabActive', $tab)
            ->bind('tabs', $tabs)
        ;

        $this->html($html);
    }

    /**
     * редактирование контракта
     */
    public function action_contract_edit()
    {
        $contractId = $this->request->param('id');
        $params = $this->request->post('params');

        list($result, $error) = Model_Supplier_Contract::edit($contractId, $params);

        if(empty($result)){
            $this->jsonResult(false, $error);
        }
        $this->jsonResult(true);
    }

    /**
     * добавление контракта
     */
    public function action_contract_add()
    {
        $params = $this->request->post('params');

        list($result, $error) = Model_Supplier_Contract::addContract($params);

        if(empty($result)){
            $this->jsonResult(false, $error);
        }

        $this->jsonResult(true);
    }
}
