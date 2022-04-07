<?php

class Model_Db_Auftragspositionen extends Model_Db_Abstract
{  
    protected $_name    = 'mr_auftragspositionen_dispofilter';
    protected $_primary = array('Mandant','Auftragsnummer','Positionsnummer');
}
