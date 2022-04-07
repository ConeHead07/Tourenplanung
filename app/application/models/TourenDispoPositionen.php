<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author rybka
 */
class Model_TourenDispoPositionen extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoPositionen';
    
    // TO-DO !!!
    // Kopie aus touren/controllers/AjaxController   
    // Muss so umgeschrieben, dass von dort die Methoden
    // des Models gentutzt werden
    public function updatepositionenAction()
    {
        $this->_helper->viewRenderer->setRender('jsonresponse');
        $response = new stdClass();
        $response->msg = '#'.__LINE__ . ' ' . __METHOD__;
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);

            $rq = Zend_Controller_Front::getInstance()->getRequest();
            $tour_id = (int) $rq->getParam('tour_id');
            $mandant = (int) $rq->getParam('Mandant');
            $auftragsnr = (int) $rq->getParam('Auftragsnummer');
            $positionsMengen = $rq->getParam('PositionsMengen');

            $auftragspositionen = array_keys($positionsMengen);

            // Hole bereits angelegte Dispostionen
            $sql =
                'SELECT Positionsnummer '
                .'FROM mr_touren_dispo_auftragspositionen '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer '. $db->quoteInto(' IN(?)',$auftragspositionen) ;
//            die('sql: '.$sql);
            $existingPositionen = $db->fetchCol($sql); //, array(':auftragspositionen',$auftragspositionen));

            $newPositionen = array_diff($auftragspositionen, $existingPositionen);

            $updateSql = 
                'UPDATE mr_touren_dispo_auftragspositionen '
                .'SET DisponierteMenge = :menge '
                .'WHERE tour_id = '.$db->quote($tour_id).' '
                .'AND Mandant = '.$db->quote($mandant).' '
                .'AND Auftragsnummer = '.$db->quote($auftragsnr).' '
                .'AND Positionsnummer = :posnr';
            
//            echo 'existingPositionen: ' . print_r($existingPositionen, 1) . PHP_EOL;
            foreach($existingPositionen as $_posnr) {
                
                $db->query($updateSql, array(
                    ':menge' => $positionsMengen[$_posnr],
                    ':posnr' => $_posnr));
            }
            
            $insertSql = 
                'INSERT mr_touren_dispo_auftragspositionen '
                .'SET DisponierteMenge = :menge, '
                .'tour_id = '.$db->quote($tour_id).', '
                .'Mandant = '.$db->quote($mandant).', '
                .'Auftragsnummer = '.$db->quote($auftragsnr).', '
                .'Positionsnummer = :posnr';

            foreach($newPositionen as $_posnr) {
                $_menge = 
                $db->query($insertSql, array(
                    ':menge' => $positionsMengen[$_posnr],
                    ':posnr' => $_posnr
                ));
            }
        
        } catch(Exception $e) {
            die( $e->getTraceAsString() );
            $response->error = $e->getMessage() . PHP_EOL .$e->getTraceAsString();
        }
    }
    
    public function updateAbschlussPosition(array $AbschlussData) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $tour_id = (int) (isset($AbschlussData['tour_id']) ? $AbschlussData['tour_id'] : 0);
        $pos_nr  = (int) (isset($AbschlussData['Positionsnummer']) ? $AbschlussData['Positionsnummer'] : 0);
        
        
        $row = $this->getPosition($tour_id, $pos_nr);
        
        if ($row === null) {
            throw new Exception('Es existiert kein Datensatz mit den angegebenen IDs: tour_id:'.$tour_id.', Positionsnummer:'.$pos_nr);
        }
        
        $row->setFromArray($AbschlussData);
        $row->save();
        
        $AbschlussTxt = new Model_Db_TourenDispoPositionenText();
        
        $txtRow = $AbschlussTxt->fetchRow('tour_id = '.$db->quote($tour_id).' AND Positionsnummer = ' . $db->quote($pos_nr));
        if (!$txtRow) {
            $AbschlussTxt->createRow()->setFromArray($AbschlussData)->save();
        } else {
            $AbschlussTxt->update(array('AbschlussBemerkung'=>$AbschlussData['AbschlussBemerkung']), 'tour_id = '.$db->quote($tour_id).' AND Positionsnummer = ' . $db->quote($pos_nr));
        }        
    }
    
    public function updateStellplatz($tour_id, $pos_nr, $stellplatz, $user ) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $row = $this->getPosition($tour_id, $pos_nr);
        $stellplatzAlt = $row->Stellplatz;
        $addHistorie = '';
        
        if ($row === null) {
            throw new Exception('Es existiert kein Datensatz mit den angegebenen IDs: tour_id:'.$tour_id.', Positionsnummer:'.$pos_nr);
        }        
        
        $row->Stellplatz = $stellplatz;
        $row->save();
        
        $PositionenTxt = new Model_Db_TourenDispoPositionenText();
        
        $txtRow = $PositionenTxt->fetchRow('tour_id = '.$db->quote($tour_id).' AND Positionsnummer = ' . $db->quote($pos_nr));
        if (!$txtRow) {
            $txtRow = $PositionenTxt->createRow();
            $txtRow->tour_id = $tour_id;
            $txtRow->Positionsnummer = $pos_nr;
                    //->setFromArray()->save();
        }
        
        if (!$txtRow->StellplatzHistorie && $stellplatzAlt) {
            $addHistorie.= $stellplatzAlt . "\n";
        }
        $addHistorie.= $stellplatz . ';' . $user . ';' . date('Y-m-d H:i:s');
        
        if ($txtRow->StellplatzHistorie) {
            $txtRow->StellplatzHistorie = $txtRow->StellplatzHistorie + "\n" + $addHistorie;
        } else {
            $txtRow->StellplatzHistorie = $addHistorie;
        }
        $txtRow->save();
    }
    
    /**
     *
     * @param integer $tour_id
     * @param integer $pos_nr 
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getPosition($tour_id, $pos_nr) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        /** @var $storage Model_Db_TourenDispoPositionen */
        $storage = new Model_Db_TourenDispoPositionen();
        $storage = $this->getStorage();
        
        $row = $storage->fetchRow( $db->quoteInto('tour_id = ?', (int) $tour_id)
                           .' AND '
                           .$db->quoteInto('Positionsnummer = ?', (int) $pos_nr));
        
        return ($row) ? $row : null;
    }
    
    /**
     *
     * @param int $tour_id
     * @param string $posBelongTo options(own, other, none, all)
     * @return array|null
     * @throws Exception 
     */
    public function getPositionen($tour_id, $posBelongTo = 'all')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        
        $NAME = Zend_Db_Table::NAME;
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        $tblBPM = MyProject_Model_Database::loadStorage('bestellpositionenMeta')->info($NAME);
        $tblDT = MyProject_Model_Database::loadStorage('tourenDispoPositionenText')->info($NAME); 
        $tblWB = MyProject_Model_Database::loadStorage('warenbewegungen')->info($NAME);      
        $tblDP = $this->getStorage()->info($NAME);
        
        try {
            /** @var $posBelongTo 
             * Positionsfilter - Optionen:
             * own:   zeige alle Positionen die dieser Tour zugeordnet sind
             * other: zeige alle Positionen die einer anderen zugeordnet sind
             * none:  zeige aller Positionen die noch keiner Tour zugeordnet sind
             * all: zeige alle 
             */
            
            switch($posBelongTo) {
                case 'own':
                   $sqlFilterBelong = 'D.tour_id = ' . $db->quote($tour_id);
                    break;
                case 'other':
                    $sqlFilterBelong = '(D.tour_id IS NOT NULL AND D.tour_id <> '.$db->quote($tour_id);
                    break;
                case 'none':
                    $sqlFilterBelong = '(D.tour_id IS NULL)';
                    break;
                
                case 'all':
                default:
                    $sqlFilterBelong = '';
            }
            
            $sqlVorgangsIdByTourId =
                 'SELECT Mandant, Auftragsnummer '
                .'FROM mr_touren_dispo_vorgaenge '
                .'WHERE tour_id = :tour_id LIMIT 1';
            $row = $db->fetchRow($sqlVorgangsIdByTourId, array(':tour_id' => $tour_id));
            
            $mandant = $row['Mandant'];
            $auftragsnr = $row['Auftragsnummer'];

            // Es gilt noch zu unterscheiden zwischen 
            // offenen Positionen
            // disponierte Positionen
            // teildisponierte Positionen
            // abhängig von disponierten Mengen in mr_touren_dispo_auftragspositionen
            $sqlAPositionen = 
                 'SELECT A.Mandant, A.Auftragsnummer, A.Positionsnummer APosNr, A.StruPosNr, A.Artikelnummer, '
                .'A.Bezeichnung, A.Positionstext, '
                .'A.Bestellmenge AMenge, A.Bestellmengeneinheit, A.Lieferwoche ALWoche, A.Lieferjahr ALJahr, '
                .'A.Liefertermin ALTermin, A.LieferterminFix ALFix, A.AvisierterTermin, A.AvisierteWoche, '
                .'A.AvisiertesJahr, A.AvisierterTerminFix, A.AvisierterTerminDauer, A.Lagerkennung AP_Lagerkennung, '
                .'BK.BestellName, '
                .'B.Bestellnummer, B.Positionsnummer BPosNr, B.Bestellmenge BMenge,B.Lieferanschrift, B.Lieferwoche, '
                .'B.Lieferjahr, B.Liefertermin, B.LieferterminFix, B.Lagerkennung BP_Lagerkennung, '
                .'D.tour_id DisponierteTour, D.DisponierteMenge, D.AbschlussMenge, D.AbschlussReklaMenge, D.AbschlussNLMenge, '
                .'D.AbschlussReklaGrund, D.AbschlussNLGrund, DT.AbschlussBemerkung, ' . PHP_EOL
                .' BPM.StellplatzHistorie, BPM.Stellplatz BPM_Stellplatz, ' . PHP_EOL
                .' WB.LaufendeNummer, WB.Lagerkennung WB_Lagerkennung, WB.Stellplatz WB_Stellplatz, WB.Artikelnummer WArtikelnr, WB.Menge WMenge, ' . PHP_EOL
                .' IF(LENGTH(BPM.Stellplatz), BPM.Stellplatz, WB.Stellplatz) Stellplatz ' . PHP_EOL
                .'FROM '.$tblAP.' A ' . PHP_EOL
                .'LEFT JOIN '.$tblDP.' D ' . PHP_EOL
                .'  USING(Mandant,Auftragsnummer,Positionsnummer) ' . PHP_EOL
                .'LEFT JOIN '.$tblDT.' DT ' . PHP_EOL
                .'  USING(tour_id,Positionsnummer) ' . PHP_EOL
                .'LEFT JOIN '.$tblBP.' B ' . PHP_EOL
                .'  ON ( ' . PHP_EOL
                .'   A.Mandant = B.Mandant ' . PHP_EOL
                .'   AND A.Auftragsnummer = B.Auftragsnummer ' . PHP_EOL
                .'   AND A.Positionsnummer = B.AuftragsPositionsnummer) ' . PHP_EOL
                .'LEFT JOIN '.$tblBPM.' BPM ' . PHP_EOL
                .'  ON ( ' . PHP_EOL
                .'   A.Mandant = BPM.Mandant ' . PHP_EOL
                .'   AND B.Bestellnummer = BPM.Bestellnummer '      . PHP_EOL
                .'   AND B.Positionsnummer = BPM.Positionsnummer) ' . PHP_EOL
                .'LEFT JOIN '.$tblBK.' BK ' . PHP_EOL
                .'  ON ( '
                .'   B.Mandant = BK.Mandant ' . PHP_EOL
                .'   AND B.Bestellnummer = BK.Bestellnummer ) ' . PHP_EOL
                .'LEFT JOIN ' . $tblWB . ' WB ' . PHP_EOL
                .'ON (A.Mandant=WB.Mandant ' . PHP_EOL
                .' AND A.Auftragsnummer = WB.Auftragsnummer ' . PHP_EOL
                .' AND A.Positionsnummer = WB.Positionsnummer) '
            
                .'WHERE A.Mandant = '.$mandant.' AND A.Auftragsnummer = '.$auftragsnr.' ' . PHP_EOL
                .'AND A.Positionsart = 1 AND A.AlternativPos = 0 ' . PHP_EOL
                .($sqlFilterBelong ? ' AND ' . $sqlFilterBelong : '')
                .'ORDER BY B.Bestellnummer, BPosNr';
            
            //die($sqlAPositionen);
            // echo '<pre>#' . __LINE__ . ' ' . $sqlAPositionen . '</pre>';
            return $db->fetchAll($sqlAPositionen); //, array(':Mandant'=>$mandant,':Auftragsnummer'=>$auftragsnr));
            
        } catch(Exception $e) {
            die($e->getMessage() . PHP_EOL . $sqlAPositionen);
            throw $e;
        }
        return null;
    }
    
    
}
