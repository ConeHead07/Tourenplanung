<?php

class Model_Db_Auftragspositionen extends Zend_Db_Table_Abstract 
{  
    protected $_name    = 'mr_auftragspositionen_dispofilter';
    protected $_primary = array('Mandant','Auftragsnummer','Positionsnummer');
}
