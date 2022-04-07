<?php
/**
 * Validator zum Vergleichen von Eingaben
 * @author     Ralf Eggert
 */
class MyProject_Validate_EqualInput extends Zend_Validate_Abstract      
{
    /**
     * Konstante für Fehlermeldung
     */
    const NOT_MATCH = 'notMatch';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => "the values do not match"
    );

    /**
     * Vergleichsfeld
     *
     * @var string
     */
    protected $_compare;

    /**
     * Konstruktur zum Setzen des Vergleichsfelds
     *
     * @param  string $compare
     * @return void
     */
    public function __construct($compare = 'compare')
    {
        $this->setCompareField($compare);
    }

    /**
     * Gibt das Vergleichsfeld zurück
     * @return string
     */
    public function getCompareField()
    {
        return $this->_compare;
    }

    /**
     * Setzt das Vergleichsfeld
     * @param  string $compare
     */
    public function setCompareField($compare)
    {
        $this->_compare = $compare;
    }

    /**
     * Vergleicht den übergebenen Wert mit dem Wert des Vergleichsfelds
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        // Bereite Wert vor
        $value = (string) $value;
        $this->_setValue($value);
        
        // Prüfe Kontext
        if (is_array($context))
        {
            if (isset($context[$this->getCompareField()])
                && ($value == $context[$this->getCompareField()]))
            {
                return true;
            }
        } 
        elseif (is_string($context) && ($value == $context)) 
        {
            return true;
        }

        // Werte sind nicht gleich, gebe Fehlermeldung
        $this->_error(self::NOT_MATCH);
        return false;
    }
}
