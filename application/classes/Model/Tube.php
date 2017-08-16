<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tube extends Model
{
    /**
     * получаем список труб
     *
     * @return array|bool
     */
    public static function getTubes()
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_TUBES_LIST')
            ->where('is_owner = 1')
            ->where('agent_id = '.$user['AGENT_ID'])
        ;

        return $db->query($sql);
    }
}