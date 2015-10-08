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
}