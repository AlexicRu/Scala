<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Clients extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Список клиентов';
	}

	/**
	 * титульная страница со списком клиентов
     *
     * был запил под многостраничность, но принудительно отключили, так как не взлетело
	 */
	public function action_index()
	{
        if ($this->request->is_ajax()) {

            $clients = Model_Client::getFullClientsList($this->request->query('search'));

            if(empty($clients)){
                $this->jsonResult(false);
            }

            foreach ($clients as &$client) {
                if (!empty($client['contracts'])) {
                    foreach ($client['contracts'] as &$contract) {
                        $contract['contract_state_class']   = Model_Contract::$statusContractClasses[$contract['CONTRACT_STATE']];
                        $contract['contract_state_name']    = Model_Contract::$statusContractNames[$contract['CONTRACT_STATE']];
                        $contract['balance_formatted']      = number_format($contract['BALANCE'], 2, ',', ' ') . ' ' . Text::RUR;
                    }
                }
            }

            $this->jsonResult(true, ['items' => $clients, 'more' => false]);
        } else {

            $popupClientAdd = Form::popup('Добавление нового клиента', 'client/add');

            $this->tpl
                ->bind('popupClientAdd', $popupClientAdd)
            ;
        }
	}

	/**
	 * страница работы с клиентом
	 */
	public function action_client()
	{
		$clientId = $this->request->param('id');
		$contractId = $this->request->query('contract_id') ?: false;

		Access::check('client', $clientId);

		$client = Model_Client::getClient($clientId);
		$contracts = Model_Contract::getContracts($clientId);

		if(empty($client)){
			throw new HTTP_Exception_404();
		}

		$popupContractAdd = Form::popup('Добавление нового договора', 'contract/add');
		$popupCabinetCreate = Form::popup('Создание личного кабинета', 'client/cabinet_create');

		$this->tpl
			->bind('client', $client)
			->bind('contractId', $contractId)
			->bind('contracts', $contracts)
			->bind('popupContractAdd', $popupContractAdd)
			->bind('popupCabinetCreate', $popupCabinetCreate)
		;
	}

	/**
	 * редактирование клиента
	 */
	public function action_clientEdit()
	{
		$clientId = $this->request->param('id');
		$params = $this->request->post('params');

		$result = Model_Client::editClient($clientId, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true);
	}

	/**
	 * грузим данные по контракту
	 */
	public function action_contract()
	{
		$contractId = $this->request->param('id');

		if($contractId == 0){
			$this->html('<div class="error_block">Контракты отсутствуют</div>');
		}

		$tab = $this->request->post('tab');
		$params = $this->request->post('params') ?: [];

		$contract = Model_Contract::getContract($contractId);

		if(empty($contract)){
			$this->html('<div class="error_block">Ошибка</div>');
		}

		$balance = Model_Contract::getContractBalance($contractId);

		switch($tab) {
			case 'contract':
				$contractSettings = Model_Contract::getContractSettings($contractId);
                $contractTariffs = Model_Contract::getTariffs();
                $noticeSettings = Model_Contract::getContractNoticeSettings($contractId);
				$popupContractNoticeSettings = Form::popup('Настройка уведомлений', 'contract/notice_settings', ['settings' => $noticeSettings]);
				$popupContractHistory = Form::popup('История по договору', 'contract/history');

				$content = View::factory('ajax/clients/contract/contract')
					->bind('contract', $contract)
					->bind('contractSettings', $contractSettings)
					->bind('contractTariffs', $contractTariffs)
					->bind('popupContractNoticeSettings', $popupContractNoticeSettings)
					->bind('popupContractHistory', $popupContractHistory)
				;
				break;
			case 'cards':
				$popupCardAdd = Form::popup('Добавление новой карты', 'card/add');

				$cardsCounter = Model_Contract::getCardsCounter($contractId);

				$content = View::factory('ajax/clients/contract/cards')
                    ->bind('params', $params)
					->bind('popupCardAdd', $popupCardAdd)
					->bind('cardsCounter', $cardsCounter)
                ;
				break;
			case 'account':
				$turnover = Model_Contract::getTurnover($contractId);
				$contractLimits = Model_Contract::getLimits($contractId);
                Listing::$limit = 999;
                $servicesList = Listing::getServices(['description' => 'LONG_DESC']);

				$popupContractPaymentAdd = Form::popup('Добавление нового платежа', 'contract/payment_add');
                $popupContractBillAdd = Form::popup('Выставление счета', 'contract/bill_add');
                $popupContractBillPrint = Form::popup('Печать счетов', 'contract/bill_print');
                $popupContractLimitIncrease = Form::popup('Изменение лимита', 'contract/increase_limit');

                $popupContractLimitsEdit = Form::popup('Редактирование лимитов договора', 'contract/limits_edit', [
                    'contractLimits' 	=> $contractLimits,
                    'servicesList'		=> $servicesList
                ]);

				$content = View::factory('ajax/clients/contract/account')
                    ->bind('balance', $balance)
					->bind('turnover', $turnover)
					->bind('contractLimits', $contractLimits)
					->bind('servicesList', $servicesList)
					->bind('popupContractPaymentAdd', $popupContractPaymentAdd)
                    ->bind('popupContractBillAdd', $popupContractBillAdd)
                    ->bind('popupContractBillPrint', $popupContractBillPrint)
                    ->bind('popupContractLimitsEdit', $popupContractLimitsEdit)
                    ->bind('popupContractLimitIncrease', $popupContractLimitIncrease)
                ;
				break;
			case 'reports':
                $reportsList = Model_Report::getAvailableReports([
                    'report_type_id' => Model_Report::REPORT_TYPE_DB_CLIENT
                ]);

                $reports = Model_Report::separateBuyGroups($reportsList);

				$content = View::factory('ajax/clients/contract/reports')
                    ->bind('reports', $reports)
                ;
				break;
		}

		$html = View::factory('ajax/clients/contract/_tabs')
			->bind('content', $content)
			->bind('balance', $balance)
			->bind('tab', $tab)
		;

		$this->html($html);
	}

	/**
	 * редактирование контракта
	 */
	public function action_contractEdit()
	{
		$contractId = $this->request->param('id');
		$params = $this->request->post('params');

		$result = Model_Contract::editContract($contractId, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true, $result);
	}

    /**
     * грузим данные по карте
     */
    public function action_card()
    {
        $cardId = $this->request->param('id');
		$contractId = $this->request->query('contract_id');

        $card = Model_Card::getCard($cardId, $contractId);

        if(empty($card)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $oilRestrictions = Model_Card::getOilRestrictions($cardId);
        $transactions = Model_Transaction::getList($cardId, $contractId, ['limit' => 20]);
        Listing::$limit = 999;
        $settings = Model_Card::getCardLimitSettings($cardId);

		$servicesList = Listing::getServices([
		    'SYSTEM_SERVICE_GROUP' => true,
		    'TUBE_ID' => $card['TUBE_ID']
        ]);

		$popupCardHolderEdit = Form::popup('Редактирование держателя карты', 'card/edit_holder', [
            'card' 				=> $card,
		], 'card_edit_holder_'.$cardId);
        $popupCardLimitsEdit = Form::popup('Редактирование лимитов карты', 'card/edit_limits', [
            'card' 				=> $card,
            'oilRestrictions' 	=> $oilRestrictions,
            'servicesList'		=> $servicesList,
            'settings'		    => $settings,
        ], 'card_edit_limits_'.$cardId);

        $html = View::factory('ajax/clients/card')
            ->bind('card', $card)
            ->bind('oilRestrictions', $oilRestrictions)
            ->bind('transactions', $transactions)
            ->bind('popupCardHolderEdit', $popupCardHolderEdit)
            ->bind('popupCardLimitsEdit', $popupCardLimitsEdit)
        ;

        $this->html($html);
    }

	/**
	 * добавление нового клиента
	 */
	public function action_clientAdd()
	{
		$params = $this->request->post('params');

		$result = Model_Client::addClient($params);

		if(empty($result)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true, $result);
	}

	/**
	 * добавление контракта
	 */
	public function action_contractAdd()
	{
		$params = $this->request->post('params');

		$result = Model_Contract::addContract($params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		if(!empty($result['error'])){
			$this->jsonResult(false, $result['error']);
		}

		$this->jsonResult(true, $result);
	}

	/**
	 * добавляем новую карту
	 */
	public function action_cardAdd()
	{
		$params = $this->request->post('params');

		$result = Model_Card::editCard($params, Model_Card::CARD_ACTION_ADD);

		if($result === true){
			$this->jsonResult(true);
		}

		$error = '';
		switch($result){
			case Oracle::CODE_ERROR :
				break;
			case 2:
				$error = 'Карта уже существует';
				break;
			case 3:
				$error = 'Неверный номер карты';
				break;
		}

		$this->jsonResult(false, $error);
	}
    /**
     * редактирование лимитов карты
     */
    public function action_cardEditLimits()
    {
        $cardId     = $this->request->post('card_id');
        $contractId = $this->request->post('contract_id');
        $limits     = $this->request->post('limits') ?: [];

        list($result, $error) = Model_Card::editCardLimits($cardId, $contractId, $limits);

        $this->jsonResult($result, $error);
    }

	/**
	 * редактирование карты
	 */
	public function action_cardEditHolder()
	{
        $cardId     = $this->request->post('card_id');
        $contractId = $this->request->post('contract_id');
        $holder     = $this->request->post('holder');
        $date       = $this->request->post('date');
        $comment    = $this->request->post('comment');

		$result = Model_Card::editCardHolder($cardId, $contractId, $holder, $date, $comment);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true, $result);
	}

	/**
	 * добавление нового платежа по контракту
	 */
	public function action_contractPaymentAdd()
	{
        $payments = [$this->request->post('params')];
		$multi = $this->request->post('multi') ?: 0;
        $message = '';

		if(!empty($multi)){
            $payments = (array)$this->request->post('payments');
        }

        foreach($payments as $payment){
            list($result, $message) = Model_Contract::payment(Model_Contract::PAYMENT_ACTION_ADD, $payment);

            if(empty($result)){
                $this->jsonResult(false, $message);
            }
        }

		$this->jsonResult(true, $message);
	}

	/**
	 * удаляем платеж по контракту
	 */
	public function action_contractPaymentDelete()
	{
		$params = $this->request->post('params');

		list($result, $message) = Model_Contract::payment(Model_Contract::PAYMENT_ACTION_DELETE, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true);
	}

	/**
	 * генерация отчетов
	 */
	public function action_report()
	{
		$params = $this->request->query();

		$report = Model_Report::generate($params);

		if(empty($report)){
			throw new HTTP_Exception_404();
		}

		foreach($report['headers'] as $header){
			header($header);
		}

		$this->html($report['report']);
	}

	/**
	 * блокируем/разблокируем карту
	 */
	public function action_cardToggle()
	{
		$params = $this->request->post('params');

		$result = Model_Card::toggleStatus($params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true);
	}

	/**
	 * аяксово+постранично получаем историю операций
	 */
	public function action_cardOperationsHistory()
	{
		$cardId = $this->request->param('id');
		$contractId = $this->request->query('contract_id');
		$params = [
		    'CONTRACT_ID'   => $contractId,
			'offset' 		=> $this->request->post('offset'),
			'pagination'	=> true
		];

		list($operationsHistory, $more) = Model_Card::getOperationsHistory($cardId, $params);

		if(empty($operationsHistory)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true, ['items' => $operationsHistory, 'more' => $more]);
	}

	/**
	 * аяксово+постранично получаем историю операций
	 */
	public function action_accountPaymentsHistory()
	{
		$contractId = $this->request->param('id');
		$params = [
			'offset' 		=> $this->request->post('offset'),
			'pagination'	=> true
		];

		list($paymentsHistory, $more) = Model_Contract::getPaymentsHistory($contractId, $params);

		if(empty($paymentsHistory)){
			$this->jsonResult(false);
		}

		foreach ($paymentsHistory as &$elem) {
		    $elem['PAY_COMMENT'] = Text::parseUrl($elem['PAY_COMMENT']);
        }

		$this->jsonResult(true, ['items' => $paymentsHistory, 'more' => $more]);
	}

	/**
	 * создание ЛК для пользователя
	 */
	public function action_cabinetCreate()
	{
		$params = $this->request->post('params');

		$result = Model_Client::createCabinet($params);

		if(!empty($result)){
			$this->jsonResult(false, $result);
		}

		$this->jsonResult(true);
	}

	/**
	 * изымаем новую карту
	 */
	public function action_cardWithdraw()
	{
		$params = $this->request->post('params');

		$result = Model_Card::withdrawCard($params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true);
	}

	/**
	 * список состояний контракта
	 */
	public function action_contractHistory()
	{
		if($this->isPost()) {
			$params = [
				'contract_id'	=> $this->request->post('contract_id'), 
				'offset' 		=> $this->request->post('offset'),
				'pagination' 	=> true
			];

			list($history, $more) = Model_Contract::getHistory($params);

			if(empty($history)){
				$this->jsonResult(false);
			}

			$this->jsonResult(true, ['items' => $history, 'more' => $more]);
		}		
	}

    /**
     * выставляем счет клиенту
     */
	public function action_addBill()
    {
        $contractId = $this->request->query('contract_id');
        $sum = $this->request->query('sum');
        $products = $this->request->query('products');

        $invoiceNum = Model_Contract::addBill($contractId, $sum, $products);

        $params = [
            'type'              => Model_Report::REPORT_TYPE_BILL,
            'format'            => 'pdf',
            'contract_id'       => $contractId,
            'invoice_number'    => $invoiceNum,
        ];

        $report = Model_Report::generate($params);

        if(empty($report)){
            throw new HTTP_Exception_500('Счет не сформировался');
        }

        foreach($report['headers'] as $header){
            header($header);
        }

        $this->html($report['report']);
    }

    /**
     * настройка уведомлений
     */
    public function action_editContractNotices()
    {
        $params = $this->request->post('params');
        $contractId = $this->request->post('contract_id');

        $result = Model_Contract::editNoticesSettings($contractId, $params);

        if(!empty($result)){
            $this->jsonResult(false, $result);
        }

        $this->jsonResult(true);
    }

    /**
     * список выставленных счетов по контракту
     */
    public function action_billsList()
    {
        $params = [
            'contract_id'	=> $this->request->post('contract_id'),
            'offset' 		=> $this->request->post('offset'),
            'pagination' 	=> true
        ];

        list($history, $more) = Model_Contract::getBillsList($params);

        if(empty($history)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $history, 'more' => $more]);
    }

    /**
     * грузим список карт
     */
    public function action_cardsList()
    {
        $contractId = $this->request->query('contract_id');
        $query = $this->request->post('query');
        $status = $this->request->post('status');

        $params = [
            'CONTRACT_ID'   => $contractId,
            'offset' 		=> $this->request->post('offset'),
            'pagination'	=> true,
            'limit'	        => 20,
        ];

        if(!empty($query) ){
            $params['query'] = $query;
        }
        if(!empty($status) ){
            $params['status'] = $status;
        }

        list($cards, $more) = Model_Card::getCards($contractId, false, $params);

        if(empty($cards)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $cards, 'more' => $more]);
    }

    /**
     * редактируем логин пользователя
     */
    public function action_editLogin()
    {
        $login = $this->request->post('login');
        $managerId = $this->request->post('manager_id');

        $result = Model_Manager::editLogin($managerId, $login);

        if (!empty($result['error'])) {
            $this->jsonResult(false, $result);
        }
        $this->jsonResult(true, $result);
    }

    /**
     * редактирование лимитов договора
     */
    public function action_contractLimitsEdit()
    {
        $limits = $this->request->post('limits');
        $contractId = $this->request->post('contract_id');
        $recalc = $this->request->post('recalc');

        $result = Model_Contract::editLimits($contractId, $limits, $recalc);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $messages = Messages::get();

        if(!empty($messages)){
            $this->jsonResult(false, $messages);
        }

        $this->jsonResult(true, $result);
    }

    /**
     * изменяем лимит
     */
    public function action_contractIncreaseLimit()
    {
        $amount = $this->request->post('amount');
        $groupId = $this->request->post('group_id');
        $contractId = $this->request->post('contract_id');

        $result = Model_Contract::editLimit($contractId, $groupId, $amount);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, $result);
    }

    /**
     * отрисовываем блок продукта для выставления счета
     */
    public function action_addBillProductTemplate()
    {
        $iteration = $this->request->post('iteration');

        $html = View::factory('ajax/clients/add_bill/product')
            ->bind('iteration', $iteration)
        ;

        $this->html($html);
    }

    /**
     * рендер шаблона лимита
     */
    public function action_cardLimitTemplate()
    {
        $cardId     = $this->request->query('cardId');
        $postfix    = $this->request->query('postfix');

        $html = Form::buildLimit($cardId, [], $postfix);

        $this->html($html);
    }

    /**
     * рендер шаблона сервиса лимита
     */
    public function action_cardLimitServiceTemplate()
    {
        $cardId         = $this->request->query('cardId');
        $postfix        = $this->request->query('postfix');

        $html = Form::buildLimitService($cardId, [], $postfix);

        $this->html($html);
    }
}