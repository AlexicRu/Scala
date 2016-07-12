<?php defined('SYSPATH') or die('No direct script access.');

class Cache extends Kohana_Cache 
{
	/**
	 * если передают массив, то переводим его в строку
	 * 
	 * @param string $id
	 * @param string $data
	 * @param int $lifetime
	 * @return bool
	 */
	public function set($id, $data, $lifetime = 3600)
	{
		if(is_array($data)){
			$data = json_encode($data);
		}
		
		return parent::set($id, $data, $lifetime);
	}

	/**
	 * если ранее записали массив, то достаем из строки массив
	 * 
	 * @param string $id
	 * @param null $default
	 * @return array|mixed
	 */
	public function get($id, $default = NULL)
	{
		$data = parent::get($id, $default);
		
		$testArray = json_decode($default, true);
		
		if(is_array($testArray)){
			$data = $testArray;
		}
		
		return $data;
	}
	
	public function delete($id)
	{
		return parent::delete($id);
	}
	
	public function delete_all()
	{
		return parent::delete_all();
	}
}