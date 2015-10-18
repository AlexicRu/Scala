<?php defined('SYSPATH') or die('No direct script access.');

class Model_Card extends Model
{
	public static function getCards($contractId)
	{
		if(empty($contractId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LIST
			where contract_id = {$contractId}
		";

		$cards = $db->query($sql);

		return $cards;
	}
}