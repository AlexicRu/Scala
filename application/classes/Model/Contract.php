<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contract extends Model
{
    const CURRENCY_RUR 		            = 643;

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

	public static $invoicePeriods = [
		self::INVOICE_PERIOD_TYPE_DAY 	=> 'День',
		self::INVOICE_PERIOD_TYPE_MONTH => 'Месяц',
	];

	/**
	 * получаем список контрактов
	 */
	public static function getContracts($clientId = false, $contractId = false)
	{
		if(empty($clientId) && empty($contractId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CL_CONTRACTS
			where 1=1
		";

		if(!empty($clientId)){
			$sql .= " and client_id = ".Oracle::quote($clientId);
		}

		if(!empty($contractId)){
			$sql .= " and contract_id = ".Oracle::quote($contractId);
		}

		$sql .= 'order by date_begin, state_id';

		$contracts = $db->query($sql);

		return $contracts;
	}

	/**
	 * получаем контракт по его id
	 *
	 * @param $contractId
	 */
	public static function getContract($contractId)
	{
		$contract = self::getContracts(false, $contractId);

		if(!empty($contractId)){
			return reset($contract);
		}

		return false;
	}

	/**
	 * получаем данные по конретному контраку
	 *
	 * @param $contractId
	 */
	public static function getContractSettings($contractId)
	{
		if(empty($contractId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CL_CONTRACTS_SET
			where contract_id = ".Oracle::quote($contractId)
		;

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
			from ".Oracle::$prefix."V_WEB_CTR_BALANCE
			where contract_id = ".Oracle::quote($contractId)
		;

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
	public static function editContract($contractId, $params)
	{
		if(
			empty($contractId) ||
			empty($params['contract']['CONTRACT_NAME'])
		){
			return false;
		}

		$db = Oracle::init();

        $user = Auth::instance()->get_user();

		$proc = 'begin '.Oracle::$prefix.'web_pack.client_contract_edit(
			:p_contract_id,
			:p_contract_name,
			:p_date_begin,
			:p_date_end,
			:p_currency,
			:p_state_id,
			:p_manager_id,
			:p_error_code
        ); end;';

		$data = [
			'p_contract_id'		=> $contractId,
			'p_contract_name' 	=> $params['contract']['CONTRACT_NAME'],
			'p_date_begin' 		=> $params['contract']['DATE_BEGIN'],
			'p_date_end' 		=> $params['contract']['DATE_END'],
			'p_currency' 		=> self::CURRENCY_RUR,
			'p_state_id' 		=> $params['contract']['STATE_ID'],
			'p_manager_id' 		=> $user['MANAGER_ID'],
			'p_error_code' 		=> 'out',
		];

        $proc1 = 'begin '.Oracle::$prefix.'web_pack.client_contract_settings_edit(
			:p_contract_id,
			:p_tarif_online,
			:p_tarif_offline,
			:p_autoblock_limit,
			:p_autoblock_flag,
			:p_penalties,
			:p_penalties_flag,
			:p_overdraft,
			:p_invoice_currency,
			:p_invoice_period_type,
			:p_invoice_period_value,
			:p_manager_id,
			:p_error_code
        ); end;';

        $data1 = [
            'p_contract_id'		        => $contractId,
            'p_tarif_online' 	        => $params['settings']['TARIF_ONLINE'],
            'p_tarif_offline' 		    => $params['settings']['TARIF_OFFLINE'],
            'p_autoblock_limit' 		=> $params['settings']['AUTOBLOCK_LIMIT'],
            'p_autoblock_flag' 		    => $params['settings']['scheme'] == 1 ? 0 : 1,
            'p_penalties' 		        => $params['settings']['PENALTIES'],
            'p_penalties_flag' 		    => $params['settings']['PENALTIES'] ? 1 : 0,
            'p_overdraft' 		        => $params['settings']['OVERDRAFT'],
            'p_invoice_currency' 		=> self::CURRENCY_RUR,
            'p_invoice_period_type' 	=> $params['settings']['INVOICE_PERIOD_TYPE'],
            'p_invoice_period_value' 	=> $params['settings']['INVOICE_PERIOD_VALUE'],
            'p_manager_id' 		        => $user['MANAGER_ID'],
            'p_error_code' 		        => 'out',
        ];

		$res = $db->ora_proced($proc, $data);
		$res1 = $db->ora_proced($proc1, $data1);

		if(empty($res['p_error_code']) || empty($res1['p_error_code'])){
			return true;
		}

		return false;
	}

	/**
	 * Получаем список тарифов
	 */
	public static function getTariffs()
	{
		$db = Oracle::init();

		$sql = "select * from ".Oracle::$prefix."v_tarifs_list";

		return $db->query($sql);
	}
}