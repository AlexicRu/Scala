<?php defined('SYSPATH') or die('No direct script access.');

class Model_Card extends Model
{
	const CARD_STATE_BLOCKED 	= 4;

	/**
	 * получаем список доступный карт по контракту
	 *
	 * @param $contractId
	 * @param $cardId
	 * @param $query
	 * @return array|int
	 */
	public static function getCards($contractId = false, $cardId = false, $query = false)
	{
		if(empty($contractId) && empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LIST
			where 1=1
		";

		if(!empty($contractId)){
			$sql .= " and contract_id = ".Oracle::quote($contractId);
		}

		if(!empty($cardId)){
			$sql .= " and card_id = ".Oracle::quote($cardId);
		}

		if(!empty($query)){
			$sql .= " and card_id like '%".Oracle::quote($query)."%'";
		}

		$cards = $db->query($sql);

		return $cards;
	}

	/**
	 * вытягиваем одну карту
	 */
	public static function getCard($cardId)
	{
		$card = self::getCards(false, $cardId);

		if(!empty($cardId)){
			return reset($card);
		}

		return false;
	}

	/**
	 * получаем данные по ограничениям по топливу
	 *
	 * @param $cardId
	 */
	public static function getOilRestrictions($cardId)
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LIMITS
			where 1=1
		";

		if(!empty($cardId)){
			$sql .= " and card_id = ".Oracle::quote($cardId);
		}

		$restrictions = $db->tree($sql, 'LIMIT_GROUP');

		return $restrictions;
	}
}