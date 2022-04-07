<?php

/**
 * Validator zum Pruefen, dass Benutzername einmalig ist
 * @author     Ralf Eggert
 */
class MyProject_Validate_Time extends Zend_Validate_Abstract {
    /**
     * Konstante fuer Fehlermeldung
     */
    const NOT_TIME = 'notTime';
    const PATTERN = '/^([0-1][0-9]|2[0-3])(:[0-5][0-9]){1,2}$/';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_TIME => "'%value%' is not a valid time"
    );

    /**
     * Vergleicht den uebergebenen Wert mit regulaerem Ausdruck
     * @param  string $value
     * @return boolean
     */
    public function isValid($value) {
//        echo '#' . __METHOD__ . "<br>\n";
        // Bereite Wert vor
        $value = (string) $value;
        $this->_setValue($value);

        // Pruefe Daten
        if ( preg_match(self::PATTERN, $value) ) {
            return true;
        }

        // Werte sind nicht gleich, gebe Fehlermeldung
        $this->_error(self::NOT_TIME);
        return false;
    }
    
    public function init()
    {
        echo '#' . __METHOD__ . "<br>\n";        
    }

}
