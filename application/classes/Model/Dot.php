<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dot extends Model
{
    /**
     * получаем список групп точек
     *
     * @return array|int
     */
    public static function getGroups($filter)
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_POS_GROUPS t where t.agent_id = ".$user['AGENT_ID']
        ;

        if(!empty($filter['search'])){
            $sql .= " and upper(t.group_name) like '%".mb_strtoupper(Oracle::quote($filter['search']))."%'";
        }

        return $db->query($sql);
    }

    /**
     * получение списка точек
     *
     * @param $params
     */
    public static function getGroupDots($params)
    {
        if(empty($params['group_id'])){
            return false;
        }

        $db = Oracle::init();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_POS_GROUP_ITEMS t where t.group_id = ".$params['group_id']
        ;

        return $db->pagination($sql, $params);
    }
}