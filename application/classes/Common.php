<?php defined('SYSPATH') or die('No direct script access.');

class Common
{
	/**
	 * генерация html для попапа
	 *
	 * @param $header
	 * @param $form
	 * @return $this
	 */
	public static function popupForm($header, $form)
	{
		$formBody = View::factory('/forms/'.$form);

		$id = str_replace('/', "_", $form);

		$content = View::factory('/includes/popup')
			->bind('popupHeader', $header)
			->bind('popupBody', $formBody)
			->bind('popupId', $id)
		;

		return $content;
	}
}