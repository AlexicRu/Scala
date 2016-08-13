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

        //todo

         /*$db = Oracle::init();

        $sql = "select * from ".Oracle::$prefix."V_WEB_NOTIFICATION where manager_id = ".$user['MANAGER_ID'];

        $sql .= ' order by date_time desc';

        if(!empty($params['pagination'])) {
            return $db->pagination($sql, $params);
        }

        return $db->query($sql);*/

        $news = [
            1 => [
                "ID" => 1,
                'SUBJECT' => 'Новость 1',
                'NOTE_DATE' => '2016-08-12',
                'NOTIFICATION_BODY' => ' flwsefgaekefuyhgaoweyu gfwouleyg fwiyetf owuey gwoyeg woue gwueyrg wieyrg wouey grwoueyr gwouey',
                'IMG' => '/img/pic/01.jpg'
            ],
        ];

        if(!empty($params['pagination'])) {
            return [$news, true];
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
    public static function addNews($params)
    {
        if(empty($params)){
            return false;
        }

        //todo

        return true;
    }
}