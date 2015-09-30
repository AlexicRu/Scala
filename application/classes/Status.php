<?php defined('SYSPATH') or die('No direct script access.');

class Status
{
	const STATUS_CONTRACT_WORK 			= 1;
	const STATUS_CONTRACT_BLOCKED 		= 4;
	const STATUS_CONTRACT_EXPIRED 		= 5;
	const STATUS_CONTRACT_NOT_IN_WORK 	= 6;

	public static $statusContractNames = [
		self::STATUS_CONTRACT_WORK 			=> 'В работе',
		self::STATUS_CONTRACT_NOT_IN_WORK 	=> 'Не в работе',
		self::STATUS_CONTRACT_BLOCKED 		=> 'Заблокирован',
		self::STATUS_CONTRACT_EXPIRED 		=> 'Завершен',
	];

	public static $statusContractClasses = [
		self::STATUS_CONTRACT_WORK 			=> 'label_success',
		self::STATUS_CONTRACT_NOT_IN_WORK 	=> 'label_info',
		self::STATUS_CONTRACT_BLOCKED 		=> 'label_error',
		self::STATUS_CONTRACT_EXPIRED 		=> 'label_warning',
	];
}