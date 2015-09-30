<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends Model
{
	/**
	 * получаем список клиентов
	 */
	public static function getClientsList($search = null)
	{
		$db = Oracle::getInstance();

		$sql = "
			select client_id, client_name, long_name, contract_id, contract_name, date_begin, date_end, balance, cards_in_work, all_cards, client_state, contract_state
			from ".Oracle::$prefix."v_web_clients_title
		";

		if(!is_null($search)){
			$search = mb_strtoupper($search);
			$sql .= "where upper(client_name) like '%{$search}%' or upper(long_name) like '%{$search}%' or upper(contract_name) like '%{$search}%'";
		}

		$result = $db->tree($sql, 'CLIENT_ID');

		$clients = [];

		foreach($result as $clientId => $rows){
			$client = reset($rows);

			foreach($rows as $row){
				if(!empty($row['CONTRACT_ID'])){
					$client['contracts'][] = $row;
				}
			}

			$clients[$clientId] = $client;
		}

		return $clients;
	}
}