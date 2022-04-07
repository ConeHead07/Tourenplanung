<?php

/**
 * Tabelle fuer Firmen externer Ressourcen (MA, FP, WZ)
 * @author Frank Barthold
 */
class Model_Db_Leistung extends Model_Db_Abstract
{ 
    protected $_name    = 'mr_ressourcen_leistungskatalog';
    protected $_primary = array('leistungs_id');
}
