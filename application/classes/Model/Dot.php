<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dot extends Model
{
    const GROUP_TYPE_USER       = 1;
    const GROUP_TYPE_SUPPLIER   = 2;

    public static $groupsTypesNames = [
        self::GROUP_TYPE_USER       => 'Пользовательская группа',
        self::GROUP_TYPE_SUPPLIER   => 'Группа точек поставщика',
    ];

    /**
     * обертка для getGroups
     *
     * @param $groupId
     * @return bool
     */
    public static function getGroup($groupId)
    {
        if(empty($groupId)){
            return false;
        }

        $groups = self::getGroups(['ids' => [$groupId]]);

        if(empty($groups[0])){
            return false;
        }

        return $groups[0];
    }

    /**
     * получаем список групп точек
     *
     * @return array|int
     */
    public static function getGroups($filter = [])
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = "
            select * from ".Oracle::$prefix."V_WEB_POS_GROUPS t where t.agent_id = ".$user['AGENT_ID']
        ;

        if(!empty($filter['search'])){
            $sql .= " and upper(t.group_name) like ".mb_strtoupper(Oracle::quote('%'.$filter['search'].'%'));
        }

        if(!empty($filter['ids'])){
            $sql .= " and t.group_id in (".implode(',', $filter['ids']).")";
        }

        if(!empty($filter['group_type'])){
            if(!is_array($filter['group_type'])){
                $filter['group_type'] = [Oracle::quote($filter['group_type'])];
            }
            $sql .= " and t.group_type in (".implode(',', $filter['group_type']).")";
        }

        $sql .= ' order by group_name ';

        if(!empty($filter['limit'])){
            return $db->query($db->limit($sql, 0, $filter['limit']));
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

        $user = Auth::instance()->get_user();
        $sql = "
            select * from ".Oracle::$prefix."V_WEB_POS_GROUP_ITEMS t where t.group_id = ".$params['group_id']." and t.agent_id = ".$user['AGENT_ID']." order by id_to";

        if (!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
    }

    /**
     * получение списка точек
     *
     * @param $params
     */
    public static function getDots($params)
    {
        $db = Oracle::init();

        $user = Auth::instance()->get_user();

        $sql = (new Builder())->select()
            ->from('V_WEB_POS_LIST t')
            ->where("t.agent_id = ".$user['AGENT_ID'])
        ;

        if(!empty($params['group_id'])){
            $sql->where('not exists (select 1 from '.Oracle::$prefix.'V_WEB_POS_GROUP_ITEMS pg where pg.group_id = '.intval($params['group_id']).' and pg.POS_ID = t.POS_ID)');
        }
        if(!empty($params['POS_ID'])){
            if (!is_array($params['POS_ID'])) {
                $params['POS_ID'] = [$params['POS_ID']];
            }
            $sql->where('t.POS_ID in ('.implode(',', ($params['POS_ID'])).')');
        }
        if(!empty($params['PROJECT_NAME'])){
            $sql->where("upper(t.PROJECT_NAME) like ".mb_strtoupper(Oracle::quote('%'.$params['PROJECT_NAME'].'%')));
        }
        if(!empty($params['ID_EMITENT'])){
            $sql->where('t.ID_EMITENT = '.intval($params['ID_EMITENT']));
        }
        if(!empty($params['ID_TO'])){
            $sql->where("t.ID_TO like ".Oracle::quote('%'.$params['ID_TO'].'%'));
        }
        if(!empty($params['POS_NAME'])){
            $sql->where("upper(t.POS_NAME) like ".mb_strtoupper(Oracle::quote('%'.$params['POS_NAME'].'%')));
        }
        if(!empty($params['OWNER'])){
            $sql->where("upper(t.OWNER) like ".mb_strtoupper(Oracle::quote('%'.$params['OWNER'].'%')));
        }
        if(!empty($params['POS_ADDRESS'])){
            $sql->where("upper(t.POS_ADDRESS) like ".mb_strtoupper(Oracle::quote('%'.$params['POS_ADDRESS'].'%')));
        }

        if (!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }
        return $db->query($sql);
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
            'p_pos_id'       => [$posIds, SQLT_INT],
            'p_manager_id'   => $user['MANAGER_ID'],
            'p_error_code' 	 => 'out',
        ];

        return $db->procedure('ctrl_pos_group_collection', $data);
    }

    /**
     * полчаем список доступных групп точек
     */
    public static function getGroupTypesNames()
    {
        $user = Auth::instance()->get_user();

        if(!in_array($user['role'], Access::$adminRoles)){
            unset(self::$groupsTypesNames[self::GROUP_TYPE_SUPPLIER]);
        }

        return self::$groupsTypesNames;
    }
}