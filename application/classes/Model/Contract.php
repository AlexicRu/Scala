<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contract extends Model
{
	const PAYMENT_SCHEME_UNLIMITED 		= 1;
	const PAYMENT_SCHEME_PREPAYMENT 	= 2;
	const PAYMENT_SCHEME_LIMIT 			= 3;

	const INVOICE_PERIOD_TYPE_DAY		= 'D';
	const INVOICE_PERIOD_TYPE_MONTH		= 'M';

	public static $paymentSchemes = [
		self::PAYMENT_SCHEME_UNLIMITED 	=> 'Безлимит',
		self::PAYMENT_SCHEME_PREPAYMENT => 'Предоплата',
		self::PAYMENT_SCHEME_LIMIT 		=> 'Порог отключения',
	];

	/**
	 * получаем список контрактов
	 */
	public static function getContracts($clientId)
	{
		if(empty($clientId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CL_CONTRACTS
			where client_id = {$clientId}
			order by date_begin, state_id
		";

		$contracts = $db->query($sql);

		return $contracts;
	}

	/**
	 * получаем данные по конретному контраку
	 *
	 * @param $contractId
	 */
	public static function getContract($contractId)
	{
		if(empty($contractId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CL_CONTRACTS_SET
			where contract_id = {$contractId}
		";

		$contract = $db->row($sql);

		if($contract['AUTOBLOCK_FLAG'] == 0 && $contract['AUTOBLOCK_LIMIT'] == 0){
			$contract['scheme'] = self::PAYMENT_SCHEME_UNLIMITED;
		}elseif($contract['AUTOBLOCK_FLAG'] == 1 && $contract['AUTOBLOCK_LIMIT'] == 0){
			$contract['scheme'] = self::PAYMENT_SCHEME_PREPAYMENT;
		}else{
			$contract['scheme'] = self::PAYMENT_SCHEME_LIMIT;
		}

		return $contract;
	}

	/**
	 * баланс и оборот по контракту
	 *
	 * @param $contractId
	 */
	public static function getContractBalance($contractId)
	{
		if(empty($contractId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix." V_WEB_CTR_BALANCE
			where contract_id = {$contractId}
		";

		$balance = $db->row($sql);

		return $balance;
	}

	/**
	 * редактируем данные по контракту
	 *
	 * @param $contractId
	 * @param $params
	 * todo переделать
	 */
	public function editContract($contractId, $params)
	{
		if(
			empty($contractId) ||
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
			'p_long_name' 	=> $params['LONG_NAME'],
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
}