<?php

class Model_Db_Bestellpositionen extends Model_Db_Abstract
{  
    protected $_name    = 'mr_bestellpositionen_dispofilter';
    protected $_primary = array('Mandant','Bestellnummer','Positionsnummer');
}
