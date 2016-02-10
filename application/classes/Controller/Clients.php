<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Clients extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Список клиентов';
	}

	/**
	 * титульная страница со списком клиентов
	 */
	public function action_index()
	{
		$search = $this->request->post('search');

		$clients = Model_Client::getClientsList($search);

        $popupClientAdd = Common::popupForm('Добавление нового клиента', 'client/add');

		$this->tpl
            ->bind('clients', $clients)
            ->bind('popupClientAdd', $popupClientAdd)
        ;
	}

	/**
	 * страница работы с клиентом
	 */
	public function action_client()
	{
		$clientId = $this->request->param('id');

		Access::check('client', $clientId);

		$client = Model_Client::getClient($clientId);
		$contracts = Model_Contract::getContracts($clientId);

		if(empty($client)){
			throw new HTTP_Exception_404();
		}

		$popupContractAdd = Common::popupForm('Добавление нового договора', 'contract/add');

		$this->tpl
			->bind('client', $client)
			->bind('contracts', $contracts)
			->bind('popupContractAdd', $popupContractAdd)
		;
	}

	/**
	 * редактирование клиента
	 */
	public function action_client_edit()
	{
		$clientId = $this->request->param('id');
		$params = $this->request->post('params');

		$result = Model_Client::editClient($clientId, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}
		$this->jsonResult(true, $result);
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
		$query = $this->request->post('query');

		$contract = Model_Contract::getContract($contractId);

		if(empty($contract)){
			$this->html('<div class="error_block">Ошибка</div>');
		}

		$balance = Model_Contract::getContractBalance($contractId);

		switch($tab) {
			case 'contract':
				$contractSettings = Model_Contract::getContractSettings($contractId);
                $contractTariffs = Model_Contract::getTariffs();

				$content = View::factory('/ajax/clients/contract/contract')
					->bind('contract', $contract)
					->bind('contractSettings', $contractSettings)
					->bind('contractTariffs', $contractTariffs)
				;
				break;
			case 'cards':
                $cards = Model_Card::getCards($contractId);
				$foundCards = false;
				if(!empty($query)){
					$foundCards = Model_Card::getCards($contractId, false, $query);
				}

				$popupCardAdd = Common::popupForm('Добавление новой карты', 'card/add');

				$content = View::factory('/ajax/clients/contract/cards')
                    ->bind('cards', $cards)
                    ->bind('foundCards', $foundCards)
                    ->bind('query', $query)
					->bind('popupCardAdd', $popupCardAdd)
                ;
				break;
			case 'account':
				$turnover = Model_Contract::getTurnover($contractId);

				$popupContractPaymentAdd = Common::popupForm('Добавление нового платежа', 'contract/payment_add');

				$content = View::factory('/ajax/clients/contract/account')
                    ->bind('balance', $balance)
					->bind('turnover', $turnover)
					->bind('popupContractPaymentAdd', $popupContractPaymentAdd)
                ;
				break;
			case 'reports':
				$content = View::factory('/ajax/clients/contract/reports');
				break;
		}

		$html = View::factory('/ajax/clients/contract/_tabs')
			->bind('content', $content)
			->bind('balance', $balance)
			->bind('tab', $tab)
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

        $card = Model_Card::getCard($cardId);

        if(empty($card)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $oilRestrictions = Model_Card::getOilRestrictions($cardId);
        $lastFilling = Model_Card::getLastFilling($cardId);
		$servicesList = Model_Card::getServicesList($cardId);

		$popupCardEdit = Common::popupForm('Редактирование карты', 'card/edit', [
				'card' 				=> $card,
				'oilRestrictions' 	=> $oilRestrictions,
				'servicesList'		=> $servicesList
		], 'card_edit_'.$cardId);

        $html = View::factory('/ajax/clients/card')
            ->bind('card', $card)
            ->bind('oilRestrictions', $oilRestrictions)
            ->bind('lastFilling', $lastFilling)
            ->bind('popupCardEdit', $popupCardEdit)
        ;

        $this->html($html);
    }

	/**
	 * добавление нового клиента
	 */
	public function action_client_add()
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
	 *
	 */
	public function action_contract_add()
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
	public function action_card_add()
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
	 * редактирование карты
	 */
	public function action_card_edit()
	{
		$params = $this->request->post('params');

		$result = Model_Card::editCard($params, Model_Card::CARD_ACTION_EDIT);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true, $result);
	}

	/**
	 * добавление нового платежа по контракту
	 */
	public function action_contract_payment_add()
	{
		$params = $this->request->post('params');

		$result = Model_Contract::payment(Model_Contract::PAYMENT_ACTION_ADD, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true, $result);
	}

	/**
	 * удаляем платеж по контракту
	 */
	public function action_contract_payment_delete()
	{
		$params = $this->request->post('params');

		$result = Model_Contract::payment(Model_Contract::PAYMENT_ACTION_DELETE, $params);

		if(empty($result)){
			$this->jsonResult(false);
		}

		$this->jsonResult(true, $result);
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
	public function action_card_toggle()
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
	public function action_card_operations_history()
	{
		$cardId = $this->request->param('id');
		$params = [
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
	public function action_account_payments_history()
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

		$this->jsonResult(true, ['items' => $paymentsHistory, 'more' => $more]);
	}
}
