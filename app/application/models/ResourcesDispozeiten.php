<?php

class Model_ResourcesDispozeiten extends MyProject_Model_Database {
    
    protected $_storageName = 'resourcesDispozeiten';
    
    public function checkErrors($data) {
        $dateFields = array('gebucht_von', 'gebucht_bis');
        foreach($dateFields as $f) {
            if (isset($data[$f])) {
                $valiDate = new MyProject_Validate_DateTime();
                if (!$valiDate->isValid($data[$f])) {
                    return $f.': ' . implode('. ', $valiDate->getMessages());
                }
            } else {
                return $f. ': Fehlende Angabe!' ;
            }
        }
        if (strtotime($data['gebucht_von']) > strtotime($data['gebucht_bis'])) {
            return 'gebucht_von darf nicht groesser als gebucht_bis sein!';
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
        
        $re = new stdClass();
        
        if (!in_array($rsrcType, array('FP','MA','WZ','all'))) {
            throw new Exception('Ungueltiger Parameter rsrcType '.$rsrcType.'. Erwartet: FP, MA, all');
        }
        
        if (!@empty($listOpts['sidx'])) {
            $listOpts['order'] = $listOpts['sidx']. ' ';
            $listOpts['order'].= (!@empty($listOpts['sord']) && in_array(strtoupper($listOpts['sord']), array('ASC','DESC'))) ? $listOpts['sord'] : 'ASC';
        }
        
        if ($listOpts == null) $listOpts = array();
        if ($rsrcType!='all') {
            $query = $db->quoteInto('ressourcen_typ = ?', $rsrcType).$db->quoteInto(' AND ressourcen_id = ?', $rsrcID);
            if (!@empty($listOpts['where'])) $listOpts['where'] = $query . ' AND (' .$listOpts['where'].')';
            else $listOpts['where'] = $query;
        }
        
        
        $re->total_records = parent::fetchCount($listOpts['where']);
        $re->total_pages   = ($re->total_records && $listOpts['count']) ? ceil($re->total_records / $listOpts['count']) : 0;
        $re->page = ceil($listOpts['offset']/$listOpts['count'])+1;
        if ($re->page > $re->total_pages) $re->page = $re->total_pages;
        $re->rows  = parent::fetchEntries($listOpts);
        return $re;
    }
}

?>
