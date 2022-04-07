<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 06.09.2018
 * Time: 10:22
 */

/**
 * Class Model_Variables
 *
 * Globaler Datenbank-gebundener Key-Value-Speicher
 * Initiale Erstellung für parallelen Import aus zwei WWS-DBs.
 */
class Model_Variables extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'variables';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function set(string $name, string $value)
    {
        $sql = 'REPLACE ' . $this->_tbl;
        $sql.= ' SET name = ' . $this->_db->quote($name) . ', ';
        $sql.= ' value = ' . $this->_db->quote($value);

        $this->_db->query( $sql );

        return $this;
    }

    /**
     * @param string $name
     * @return null|string
     * @throws Zend_Db_Statement_Exception
     */
    public function get(string $name)
    {
        $sql = 'SELECT value FROM ' . $this->_tbl;
        $sql.= ' WHERE name = ' . $this->_db->quote($name) . ' LIMIT 1';

        $stmt = $this->_db->query( $sql );

        if ($stmt->rowCount()) {
            return $stmt->fetchColumn(0);
        }
        return null;

    }
}