<?php defined('SYSPATH') or die('No direct script access.');

class Oracle{

	const CODE_SUCCESS      = 0;
	const CODE_ERROR        = 1;
	const CODE_ERROR_EXISTS = 2;

    /**
     * @deprecated
     */
	public static $prefix = 's_dev.';

    protected $_prefix = 's_dev.';
    protected $_pack = 'web_pack.';

    protected $_conn = null;
    protected static $_instances = [];

    protected $_fullResponse = [];

    /**
     * Oracle constructor.
     * @param string $configName
     * @throws Kohana_Exception
     */
    protected function __construct($configName = 'database')
    {
        $config = Kohana::$config->load($configName);

        $this->_conn = oci_connect($config['name'], $config['password'], $config['db'], 'UTF8');

        if (!$this->_conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        return $this;
    }

    public function __destruct()
    {
        oci_close($this->_conn);
    }

	protected function __clone() {}

    /**
     * @param string $instanceName
     * @param string $configName
     * @return self
     */
	static public function init($instanceName = 'default', $configName = 'database')
    {
		if(!isset(self::$_instances[$instanceName]))
		{
            self::$_instances[$instanceName] = new self($configName);
		}

		return self::$_instances[$instanceName];
	}

    /**
     * выполняем запрос
     *
     * @param $sql Builder|string
     * @param string $type
     * @return array|bool
     */
	public function query($sql, $type='select')
    {
        //builder
        if (is_a($sql, 'Builder')) {
            $sql = $sql->build($this->_prefix);
        }

        try {
            if ($type == 'select') {
                $ret = array();
                $res = oci_parse($this->_conn, $sql);

                oci_execute($res);
                while ($row = oci_fetch_array($res, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $ret[] = $row;
                }
                return $ret;
            } else {
                $res = oci_parse($this->_conn, $sql);
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
		$res = oci_parse($this->_conn, $sql);
		
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
	 * @param $subField
	 */
	public function tree($sql, $field, $noArray = false, $subField = false)
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
					$return[$row[$field]] = !empty($subField) ? $row[$subField] : $row;
				}else{
					$return[$row[$field]][] =  !empty($subField) ? $row[$subField] : $row;
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
        $str = str_replace(["%", "*", "?"], ["\%", "\*", "\?"], $val);
        $str = preg_replace('/^\\\\%|\\\\%$/', "%", $str);

        $postfix = '';
        if ($str != $val) {
            $postfix = " ESCAPE '\' ";
        }

        return "'".str_replace(["'"], ["''"], trim($str))."'" . $postfix;
    }

    /**
     * выполнение функции
     *
     * @param $function
     * @param $data
     * @return int|mixed
     */
    public function func($function, $data)
    {
        if(empty($function) || empty($data)){
            return false;
        }

        $keys = [];
        foreach(array_keys($data) as $key){
            $keys[] = ':'.$key;
        }

        $query = 'begin :result := '.$this->_prefix.$this->_pack.$function.'('.implode(', ', $keys).'); end;';

        $data['result'] = 'out';

        $response = $this->ora_proced($query, $data);

        return $response['result'];
    }

	/**
	 * Выполнение процедуры
	 */
	public function procedure($procedure, $data, $fullResponse = false)
	{
		if(empty($procedure) || empty($data)){
			return self::CODE_ERROR;
		}

        $user = User::current();

		if (Access::checkReadOnly($procedure, $user['role'])) {
            Messages::put('Данной роли разрешен только просмотр', 'info');
            return self::CODE_ERROR;
        }

		$keys = [];
		foreach(array_keys($data) as $key){
			$keys[] = ':'.$key;
		}

		$proc = 'begin '.$this->_prefix.$this->_pack.$procedure.'('.implode(', ', $keys).'); end;';

		$this->_fullResponse = $this->ora_proced($proc, $data);

        if($fullResponse){
            return $this->_fullResponse;
        }

		if(isset($this->_fullResponse['p_error_code'])){
			return $this->_fullResponse['p_error_code'];
		}

		return self::CODE_ERROR;
	}

    /**
     * получение полного ответа на процедуру
     *
     * @return array
     */
	public function getFullResponse()
    {
        return $this->_fullResponse;
    }

	/**
	 * вот такой вот кривой лимит с офсетом
	 * @param $sql
	 * @param $offset
	 * @param $limit
	 * @return string
     * @deprecated
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

		$params['offset'] = (int)$params['offset'];
		$params['limit'] = (int)$params['limit'];

		$from = $params['offset'];
		$to = $params['limit']+$params['offset'];

		if(!empty($params['pagination'])){
			$to++;
		}

        //builder
        if (is_a($sql, 'Builder')) {
            $sql = $sql->build();
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
		return preg_replace("/[^\d\.-]+/", "", str_replace([',', ' ', " "], ['.', '', ""], $number));
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
	public static function toDate($string, $format = 'd.m.Y H:i:s')
    {
        $dateTime = DateTime::createFromFormat($format, $string);

        if (empty($dateTime)) {
            return false;
        }

        return $dateTime->format('d.m.Y');
    }

    /**
     * переводим строку в оракловую дату
     *
     * @param $string
     * @return string
     */
    public static function toDateOracle($string, $format = 'd.m.Y H:i:s')
    {
        $dateTime = DateTime::createFromFormat($format, $string);

        return "to_date('".$dateTime->format('d.m.Y')."', 'dd.mm.yyyy')";
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    public function setPack($pack)
    {
        $this->_pack = $pack;
    }
}