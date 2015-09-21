<?php defined('SYSPATH') or die('No direct script access.');

class Access
{
	public static function check($roles = 1, $only = false)
	{
        if(!is_array($roles)){
            $roles = array($roles);
        }

        if(!$only){
            $roles[] = 1; //admin
			//$roles = array(1,3);
        }

        $user = Auth::instance()->get_user();

        if(in_array($user['role'], $roles)){
            return true;
        }

        return false;
	}
}