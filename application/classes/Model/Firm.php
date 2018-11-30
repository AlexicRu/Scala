<?php defined('SYSPATH') or die('No direct script access.');

class Model_Firm extends Model
{
    /**
     * получаем список групп
     *
     * @return array|int
     */
    public static function getFirmsGroups($filter = [])
    {
        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_CLIENT_GROUPS')
            ->where('manager_id = '.$user['MANAGER_ID'])
        ;

        if(!empty($filter['search'])){
            $sql->where("upper(t.group_name) like ".mb_strtoupper(Oracle::quoteLike('%'.$filter['search'].'%')));
        }

        return Oracle::init()->query($sql);
    }

    /**
     * добавляем группу фирм
     *
     * @param $params
     */
    public static function addFirmsGroup($params)
    {
        if(empty($params['name'])){
            return false;
        }

        $db = Oracle::init();

        $user = User::current();

        $data = [
            'p_group_name'    => $params['name'],
            'p_manager_id'    => $user['MANAGER_ID'],
            'p_group_id'      => 'out',
            'p_error_code' 	  => 'out',
        ];

        $result = $db->procedure('ctrl_client_group_add', $data, true);

        return $result == Oracle::CODE_SUCCESS;
    }
}