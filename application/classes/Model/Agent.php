<?php defined('SYSPATH') or die('No direct script access.');

class Model_Agent extends Model
{
    const AGENT_STATUS_ACTIVE   = 1;
    const AGENT_STATUS_BLOCK    = 2;

    const AGENT_PART_TITLE      = 'title';
    const AGENT_PART_INFO       = 'info';
    const AGENT_PART_SERVICE    = 'service';

    public static $agentStatuses = [
        self::AGENT_STATUS_ACTIVE   => 'В работе',
        self::AGENT_STATUS_BLOCK    => 'Заблокирован',
    ];

    public static $agentsParts = [
        self::AGENT_PART_TITLE,
        self::AGENT_PART_INFO,
        self::AGENT_PART_SERVICE,
    ];

    /**
     * Достаем инфу по агенту
     *
     * @param $agentId
     * @return bool|mixed
     */
    public static function getAgentInfo($agentId)
    {
        if (empty($agentId)) {
            return false;
        }

        $sql = (new Builder())->select()
            ->from('V_WEB_AGENT_INFO')
            ->where('agent_id = ' . (int)$agentId)
        ;

        return Oracle::init()->row($sql);
    }

    /**
     * редактируем агента
     *
     * @param $agentId
     * @param $params
     * @param string $part
     * @return bool
     */
    public static function editAgent($agentId, $params, $part = 'title')
    {
        if (empty($agentId) || empty($params) || !in_array($part, self::$agentsParts)) {
            return false;
        }

        $db = Oracle::init();

        switch ($part) {
            case self::AGENT_PART_INFO:
                $data = [
                    'p_agent_id'            => $agentId,
                    'p_agent_signer_name_1' => $params['AGENT_SIGNER_NAME_1'],
                    'p_agent_signer_name_2' => $params['AGENT_SIGNER_NAME_2'],
                    'p_agent_signer_post_1' => $params['AGENT_SIGNER_POST_1'],
                    'p_agent_signer_post_2' => $params['AGENT_SIGNER_POST_2'],
                    'p_agent_tin'           => $params['AGENT_INN'],
                    'p_agent_iec'           => $params['AGENT_KPP'],
                    'p_agent_y_address'     => $params['AGENT_Y_ADDRESS'],
                    'p_agent_p_address'     => $params['AGENT_P_ADDRESS'],
                    'p_agent_f_address'     => $params['AGENT_F_ADDRESS'],
                    'p_agent_email'         => $params['AGENT_EMAIL'],
                    'p_agent_phone'         => $params['AGENT_PHONE'],
                    'p_agent_city'          => $params['AGENT_CITY'],
                    'p_manager_id'          => User::id(),
                    'p_error_code'          => 'out',
                ];

                $res = $db->procedure('agent_info_edit', $data);

                if($res != Oracle::CODE_SUCCESS){
                    return false;
                }
                break;
        }

        return true;
    }
}