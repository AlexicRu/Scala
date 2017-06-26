<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Administration extends Controller_Common
{

    public function before()
    {
        parent::before();

        $this->title[] = 'Администрирование';
    }

    /**
     * титульная страница
     */
    public function action_index()
    {
        $this->redirect('/administration/transactions');
    }


    public function action_transactions()
    {

    }

    /**
     * грузим отказные транзакции
     */
    public function action_transactions_errors()
    {
        $params = [
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => $this->toXls ? false : true
        ];

        $result = Model_Transaction::getTransactionsErrors($params);

        if ($this->toXls){
            $this->showXls('transactions_errors', $result, [
                'SOURCE_NAME'       => 'SOURCE_NAME',
                'DATETIME_TRN'      => 'DATETIME_TRN',
                'CARD_ID'           => 'CARD_ID',
                'OPERATION'         => 'OPERATION',
                'SERVICE_NAME'      => 'SERVICE_NAME',
                'SERVICE_AMOUNT'    => 'SERVICE_AMOUNT',
                'SERVICE_PRICE'     => 'SERVICE_PRICE',
                'SERVICE_SUMPRICE'  => 'SERVICE_SUMPRICE',
                'SUPPLIER_EMITENT'  => 'SUPPLIER_EMITENT',
                'SUPPLIER_TERMINAL' => 'SUPPLIER_TERMINAL',
                'POS_ADDRESS'       => 'POS_ADDRESS',
                'PROJECT_SERVICE'   => 'PROJECT_SERVICE',
                'ERROR_DESCR'       => 'ERROR_DESCR'
            ],true);
        } else {
            list($transactions, $more) = $result;
        }

        if(empty($transactions)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $transactions, 'more' => $more]);
    }

    /**
     * грузим историю операций
     */
    public function action_transactions_history()
    {
        $params = [
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => true
        ];

        if ($this->toXls) {
            unset($params['pagination']);

            $params['rnum'] = explode(',', $this->request->query('rnum'));
        }

        $result = Model_Transaction::getTransactionsHistory($params);

        if ($this->toXls){
            $this->showXls('transactions_history', $result, [
                'SOURCE_NAME'       => 'SOURCE_NAME',
                'FILE_NAME'         => 'FILE_NAME',
                'DATETIME_RECIEVE'  => 'DATETIME_RECIEVE',
                'TRZ_UPLOADED'      => 'TRZ_UPLOADED',
                'DB_FAILS'          => 'DB_FAILS',
                'TRZ_FAILS'         => 'TRZ_FAILS',
                'DATE_FIRST_TRZ'    => 'DATE_FIRST_TRZ',
                'DATE_LAST_TRZ'     => 'DATE_LAST_TRZ'
            ], true);
        } else {
            list($history, $more) = $result;
        }

        if(empty($history)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $history, 'more' => $more]);
    }
}