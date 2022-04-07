<?php

/**
 * Tabelle fuer Firmen externer Ressourcen (MA, FP, WZ)
 * @author Frank Barthold
 */
class Model_Db_Leistung extends Zend_Db_Table_Abstract 
{ 
    protected $_name    = 'mr_ressourcen_leistungskatalog';
    protected $_primary = array('leistungs_id');
}
