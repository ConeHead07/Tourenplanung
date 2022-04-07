<?php
// definiere Validator-Klasse
class MyProject_Validate_Tuersteher extends Zend_Validate_Abstract 
{
    /**
     * Fehlerkonstante
     */
    const NO_ENTRY = 'noEntry';

    /**
     * Fehlermeldungen
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::NO_ENTRY => "%value%, Zugang verwehrt!"
    );

    /**
     * Definiert von Zend_Validate_Interface
     *
     * Lasse nur bestimmte Personen rein
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        // lege erlaubte Personen fest
        $allowed = array('Luigi', 'Valentino', 'Mamma', 'Salvatore', 'Maria');
        
        // speichere den Wert 
        $this->_setValue($value);

        // Prüfe, ob Person rein darf
        if (!in_array($value, $allowed)) {
            // Prüfung nicht bestanden
            $this->_error();
            return false;
        }
        
        // Prüfung bestanden
        return true;
    }
}
