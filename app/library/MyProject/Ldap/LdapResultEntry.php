<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 24.01.2019
 * Time: 11:36
 */


class MyProject_Ldap_LdapResultEntry
{
    private $_result_entry = null;
    private $_result = null;
    private $_connect = null;

    public function __construct($ldap_result_entry, $ldap_result, $ldap_connect)
    {
        $this->_result_entry = $ldap_result_entry;
        $this->_result = $ldap_result;
        $this->_connect = $ldap_connect;
    }
    /**
     * @return resource|null
     */
    public function getResultEntryHandle() {
        return $this->_resultEntry;
    }

    /**
     * @return array
     */
    public function attributes() {
        return ldap_get_attributes($this->_connect, $this->_result_entry);
    }

    /**
     * @return string
     */
    public function firstAttribute(): string {
        return ldap_first_attribute($this->_connect, $this->_result_entry);
    }

    /**
     * @param string $attributeName
     * @return array
     */
    public function getAttributeValue(string $attributeName) {
        return ldap_get_values($this->_connect, $this->_result_entry, $attributeName );
    }

    /**
     * @param string $attributeName
     * @return array
     */
    public function getAttributeValuesLength(string $attributeName) {
        return ldap_get_values_len($this->_connect, $this->_result_entry, $attributeName );
    }

    /**
     * @return string
     */
    public function nextAttribute(): string {
        return ldap_next_attribute($this->_connect, $this->_result_entry);
    }

    public function getData() {
        $aData = [];
        while($attributeName = $this->nextAttribute()) {
            $aData[ $attributeName ] = $this->getAttributeValue( $attributeName );
        }

        return $aData;

    }

    /**
     * @return MyProject_Ldap_LdapResultEntry|null
     */
    public function next() {
        $next = ldap_next_entry($this->_connect, $this->_result_entry);
        return new self($next, $this->_result, $this->_connect);
    }




}