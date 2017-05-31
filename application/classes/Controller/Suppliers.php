<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Suppliers extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Поставщики';
	}

	public function action_index()
	{
        $popupSupplierAdd = Common::popupForm('Добавление нового поставщика', 'supplier/add');

        $this->tpl
            ->bind('popupSupplierAdd', $popupSupplierAdd)
        ;
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

        list($suppliers, $more) = Model_Supplier::getList($params);

        if(empty($suppliers)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $suppliers, 'more' => $more]);
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
            $this->html('<div class="error_block">Договоры отсутствуют</div>');
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
                $agreements = Model_Supplier_Agreement::getList(['contract_id' => $contractId]);

                $popupAgreementAdd = Common::popupForm('Добавление нового соглашения', 'supplier/agreement/add');

                $content = View::factory('ajax/suppliers/contract/agreements')
                    ->bind('agreements', $agreements)
                    ->bind('popupAgreementAdd', $popupAgreementAdd)
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

    /**
     * создание нового поставщика
     */
    public function action_supplier_add()
    {
        $params = $this->request->post('params');

        $result = Model_Supplier::add($params);

        if(empty($result)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true);
    }

    /**
     * грузим соглашение по контракту поставщика
     */
    public function action_agreement()
    {
        $agreementId = $this->request->param('id');
        $contractId = $this->request->query('contract_id');

        $agreement = Model_Supplier_Agreement::get($agreementId, $contractId);

        if(empty($agreement)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $tariffs = Model_Tariff::getAvailableTariffs();

        $html = View::factory('ajax/suppliers/agreement')
            ->bind('agreement', $agreement)
            ->bind('tariffs', $tariffs)
        ;

        $this->html($html);
    }

    /**
     * редактирование соглашения
     */
    public function action_agreement_edit()
    {
        $params = $this->request->post('params');

        $result = Model_Supplier_Agreement::edit($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * добавление нового соглашения
     */
    public function action_agreement_add()
    {
        $params = $this->request->post('params');

        $result = Model_Supplier_Agreement::add($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }
}
