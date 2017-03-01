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
            'pagination'    => true
        ];

        list($transactions, $more) = Model_Transaction::getTransactionsErrors($params);

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

        list($history, $more) = Model_Transaction::getTransactionsHistory($params);

        if(empty($history)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $history, 'more' => $more]);
    }
}