<?php defined('SYSPATH') or die('No direct script access.');

class Listing
{
    public static $limit = 10;

    /**
     * список стран
     *
     * @param $search
     * @return array|bool|int
     */
    public static function getCountries($search)
    {
        if(empty($search)){
            return false;
        }

        $db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_DIC_COUNTRY t where 1=1";

        if(!empty($search)){
            $sql .= " and upper(t.NAME_RU) like '%".mb_strtoupper(Oracle::quote($search))."%'";
        }

        $sql .= " order by t.name_ru";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список услуг
     *
     * @param $search
     * @return array|bool|int
     */
    public static function getServices($search)
    {
        if(empty($search)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SERVICE_LIST t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and upper(t.long_desc) like '%".mb_strtoupper(Oracle::quote($search))."%'";
        }

        $sql .= " order by t.long_desc";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список карт
     *
     * @param $search
     * @return array|bool|int
     */
    public static function getCards($search)
    {
        if(empty($search)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_CARDS_ALL t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and t.CARD_ID like '%".Oracle::quote($search)."%'";
        }

        $sql .= " order by t.card_id";

        return $db->query($db->limit($sql, 0, self::$limit));
    }

    /**
     * список поставщиков
     *
     * @param $search
     * @return array|bool|int
     */
    public static function getSuppliers($search)
    {
        if(empty($search)){
            return false;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "select * from ".Oracle::$prefix."V_WEB_SUPPLIERS_LIST t where t.agent_id = ".$user['AGENT_ID'];

        if(!empty($search)){
            $sql .= " and upper(t.SUPPLIER_NAME) like '%".mb_strtoupper(Oracle::quote($search))."%'";
        }

        return $db->query($db->limit($sql, 0, self::$limit));
    }
}