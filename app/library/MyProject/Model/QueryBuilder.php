<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 13.09.2018
 * Time: 15:21
 */

class MyProject_Model_QueryBuilder
{
    protected $select = '*';
    protected $from = '';
    protected $join = [];
    protected $where = '';
    protected $group = '';
    protected $having = '';
    protected $order = '';
    protected $orderdir = '';
    protected $offset = null;
    protected $limit = null;
    protected $params = [];
    protected $offsetDefault = 0;
    protected $limitDefault = 0;
    protected $orderDefault = '';
    protected $orderdirDefault = '';
    protected $joinIsRequiredForCountQuery = false;

    public function __construct(array $aQueryParts = [])
    {
        $this->setProperties($aQueryParts);
    }

    public static function getInstance(array $aQueryParts = []) {
        return new self( $aQueryParts );
    }

    public function setProperties(array $aQueryParts)
    {
        foreach($aQueryParts as $k => $v) {
            $checkMethod = 'set'.ucfirst($k);
            if (method_exists($this, $checkMethod)) {
                $this->{$checkMethod}($v);
            }
        }
        return $this;
    }

    public function setOrderDefault( $v ) {
        $this->orderDefault = $this->_getCsvValue($v);
        return $this;
    }

    public function setOrderDirDefault( $v ) {
        if (is_string($v) && in_array( strtoupper($v), ['ASC','DESC'])) {
            $this->orderdirDefault = $v;
        }
        return $this;
    }

    public function setLimitDefault(int $v) {
        $this->limitDefault = $v;
        return $this;
    }

    public function setOffsetDefault(int $v) {
        $this->offsetDefault = max(0, $v);
        return $this;
    }


    public function setSelect($v) {
        $this->select = $this->_getCsvValue($v);
        return $this;
    }

    public function getSelect(bool $doRender = false) {
        if ($doRender) {
            if ( trim($this->select) === '*' || trim($this->select) === '' ) {
                return 'SELECT *';
            }
            return 'SELECT ' . $this->quoteParamsInto( $this->select );
        }
        return $this->select;
    }

    public function setFrom($v) {
        $this->from = $this->_getCsvValue($v);
        return $this;
    }

    public function getFrom(bool $doRender = false) {
        if ($doRender && $this->from) {
            return ' FROM ' . $this->quoteParamsInto($this->from);
        }
        return $this->from;
    }

    public function setJoin($v, bool $joinIsRequiredForCountQuery = false) {
        $this->join = [];
        return $this->addJoin($v, $joinIsRequiredForCountQuery);
    }

    public function addJoin($v, bool $joinIsRequiredForCountQuery = false) {
        if ($joinIsRequiredForCountQuery) {
            $this->joinIsRequiredForCountQuery = $joinIsRequiredForCountQuery;
        }
        if (is_array($v)) {
            foreach($v as $_join) {
                $this->join[] = [ 'join' => $_join, 'required' => $joinIsRequiredForCountQuery];
            }
        } else {
            $this->join[] = [ 'join' => $v, 'required' => $joinIsRequiredForCountQuery];
        }
        return $this;
    }

    public function getJoin(bool $doRender = false, $bForCountQuery = false) {
        $re = '';
        foreach($this->join as $_join) {
            if ($bForCountQuery && !$_join['required']) {
                continue;
            }
            if ($doRender && $_join) {
                $re.= ' ' . $this->quoteParamsInto($_join['join']) . "\n";
            }
        }
        return $re;
    }

    public function quoteParamsInto($query) {
        $qq = $query;
        if (is_array($this->params)) {
            $db = Zend_Registry::get('db');
            foreach ($this->params as $k => $v) {
                if (is_array($v)) {
                    $aInVals = array_map(function($val) use($db){
                        return $db->quote($val);
                    }, $v);
                    $qVals = implode(',', $aInVals);
                    $qq = preg_replace('/:' . preg_quote($k, '/') . '\b/', $qVals, $qq);
                } else {
                    $qq = preg_replace('/:' . preg_quote($k, '/') . '\b/', $db->quote($v), $qq);
                }
            }
        }
        return $qq;
    }

    public function setWhere($v) {
        $w = $this->_getCsvValue($v, ') AND (');
        if (trim($w)) {
            $this->where = '(' . $w . ')';
        }
        return $this;
    }

    public function andWhere($v) {
        $w = $this->_getCsvValue($v, ') AND (');
        if (!trim($w)) {
            return $this;
        }
        if (trim($this->where)) {
            $this->where.= ' AND ';
        }
        $this->where.= '(' . $w . ')';
        return $this;
    }

    public function getWhere(bool $doRender = false) {
        if ($doRender && $this->where) {
            return ' WHERE ' . $this->quoteParamsInto($this->where);
        }
        return $this->where;
    }

    public function setGroup($v) {
        $this->group = $this->_getCsvValue($v);
        return $this;
    }

    public function getGroup(bool $doRender = false) {
        if ($doRender && $this->group) {
            return ' GROUP BY ' . $this->quoteParamsInto($this->group);
        }
        return $this->group;
    }

    public function setHaving($v) {
        $this->having = (string)$v;
        return $this;
    }

    public function getHaving(bool $doRender = false) {
        if ($doRender && $this->having) {
            return ' HAVING ' . $this->quoteParamsInto($this->having);
        }
        return $this->having;
    }

    public function setOrder($v) {
        $this->order = $this->_getCsvValue($v);
        return $this;
    }

    public function getOrder(bool $doRender = false) {
        $of = $this->order ?: $this->orderDefault;
        $od = $this->orderdir ?: $this->orderdirDefault;
        if ($doRender && $of) {
            return ' ORDER BY ' . $of . ' ' . $od;
        }
        return $this->order;
    }

    public function setOrderDir($v) {
        if (is_string($v) && in_array( strtoupper($v), ['ASC','DESC'])) {
            $this->orderdir = $v;
        }
        return $this;
    }

    public function getOrderDir() {
        return $this->orderdir;
    }


    public function setOffset(int $v) {
        $this->offset = max(0, $v);
        return $this;
    }

    public function getOffset() {
        return is_numeric($this->offset) ? $this->offset : ($this->offsetDefault ?: 0);
    }

    public function setLimit(int $v) {
        $this->limit = $v;
        return $this;
    }

    public function getLimit(bool $doRender = false) {
        $l = (string)is_numeric($this->limit) ? (int)$this->limit : (int)$this->limitDefault;
        if ($doRender) {
            if ($l) {
                return ' LIMIT ' . $this->getOffset() . ', ' . $l;
            } else {
                return '';
            }
        }
        return $this->limit;
    }

    public function setParams(array $aParams) {
        $this->params = $aParams;
        return $this;
    }

    public function setParam(string $key, $val) {
        $this->params[$key] = $val;
        return $this;
    }

    protected function _getCsvValue($v, $sep = ', ') {
        if (is_string($v)) {
            return $v;
        } elseif (is_array($v)) {
            return implode($sep, $v);
        }
        return (string)$v;
    }

    public function assemble( string $type = 'query')
    {

        $a['select'] = $this->getSelect( true );
        $a['from']   = $this->getFrom( true );      //' FROM ' . $this->from;
        $a['join']   = $this->getJoin( true, $type === 'count' );      //' FROM ' . $this->from;
        $a['where']  = $this->getWhere( true );     //($this->where ? ' WHERE ' . $this->where : '');
        $a['group']  = $this->getGroup(true );      //($this->group ? ' GROUP BY ' . $this->group : '');
        if ($a['group']) {
            $a['group'].= $this->getHaving( true );
        }
        $a['order']  = $this->getOrder( true );      // ($this->order ? ' ORDER BY ' . $this->order : '');
        $a['limit']  = $this->getLimit(true );       // ($this->limit ? ' LIMIT ' . $this->offset . ', ' . $this->limit : '');

        // Complex Part
        if ($type === 'query') {
            // If Type == query, build normal query
            $query = $a['select'] . $a['from'] . $a['join'] . $a['where'] . $a['group'] . $a['order'] . $a['limit'];
        } elseif( $type === 'count') {
            if (!$this->joinIsRequiredForCountQuery ) {
                $a['join'] = '';
            }
            // If Type == count, remove order and limit
            if (!$a['group']) {
                $query = 'SELECT count(1) '. $a['from'] . $a['join'] . $a['where'];
            } else {
                // If Type == count query contains group, wrap query into virtual from
                $query = 'SELECT count(1) FROM (' . $a['select'] . $a['from'] . $a['join'] . $a['where'] . $a['group'] . ') V';
            }
        }

        return $query;
    }

    public function assembleCount() {
        return $this->assemble(  'count' );
    }

    public function __toString() {
        return $this->assemble();
    }

    public function toArray() {
        return [
            'select'   => $this->select,
            'from'     => $this->from,
            'where'    => $this->where,
            'group'    => $this->group,
            'having'   => $this->having,
            'order'    => $this->order,
            'orderdir' => $this->orderdir,
            'offset'   => $this->offset,
            'limit'    => $this->limit,
        ];
    }

}