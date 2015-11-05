<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends Model
{
	/**
	 * получаем список клиентов
	 */
	public static function getClientsList($search = null)
	{
		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		$sql = "
			select *
			from ".Oracle::$prefix."v_web_clients_title v
			where v.manager_id = ".Oracle::quote($user['MANAGER_ID'])
		;

		if(!is_null($search)){
			$search = mb_strtoupper($search);
			$sql .= " and (upper(v.client_name) like '%".Oracle::quote($search)."%' or upper(v.long_name) like '%".Oracle::quote($search)."%' or upper(v.contract_name) like '%".Oracle::quote($search)."%' or exists (select 1 from ".Oracle::$prefix."V_WEB_CRD_LIST c where c.contract_id = v.contract_id and c.card_id like '%".Oracle::quote($search)."%'))";
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

	/**
	 * получаем данные по клиенту
	 *
	 * @param $clientId
	 */
	public static function getClient($clientId)
	{
		if(empty($clientId)){
			return false;
		}

		$db = Oracle::init();

		$sql = "select * from ".Oracle::$prefix."V_WEB_CLIENTS_PROFILE where client_id = ".Oracle::quote($clientId);

		$client = $db->row($sql);

		return $client;
	}

	/**
	 * радактируем клиента по его id
	 *
	 * @param $clientId
	 * @param $params
	 * @return bool
	 */
	public static function editClient($clientId, $params)
	{
		if(
			empty($clientId) ||
			empty($params['NAME']) ||
			empty($params['Y_ADDRESS']) ||
			empty($params['PHONE']) ||
			empty($params['EMAIL']) ||
			empty($params['INN']) ||
			empty($params['KPP'])
		){
			return false;
		}

		$db = Oracle::init();

		$proc = 'begin '.Oracle::$prefix.'web_pack.client_edit(
			:p_client_id,
			:p_name,
			:p_long_name,
			:p_inn,
			:p_kpp,
			:p_ogrn,
			:p_okpo,
			:p_y_address,
			:p_f_address,
			:p_p_address,
			:p_email,
			:p_phone,
			:p_comments,
			:p_manager_id,
			:p_error_code
        ); end;';

		$user = Auth::instance()->get_user();

		$data = [
			'p_client_id' 	=> $clientId,
			'p_name' 		=> $params['NAME'],
			'p_long_name' 	=> $params['LONG_NAME'] ?: $params['NAME'],
			'p_inn' 		=> $params['INN'],
			'p_kpp' 		=> $params['KPP'],
			'p_ogrn' 		=> $params['OGRN'],
			'p_okpo' 		=> $params['OKPO'],
			'p_y_address' 	=> $params['Y_ADDRESS'],
			'p_f_address' 	=> $params['F_ADDRESS'],
			'p_p_address' 	=> $params['P_ADDRESS'],
			'p_email' 		=> $params['EMAIL'],
			'p_phone' 		=> $params['PHONE'],
			'p_comments' 	=> $params['COMMENTS'],
			'p_manager_id' 	=> $user['MANAGER_ID'],
			'p_error_code' 	=> 'out',
		];

		$res = $db->ora_proced($proc, $data);

		if(empty($res['p_error_code'])){
			return true;
		}

		return false;
	}

	/**
	 * добавление клиента по имени
	 *
	 * @param $params
	 */
	public static function addClient($params)
	{
		if(empty($params['name'])){
			return false;
		}

		$db = Oracle::init();

		$proc = 'begin '.Oracle::$prefix.'web_pack.client_add(
			:p_name,
			:p_manager_id,
			:p_client_id,
			:p_error_code
        ); end;';

		$user = Auth::instance()->get_user();

		$data = [
			'p_name' 		=> $params['name'],
			'p_manager_id' 	=> $user['MANAGER_ID'],
			'p_client_id' 	=> 'out',
			'p_error_code' 	=> 'out',
		];

		$res = $db->ora_proced($proc, $data);

		if(empty($res['p_error_code'])){
			return true;
		}

		return false;
	}
}