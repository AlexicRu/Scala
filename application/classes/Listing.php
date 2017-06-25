<?php defined('SYSPATH') or die('No direct script access.');

class Listing
{
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
            $sql .= " and upper(t.NAME_RU) like ".mb_strtoupper(Oracle::quote('%'.$search.'%'));
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
    public static function getServices($params)
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $description = 'LONG_DESC';
        if(array_key_exists('TUBE_ID', $params)){
            $description = 'FOREIGN_DESC';
        }

        $sql = "select distinct t.SERVICE_ID, t.{$description} from ".Oracle::$prefix."V_WEB_SERVICE_LIST t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($params['search'])){
            $sql .= " and upper(t.long_desc) like ".mb_strtoupper(Oracle::quote('%'.$params['search'].'%'));
        }

        if(!empty($params['ids'])){
            $sql .= " and t.SERVICE_ID in (".implode(',', $params['ids']).")";
        }

        if(!empty($params['TUBE_ID'])){
            $sql .= " and t.TUBE_ID = ".intval($params['TUBE_ID']);
        }

        $sql .= " order by t.{$description}";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список услуг
     *
     * @param $params
     * @return array|bool|int
     */
    public static function getServicesSupplier($params)
    {
        $db = Oracle::init();

        $sql = (new Builder())->select()
            ->from('V_WEB_SUPL_CONTRACT_SERVICES t')
            ->orderBy("t.LONG_DESC")
            ->limit(self::$limit)
            ->columns([
                't.SERVICE_ID',
                't.LONG_DESC'
            ])
        ;

        if(!empty($params['search'])){
            $sql->where("upper(t.long_desc) like ".mb_strtoupper(Oracle::quote('%'.$params['search'].'%')));
        }

        if(!empty($params['ids'])){
            $sql->where("t.SERVICE_ID in (".implode(',', $params['ids']).")");
        }

        if(!empty($params['params']['contract_id'])){
            $sql->where("and t.contract_id  = ".intval($params['params']['contract_id']));
        }

        return $db->query($sql);
    }

    /**
     * список групп точек для поставщика
     *
     * @param $params
     */
    public static function getSupplierDotsGroups($params)
    {
        $db = Oracle::init();

        $sql = (new Builder())->select()
            ->from('V_WEB_SUPL_POS_GROUPS t')
        ;

        if(!empty($params['search'])){
            $sql->where("upper(t.long_desc) like ".mb_strtoupper(Oracle::quote('%'.$params['search'].'%')));
        }

        if(!empty($params['ids'])){
            $sql->where("t.SERVICE_ID in (".implode(',', $params['ids']).")");
        }

        if(!empty($params['params']['contract_id'])){
            $sql->where("and t.contract_id  = ".intval($params['params']['contract_id']));
        }

        return $db->query($sql);
    }

    /**
     * список карт
     *
     * @param $search
     * @param ids
     * @return array|bool|int
     */
    public static function getCards($search, $ids = [])
    {
        if(empty($search) && empty($ids)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_CARDS_ALL t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and t.CARD_ID like ".Oracle::quote('%'.$search.'%');
        }

        if(!empty($ids)){
            $sql .= " and t.CARD_ID in (".implode(',', $ids).")";
        }

        $sql .= " order by t.card_id";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список карт
     *
     * @param $search
     * @param ids
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
            $sql .= " and t.CARD_ID like ".Oracle::quote('%'.$search.'%');
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
            $sql .= " and upper(t.SUPPLIER_NAME) like ".mb_strtoupper(Oracle::quote('%'.$search.'%'));
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
            $sql .= " and upper(t.CONTRACT_NAME) like ".mb_strtoupper(Oracle::quote('%'.$search.'%'));
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список труб
     *
     * @param $search
     * @param ids
     * @return array|bool|int
     */
    public static function getTubes($search = '', $ids = [])
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_TUBES_LIST t where t.agent_id=".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and upper(t.TUBE_NAME) like ".mb_strtoupper(Oracle::quote('%'.$search.'%'));
        }

        if(!empty($ids)){
            $sql .= " and t.TUBE_ID in (".implode(',', $ids).")";
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }
}