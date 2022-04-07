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
    public function isValid($value)
    {
        // Wandle Wert in Array um
        $values = (array) $value;
        
        // Lege Wert fest
        $this->_setValue($values);
        
        // Validere Array mit Werten
        foreach ($values as $value) {
            if (!in_array($value, $this->_haystack, $this->_strict)) {
                $this->_error();
                return false;
            }
        }
        return true;
    }
}
