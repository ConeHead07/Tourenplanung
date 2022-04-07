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
class Model_Db_TourenDispoPositionen extends Model_Db_Abstract
{
    //put your code here
    protected $_name = 'mr_touren_dispo_auftragspositionen';
    protected $_primary = array('tour_id', 'Mandant', 'Auftragsnummer', 'Positionsnummer');
    
}


