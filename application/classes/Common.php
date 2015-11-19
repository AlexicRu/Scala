<?php defined('SYSPATH') or die('No direct script access.');

class Common
{
	/**
	 * генерация html для попапа
	 *
	 * @param $header
	 * @param $form
	 * @param $data
	 * @return $this
	 */
	public static function popupForm($header, $form, $data = [])
	{
		$formBody = View::factory('/forms/'.$form);

		if(!empty($data) && is_array($data)){
			foreach($data as $key => $value){
				$formBody->bind($key, $value);
			}
		}

		$id = str_replace('/', "_", $form);

		$content = View::factory('/includes/popup')
			->bind('popupHeader', $header)
			->bind('popupBody', $formBody)
			->bind('popupId', $id)
		;

		return $content;
	}
}