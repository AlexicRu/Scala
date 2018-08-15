<?php defined('SYSPATH') or die('No direct script access.');

class Form extends Kohana_Form
{
    /**
     * генерация html для попапа
     *
     * @param $header
     * @param $form
     * @param $data
     */
    public static function popup($header, $form, $data = [], $formName = '')
    {
        $formBody = View::factory('forms/'.$form);

        if(!empty($data) && is_array($data)){
            foreach($data as $key => $value){
                $formBody->set($key, $value);
            }
        }

        $id = $formName ?: str_replace('/', "_", $form);

        $content = View::factory('_includes/popup')
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
     * @param $params
     */
    public static function buildField($type, $name, $value = false, $params = [])
    {
        try {
            $configFields = Kohana::$config->load('fields')->as_array();

            $value = is_array($value) ? implode(',', $value) : $value;

            $templateUrl = 'forms/_fields/' . $type;

            if (isset($configFields[$type])) {
                $templateUrl = 'forms/_fields/_template';

                //чтобы можно было подменять отдельные значения зависимых полей
                if (isset($configFields[$type]['depend_on']) && isset($params['depend_on'])) {
                    $params['depend_on'] = array_merge($configFields[$type]['depend_on'], $params['depend_on']);
                }

                $params = array_merge($configFields[$type], $params);
            }

            $content = View::factory($templateUrl)
                ->bind('type', $type)
                ->bind('name', $name)
                ->bind('value', $value)
                ->bind('params', $params)
            ;
        } catch (Exception $e){
            return $type;
        }

        return $content;
    }

    /**
     * генерация шаблона лимита
     */
    public static function buildLimit($cardId, $limit, $postfix)
    {
        $settings = Model_Card::getCardLimitSettings($cardId);

        //добавление нового
        if (empty($limit)) {
            $settings['editSelect'] = true;
            $settings['editDurationValue'] = true;
        }

        $content = View::factory('forms/_limit/card_limit')
            ->bind('limit', $limit)
            ->bind('postfix', $postfix)
            ->bind('settings', $settings)
            ->bind('cardId', $cardId)
        ;

        return $content;
    }

    /**
     * генерация шаблона сервиса лимита
     *
     * @param $cardId
     * @param $limitService
     * @param $postfix
     * @return $this|string
     */
    public static function buildLimitService($cardId, $limitService, $postfix)
    {
        $settings = Model_Card::getCardLimitSettings($cardId);

        $card = Model_Card::getCard($cardId);

        Listing::$limit = 999;
        $servicesList = Listing::getServices([
            'SYSTEM_SERVICE_CATEGORY' => true,
            'TUBE_ID' => $card['TUBE_ID']
        ]);

        $content = View::factory('forms/_limit/card_limit_service')
            ->bind('limitService', $limitService)
            ->bind('postfix', $postfix)
            ->bind('settings', $settings)
            ->bind('servicesList', $servicesList)
        ;

        return $content;
    }
}