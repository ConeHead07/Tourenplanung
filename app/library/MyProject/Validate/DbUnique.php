<?php

class MyProject_Validate_DbUnique extends Zend_Validate_Abstract
{
    const NOT_UNIQUE = 'uniqueNotUnique';

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "'%value%' already exists"
    );
	
    protected $_table;
    protected $_column;
    protected $_current;

    public function __construct(Zend_Db_Table_Abstract $table, $column, $current = null)
    {
        $this->_table = $table;
        $this->_column = $column;
        // the primary key of the row being updated
        // without knowing this validating an unchanged value would fail
	$this->_current = $current;
    }

    public function isValid($value)
    {
        $this->_setValue($value);	
        $db = $this->_table->getAdapter();
        $where = array($db->quoteInto($this->_column . ' = ?', $value));
        if (isset($this->_current)) {
            $current = (array) $this->_current;
            $info = $this->_table->info();
            foreach ($info['primary'] as $key => $column) {
                $where[] = $db->quoteInto($column . ' <> ?', $current[$key - 1]);
            }
        }
        
        $row = $this->_table->fetchAll($where);
        if ($row->count()) {
            $this->_error('Es existiert bereits ein Eintrag fuer '.$column.' mit gleichem Wert!' );
            return false;
        }
        return true;
    }
}
?>
