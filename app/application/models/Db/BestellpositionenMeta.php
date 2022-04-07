<?php

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_BestellpositionenMeta extends Model_Db_Abstract
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_bestellpositionen';
    protected $_primary = array('Mandant', 'Bestellnummer', 'Positionsnummer');
}

