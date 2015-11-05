<?php defined('SYSPATH') or die('No direct script access.');

class Model_Card extends Model
{
	const CARD_STATE_BLOCKED 	= 4;

	const CARD_ACTION_DELETE 	= 0;
	const CARD_ACTION_ADD 		= 1;
	const CARD_ACTION_EDIT		= 2;

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

	/**
	 * данные по последней заправке
	 *
	 * @param $cardId
	 */
	public static function getLastFilling($cardId)
	{
		if(empty($cardId)){
			return [];
		}

		$db = Oracle::init();

		$sql = "
			select *
			from ".Oracle::$prefix."V_WEB_CRD_LAST_SERV
			where 1=1
		";

		if(!empty($cardId)){
			$sql .= " and card_id = ".Oracle::quote($cardId);
		}

		$restrictions = $db->row($sql);

		return $restrictions;
	}

	/**
	 * добавление новой карты
	 *
	 * @param $params
	 */
	public static function addCard($params)
	{
		if(empty($params['contract_id']) || empty($params['card_id'])){
			return false;
		}

		$db = Oracle::init();

		$proc = 'begin '.Oracle::$prefix.'web_pack.client_contract_card(
			:p_contract_id,
			:p_card_id,
			:p_card_type,
			:p_holder,
			:p_expire_date,
			:v_action,
			:p_manager_id,
			:p_error_code
        ); end;';

		$user = Auth::instance()->get_user();

		$data = [
			'p_contract_id' 	=> $params['contract_id'],
			'p_card_id' 		=> $params['card_id'],
			'p_card_type' 		=> 1, //1-EMV, 2 - PayFlex, 3 - Loyalty
			'p_holder' 			=> $params['holder'],
			'p_expire_date' 	=> !empty($params['expire_date']) ? date('m/Y', strtotime($params['expire_date'])) : '',
			'v_action' 			=> self::CARD_ACTION_ADD,
			'p_manager_id' 		=> $user['MANAGER_ID'],
			'p_error_code' 		=> 'out',
		];

		$res = $db->ora_proced($proc, $data);

		if(empty($res['p_error_code'])){
			return true;
		}

		return false;
	}
}