<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tariff extends Model
{
    const TARIFF_PARAM_TYPE_DISCOUNT    = 1;
    const TARIFF_PARAM_TYPE_MARKUP      = 2;
    const TARIFF_PARAM_TYPE_FIX_PRICE   = 3;
    const TARIFF_PARAM_TYPE_SUPPLIER    = 4;

    const TARIFF_PARAM_PARAM_PERCENT    = 1;
    const TARIFF_PARAM_PARAM_CURRENCY   = 2;
    const TARIFF_PARAM_PARAM_DISCOUNT   = 3;

    const TARIFF_STATUS_ACTIVE = 1;
    const TARIFF_STATUS_DELETED = 4;

    public static $paramsTypes = [
        self::TARIFF_PARAM_TYPE_DISCOUNT    => 'Скидка',
        self::TARIFF_PARAM_TYPE_MARKUP      => 'Наценка',
        self::TARIFF_PARAM_TYPE_FIX_PRICE   => 'Фиксированная цена',
        self::TARIFF_PARAM_TYPE_SUPPLIER    => 'От условий поставщика',
    ];

    public static $paramsParams = [
        self::TARIFF_PARAM_PARAM_PERCENT    => 'в %',
        self::TARIFF_PARAM_PARAM_CURRENCY   => 'в валюте',
        self::TARIFF_PARAM_PARAM_DISCOUNT   => '% от скидки',
    ];

    public static $paramsTypesParams = [
        self::TARIFF_PARAM_TYPE_DISCOUNT    => [self::TARIFF_PARAM_PARAM_PERCENT, self::TARIFF_PARAM_PARAM_CURRENCY],
        self::TARIFF_PARAM_TYPE_MARKUP      => [self::TARIFF_PARAM_PARAM_PERCENT, self::TARIFF_PARAM_PARAM_CURRENCY],
        self::TARIFF_PARAM_TYPE_FIX_PRICE   => [self::TARIFF_PARAM_PARAM_CURRENCY],
        self::TARIFF_PARAM_TYPE_SUPPLIER    => [self::TARIFF_PARAM_PARAM_PERCENT, self::TARIFF_PARAM_PARAM_CURRENCY, self::TARIFF_PARAM_PARAM_DISCOUNT],
    ];

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

        $sql = (new Builder())->select()
            ->from('V_WEB_TARIF_LIST t')
            ->whereNotIn('t.tarif_id', [0, -1])
            ->where('t.tarif_status = ' . Model_Tariff::TARIFF_STATUS_ACTIVE)
            ->orderBy('t.TARIF_NAME')
        ;

        if(!empty($params['agent_id'])){
            $sql->where("t.agent_id = ".$params['agent_id']);
        }

        if(!empty($params['search'])){
            if (is_numeric($params['search'])) {
                $sql
                    ->whereStart()
                    ->where('t.tarif_id = ' . (int)$params['search'])
                    ->whereOr("upper(t.TARIF_NAME) like ".mb_strtoupper(Oracle::quote('%'.$params['search'].'%')))
                    ->whereEnd()
                ;
            } else {
                $sql->where("upper(t.TARIF_NAME) like " . mb_strtoupper(Oracle::quote('%' . $params['search'] . '%')));
            }
        }

        if(!empty($params['tariff_id'])){
            $sql->where('t.tarif_id = ' . (int)$params['tariff_id']);
        }

        return $db->query($sql);
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

        $sql = "select * from ".Oracle::$prefix."V_WEB_TARIF_SECTIONS t where t.tarif_id = {$tariffId} and t.version_id = {$lastVersion} order by t.SECTION_NUM, t.CONDITION_NUM";

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

        $sectionParams = $db->row($sql);

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

    /**
     * рисуем справочник
     *
     * @param bool $uid
     * @param bool $reference
     */
    public static function buildReference($uid, $reference = false)
    {
        if(empty($reference)){
            $reference = Model_Tariff::getReference();
        }

        $content = View::factory('forms/tariffs/reference')
            ->bind('uid', $uid)
            ->bind('reference', $reference)
        ;
        return $content;
    }

    /**
     * рисуем справочник параметров
     *
     * @param bool $uid
     * @param bool $params
     */
    public static function buildParams($uid, $params = [])
    {
        $content = View::factory('forms/tariffs/params')
            ->bind('uid', $uid)
            ->bind('params', $params)
        ;
        return $content;
    }

    /**
     * рисуем секцию
     *
     * @param $uidSection
     * @param $section
     * @param $tariff
     * @param $conditions
     * @param $reference
     */
    public static function buildSection($uidSection, $section, $tariffId = false, $conditions = [], $reference = [])
    {
        $content = View::factory('forms/tariffs/section')
            ->bind('uidSection', $uidSection)
            ->bind('section', $section)
            ->bind('conditions', $conditions)
            ->bind('reference', $reference)
            ->bind('tariffId', $tariffId)
        ;
        return $content;
    }

    /**
     * редактируем тариф
     *
     * @param $tariffId
     * @param $params
     */
    public static function edit($params,  $tariffId = 0)
    {
        if(empty($params)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        //редактирование тарифа
        $data = [
            'p_tarif_id' 	    => $tariffId,
            'p_tarif_name' 	    => $params['name'],
            'p_tarif_descr'     => '',
            'p_tarif_agent'     => $user['AGENT_ID'],
            'p_manager_id' 	    => $user['MANAGER_ID'],
            'p_version_id' 	    => 'out',
            'p_error_code' 	    => 'out',
        ];

        $res = $db->procedure('ctrl_tarif_edit', $data, true);

        if($res['p_error_code'] != Oracle::CODE_SUCCESS){
            return false;
        }

        $versionId = $res['p_version_id'];

        //добавление
        if($tariffId == 0){
            $tariffId = $versionId;
            $versionId = 1;
        }

        if(!empty($params['sections'])) {
            foreach ($params['sections'] as $sectionNum => $section) {
                $sectionNum += 1; //так как начали с 0, а нужны нормальные числа

                if(!empty($section['conditions'])) {
                    //редактирование условий
                    foreach ($section['conditions'] as $conditionNum => $condition) {
                        $conditionNum += 1; //так как начали с 0, а нужны нормальные числа

                        $data = [
                            'p_tarif_id' => $tariffId,
                            'p_version_id' => $versionId,
                            'p_section_num' => $sectionNum,
                            'p_condition_num' => $conditionNum,
                            'p_condition_id' => $condition['CONDITION_ID'],
                            'p_compare_id' => $condition['COMPARE_ID'],
                            'p_condition_value' => $condition['CONDITION_VALUE'],
                            'p_manager_id' => $user['MANAGER_ID'],
                            'p_error_code' => 'out',
                        ];

                        $res = $db->procedure('ctrl_tarif_sections', $data);

                        if ($res == Oracle::CODE_ERROR) {
                            return false;
                        }
                    }
                }

                //редактирование параметров
                $data = [
                    'p_tarif_id' => $tariffId,
                    'p_version_id' => $versionId,
                    'p_section_num' => $sectionNum,
                    'p_disc_type' => $section['params']['DISC_TYPE'],
                    'p_disc_param' => $section['params']['DISC_PARAM'],
                    'p_disc_value' => str_replace(',', '.', $section['params']['DISC_VALUE']),
                    'p_close_calculation' => (int)$section['params']['CLOSE_CALCULATION'],
                    'p_manager_id' => $user['MANAGER_ID'],
                    'p_error_code' => 'out',
                ];

                $res = $db->procedure('ctrl_tarif_params', $data);

                if ($res == Oracle::CODE_ERROR) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * получаем доступные версии тарифов
     *
     * @param $tariffId
     * @return array|bool
     */
    public static function getVersions($tariffId)
    {
        if (empty($tariffId)) {
            return false;
        }

        $sql = (new Builder())->select()
            ->from('V_WEB_TARIF_HISTORY')
            ->where('tarif_id = ' . $tariffId)
            ->orderBy('VERSION_ID desc')
        ;

        return Oracle::init()->query($sql);
    }

    /**
     * редактирование статуса тарифа
     *
     * @param $tariffId
     * @param $status
     * @return bool
     */
    public static function changeTariffStatus($tariffId, $status)
    {
        if (empty($tariffId) || empty($status)) {
            return false;
        }

        $data = [
            'p_tarif_id' 	    => $tariffId,
            'p_new_status' 	    => $status,
            'p_manager_id' 	    => User::id(),
            'p_error_code' 	    => 'out',
        ];

        $res = Oracle::init()->procedure('ctrl_tarif_status', $data, true);

        if($res['p_error_code'] != Oracle::CODE_SUCCESS){
            if($res['p_error_code'] == Oracle::CODE_ERROR_EXISTS) {
                Messages::put('Тариф закреплен за договором');
            }

            return false;
        }
        return true;
    }

    /**
     * ставим расчет тарифа в очередь
     *
     * @param $tariffId
     * @param $contractId
     * @param array $params
     * @return bool
     */
    public static function calcTariff($tariffId, $contractId, $params = [])
    {
        if (empty($tariffId) || empty($contractId)) {
            return false;
        }

        $data = [
            'p_contract_id' 	=> $contractId,
            'p_tarif_id' 	    => $tariffId,
            'p_tarif_version' 	=> null,
            'p_date_begin' 	    => !empty($params['start']) ? $params['start'] : date('01.m.Y'),
            'p_date_end' 	    => !empty($params['end']) ? $params['end'] : date('d.m.Y'),
            'p_manager_id' 	    => User::id(),
            'p_error_code' 	    => 'out',
        ];

        $res = Oracle::init()->procedure('srv_ctr_tarif_calculate', $data);

        if($res != Oracle::CODE_SUCCESS){
            return false;
        }
        return true;
    }

    /**
     * получаем текущую очередь расчета тарифов
     */
    public static function getCalcQueue($params = [])
    {
        $sql = (new Builder())->select()
            ->from('V_WEB_QUEUE_TARIF_CALC')
            ->where('agent_id = ' . User::current()['AGENT_ID'])
            ->orderBy('record_id')
        ;

        if (isset($params['RECORD_STATUS_ID'])) {
            $sql->whereIn('RECORD_STATUS_ID', (array)$params['RECORD_STATUS_ID']);
        }

        return Oracle::init()->query($sql);
    }
}