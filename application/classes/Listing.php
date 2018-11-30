<?php defined('SYSPATH') or die('No direct script access.');

class Listing
{
    const SERVICE_GROUP_FUEL = 'Топливо';
    const SERVICE_GROUP_WASH = 'Услуги мойки';

    public static $limit = 10;

    /**
     * список стран
     *
     * @param $search
     * @param ids
     * @return array|bool|int
     */
    public static function getCountries($search, $ids = [])
    {
        if(empty($search) && empty($ids)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_DIC_COUNTRY t where 1=1";

        if(!empty($search)){
            $sql .= " and upper(t.NAME_RU) like ".mb_strtoupper(Oracle::quoteLike('%'.$search.'%'));
        }

        if(!empty($ids)){
            $sql .= " and t.id in (".implode(',', $ids).")";
        }

        $sql .= " order by t.name_ru";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список услуг
     *
     * @param $params
     * @return array|bool|int
     */
    public static function getServices($params = [])
    {
        $user = Auth::instance()->get_user();

        if (!empty($params['description'])) {
            $description = $params['description'];
        }else{
            $description = 'LONG_DESC';
            if (array_key_exists('TUBE_ID', $params)) {
                $description = 'FOREIGN_DESC';
            }
        }

        $sql = (new Builder())->select([
                't.SERVICE_ID',
                't.MEASURE',
                't.' . $description
            ])->distinct()
            ->from('V_WEB_SERVICE_LIST t')
            ->where('t.agent_id = ' . $user['AGENT_ID'])
            ->orderBy('t.' . $description)
            ->limit(self::$limit)
        ;

        if(!empty($params['ids'])){
            $sql->where('t.SERVICE_ID in ('.implode(',', $params['ids']).')');
        } else {

            if (!empty($params['search'])) {
                $sql->where("upper(t.long_desc) like " . mb_strtoupper(Oracle::quoteLike('%' . $params['search'] . '%')));
            }

            if (!empty($params['TUBE_ID'])) {
                $sql->where("t.TUBE_ID = " . intval($params['TUBE_ID']));
            }

            if (!empty($params['SYSTEM_SERVICE_CATEGORY'])) {
                $sql->columns([
                    't.SYSTEM_SERVICE_CATEGORY'
                ]);
            }
        }

        $services = Oracle::init()->query($sql);

        foreach ($services as &$service) {
            unset($service['RNUM']);
        }

        return $services;
    }

    /**
     * список карт
     *
     * @param string $params
     * @param array $ids
     * @return array|bool|int
     */
    public static function getCards($params, $ids = [])
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_CARDS_ALL t')
            ->where('t.agent_id = ' . $user['AGENT_ID'])
            ->orderBy('t.card_id')
            ->limit(self::$limit)
        ;

        if(!empty($ids)){
            $sql->where("t.CARD_ID in (".implode(',', $ids).")");
        } else {
            if(!empty($params['search'])){
                $sql->where("t.CARD_ID like ".Oracle::quoteLike('%'.$params['search'].'%'));
            }
            if(!empty($params['contract_id'])){
                $sql->where("t.contract_id = ".(int)$params['contract_id']);
            }
        }

        return $db->query($sql);
    }

    /**
     * список карт
     *
     * @param string $search
     * @param array ids
     * @return array|bool|int
     */
    public static function getCardsAvailable($search, $ids = [])
    {
        if(empty($search) && empty($ids)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_CRD_AVAILABLE t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and t.CARD_ID like ".Oracle::quoteLike('%'.$search.'%');
        }

        if(!empty($ids)){
            $sql .= " and t.CARD_ID in (".implode(',', $ids).")";
        }

        $sql .= " order by t.card_id";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список поставщиков
     *
     * @param $search
     * @param ids
     * @return array|bool|int
     */
    public static function getSuppliers($search = '', $ids = [])
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SUPPLIERS_LIST t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and upper(t.SUPPLIER_NAME) like ".mb_strtoupper(Oracle::quoteLike('%'.$search.'%'));
        }

        if(!empty($ids)){
            $sql .= " and t.ID in (".implode(',', $ids).")";
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список контрактов поставщиков
     *
     * @param $search
     * @return array|bool|int
     */
    public static function getSuppliersContracts($supplierId, $search = '')
    {
        if(empty($supplierId)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SUPPLIERS_CONTRACTS t where t.agent_id = ".$user['AGENT_ID']." and t.supplier_id = ".$supplierId;

        if(!empty($search)){
            $sql .= " and upper(t.CONTRACT_NAME) like ".mb_strtoupper(Oracle::quoteLike('%'.$search.'%'));
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список труб
     *
     * @param $search
     * @param $ids
     * @return array|bool|int
     */
    public static function getTubes($search = '', $ids = [])
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_TUBES_LIST')
            ->where('agent_id = '.$user['AGENT_ID'])
        ;

        if(!empty($search)){
            $sql->where("upper(TUBE_NAME) like " . mb_strtoupper(Oracle::quoteLike('%'.$search.'%')));
        }

        if(!empty($ids)){
            $sql->whereIn('TUBE_ID', $ids);
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * получаем список агентов
     *
     * @return array|bool
     */
    public static function getAgents()
    {
        $db = Oracle::init();

        $sql = (new Builder())->select(['group_id', 'group_name'])->distinct()
            ->from('V_WEB_AGENTS_LIST')
        ;

        return $db->query($sql);
    }
}