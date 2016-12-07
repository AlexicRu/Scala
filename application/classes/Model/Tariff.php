<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tariff extends Model
{
    /**
     * получаем список доступных тарифов
     */
    public static function getAvailableTariffs($params = [])
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        if(empty($params['agent_id'])){
            $params['agent_id'] = $user['AGENT_ID'];
        }

        $sql = "select * from ".Oracle::$prefix."V_WEB_TARIF_LIST t where t.tarif_id != 0";

        if(!empty($params['agent_id'])){
            $sql .= " and t.agent_id = ".$params['agent_id'];
        }

        if(!empty($params['tariff_id'])){
            $sql .= " and t.tarif_id = ".$params['tariff_id'];
        }

        $tariffs = $db->query($sql);

        return $tariffs;
    }

    /**
     * получаем параметры тарифа
     *
     * @param $tariffId
     * @param $lastVersion
     */
    public static function getTariffSettings($tariffId, $lastVersion)
    {
        if(empty($tariffId) || empty($lastVersion)){
            return false;
        }

        $sections = self::getSections($tariffId, $lastVersion);

        if(empty($sections)){
            return false;
        }

        foreach($sections as &$sectionsList){
            foreach($sectionsList as &$section) {
                $section['params'] = self::getSectionParams($tariffId, $lastVersion, $section['SECTION_NUM']);
            }
        }

        return $sections;
    }

    /**
     * получаем серкции тарифа
     *
     * @param $tariffId
     * @param $lastVersion
     */
    public static function getSections($tariffId, $lastVersion)
    {
        if(empty($tariffId) || empty($lastVersion)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_TARIF_SECTIONS t where t.tarif_id = {$tariffId} and t.version_id = {$lastVersion} order by t.SECTION_NUM";

        $sections = $db->tree($sql, 'SECTION_NUM');

        return $sections;
    }

    /**
     * получаем параметры секции тарифа
     *
     * @param $tariffId
     * @param $lastVersion
     * @param $sectionNum
     */
    public static function getSectionParams($tariffId, $lastVersion, $sectionNum)
    {
        if(empty($tariffId) || empty($lastVersion) || empty($sectionNum)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_TARIF_PARAMS t where t.tarif_id = {$tariffId} and t.version_id = {$lastVersion} and t.section_num = {$sectionNum}";

        $sectionParams = $db->query($sql);

        return $sectionParams;
    }

    /**
     * создаем шаблон тарифа
     *
     * @param $tariff
     * @param $templateSettings
     */
    public static function buildTemplate($tariff, $templateSettings)
    {
        $tariffReference = self::getReference();

        $html = View::factory('forms/tariffs/constructor')
            ->bind('tariff', $tariff)
            ->bind('settings', $templateSettings)
            ->bind('reference', $tariffReference)
        ;
        return $html;
    }

    /**
     * получение справочников
     */
    public static function getReference()
    {
        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_TARIF_CONSTRUCT t";

        return $db->tree($sql, 'CONDITION_ID');
    }
}