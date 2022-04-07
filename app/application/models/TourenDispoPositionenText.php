<?php

/**
 * Description of User
 * @author rybka
 */
class Model_TourenDispoPositionenText extends MyProject_Model_Database
{
    protected $_storageName = 'tourenDispoPositionenText';
    
    public function addStellplatzHistorie(array $data, $user) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $tour_id = (int) (isset($data['tour_id'])         ? $data['tour_id'] : 0);
        $pos_nr  = (int) (isset($data['Positionsnummer']) ? $data['Positionsnummer'] : 0);
        
        $posModel = new Model_TourenDispoPositionen();
        $row = $posModel->getPosition($tour_id, $pos_nr);
        
        $data['stellplatz_historie'] = $data['stellplatz'] . ';' . $user . ';' . date('Y-m-d H:i:s') . PHP_EOL;
        
        if ($row === null) {
            throw new Exception('Es existiert kein Datensatz mit den angegebenen IDs: tour_id:'.$tour_id.', Positionsnummer:'.$pos_nr);
        }
        
        $row->setFromArray($data);
        $row->save();
        
        $AbschlussTxt = $this->getStorage();
        
        // stellplatz_historie        
        $txtRow = $AbschlussTxt->fetchRow('tour_id = '.$db->quote($tour_id).' AND Positionsnummer = ' . $db->quote($pos_nr));
                
        if (!$txtRow) {
            $data['stellplatz_historie'] = 
            $AbschlussTxt->createRow()->setFromArray($data)->save();
        } else {
            $AbschlussTxt->update(
                array(
                    'stellplatz_historie'=>new Zend_Db_Expr('CONCAT(stellplatz_historie, ' . $db->quote( $data['stellplatz_historie']) . ' )')
                ), 
                'tour_id = '.$db->quote($tour_id).' AND Positionsnummer = ' . $db->quote($pos_nr));
        }
    }
}
