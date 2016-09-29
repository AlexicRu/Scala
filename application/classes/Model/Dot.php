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
     * получение списка точек по группе
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

    /**
     * получение списка точек
     *
     * @param $params
     */
    public static function getDots($params)
    {
        $db = Oracle::init();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_POS_LIST t where 1 = 1
        ";
        
        if(!empty($params['group_id'])){
            $sql .= ' and not exists (select 1 from v_pos_groups_items pg where pg.group_id = '.intval($params['group_id']).')';
        }

        if(!empty($params['POS_ID'])){
            $sql .= ' and t.POS_ID = '.intval($params['POS_ID']);
        }
        if(!empty($params['ID_EMITENT'])){
            $sql .= ' and t.ID_EMITENT = '.intval($params['ID_EMITENT']);
        }
        if(!empty($params['ID_TO'])){
            $sql .= ' and t.ID_TO like "%'.Oracle::quote($params['ID_TO']).'%"';
        }
        if(!empty($params['POS_NAME'])){
            $sql .= ' and t.POS_NAME like "%'.Oracle::quote($params['POS_NAME']).'%"';
        }
        if(!empty($params['OWNER'])){
            $sql .= ' and t.OWNER like "%'.Oracle::quote($params['OWNER']).'%"';
        }
        if(!empty($params['POS_ADDRESS'])){
            $sql .= ' and t.POS_ADDRESS like "%'.Oracle::quote($params['POS_ADDRESS']).'%"';
        }

        return $db->pagination($sql, $params);
    }

    /**
     * добавляем точки к группе
     *
     * @param $groupId
     * @param $posIds
     */
    public static function addDotsToGroup($groupId, $posIds)
    {
        if(empty($groupId) || empty($posIds)){
            return Oracle::CODE_ERROR;
        }

        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $data = [
            'p_pos_group_id' => $groupId,
            'p_action'       => 1,
            'p_pos_id'       => $posIds,
            'p_manager_id'   => $user['MANAGER_ID'],
            'p_error_code' 	 => 'out',
        ];
print_r($data);die;
        return $db->procedure('ctrl_pos_group_collection', $data);
    }
}