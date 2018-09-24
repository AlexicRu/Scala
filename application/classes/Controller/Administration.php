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

    /**
     * управление транзакциями
     */
    public function action_transactions()
    {
        $this->_initDropZone();
        $this->_initJsGrid();
    }

    /**
     * грузим отказные транзакции
     */
    public function action_transactionsErrors()
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
     * грузим транзакции, которые в процессе
     */
    public function action_transactionsProcess()
    {
        $params = [
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => $this->toXls ? false : true
        ];

        $result = Model_Transaction::getTransactionsProcess($params);

        if ($this->toXls){
            $this->showXls('transactions_process', $result, [
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
    public function action_transactionsHistory()
    {
        $params = [
            'filter'        => $this->request->post('filter'),
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

    /**
     * расчет тарифов
     */
    public function action_calcTariffs()
    {
        $this->_initJsGrid();
    }

    /**
     * отрисовываем блок клиента
     */
    public function action_calcTariffsRenderClient()
    {
        $iteration = $this->request->post('iteration');

        $html = View::factory('ajax/administration/calc_tariffs/client')
            ->bind('iteration', $iteration)
        ;

        $this->html($html);
    }

    /**
     * рассчет тарифов
     */
    public function action_calcTariff()
    {
        $contractId = $this->request->post('contract_id');
        $start = $this->request->post('start');
        $end = $this->request->post('end');

        $queuedContracts = Model_Tariff::getCalcQueue();

        $badFl = false;
        foreach ($queuedContracts as $contract) {
            if ($contract['CONTRACT_ID'] == $contractId) {
                Messages::put('Уже ведется расчет по договору: ' . $contract['CONTRACT_NAME']);
                $badFl = true;
            }
        }

        if ($badFl) {
            $this->jsonResult(false);
        }

        //дата начала и дата окончания расчета тарифа не старше чем 3 месяца от текущей даты (указать в примечании, что расчет тарифа старше 3х месяцав через поддержку)
        $dateStart = DateTime::createFromFormat('d.m.Y', $start);
        $dateEnd = DateTime::createFromFormat('d.m.Y', $end);

        if (
            !$dateEnd ||
            !$dateStart ||
            $dateEnd->getTimestamp() < $dateStart->getTimestamp() ||
            (time() - 60*60*24*30*3) > $dateStart->getTimestamp()  ||
            (time() - 60*60*24*30*3) > $dateEnd->getTimestamp()
        ) {
            Messages::put('Некорректные даты');
            $this->jsonResult(false);
        }

        $contractSettings = Model_Contract::getContractSettings($contractId);

        $tariffId = $contractSettings['TARIF_OFFLINE'];

        $result = Model_Tariff::calcTariff($tariffId, $contractId, [
            'start' => $start,
            'end' => $end,
        ]);

        if (empty($result)) {
            $this->jsonResult(false);
        }
        $this->jsonResult(true);
    }

    /**
     * грузим очередь текущих расечтов тарифов
     */
    public function action_loadCalcQueue()
    {
        $calcQueue = Model_Tariff::getCalcQueue();

        $html = View::factory('ajax/administration/calc_tariffs/calc_queue')
            ->bind('queue', $calcQueue)
        ;

        $this->html($html);
    }

    /**
     * страница пеерноса карт и транзакций
     */
    public function action_cardsTransfer()
    {
        if ($this->request->is_ajax()) {
            $oldContractId = $this->request->post('old_contract');
            $newContractId = $this->request->post('new_contract');
            $cards = $this->request->post('cards') ?: [];
            $cardsList = $this->request->post('cards_list');
            $params = $this->request->post('params');

            if (!empty($cardsList)) {
                $cardsList = array_filter(explode("\n", preg_replace("/[^\d\n]/", '', $cardsList)));

                $cards = array_merge($cards, $cardsList);
            }

            $result = Model_Card::transferCards($oldContractId, $newContractId, $cards, $params);

            if (empty($result)) {
                $this->jsonResult(false);
            }
            $this->jsonResult(true);
        }
    }
}