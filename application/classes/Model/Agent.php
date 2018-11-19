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

        return true;
    }
}