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

        $this->tpl
            ->bind('supplier', $supplier)
        ;
    }
}
