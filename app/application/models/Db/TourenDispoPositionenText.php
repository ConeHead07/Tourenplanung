<?php

/**
 * Description of user
 *
 * @author rybka
 */
class Model_Db_TourenDispoPositionenText extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'mr_touren_dispo_auftragspositionen_txt';
    protected $_primary = array('tour_id', 'Positionsnummer');
    
    protected $_referenceMap = array(
        'touren_positionen' => array(
            'columns'          => array('tour_id'),
            'refTableClass'    => 'Model_Db_TourenDispoPositionen',
            'refColumns'       => array('tour_id', 'Positionsnummer'),
        )
    );
}


