<?php

class MyProject_Model_QueryResult {
    /**
     * @var int
     */
    private $_offset = 0;
    /**
     * @var int
     */
    private $_limit = 0;
    /**
     * @var int
     */
    private $_total = 0;
    /**
     * @var array
     */
    private $_rows = [];
    /**
     * @var string
     */
    private $_order = '';

    private $_success = true;

    private $_error = '';

    private $_sql = '';

    private $_countSql = '';

    private $_logs = [];


    /**
     * MyProject_Model_QueryResult constructor.
     */
    public function __construct() {
    }


    /**
     * @param array $rows
     * @return MyProject_Model_QueryResult
     */
    public function setRows(array $rows): self {
        $this->_rows = $rows;
        $cnt = count($rows);
        if ($this->_total < $cnt) {
            $this->setTotal( $cnt);
        }
        if ($this->_limit < $cnt) {
            $this->setLimit( $cnt);
        }
        return $this;
    }

    public function getRows(): array {
        return $this->_rows;
    }

    /**
     * @param int $offset
     * @return MyProject_Model_QueryResult
     */
    public function setOffset(int $offset): self {
        $this->_offset = $offset;
        return $this;
    }


    public function getOffset(): int {
        return $this->_offset;
    }

    public function getPage(): int {
        if (!$this->_limit) return 0;
        return ($this->_offset + $this->_limit) / $this->_limit;
    }

    public function getTotalPages(): int {
        if (!$this->_total) return 0;
        return ceil($this->_total / $this->_limit);
    }

    /**
     * @param int $limit
     * @return MyProject_Model_QueryResult
     */
    public function setLimit(int $limit): self {
        $this->_limit = $limit;
        return $this;
    }

    public function getLimit(): int {
        return $this->_limit;
    }


    /**
     * @param int $total
     * @return MyProject_Model_QueryResult
     */
    public function setTotal(int $total): self
    {
        $this->_total = $total;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->_total;
    }


    /**
     * @param string $order
     * @return MyProject_Model_QueryResult
     */
    public function setOrder(string $order): self
    {
        $this->_order = $order;
        return $this;
    }

    public function getOrder(): string
    {
        return $this->_order;
    }

    public function setError($error): self {
        $this->_error = $error;
        $this->_success = false;
        return $this;
    }

    public function getError() {
        return $this->_error;
    }

    public function setSql(string $sql): self {
        $this->_sql = $sql;
        return $this;
    }

    public function getSql() {
        return $this->_sql;
    }

    public function setCountSql(string $sql): self {
        $this->_countSql = $sql;
        return $this;
    }

    public function getCountSql() {
        return $this->_countSql;
    }

    public function addLog(string $m): self {
        $this->_logs[]= $m;
        return $this;
    }

    public function getLogs(): array {
        return $this->_logs;
    }

}
