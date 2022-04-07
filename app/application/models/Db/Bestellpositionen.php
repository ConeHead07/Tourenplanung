<?php

class Model_Db_Bestellpositionen extends Zend_Db_Table_Abstract 
{  
    protected $_name    = 'mr_bestellpositionen_dispofilter';
    protected $_primary = array('Mandant','Bestellnummer','Positionsnummer');
}
