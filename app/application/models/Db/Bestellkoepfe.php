<?php

class Model_Db_Bestellkoepfe extends Zend_Db_Table_Abstract 
{   
    protected $_name    = 'mr_bestellkoepfe_dispofilter';
    protected $_primary = array('Mandant','Bestellnummer');
}
