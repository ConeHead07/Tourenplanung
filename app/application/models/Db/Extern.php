<?php

/**
 * Tabelle fuer Firmen externer Ressourcen (MA, FP, WZ)
 * @author Frank Barthold
 */
class Model_Db_Extern extends Model_Db_Abstract
{  
    protected $_name    = 'mr_extern';
    protected $_primary = array('extern_id');
}

