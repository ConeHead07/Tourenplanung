<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_VorgaengeRef extends Zend_Db_Table_Abstract 
{
    //put your code here
    // Vars fuer Zend_Db_Table    
    protected $_name    = 'mr_auftragskoepfe_refs';
    protected $_primary = array('Mandant', 'Auftragsnummer');
    
}

//$x = new Model_Db_Vorgaenge();
//$x->setDefaultAdapter();

