<?php defined('SYSPATH') or die('No direct script access.');

class Common
{
	/**
	 * генерация html для попапа
	 *
	 * @param $header
	 * @param $form
	 * @param $data
	 */
	public static function popupForm($header, $form, $data = [], $formName = '')
	{
		$formBody = View::factory('forms/'.$form);

		if(!empty($data) && is_array($data)){
			foreach($data as $key => $value){
				$formBody->set($key, $value);
			}
		}

		$id = $formName ?: str_replace('/', "_", $form);

		$content = View::factory('includes/popup')
			->bind('popupHeader', $header)
			->bind('popupBody', $formBody)
			->bind('popupId', $id)
		;

		return $content;
	}

    /**
     * генерация шаблона конкретного типа поля
     *
     * @param $type
     * @param $name
     * @param $value
     */
	public static function buildFormField($type, $name, $value = false)
    {
        try {
            $content = View::factory('forms/_fields/' . $type)
                ->bind('name', $name)
                ->bind('value', $value)
            ;
        } catch (Exception $e){
            return $type;
        }

        return $content;
    }
}