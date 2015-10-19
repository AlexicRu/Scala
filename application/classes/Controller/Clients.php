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

		$this->tpl->bind('clients', $clients);
	}

	/**
	 * страница работы с клиентом
	 */
	public function action_client()
	{
		$clientId = $this->request->param('id');

		$client = Model_Client::getClient($clientId);
		$contracts = Model_Contract::getContracts($clientId);

		if(empty($client)){
			throw new HTTP_Exception_404();
		}

		$this->tpl
			->bind('client', $client)
			->bind('contracts', $contracts)
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
                $cards = Model_Card::getCards($contractId, false, $query);

				$content = View::factory('/ajax/clients/contract/cards')
                    ->bind('cards', $cards)
                    ->bind('query', $query)
                ;
				break;
			case 'account':
				$content = View::factory('/ajax/clients/contract/account')
                    ->bind('balance', $balance)
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
     * грузим данные по контракту
     */
    public function action_card()
    {
        $cardId = $this->request->param('id');

        $card = Model_Card::getCard($cardId);

        if(empty($card)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $oilRestrictions = Model_Card::getOilRestrictions($cardId);

        $html = View::factory('/ajax/clients/card')
            ->bind('card', $card)
            ->bind('oilRestrictions', $oilRestrictions)
        ;

        $this->html($html);
    }
}
