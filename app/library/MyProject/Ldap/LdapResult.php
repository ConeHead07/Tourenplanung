<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 24.01.2019
 * Time: 11:23
 */


class MyProject_Ldap_LdapResult
{
    private $_result = null;
    private $_connect = null;
    private $_resultEntry = null;
    private $_filter = '';

    public function __construct( $ldap_connect, $ldap_result, string $sFilter = '') {
        $this->_connect = $ldap_connect;
        $this->_result = $ldap_result;
        $this->_filter = $sFilter;
    }

    /**
     * @return resource|null
     */
    public function getResultHandle() {
        return $this->_result;
    }

    /**
     * @return resource|null
     */
    public function getResultEntryHandle() {
        return $this->_resultEntry;
    }

    /**
     * @return int
     */
    public function count() {
        return ldap_count_entries($this->_connect, $this->_result);
    }

    /**
     * @return array
     */
    public function entries() {
        return ldap_get_entries($this->_connect, $this->_result);
    }

    /**
     * @return MyProject_Ldap_LdapResultEntry
     */
    public function fetch() {
        if (is_null($this->_resultEntry)) {
            $this->_resultEntry = ldap_first_entry($this->_connect, $this->_result);
        }
        return new MyProject_Ldap_LdapResultEntry($this->_resultEntry, $this->_result, $this->_connect);
        // ldap_next_entry($this->_connect, $this->_resultEntry);
    }

    public function free() {
        if (!is_null($this->_result) && is_resource($this->_result) ) {
            if (ldap_free_result($this->_result)) {
                $this->_result = null;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $entryIdentifier
     * @return array
     */
    public function attributes( $entryIdentifier) {
        return ldap_get_attributes($this->_connect, $entryIdentifier);
    }

    /**
     * @return string
     */
    public function getFilter() {
        return $this->_filter;
    }

    public function __destruct()
    {
        $this->free();
    }

}