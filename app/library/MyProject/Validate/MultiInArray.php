<?php
/**
 * Validator für Arrays mit mehreren Werten
 * @author     Ralf Eggert
 */
class MyProject_Validate_MultiInArray extends Zend_Validate_InArray      
{
    /**
     * Füre Validierung für mehrere Werte aus
     * 
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value): bool
    {
        // Wandle Wert in Array um
        $values = (array) $value;
        
        // Lege Wert fest
        $this->_setValue($values);
        
        // Validere Array mit Werten
        foreach ($values as $_val) {
            if (!in_array($_val, $this->_haystack, $this->_strict)) {
                $this->_error();
                return false;
            }
        }
        return true;
    }

    public function keysExists(array $aKeys): bool
    {
        foreach($aKeys as $_key) {
            if (!array_key_exists($_key, $this->_haystack)) {
                $this->_error();
                return false;
            }
        }
        return true;
    }
}
