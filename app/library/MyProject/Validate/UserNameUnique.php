<?php

/**
 * Validator zum Prüfen, dass Benutzername einmalig ist
 * @author     Ralf Eggert
 */
class MyProject_Validate_UserNameUnique extends Zend_Validate_Abstract {
    /**
     * Konstante für Fehlermeldung
     */
    const NOT_UNIQUE = 'notUnique';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "'%value%' already exists"
    );

    /**
     * Vergleicht den übergebenen Wert mit dem Wert des Vergleichsfelds
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid($value, $context = null) {
        echo '#' . __METHOD__ . "<br>\n";
// Bereite Wert vor
        $value = (string) $value;
        $this->_setValue($value);

        // Lade Model
        $modelUsers = MyProject_Model_Database::loadModel('user');

        // Versuche Benutzer mit aktuellem Wert als Benutzernamen zu laden
        $row = $modelUsers->fetchEntryByName($value);

        // Prüfe Benutzerdaten
        if (empty($row)) {
            return true;
        }

        // Werte sind nicht gleich, gebe Fehlermeldung
        $this->_error(self::NOT_UNIQUE);
        return false;
    }
    
    public function init()
    {
        echo '#' . __METHOD__ . "<br>\n";        
    }

}
