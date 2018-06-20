<?php defined('SYSPATH') or die('No direct script access.');

class Model_News extends Model
{
    /**
     * грузим новости
     *
     * @param array $params
     * @param bool $user
     * @return array
     */
    public static function getList($params = [], $user = false)
    {
        if(empty($user)){
            $user = Auth::instance()->get_user();
        }

        $db = Oracle::init();

        $agentIds = [$user['AGENT_ID']];
        if(!in_array($user['ROLE_ID'], array_keys(Access::$clientRoles))){
            $agentIds[] = 0;
        }

        $sql = "select * from ".Oracle::$prefix."V_WEB_NEWS where agent_id in (".implode(',', $agentIds).")";

        $sql .= ' order by DATE_CREATE desc';

        if(!empty($params['pagination'])) {
            list($news, $more) = $db->pagination($sql, $params);

            foreach($news as &$newsDetail){
                $newsDetail['announce'] = strip_tags($newsDetail['CONTENT']);

                if(strlen($newsDetail['announce']) > 500){
                    $newsDetail['announce'] = mb_strcut($newsDetail['announce'], 0, 500);
                }
            }

            return [$news, $more];
        }

        $news = $db->tree($sql, 'NEWS_ID', true);

        foreach($news as &$newsDetail){
            $newsDetail['announce'] = strip_tags($newsDetail['CONTENT']);

            if(strlen($newsDetail['announce']) > 500){
                $newsDetail['announce'] = mb_strcut($newsDetail['announce'], 0, 500);
            }
        }

        return $news;
    }

    /**
     * получаем конкретную новость
     *
     * @param $newsId
     */
    public static function getNewsById($newsId)
    {
        if(empty($newsId)){
            return false;
        }

        $detail = self::getList([
            'pagination'    => false,
            'id'            => $newsId
        ]);

        if(!empty($detail[$newsId])){
            return $detail[$newsId];
        }

        return false;
    }

    /**
     * добавляем новость
     *
     * @param $params
     */
    public static function editNews($params)
    {
        if(empty($params)){
            return false;
        }

        $db = Oracle::init();

        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        $image = (!empty($params['image']) && is_file($path.$params['image'])) ? $params['image'] : '';

        $data = [
            'p_type_id' 	    => 0,
            'p_announce' 	    => 'анонс',
            'p_title' 		    => $params['title'],
            'p_content' 	    => $params['text'],
            'p_picture' 	    => $image,
            'p_is_published' 	=> 1,
            'p_error_code' 	    => 'out',
        ];

        if(!empty($params['id'])) {
            $preData = ['p_news_id' => $params['id']];

            $res = $db->procedure('news_modify', array_merge($preData, $data));
        } else {
            $user = Auth::instance()->get_user();
            $preData = ['p_agent_id' => $user['AGENT_ID']];

            $res = $db->procedure('news_add', array_merge($preData, $data));
        }

        if($res == Oracle::CODE_ERROR){
            return false;
        }
        return true;
    }
}