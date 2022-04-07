<?php

/**
 * Validator zum Pruefen, ob ein im erwarteten Format gültiges Datum übergeben wurde
 * @author     Frank Barthold
 */
class MyProject_Validate_Date extends Zend_Validate_Abstract {
    /**
     * Konstante fuer Fehlermeldung
     */
    const NOT_DATE = 'notDate';
    const NOT_DATE_FORMAT = 'notDateFormat';
    const PATTERN_YMD = '/^((19|20|21)([0-9][0-9]))-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DATE => "'%value%' is not a valid date",
        self::NOT_DATE_FORMAT => "'%value%' is not a valid date format => JJJJ-MM-TT"
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
        if ( preg_match(self::PATTERN_YMD, $value, $m) ) {
            list($year, $month, $day) = array($m[1], $m[4], $m[5]);
            if (checkdate($month, $day, $year)) {
                return true;
            }
            $this->_error(self::NOT_DATE);
        } else {
            $this->_error(self::NOT_DATE_FORMAT);
        }
        
        return false;
    }
    
    public function init()
    {
        echo '#' . __METHOD__ . "<br>\n";        
    }

}
