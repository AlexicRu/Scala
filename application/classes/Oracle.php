<?php defined('SYSPATH') or die('No direct script access.');

class Oracle{

	const CODE_SUCCESS      = 0;
	const CODE_ERROR        = 1;
	const CODE_ERROR_EXISTS = 2;

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

	public function query($sql, $type='select')
    {
        try {
            if ($type == 'select') {
                $ret = array();
                $res = oci_parse(self::$_conn, $sql);

                oci_execute($res);
                while ($row = oci_fetch_array($res, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $ret[] = $row;
                }
                return $ret;
            } else {
                $res = oci_parse(self::$_conn, $sql);
                return oci_execute($res);
            }
        } catch (Exception $e) {
            return false;
        }
	}

	/**
	 * непосредственное выполнение сформированной процедуры
	 *
	 * @param $sql
	 * @param $params
	 * @return mixed
	 */
	public function ora_proced($sql, $params)
	{
		$res = oci_parse(self::$_conn, $sql);
		
		foreach($params as $key=>&$param){
			if($param == 'out'){
				oci_bind_by_name($res, ':'.$key, $param, 255);
			}else{
			    if(is_array($param)){
                    oci_bind_array_by_name($res, ':' . $key, $param[0], count($param[0]), -1, $param[1]);
                }else {
                    oci_bind_by_name($res, ':' . $key, $param);
                }
			}
		}

		oci_execute($res, OCI_DEFAULT);
		return $params;
	}

	public function update($sql){
		return $this->query($sql, "update");
	}
	
	public function insert($sql){
		return $this->query($sql, "update");
	}

	public function row($sql){
		$r = $this->query($sql);
		if(!empty($r) && !empty($r[0])){
			return $r[0];
		}
		return false;
	}

	public function column($sql, $column_key){
		$r = $this->query($sql);

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

	public function one($sql){
		$r = $this->query($sql);
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
	public function tree($sql, $field, $noArray = false)
	{
		$result = $this->query($sql);

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
		return "'".str_replace("'", "''", trim($val))."'";
	}

	/**
	 * Выполнение процедуры
	 */
	public function procedure($procedure, $data, $fullResponse = false)
	{
		if(empty($procedure) || empty($data)){
			return self::CODE_ERROR;
		}

		$keys = [];
		foreach(array_keys($data) as $key){
			$keys[] = ':'.$key;
		}

		$proc = 'begin '.self::$prefix.'web_pack.'.$procedure.'('.implode(', ', $keys).'); end;';

		$res = $this->ora_proced($proc, $data);

        if($fullResponse){
            return $res;
        }

		if(isset($res['p_error_code'])){
			return $res['p_error_code'];
		}

		return self::CODE_ERROR;
	}

	/**
	 * вот такой вот кривой лимит с офсетом
	 * @param $sql
	 * @param $offset
	 * @param $limit
	 * @return string
	 */
	public function limit($sql, $offset = 0, $limit = 9999999)
	{
		$sql = "
			select * from (
			  select a.*, ROWNUM rnum from (
				{$sql}
			  ) a where rownum <= ".$limit."
			) where rnum > ".$offset."
		";

		return $sql;
	}

	/**
	 * подготавливаем запрос для пагиниции
	 * @param $sql
	 * @param $params
	 * @return mixed
	 */
	public function pagination($sql, $params)
	{
		if(empty($params['offset'])){
			$params['offset'] = 0;
		}
		if(empty($params['limit'])){
			$params['limit'] = 10;
		}

		$from = $params['offset'];
		$to = $params['limit']+$params['offset'];

		if(!empty($params['pagination'])){
			$to++;
		}

		$sql = $this->limit($sql, $from, $to);

		$items = $this->query($sql);

		$more = false;
		if (count($items) > $params['limit']) {
			$more = true;
			array_pop($items);
		}

		return [$items, $more];
	}

	/**
	 * меняем запятую на точку, это для корректной записи в базу
	 *
	 * @param $number
	 * @return mixed
	 */
	public static function toFloat($number)
	{
		return str_replace(',', '.', $number);
	}

    /**
     * возвращает инт
     *
     * @param $number
     * @return int
     */
	public static function toInt($number)
    {
        return (int)$number;
    }

    /**
     * переводим строку в дату
     *
     * @param $string
     * @return false|string
     */
	public static function toDate($string)
    {
        $dateTime = DateTime::createFromFormat('d.m.Y H:i:s', $string);

        return "'".$dateTime->format('d.m.Y')."'";
    }

    /**
     * переводим строку в оракловую дату
     *
     * @param $string
     * @return string
     */
    public static function toDateOracle($string)
    {
        $dateTime = DateTime::createFromFormat('d.m.Y H:i:s', $string);

        return "to_date('".$dateTime->format('d.m.Y')."', 'dd.mm.yyyy')";
    }
}