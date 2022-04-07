<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourcesDispoSperrzeiten
 *
 * @author rybka
 */
class Model_ResourcesSperrzeiten extends MyProject_Model_Database {
    //put your code here
    protected $_storageName = 'resourcesSperrzeiten';
    
    public function checkErrors($data) {
        $dateFields = array('gesperrt_von', 'gesperrt_bis');
        foreach($dateFields as $f) {
            if (isset($data[$f])) {
                $valiDate = new MyProject_Validate_Date();
                if (!$valiDate->isValid($data[$f])) {
                    return $f.': ' . implode('. ', $valiDate->getMessages());
                }
            } else {
                return $f. ': Fehlende Angabe!' ;
            }
        }
        if (strtotime($data['gesperrt_von']) > strtotime($data['gesperrt_bis'])) {
            return 'gesperrt_von darf nicht groesser als gesperrt_bis sein!';
        }
        return '';
    }
    
    public function insert(array $data) {
//        echo '#'.__LINE__ . ' ' . __METHOD__ . ' data: ' . print_r($data,1).PHP_EOL;
        $error = $this->checkErrors($data);
        if ($error) {
            throw new Exception($error);
        }
        return parent::insert($data);
    }
    
    public function update(array $data, $id) {
        $error = $this->checkErrors($data);
        if ($error) {
            throw new Exception($error);
        }
        return parent::update($data, $id);
    }

    public function setRemovedItems(int $iNumRemoved, $id) {
        return parent::update(['anzahl_entfernt'=>$iNumRemoved], $id);
    }

    public function addRemovedItems(int $iNumRemoved, $id) {
        return parent::update([
            'anzahl_entfernt'=> new Zend_Db_Expr('anzahl_entfernt + ' . $iNumRemoved)
        ], $id);
    }
    
    public function delete($id) {
        return parent::delete($id);
    }
    
    /**
     * 
     * @param string $rsrcType
     * @param int $rsrcID
     * @param array $listOpts
     * @return \stdClass Member: re->total, re->rows)
     * @throws Exception
     */
    public function fetchList($rsrcType, $rsrcID, $listOpts = null) {
        $db = MyProject_Model_Database::loadStorage($this->_storageName)->getAdapter();
        $tbl = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblLog = MyProject_Model_Database::loadStorage('tourenDispoLog')->info(Zend_Db_Table::NAME);
        
        $re = new stdClass();
        
        if (!in_array($rsrcType, array('FP','MA','WZ','all'))) {
            throw new Exception('Ungueltiger Parameter rsrcType '.$rsrcType.'. Erwartet: FP, MA, all');
        }

        if ($listOpts == null) {
            $listOpts = array();
        }

        // (!@empty($listOpts['sord']) && in_array(strtoupper($listOpts['sord']), array('ASC','DESC'))) ? $listOpts['sord'] : 'ASC';

        if ($rsrcType!='all') {
            $query = 'ressourcen_typ = :ressourcen_typ AND ressourcen_id = :ressourcen_id';
            if (!@empty($listOpts['where'])) $listOpts['where'] = $query . ' AND (' .$listOpts['where'].')';
            else $listOpts['where'] = $query;
        }

        $queryObject = MyProject_Model_QueryBuilder::getInstance()
            ->setSelect('s.*, count(l.tour_id) num_removed')
            ->setFrom($tbl . ' s LEFT JOIN ' . $tblLog . ' l ON (
            l.object_type = :ressourcen_typ
            AND l.object_id = :ressourcen_id
            AND s.sperrzeiten_id = l.sperrzeiten_id) 
            ')
            ->setWhere($listOpts['where'] ?? '')
            ->setGroup( $listOpts['group'] ?? 's.sperrzeiten_id')
            ->setHaving( $listOpts['having'] ?? '')
            ->setOrder( $listOpts['sidx'] ?? '')
            ->setOrderDir( $listOpts['sord'] ?? '')
            ->setOffset( (int)($listOpts['offset'] ?? 0))
            ->setLimit( (int)($listOpts['count'] ?? 0))
            ->setParams([
                'ressourcen_id' => (int)$rsrcID,
                'ressourcen_typ' => $rsrcType
            ]);

        
        $re->total_records = parent::fetchCount( $queryObject->quoteParamsInto($listOpts['where']) );
        $re->total_pages   = ($re->total_records && $listOpts['count']) ? ceil($re->total_records / $listOpts['count']) : 0;
        $re->page = ceil($listOpts['offset']/$listOpts['count'])+1;
        if ($re->page > $re->total_pages) $re->page = $re->total_pages;
        // $re->rows = parent::fetchEntries($listOpts);
        $re->rows = $db->fetchAll($queryObject->assemble());


        $qo = $queryObject;
        if (0) die(print_r([
            '<pre>' => 'Debug-Infos:',
            'line' => __LINE__,
            'file' => __FILE__,
            'array' => $qo->toArray(),
            'getSelect' => $qo->getSelect(),
            'getSelect 1' => $qo->getSelect( true ),
            'getFrom' => $qo->getFrom(),
            'getFrom 1' => $qo->getFrom( true),
            'getWhere' => $qo->getWhere(),
            'getWhere 1' => $qo->getWhere( true),
            'getOrder' => $qo->getOrder(),
            'getOrder 1' => $qo->getOrder( true),
            'getLimit' => $qo->getLimit(),
            'getLimit 1' => $qo->getLimit( true),
            'assemble' => $qo->assemble(),
            'rows' => $re->rows,
        ], 1));
        return $re;
    }


    /**
     *
     * @param string $rsrcType
     * @param int $rsrcID
     * @param array $listOpts
     * @return \stdClass Member: re->total, re->rows)
     * @throws Exception
     */
    public function getRemovedTourlist(int $sperrzeiten_id, string $rsrcType, int $rsrcID, array $listOpts = []) {
        $db = MyProject_Model_Database::loadStorage($this->_storageName)->getAdapter();
        $tbl = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblLog = MyProject_Model_Database::loadStorage('tourenDispoLog')->info(Zend_Db_Table::NAME);

        if (!$rsrcID || !$rsrcType) {
            $szData = $this->getStorage()->find($sperrzeiten_id)->current();
            $rsrcID = $szData->ressourcen_id;
            $rsrcType = $szData->ressourcen_typ;
        }

        $listOpts['where'] = ' s.sperrzeiten_id = :sperrzeiten_id AND  dv.tour_id is not null ';

        $listOpts['group'] = '';

        $re = new stdClass();

        if (!in_array($rsrcType, array('FP','MA','WZ'))) {
            throw new Exception('Ungueltiger Parameter rsrcType '.$rsrcType.'. Erwartet: FP, MA, all');
        }

        $queryObject = MyProject_Model_QueryBuilder::getInstance()
            ->setSelect('s.sperrzeiten_id'
            .', dv.DatumVon, dv.ZeitVon, dv.ZeitBis, dv.Auftragsnummer,'
            .'ak.LieferungName, ak.Vorgangstitel'
            .',p.tagesnr')
            ->setFrom($tbl . ' s '
                .'LEFT JOIN ' . $tblLog . ' l ON ('
                .' s.sperrzeiten_id = l.sperrzeiten_id '
                .' AND s.ressourcen_typ = l.object_type '
                .' AND s.ressourcen_id = l.object_id '
                .')'
                .' LEFT JOIN mr_touren_dispo_vorgaenge dv '
                .'  ON (l.tour_id = dv.tour_id) ')
            ->setJoin(' '
                .' LEFT JOIN mr_auftragskoepfe_dispofilter ak '
                .'  ON (dv.Mandant=ak.Mandant AND dv.Auftragsnummer=ak.Auftragsnummer) '
                .' LEFT JOIN mr_touren_timelines tl '
                .'	ON (dv.timeline_id = tl.timeline_id) '
                .' LEFT JOIN mr_touren_portlets p '
                .' 	ON (tl.portlet_id = p.portlet_id) ', false)
            ->setWhere(  $listOpts['where'] ?? '')
            ->setGroup(  $listOpts['group'] ?? '')
            ->setHaving( $listOpts['having'] ?? '')
            ->setOrder(  $listOpts['sidx'] ?? 'dv.DatumVon, dv.ZeitVon')
            ->setOrderDir(  $listOpts['sord'] ?? '')
            ->setOffset( (int)($listOpts['offset'] ?? 0))
            ->setLimit(  (int)($listOpts['count'] ?? 100))
            ->setParams([
                'sperrzeiten_id' => (int)$sperrzeiten_id,
                'ressourcen_id' =>  (int)$rsrcID,
                'ressourcen_typ' => $rsrcType
            ]);
        $qo = $queryObject;
        $offset = (int)$qo->getOffset();
        $limit = (int)$qo->getLimit();

        $qo = $queryObject;
        $re->total_records = $db->fetchOne( $qo->assembleCount() );
        $re->total_pages = 1;
        $re->page = 1;
        $re->rows = $db->fetchAll( $qo->assemble() );

        // $re->total_records = parent::fetchCount( $qo->quoteParamsInto($listOpts['where']) );
        $re->total_pages   = ($re->total_records && $qo->getLimit()) ? ceil($re->total_records / $limit) : 0;
        $re->page = ceil($offset/$limit)+1;
        if ($re->page > $re->total_pages) $re->page = $re->total_pages;

        $qo = $queryObject;
        if (0) die(print_r([
            '<pre>' => 'Debug-Infos:',
            'line' => __LINE__,
            'file' => __FILE__,
            'array' => $qo->toArray(),
            'getSelect' => $qo->getSelect(),
            'getSelect 1' => $qo->getSelect( true ),
            'getFrom' => $qo->getFrom(),
            'getFrom 1' => $qo->getFrom( true),
            'getJoin' => $qo->getJoin( ),
            'getJoin 1' => $qo->getJoin( true),
            'getWhere' => $qo->getWhere(),
            'getWhere 1' => $qo->getWhere( true),
            'getOrder' => $qo->getOrder(),
            'getOrder 1' => $qo->getOrder( true),
            'getLimit' => $qo->getLimit(),
            'getLimit 1' => $qo->getLimit( true),
            'assembleCount' => $qo->assembleCount(),
            'assemble' => $qo->assemble(),
            're' => $re,
        ], 1));
        return $re;
    }
}

