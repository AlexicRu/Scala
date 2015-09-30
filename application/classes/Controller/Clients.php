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
}
