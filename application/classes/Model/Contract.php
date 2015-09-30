<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contract extends Model
{
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
}