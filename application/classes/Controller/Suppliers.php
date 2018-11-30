<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Suppliers extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Поставщики';
	}

	public function action_index()
	{
        $popupSupplierAdd = Form::popup('Добавление нового поставщика', 'supplier/add');

        $this->tpl
            ->bind('popupSupplierAdd', $popupSupplierAdd)
        ;
	}

    /**
     * аяксово грузим поставщиков
     */
    public function action_suppliersList()
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
    public function action_supplierDetail()
    {
        $supplierId = $this->request->param('id');

        $supplier = Model_Supplier::getSupplier($supplierId);

        if (empty($supplier)) {
            throw new HTTP_Exception_404();
        }

        $this->_initVueJs();
        $this->_initPhoneInputWithFlags();

        $supplierContracts = Model_Supplier_Contract::getList(['supplier_id' => $supplierId]);

        $this->title[] = $supplier['SUPPLIER_NAME'];

        $this->_initDropZone();

        $popupSupplierContractAdd = Form::popup('Добавление нового договора', 'supplier/contract/add');

        $this->tpl
            ->bind('supplier', $supplier)
            ->bind('supplierContracts', $supplierContracts)
            ->bind('popupSupplierContractAdd', $popupSupplierContractAdd)
        ;
    }

    /**
     * редактирование поставщика
     */
    public function action_supplierEdit()
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
        $tubes = Model_Tube::getTubes();

        switch($tab) {
            case 'contract':

                $contractServices = array_column(Model_Supplier_Contract::getContractServices($contractId), 'SERVICE_ID');
                $contractServices = reset($contractServices);
                $contractDotsGroups = array_column(Model_Supplier_Contract::getContractDotsGroups($contractId), 'POS_GROUP_ID');

                $content = View::factory('ajax/suppliers/contract/contract')
                    ->bind('contract', $contract)
                    ->bind('tubes', $tubes)
                    ->bind('contractServices', $contractServices)
                    ->bind('contractDotsGroups', $contractDotsGroups)
                ;
                break;
            case 'agreements':
                $agreements = Model_Supplier_Agreement::getList(['contract_id' => $contractId]);

                $popupAgreementAdd = Form::popup('Добавление нового соглашения', 'supplier/agreement/add');

                $content = View::factory('ajax/suppliers/contract/agreements')
                    ->bind('agreements', $agreements)
                    ->bind('popupAgreementAdd', $popupAgreementAdd)
                ;
                break;
            case 'payments':
                $content = View::factory('ajax/suppliers/contract/payments');
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
            ],
            'payments'    => [
                'name' => 'Оплаты',
                'icon' => 'icon-account',
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
     * аяксово грузим историю
     */
    public function action_contractPaymentsHistory()
    {
        $params = [
            'contract_id'       => $this->request->param('id'),
            'offset' 		    => $this->request->post('offset'),
            'pagination'        => true
        ];

        list($paymentsHistory, $more) = Model_Supplier_Contract::getPaymentsHistory($params);

        if(empty($paymentsHistory)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $paymentsHistory, 'more' => $more]);
    }

    /**
     * редактирование контракта
     */
    public function action_contractEdit()
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
    public function action_contractAdd()
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
    public function action_supplierAdd()
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
    public function action_agreementEdit()
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
    public function action_agreementAdd()
    {
        $params = $this->request->post('params');

        $result = Model_Supplier_Agreement::add($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }
}
