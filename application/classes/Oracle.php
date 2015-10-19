<?php defined('SYSPATH') or die('No direct script access.');

class Oracle{
	public static $prefix = 's_dev.';
	private static $_conn = null;
	private static $_instance = null;

	private function __construct() {}
    public function __destruct() {
        oci_close(self::$_conn);
    }
	protected function __clone() {}

	static public function init() {
		if(is_null(self::$_instance))
		{
			$config = Kohana::$config->load('database');

            self::$_conn = oci_connect($config['name'], $config['password'], $config['db'], 'UTF8');

			if (!self::$_conn) {
				$e = oci_error();
				trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}

			self::$_instance = new self();
		}
		return self::$_instance;
	}

	static public function query($sql, $type='select'){
		if($type == 'select'){
			$ret = array();
			$res = oci_parse(self::$_conn, $sql);
			
			oci_execute($res);
			while ($row = oci_fetch_array($res, OCI_ASSOC+OCI_RETURN_NULLS)) {
				$ret[] = $row;
			}
			return $ret;
		}else{
			$res = oci_parse(self::$_conn, $sql);
			oci_execute($res);
			return 1;
		}
	}
	
	static public function ora_proced($sql, $params)
	{
		$res = oci_parse(self::$_conn, $sql);
		
		foreach($params as $key=>&$param){
			if($param == 'out'){
				oci_bind_by_name($res, ':'.$key, $param, 255);
			}else{
				oci_bind_by_name($res, ':'.$key, $param);
			}
		}

		oci_execute($res, OCI_DEFAULT);
		return $params;
	}

	static public function update($sql){
		return self::query($sql, "update");
	}
	
	static public function insert($sql){
		return self::query($sql, "update");
	}

	static public function row($sql){
		$r = self::query($sql);
		if(!empty($r) && !empty($r[0])){
			return $r[0];
		}
		return false;
	}

	static public function column($sql, $column_key){
		$r = self::query($sql);

		if(!empty($r) && count($r)){
			$arr = array();

			foreach($r as $row){
				foreach($row as $k=>$elem){
					if(strtolower($column_key) == strtolower($k)){
						$arr[] = $elem;
					}
				}	
			}
			return $arr;
		}
		return false;
	}	

	static public function one($sql){
		$r = self::query($sql);
		if(!empty($r) && !empty($r[0])){
			return array_pop($r[0]);
		}
		return false;
	}

	/**
	 * создаем древовидную структуру, где ключом является одно из полей
	 *
	 * @param $sql
	 * @param $field
	 * @param $noArray
	 */
	public static function tree($sql, $field, $noArray = false)
	{
		$result = self::query($sql);

		$return = [];

		if(!empty($result)){
			$check = reset($result);

			if(!isset($check[$field])){
				return $return;
			}

			foreach($result as $row){
				if($noArray) {
					$return[$row[$field]] = $row;
				}else{
					$return[$row[$field]][] = $row;
				}
			}
		}

		return $return;
	}

	/**
	 * экранируем
	 *
	 * @param $val
	 */
	public static function quote($val)
	{
		return addslashes(trim($val));
	}
}