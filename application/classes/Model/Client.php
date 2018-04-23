<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends Model
{
    /**
     * получаем список клиентов
     *
     * @param array $search
     */
    public static function getFullClientsList($search = '')
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('v_web_clients_title v')
            ->where("v.manager_id = " . (int)$user['MANAGER_ID'])
            ->orderBy([
                'client_id desc'
            ])
        ;

        if(!empty($search)){
            $search = mb_strtoupper(Oracle::quote('%'.$search.'%'));

            $sql
                ->whereStart()
                ->where("upper(v.client_name) like " . $search)
                ->whereOr("upper(v.long_name) like " . $search)
                ->whereOr("upper(v.contract_name) like " . $search)
                ->whereOr("upper(v.CARD_ID) like " . $search)
                ->whereEnd()
            ;
        }

        $result = $db->tree($sql, 'CLIENT_ID');

        $clients = [];

        $user = User::current();

        foreach($result as $clientId => $rows){
            $client = reset($rows);

            foreach($rows as $row){
                if(!empty($row['CONTRACT_ID'])){

                    if (!empty($user['contracts'][$clientId])) {
                        if (in_array($row['CONTRACT_ID'], $user['contracts'][$clientId])) {
                            $client['contracts'][$row['CONTRACT_ID']] = $row;
                        }
                    } else {
                        $client['contracts'][$row['CONTRACT_ID']] = $row;
                    }
                }
            }

            $client['contracts'] = array_values($client['contracts']);

            $clients[] = $client;
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
			empty($params['INN'])
		){
			return false;
		}

		$db = Oracle::init();

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

		$res = $db->procedure('client_edit', $data);

		if(empty($res)){
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

		$user = Auth::instance()->get_user();

		$data = [
			'p_name' 		=> $params['name'],
			'p_manager_id' 	=> $user['MANAGER_ID'],
			'p_client_id' 	=> 'out',
			'p_error_code' 	=> 'out',
		];

		$res = $db->procedure('client_add', $data);

		if(empty($res)){
			return true;
		}

		return false;
	}

	/**
	 * созданеи ЛК для пользователя
	 *
	 * @param $params
	 */
	public static function createCabinet($params)
	{
		if(empty($params['client_id']) || empty($params['email_to'])){
			return false;
		}

		$db = Oracle::init();

		$user = Auth::instance()->get_user();

		$client = Model_Client::getClient($params['client_id']);

		if(empty($client)){
			return false;
		}

		$data = [
			'p_client_id' 	=> $client['CLIENT_ID'],
			'p_role_id' 	=> $params['role'],
			'p_login' 		=> $client['EMAIL'],
			'p_password' 	=> null,
			'p_email_to' 	=> $params['email_to'],
			'p_fl_send' 	=> 0,
			'p_manager_id' 	=> $user['MANAGER_ID'],
			'p_error_code' 	=> 'out',
		];

		$res = $db->procedure('client_private_office', $data);

		switch($res){
			case Oracle::CODE_ERROR:
			case 3:
				return Oracle::CODE_ERROR;
			case 2:
				return 'Неверный email';
			case 4:
				return 'Линчый кабинет уже создан';
			case 5:
				return 'Не удалось отправить почту на указанный email';
			default:
				return Oracle::CODE_SUCCESS;
		}
	}
}