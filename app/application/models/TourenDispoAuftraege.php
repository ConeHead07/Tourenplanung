<?php

class Model_TourenDispoAuftraege extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoAuftraege';
    
    const STATUS_ABGESCHLOSSEN = 2;
    const STATUS_DISPONIERT    = 1;
    
    public function finishauftrag($mandant, $auftragsnr, $addData, $user_name)
    {
        if (is_null($user_name)) $user_name = MyProject_Auth_Adapter::getUserName();
        
        
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        $model = new Model_Db_TourenDispoAuftraege();
        //die( '<pre>'.print_r($model->info(Zend_Db_Table::PRIMARY),1) . PHP_EOL . print_r(array('1' => $mandant, '2' => $auftragsnr),1));
        
        $data = array(
            'auftrag_abgeschlossen_am'   => new Zend_Db_Expr('NOW()'),
            'auftrag_abgeschlossen_user' => $user_name
        );
        
        foreach($addData as $k => $v) {
            switch($k) {
                case 'auftrag_abschluss_summe':
                case 'auftrag_abschluss_prozent':
                $data[$k] = $v;
                break;
            }
        }
        
        $row = $model->find($mandant,  $auftragsnr)->current();
		if (!$row) {
			$row = $model->createRow(array('Mandant' => $mandant, 'Auftragsnummer' => $auftragsnr));
        }
        
        $row->setFromArray( $data )->save();        
    }
    
    public function openauftrag($mandant, $auftragsnr)
    {
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        //$model = new Model_Db_TourenAuftraege();
        
        $row = $model->find($mandant, $auftragsnr)->current();
        
        if ($row) $row->setFromArray(array(
            'auftrag_abgeschlossen_am'   => new Zend_Db_Expr('NULL'),
            'auftrag_abgeschlossen_user' => new Zend_Db_Expr('NULL')
        ))->save();
    }
    
    /**
     * 
     * @param int $mandant
     * @param int $auftragsnr
     * @param string $datum ISO YYYY-MM-DD
     * @return object with members success: bool, error: array with errors
     */
    public function setWiedervorlage($mandant, $auftragsnr, $datum)
    {
        $dateValidator = new MyProject_Validate_Date();
        if ($datum && !$dateValidator->isValid($datum)) {
            return (object)array(
                'success' => false,
                'error' => $dateValidator->getErrors()
            );
        }
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        //$model = new Model_Db_TourenAuftraege();
        
        $row = $model->find($mandant, $auftragsnr)->current();
        
        try {
            if ($row) $row->setFromArray(array(
                'auftrag_wiedervorlage_am'   => (strlen($datum) ? $datum : null)
            ))->save();
            return (object)array(
                'success' => true
            );
        } catch(Exception $e) {
            return (object)array(
                'success' => false,
                'error' => 'Wiedervorlage konnte nicht aktualisiert werden!',
            );
        }
    }
    
    public function unsetWiedervorlage($mandant, $auftragsnr)
    {
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        //$model = new Model_Db_TourenAuftraege();
        
        $row = $model->find($mandant, $auftragsnr)->current();
        
        if ($row) $row->setFromArray(array(
            'auftrag_wiedervorlage_am'   => new Zend_Db_Expr('NULL')
        ))->save();
        return (object)array(
            'success' => true
        );
    }
    
    public function finishdispo($mandant, $auftragsnr, $username)
    {
        if (is_null($username)) $username = MyProject_Auth_Adapter::getUserName();
        
        $modelDV = MyProject_Model_Database::loadModel('tourenDispoVorgaenge');        
        if (!$modelDV->finishdispoByAuftragsnr($auftragsnr, $mandant, $username))
                return;
        
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        
        $row = $model->find($mandant, $auftragsnr)->current();
        if (!$row) {
            $row = $model->createRow(array('Mandant' => $mandant, 'Auftragsnummer' => $auftragsnr));
        }
        
        $row->setFromArray(array(
            'auftrag_disponiert_am'   => new Zend_Db_Expr('NOW()'),
            'auftrag_disponiert_user' => $username
        ))->save();
        
        
    }
    
    public function opendispo($mandant, $auftragsnr)
    {
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        //$model = new Model_Db_TourenAuftraege();
        $row = $model->find($mandant, $auftragsnr)->current();
        
        if ($row) $row->setFromArray(array(
            'auftrag_disponiert_am'   => new Zend_Db_Expr('NULL'),
            'auftrag_disponiert_user' => new Zend_Db_Expr('NULL')
        ))->save();
    }
    
    public function refreshTourDispoCount($mandant, $auftragsnr)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $tourStorage = new Model_Db_TourenDispoVorgaenge();
        
        // Auftrags-Statistik aktualisieren
        $sql = 'SELECT COUNT(1) FROM ' . $db->quoteIdentifier($tourStorage->info(Zend_Db_Table::NAME))
              .' WHERE '
              .' Mandant = ' . $db->quote($mandant)  
              .' AND Auftragsnummer = ' . $db->quote($auftragsnr)
              .' AND tour_disponiert_user IS NOT NULL';
        
        $dispo_count = $db->fetchOne($sql);
        
        $da = $this->getStorage();
        $rec = $da->find($mandant, $auftragsnr)->current();
        
        if ($rec) $rec->setFromArray(array(
            'tour_dispo_count' => $db->quote($dispo_count)
        ))->save();
    }
    
    public function addTourDispoCount($mandant, $auftragsnr, $plus = 1 )
    {
        if (!$plus ) return;
        $counter = ( ( $plus > 0) ? ' +' : ' -') . abs($plus);
        $row = $this->getStorage()->find($mandant, $auftragsnr)->current();
        if ( !$row ) return;
        
        $row->setFromArray(array(
            'tour_dispo_count' => new Zend_Db_Expr("tour_dispo_count " . $counter)
        ))->save();
    }
    
    public function addTourNeulieferung($mandant, $auftragsnr, $plus = 1)
    {
        if (!$plus ) return;
        $counter = ( ( $plus > 0) ? ' +' : ' -') . abs($plus);
        $row = $this->getStorage()->find($mandant, $auftragsnr)->current();
        if ( !$row ) return;
        
        $row->setFromArray(array(
            'tour_abschluss_count' => new Zend_Db_Expr("tour_abschluss_count " . $counter)
        ))->save();  
    }
    
    public function refreshAllToursFinishCount() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $da = $this->getStorage();
        $rs = $db->query(  
                'SELECT Mandant, Auftragsnummer FROM ' . $da->info(Zend_Db_Table::NAME)
               .' WHERE tour_abschluss_count > 0 AND auftrag_abgeschlossen_user IS NULL'
        );
        
        while($row = $rs->fetch(Zend_Db::FETCH_OBJ)) {
            $this->refreshTourFinishCount($row->Mandant, $row->Auftragsnummer);
        }
    }
    
    public function refreshAllToursNeulieferungCount() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $da = $this->getStorage();
        $rs = $db->query(  
                'SELECT Mandant, Auftragsnummer FROM ' . $da->info(Zend_Db_Table::NAME)
               .' WHERE tour_abschluss_count > 0 AND auftrag_abgeschlossen_user IS NULL'
        );
        
        while($row = $rs->fetch(Zend_Db::FETCH_OBJ)) {
            $this->refreshTourNeulieferungenCount($row->Mandant, $row->Auftragsnummer);
        }
    }
    
    public function refreshAllToursDispoCount() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $da = $this->getStorage();
        $rs = $db->query(  
                'SELECT Mandant, Auftragsnummer FROM ' . $da->info(Zend_Db_Table::NAME)
               .' WHERE tour_dispo_count > 0 AND auftrag_disponiert_user IS NULL'
        );
        
        while($row = $rs->fetch(Zend_Db::FETCH_OBJ)) {
            $this->refreshTourDispoCount($row->Mandant, $row->Auftragsnummer);
        }
    }
    
    public function refreshTourNeulieferungenCount($mandant, $auftragsnr)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $tourStorage = new Model_Db_TourenDispoVorgaenge();
        
        // Auftrags-Statistik aktualisieren
        $sql = 'SELECT COUNT(1) FROM ' . $db->quoteIdentifier($tourStorage->info(Zend_Db_Table::NAME))
              .' WHERE '
              .' Mandant = ' . $db->quote($mandant)  
              .' AND Auftragsnummer = ' . $db->quote($auftragsnr)
              .' AND neulieferung > 0';
        
        $nl_count = $db->fetchOne($sql);
        
        $da = $this->getStorage();
        $rec = $da->find($mandant, $auftragsnr)->current();        
        
        if ($rec) {
            $rec->tour_neulieferungen_count = $nl_count;
            $rec->save();
        }
    }
    
    public function refreshTourFinishCount($mandant, $auftragsnr)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $tourStorage = new Model_Db_TourenDispoVorgaenge();
        
        // Auftrags-Statistik aktualisieren
        $sql = 'SELECT COUNT(1) FROM ' . $db->quoteIdentifier($tourStorage->info(Zend_Db_Table::NAME))
              .' WHERE '
              .' Mandant = ' . $db->quote($mandant)  
              .' AND Auftragsnummer = ' . $db->quote($auftragsnr)
              .' AND tour_abgeschlossen_user IS NOT NULL';
        
        $dispo_count = $db->fetchOne($sql);
        
        $da = $this->getStorage();
        $rec = $da->find($mandant, $auftragsnr)->current();
        
        if ($rec) {
            $rec->tour_abschluss_count = $dispo_count;
            $rec->save();
        }
    }

    public function isLocked($mandant, $auftragsnr)
    {        
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        
        $row = $model->find($mandant, $auftragsnr)->current();
        
        if ($row) {
            if ($row->auftrag_abgeschlossen_am) return self::STATUS_ABGESCHLOSSEN;
            if ($row->auftrag_disponiert_am)    return self::STATUS_DISPONIERT;
        }
        return 0;        
    }
    
    public function isLockedText($mandant, $auftragsnr)
    {        
        $model = MyProject_Model_Database::loadStorage($this->_storageName);
        
        $row = $model->find($mandant, $auftragsnr)->current();
        
//        die( print_r($row, 1));
        
        if ($row) {
            if ($row->auftrag_abgeschlossen_am) return 'Vorgang wurde bereits am ' . date('d.m.Y', strtotime($row->auftrag_geschlossen_am)) . ' abgeschlossen';
            if ($row->auftrag_disponiert_am)    return 'Vorgang wurde bereits am ' . date('d.m.Y', strtotime($row->auftrag_disponiert_am)) . ' fertig disponiert';
        }
        return '';        
    }
    
    /**
     * Import offene Auftraege, die noch nicht in tour_dispo_auftraege angelegt sind 
     */
    public function importNeueAuftraege()
    {
        $db = $this->getStorage()->getDefaultAdapter();
        
        $storeAK = new Model_Db_Vorgaenge;
        $tblAK   = $storeAK->info(Zend_Db_Table::NAME);
        
        $sql = 
             'INSERT INTO mr_touren_dispo_auftraege (Mandant, Auftragsnummer, tour_dispo_count, tour_abschluss_count, wws_last_geaendertam)  ' . PHP_EOL
            .'SELECT A.Mandant, A.Auftragsnummer, '
            .' SUM( IF( tour_disponiert_user IS NULL , 0, 1 ) ) , '
            .' SUM( IF( tour_abgeschlossen_user IS NULL , 0, 1 ) ), ' . PHP_EOL
            .' A.GeaendertAm ' . PHP_EOL
            .'FROM ' . $tblAK . ' A ' . PHP_EOL
            .'LEFT JOIN mr_touren_dispo_auftraege DA  ' . PHP_EOL
            .' ON A.Mandant = DA.Mandant  ' . PHP_EOL
            .' AND A.Auftragsnummer = DA.Auftragsnummer  ' . PHP_EOL
            .'LEFT JOIN mr_touren_dispo_vorgaenge DV  ' . PHP_EOL
            .' ON A.Mandant = DV.Mandant  ' . PHP_EOL
            .' AND A.Auftragsnummer = DV.Auftragsnummer  ' . PHP_EOL
            .'WHERE  ' . PHP_EOL
            .' A.Bearbeitungsstatus Between 2 AND 9  ' . PHP_EOL
            .' AND DA.Mandant IS NULL ' . PHP_EOL
            .'GROUP BY A.Mandant, A.Auftragsnummer';
//        die($sql);
        $db->query($sql);
    }
    
    /**
     * @param int $mandant
     * @param int $Auftragsnummer 
     * Importiere einzelnen Auftrage per IDs (Mandant, Auftragsnummer 
     */
    public function importAuftrag($mandant, $auftragsnr, $overwrite = false) 
    {
        $db = $this->getStorage()->getDefaultAdapter();     
        
        $storeAK = new Model_Db_Vorgaenge;
        $tblAK   = $storeAK->info(Zend_Db_Table::NAME);
        
        if ($overwrite) {
            $this->getStorage()->delete('Mandant = '.(int)$mandant . ' AND Auftragsnummer = ' . (int)$auftragsnr);
        }
        
        $sql = 
             'INSERT INTO mr_touren_dispo_auftraege (Mandant, Auftragsnummer, tour_dispo_count, tour_abschluss_count, wws_last_geaendertam)  ' . PHP_EOL
            .'SELECT A.Mandant, A.Auftragsnummer, '
            .' SUM( IF( tour_disponiert_user IS NULL , 0, 1 ) ) , '
            .' SUM( IF( tour_abgeschlossen_user IS NULL , 0, 1 ) ), ' . PHP_EOL
            .' IFNULL(A.GeaendertAm, AngelegtAm) ' . PHP_EOL
            .'FROM ' . $tblAK . ' A  ' . PHP_EOL
            .'LEFT JOIN mr_touren_dispo_auftraege DA  ' . PHP_EOL
            .' ON A.Mandant = DA.Mandant  ' . PHP_EOL
            .' AND A.Auftragsnummer = DA.Auftragsnummer  ' . PHP_EOL
            .'LEFT JOIN mr_touren_dispo_vorgaenge DV  ' . PHP_EOL
            .' ON A.Mandant = DV.Mandant  ' . PHP_EOL
            .' AND A.Auftragsnummer = DV.Auftragsnummer  ' . PHP_EOL
            .'WHERE  ' . PHP_EOL
            .' A.Mandant = :mandant AND A.Auftragsnummer = :auftragsnr ' . PHP_EOL
            .' AND DA.Mandant IS NULL ' . PHP_EOL
            .'GROUP BY A.Mandant, A.Auftragsnummer';
//        die($sql);
        $db->query($sql, array('mandant' => $mandant, 'auftragsnr' => $auftragsnr));
        //die( strtr($sql, array(':mandant' => $mandant, ':auftragsnr' => $auftragsnr)));
        
        
        return true;
    }
    
    public function importWwsGeandertAm() 
    {        
        $db = $this->getStorage()->getDefaultAdapter();  
        
        $storeAK = new Model_Db_Vorgaenge;
        $tblAK   = $storeAK->info(Zend_Db_Table::NAME);
        
        $sql = 
            'SELECT A.Mandant, A.Auftragsnummer, A.GeaendertAm '
           .' FROM mr_touren_dispo_auftraege TA '
           .' LEFT JOIN ' . $tblAK . ' A '
           .' ON (TA.Mandant = A.Mandant AND TA.Auftragsnummer = A.Auftragsnummer) '
           .'  WHERE wws_last_geaendertam LIKE "0000%"';
//        die( $sql );
        
        $updateSql = 
             'UPDATE mr_touren_dispo_auftraege SET wws_last_geaendertam = :geaendertam '
            .' WHERE Mandant = :mandant AND Auftragsnummer = :auftragsnr';
        
        try {
            $updateStmt = $db->prepare($updateSql);
            $rslt = $db->query($sql);
            while($row = $rslt->fetch()) {
                
                $updateStmt->execute( array(
                        ':geaendertam' => $row['GeaendertAm'],
                        ':mandant'     => $row['Mandant'],
                        ':auftragsnr'  => $row['Auftragsnummer'],
                    )
                );
            }
        } catch(Exception $e) {
            die('#' . __LINE__ . ' ' . __METHOD__ . $e->getMessage());
        }
    }
    
    public function findFinishedWithNewPositions() 
    {
        $db = $this->getStorage()->getDefaultAdapter();  
        
        $storeAK = new Model_Db_Vorgaenge;
        $tblAK   = $storeAK->info(Zend_Db_Table::NAME);
        
        $sql = 
            'SELECT A.Mandant, A.Auftragsnummer, A.GeaendertAm '
           .' FROM mr_touren_dispo_auftraege TA '
           .' LEFT JOIN ' . $tblAK . ' A '
           .' ON (TA.Mandant = A.Mandant AND TA.Auftragsnummer = A.Auftragsnummer AND wws_last_geaendertam < A.GeaendertAm)'; 
        
        return $db->fetchAll($sql);
    }
    
    public function getGruppierteVorgaenge($mandant, $auftragsnr)
    {
        $db = $this->getStorage()->getDefaultAdapter();
        
        $storageV = new Model_Db_Vorgaenge();
        $tblDA = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblV = $storageV->info(Zend_Db_Table::NAME);
//        die('#'.__LINE__.' mandant:'.$mandant.';auftragsnr:'.$auftragsnr);
        $rowV = $storageV->find($mandant, $auftragsnr)->current();
        if ($rowV->Gruppierungsnummer > 0) {
            $select = $db->select()
                    ->from(array('v'=>$tblV), array('ANR'=>'Auftragsnummer', 'Vorgangstitel', 'Bearbeitungsstatus'))
                    ->joinLeft( array( 'a'=>$tblDA), 'v.Mandant=a.Mandant AND v.Auftragsnummer=a.Auftragsnummer', '*')
                    ->where('v.Mandant = ?' , $mandant)
                    ->where('v.Gruppierungsnummer = ?', $rowV->Gruppierungsnummer)
                    ->order('ANR ASC');
//            die( $select->assemble() );
            return $db->fetchAll($select);
        }
        return array();
        
    }
}