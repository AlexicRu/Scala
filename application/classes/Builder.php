<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Builder
 *
 * simple query builder
 */
class Builder
{
    private $_action    = '';
    private $_from      = '';
    private $_joins     = [];
    private $_columns   = [];
    private $_where     = [];
    private $_having     = [];
    private $_groupBy   = [];
    private $_orderBy   = [];
    private $_limit     = 0;
    private $_offset    = 0;
    private $_distinct  = false;

    /**
     * echo $sql;
     *
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @return $this
     */
    public function select($array = [])
    {
        $this->columns($array);

        $this->_action = 'select';

        return $this;
    }

    /**
     * @return $this
     */
    public function distinct()
    {
        $this->_distinct = true;

        return $this;
    }

    /**
     * @param $str
     * @return $this
     */
    public function from($str)
    {
        if (empty($str)) {
            return $this;
        }

        $this->_from = $str;

        return $this;
    }

    /**
     * @param $table
     * @param $str
     * @return $this
     */
    public function join($table, $str)
    {
        if (empty($str) || empty($table)) {
            return $this;
        }

        $this->_joins[] = [
            'join'  => 'join',
            'table' => $table,
            'str'   => $str
        ];

        return $this;
    }

    /**
     * @param $table
     * @param $str
     * @return $this
     */
    public function joinLeft($table, $str)
    {
        if (empty($str) || empty($table)) {
            return $this;
        }

        $this->_joins[] = [
            'join'  => 'left join',
            'table' => $table,
            'str'   => $str
        ];

        return $this;
    }

    /**
     * @param $array
     * @return $this
     */
    public function columns($array)
    {
        if (empty($array)) {
            return $this;
        }
        if (!is_array($array)) {
            $array = [$array];
        }

        $this->_columns = array_merge($this->_columns, $array);

        return $this;
    }

    /**
     * @param $connector
     * @return $this
     */
    public function whereStart($connector = 'and')
    {
        $this->_where[] = [
            'connector'             => $connector,
            'where'                 => '(',
            'skip_next_connector'   => true
        ];

        return $this;
    }

    /**
     * @return $this
     */
    public function whereEnd()
    {
        $this->_where[] = [
            'connector' => '',
            'where'     => ')',
        ];

        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function whereOr($where)
    {
        if (empty($where)) {
            return $this;
        }

        $this->_where[] = [
            'connector' => 'or',
            'where'     => $where,
        ];

        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function where($where)
    {
        if (empty($where)) {
            return $this;
        }

        $this->_where[] = [
            'connector' => 'and',
            'where'     => $where,
        ];

        return $this;
    }

    /**
     * @param $param
     * @param $array
     * @return $this
     */
    public function whereIn($param, $array)
    {
        if (empty($param) || empty($array)) {
            return $this;
        }

        $this->_where[] = [
            'connector' => 'and',
            'where'     => $param . ' in (' . implode(', ', (array)$array) . ')',
        ];

        return $this;
    }

    /**
     * @param $array
     * @return $this
     */
    public function orderBy($array)
    {
        if (empty($array)) {
            return $this;
        }

        if (is_string($array)) {
            $array = [$array];
        }

        $this->_orderBy = array_merge($this->_orderBy, $array);

        return $this;
    }

    /**
     * @param $array
     * @return $this
     */
    public function having($array)
    {
        if (empty($array)) {
            return $this;
        }

        if (is_string($array)) {
            $array = [$array];
        }

        $this->_having = array_merge($this->_having, $array);

        return $this;
    }

    /**
     * @param $array
     * @return $this
     */
    public function groupBy($array)
    {
        if (empty($array)) {
            return $this;
        }

        if (is_string($array)) {
            $array = [$array];
        }

        $this->_groupBy = array_merge($this->_groupBy, $array);

        return $this;
    }

    /**
     * @param $int
     * @return $this
     */
    public function limit($int)
    {
        if (empty($int)) {
            return $this;
        }

        $this->_limit = (int)$int;

        return $this;
    }

    /**
     * @param $int
     * @return $this
     */
    public function offset($int)
    {
        if (empty($int)) {
            return $this;
        }

        $this->_offset = (int)$int;

        return $this;
    }

    /**
     * сбрасываем параметры
     */
    public function resetColumns()
    {
        $this->_columns = [];

        return $this;
    }
    public function resetOrderBy()
    {
        $this->_orderBy = [];

        return $this;
    }
    public function resetGroupBy()
    {
        $this->_groupBy = [];

        return $this;
    }
    public function resetHaving()
    {
        $this->_having= [];

        return $this;
    }

    /**
     * собираем из этого всего SQL
     */
    public function build($prefix = false)
    {
        $sql = " {$this->_action} ";

        if ($this->_distinct) {
            $sql .= " distinct ";
        }

        //columns
        if (empty($this->_columns)) {
            $sql .= " * ";
        } else {
            $sql .= " ".implode(" , ", $this->_columns)." ";
        }

        $prefix = $prefix ?: Oracle::$prefix;

        //from
        $sql .= " from {$prefix}{$this->_from} ";

        //joins
        if (!empty($this->_joins)) {
            foreach ($this->_joins as $join) {
                $sql .= " {$join['join']} {$prefix}{$join['table']} on {$join['str']} ";
            }
        }

        //where
        if (!empty($this->_where)) {
            $sql .= " where ";

            $skipNextConnector = true;

            foreach($this->_where as $where) {
                if (empty($skipNextConnector)) {
                    $sql .= " {$where['connector']} ";
                }

                $sql .= " {$where['where']} ";

                if (!empty($where['skip_next_connector'])) {
                    $skipNextConnector = true;
                } else {
                    $skipNextConnector = false;
                }
            }
        }

        //group by
        if (!empty($this->_groupBy)) {
            $sql .= " group by ".implode(" , ", $this->_groupBy)." ";
        }

        //having
        if (!empty($this->_having)) {
            $sql .= " having ".implode(" , ", $this->_having)." ";
        }

        //order by
        if (!empty($this->_orderBy)) {
            $sql .= " order by ".implode(" , ", $this->_orderBy)." ";
        }

        if (!empty($this->_limit) || !empty($this->_offset)) {
            $sql = "
                select * from (
                  select a.*, ROWNUM rnum from (
                    {$sql}
                  ) a where rownum <= ".($this->_limit ?: 999999999)."
                ) where rnum > ".$this->_offset."
            ";
        }

        return $sql;
    }
}