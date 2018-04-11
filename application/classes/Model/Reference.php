<?php defined('SYSPATH') or die('No direct script access.');

class Model_Reference extends Model
{
    /**
     * получаем список услуг
     *
     * @return array|bool
     */
    public static function getConverterServices($arParams = [])
    {
        $db = Oracle::init();

        $user = User::current();

        $sql = (new Builder())->select()
            ->from('V_WEB_SERVICE_CONVERTION')
            ->where('agent_id = '.$user['AGENT_ID'])
        ;

        if (!empty($arParams['tube_id'])) {
            $sql->where('tube_id = '.Num::toInt($arParams['tube_id']));
        }

        return $db->query($sql);
    }

    /**
     * доабвление нового наименования услуги
     *
     * @param $serviceId
     * @param $tubeId
     * @param $name
     */
    public static function addConvertService($serviceId, $tubeId, $name)
    {
        if (empty($serviceId) || empty($tubeId) || empty($name)) {
            return [0, 'Неверные данные'];
        }

        $db = Oracle::init();
        $user = User::current();

        $data = [
            'p_tube_id' 	        => $tubeId,
            'p_service_id' 	        => $serviceId,
            'p_service_tube_name' 	=> $name,
            'p_manager_id' 	        => $user['MANAGER_ID'],
            'p_error_code' 	        => 'out',
        ];

        $res = $db->procedure('dic_srv_convertion_add', $data);

        $error = '';
        $result = 1;

        switch ($res) {
            case Oracle::CODE_ERROR:
                $error = 'Ошибка';
                $result = 0;
                break;
            case Oracle::CODE_ERROR_EXISTS:
                $error = 'Услуга уже закреплена за источником';
                $result = 0;
                break;
        }

        return [$result, $error];
    }
}