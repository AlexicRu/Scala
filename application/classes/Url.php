<?php defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL 
{
	public static function generate($key, $value)
	{
		$params = !empty($_GET) ? $_GET: array();
		
		if(!empty($_POST['search_client'])){
			$params = array('q' => $_POST['search_client']);
		}
        if(!empty($_POST['filter_group'])){
            $params = array('filter_group' => $_POST['filter_group']);
        }
        if(!empty($_POST['filter_condition'])){
            $params = array('filter_condition' => $_POST['filter_condition']);
        }

        $params[$key] = $value;
		
		$link = array();
		
		foreach($params as $k => $v){
			$link[] = $k.'='.urlencode($v);
		}
		
		$url = explode('?', $_SERVER['REQUEST_URI']);
		$url = $url[0];
		
		return $url.'?'.implode('&', $link);
	}
}