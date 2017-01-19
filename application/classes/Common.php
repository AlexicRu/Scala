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
     * @param $prefix
     * @param $type
     * @param $name
     * @param $value
     */
	public static function buildFormField($prefix, $type, $name, $value = false, $classes = false)
    {
        try {
            $content = View::factory('forms/_fields/' . ($prefix ? $prefix.'/' : '') . $type)
                ->bind('type', $type)
                ->bind('name', $name)
                ->bind('value', $value)
                ->bind('classes', $classes)
            ;
        } catch (Exception $e){
            return $type;
        }

        return $content;
    }
}