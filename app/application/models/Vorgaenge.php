<?php 

/**
 * Description of User
 *
 * @author rybka
 */
class Model_Vorgaenge extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'vorgaenge';
    
    protected $_modelFilterName = 'vorgaengeDispoFilter';
    protected $_modelFilter = null;
    protected $_mainTblAlias = 'AKDispoFilter';
    protected $_useFilter = true;
    
    public function __construct($opts = array() ) {
        $opts = array_merge(array( 'useFilter' => true), $opts);
//        die( ' opts: ' . print_r($opts, 1));
        $this->useDispoFilter( $opts['useFilter'] );
    }
    
    public function getMaxId($mandant, $prefix = '') 
    {
        $tbl = self::loadStorage($this->_storageName)->info(Zend_Db_Table::NAME);
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($prefix) {
            $sql = 'SELECT max( convert( substr( Auftragsnummer, length('.$db->quote($prefix).' ) , unsigned ) ) maxnum '
                  .'FROM ' . $db->quoteIdentifier($tbl)
                  .'WHERE Mandant = :mandat AND Auftragsnummer LIKE ' . $db->quote($prefix.'%');
        } else {
        
            $sql = 'SELECT max( convert( Auftragsnummer ) , unsigned ) ) maxnum '
                  .'FROM ' . $db->quoteIdentifier($tbl)
                  .'WHERE Mandant = :mandat AND Auftragsnummer regexp \'^[:digit:]\'';
        }
        return $db->fetchOne($sql, array('mandant'=>$mandant));
        
    }
    
    public function getNewId($mandant, $prefix = '') 
    {
        $maxnum = $this->getMaxId($mandant, $prefix);
        return $prefix . ($maxnum + 1);        
    }
    
    /**
     *
     * @return array(10 => mertens, ... ) 
     */
    public function getMandanten()
    {
        $ini = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mandanten.ini', 'mandanten');
        return $ini->toArray();
    }
    
    /**
     * return array(10 => mertens, ... )
     * @return array
     */
    public function getGeschaeftsbereiche()
    {
        
        $frontendOptions = array(
            'lifetime' => 7200, // Lebensdauer des Caches 2 Stunden
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/' // Verzeichnis, in welches die Cache Dateien kommen
        );

        // Ein Zend_Cache_Core Objekt erzeugen
        $cache = Zend_Cache::factory('Core',
                                 'File',
                                 $frontendOptions,
                                 $backendOptions);
        $cacheid = 'geschaeftsbereiche';
        
        // Nachsehen, ob der Cache bereits existiert:
        if(!$result = $cache->load( $cacheid )) {

            // Cache miss; mit Datenbank verbinden
            $db = Zend_Registry::get('db');
            $db = Zend_Db_Table::getDefaultAdapter();
            $tbl = MyProject_Model_Database::loadStorage( $this->_storageName )->info( Zend_Db_Table::NAME );

            $sql = 'SELECT DISTINCT(Geschaeftsbereich) FROM ' . $db->quoteIdentifier($tbl) . PHP_EOL;
            $sql.= 'ORDER BY Geschaeftsbereich';

            $result = $db->fetchCol($sql, array(), Zend_Db::FETCH_ASSOC);

            $cache->save($result, $cacheid );

        } else {
            // Cache hit! Ausgeben, damit wir es wissen
//            echo "Der ist vom Cache!\n\n";
        }
        
        return $result;
    }
    
    /**
     * @param bool $setFilter 
     */
    public function useDispoFilter($useFilter = true)
    {
        $this->_useFilter = $useFilter;
        if ($this->_useFilter) {
            $this->_modelFilterName = 'vorgaengeDispoFilter';
            $this->_mainTblAlias = 'AKDispoFilter';
        } else {
            $this->_modelFilterName = 'vorgaenge';
            $this->_mainTblAlias = 'Vorgaenge';
        }
        
        $this->_modelFilter = null;
        $this->_modelFilter = MyProject_Model_Database::loadModel($this->_modelFilterName);
    }
    
    public function searchTable($filters, $sort, $limit, $opts = array()) 
    {
        $re = new stdClass();
        $re->total = 0;
        $re->total_pages = 0;
        $re->page = 1;
        $re->rows = null;
        $re->result = null;
        
        $s    = $this->getStorage();
        $db   = $s->getDefaultAdapter();
        $tbl  = $s->info(Zend_Db_Table::NAME);
        $cols = $s->info(Zend_Db_Table::COLS);
                
        $sort  = (object) $sort;
        $limit = (object) $limit;
        if (!isset($sort->sidx) || !in_array($sort->sidx, $cols)) {
            $sort->sidx = 'Auftragsnummer';
        }
        if (!isset($sort->sort) || !preg_match( '/asc|desc/', $sort->sort)) {
            $sort->sort = 'asc';
        }
        
        if (empty($limit->page) || !(int)$limit->page) {
            $limit->page = 1;
        }
        if (empty($limit->rows) || !(int)$limit->rows) {
            $limit->rows = 20;
        }
//        echo '<pre>#' . __LINE__ . ' ' . __METHOD__ . ' filters: ' . print_r($filters,1) . '</pre>' . PHP_EOL;
        $search = '';
        if (is_object($filters) && property_exists($filters, 'rules')) {
            foreach($filters->rules as $_rule) {
                if (in_array($_rule->field, $cols)) {
                    
                    if ($search) $search.= ' ' . $filters->groupOp;
                    $search.= $db->quoteIdentifier($_rule->field);
                    
                    switch($_rule->op) {
                        case 'bw':
                            $search.= ' LIKE ' . $db->quote($_rule->data.'%');
                            break;
                        
                        case 'cn':
                            $search.= ' LIKE ' . $db->quote( '%' . $_rule->data . '%');
                            break;
                            break;
                        
                        case 'gt':
                            $search.= ' > ' . $db->quote($_rule->data);
                            break;
                        
                        case 'lt':
                            $search.= ' < ' . $db->quote($_rule->data);
                            break;
                        
                        
                        case 'eq':
                        default:
                            $search.= ' LIKE ' . $db->quote($_rule->data);
                    }                    
                }
            }
        }
        $where = ($search) ? ' WHERE ' . $search : '';
        
        $sql = 'SELECT COUNT(1) FROM ' . $tbl . $where;
        $re->total = $db->fetchOne($sql);
        $re->total_pages = ceil($re->total / $limit->rows);
        $re->page = max(1, min($re->total_pages, $limit->page));
        
        
        $offset = ($limit->page-1) * $limit->rows;
        
        $re->sql = 'SELECT '
             . 'Mandant '
             . ', Auftragsnummer '
             . ', Geschaeftsbereich '
             . ', Auftragswert '
             . ', AuftragswertListe '
             . ', Vorgangstitel '
             . ', AnsprechpartnerNachnameLief '
             . ', LieferungStrassePostfach '
             . ', LieferungPostleitzahl '
             . ', LieferungOrt '
             . ', LieferungLand '
             . ', LieferungName '
             . ', Lieferwoche '
             . ', Lieferjahr '
             . ', Liefertermin '
             . ', BestaetigtAm '
             . ', Auftragswert '
             . ', LieferterminFix '
             . ', LieferterminHinweisText '
             . ' FROM ' . $tbl . ' '
             . $where
             . ' ORDER BY ' . $sort->sidx . ' ' . $sort->sort . ' '
             .  'LIMIT ' . $offset . ', ' . $limit->rows;
//        echo '<pre>#' . __LINE__ . ' ' . __METHOD__ . ' result: '  . print_r($re,1) . '</pre>';
        $re->rows = $db->fetchAll($re->sql);
        
        return $re;       
    }
    
    public function insert(array $data) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $s  = $this->getStorage();
        $t  = $s->info(Zend_Db_Table::NAME);
        if ($data['Mandant']) {
            $maxid = $db->fetchOne(
                'SELECT MAX(Auftragsnummer) FROM ' . $db->quoteIdentifier($t)
               .' WHERE Mandant = ' . $db->quote($data['Mandant'])
            );
            $data['Auftragsnummer'] = ((int)$maxid) + 1;
            $id = parent::insert($data);
            if ( $id ) {            
                
                $db->query(
                    'DELETE FROM mr_auftragskoepfe_refs WHERE '
                   .' Mandant = ' . $db->quote($data['Mandant']) . ' '
                   .' AND Auftragsnummer = ' . $db->quote($data['Auftragsnummer'])                        
                );
                if ( isset($data['WwsRefs']) ) {
                    foreach($data['WwsRefs'] as $_ids) {
                        list($_m, $_a) = explode(':', $_ids);
                        $db->query(
                            'INSERT INTO mr_auftragskoepfe_refs SET '
                           .' Mandant = ' . $db->quote($data['Mandant']) . ', '
                           .' Auftragsnummer = ' . $db->quote($data['Auftragsnummer']) . ', '
                           .' Mandant_ref = ' . $db->quote($_m) . ', '
                           .' Auftragsnummer_ref = ' . $db->quote($_a)                           
                        );
                    }
                }
                
                return $id;
            }
        }
        return false;
    }
    
    public function update(array $data, $id) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $s  = $this->getStorage();
        list($mandant, $auftragsnr) = $id;
        if ($mandant) {
            if (parent::update($data, $id) && $mandant && $auftragsnr ) {            
                $sql = 'DELETE FROM mr_auftragskoepfe_refs WHERE '
                   .' Mandant = ' . $db->quote($mandant) . ' '
                   .' AND Auftragsnummer = ' . $db->quote($auftragsnr) ;
                $db->query( $sql );
                
                if ( isset($data['WwsRefs']) ) {
                    foreach($data['WwsRefs'] as $_ids) {
                        list($_m, $_a) = explode(':', $_ids);
                        $db->query(
                            'INSERT INTO mr_auftragskoepfe_refs SET '
                           .' Mandant = ' . $db->quote($mandant) . ', '
                           .' Auftragsnummer = ' . $db->quote($auftragsnr) . ', '
                           .' Mandant_ref = ' . $db->quote($_m) . ', '
                           .' Auftragsnummer_ref = ' . $db->quote($_a)                           
                        );
                    }
                }                
                return array(
                    'Mandant'=>$mandant,
                    'Auftragsnummer'=>$auftragsnr,
                );
            }
        }
        return false;
    }
    
    public function getWwsRefItems($mandant, $anr) 
    {
        $modelRef = new Model_VorgaengeRef();        
        $storageRef = $modelRef->getStorage();
        $db = $storageRef->getAdapter();
        $refIds = $storageRef->fetchAll(
            'Mandant = ' . $db->quote($mandant) . ' AND Auftragsnummer = ' . $db->quote($anr)
        );
        
        $wwsRefItems = array();
        foreach($refIds as $_ref) {
            $wwsRefItems[] = $this->fetchEntry($_ref['Mandant_ref'], $_ref['Auftragsnummer_ref'] );
        }
        return $wwsRefItems;
    }

    public function query($filters, $sort, $limit, $opts) 
    {
        $tblAK = $this->getStorage()->info(Zend_Db_Table::NAME);
        
        $re = new stdClass();
        $re->modelName = $this->_modelFilterName;
        $re->sql = '';
        $re->sqlFilterByDate = '';
        $re->total = 0;
        $re->total_pages = 0;
        $re->page = 1;
        $re->result = null;
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
               
        /* @var $storage Model_Db_vorgaenge */
        $TblCnf       = $this->infoToTblConf();
        
        $opts = array_merge(array(
//            'mandant' => 10,
            'date'   => 1,
            'dateTo' => 1,
            'dateKwOnly'    => '>=',
            'dispoStatus'   => '',
            'dispoStatusWV' => '',
            'view'   => '',
        ), $opts);
        
        $sort = array_merge( array(
            'sidx' => null,
            'sord' => 'ASC',
            ), $sort                
        );
        
        $limit = array_merge( array(
            'page' => 1,
            'rows' => 100
            ), $limit
        );
        
        $page       = (int) $limit['page'];
        $limit      = (int) $limit['rows'];
        
        $sidx       = $sort['sidx'];
        $sord       = $sort['sord'];
        
        $dispoStatus = $opts['dispoStatus'];
        $dispoStatusWV = $opts['dispoStatusWV'];
        $mandantid  = (int) $opts['mandant'];
        
        switch($opts['view']) {
            case 'touren':
                $selectFromAuftrag = 
                  'A.Mandant'
                . ',A.Auftragsnummer'
                . ',A.Geschaeftsbereich'
                . ',A.Auftragswert'
                . ',A.AuftragswertListe'
                . ',A.Vorgangstitel'
                . ',A.AnsprechpartnerNachnameLief'
                . ',A.LieferungStrassePostfach'
                . ',A.LieferungPostleitzahl'
                . ',A.LieferungOrt'
                . ',A.LieferungLand'
                . ',A.LieferungName'
                . ',A.Lieferwoche'
                . ',A.Lieferjahr'
                . ',A.Liefertermin'
                . ',A.BestaetigtAm'
                . ',A.Auftragswert'
                . ',A.LieferterminFix'
                . ',A.LieferterminHinweisText';
                break;
            
            default:
                $selectFromAuftrag = 'A.*';
        }
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
            $sord = 'ASC';
        
        $opt = array(
            "additionalFields" => array(),
            "tableNamespace" => "A"
        );

        $dateKwCompare = $opts['dateKwOnly'];
        
        // Anfrage untersuchen, ob Auftragsnummer abgefragt wird
        // Entscheidende Weiche fuer Datebankabfrage
        $ANR = 0;
        if ($filters) {
            $jsonFilter = json_decode( $filters );
//            die( '#' . __LINE__ . ' ' . basename(__FILE__) . ' jsonFilter: (src:'.print_r($filters,1).')' . print_r( $jsonFilter, 1));
            if ($jsonFilter && isset($jsonFilter->rules)) foreach($jsonFilter->rules as $_rule) {
                if (0 && $_rule->field == 'Auftragsnummer' && $_rule->data) {
                    $ANR = trim($_rule->data);
                    $ANR_OPER = trim($_rule->op);
                }
                if (0 && $_rule->field == 'Mandant' && $_rule->data) {
                    $mandantid = '';
                }
                if ($_rule->field == 'dispoStatus' && $_rule->data) {
                    $dispoStatus = $_rule->data;
                }
            }
        }
        
        if ($ANR) { // && preg_match('/eq|=/', $ANR_OPER) ) {
            //die('#'.__LINE__.' '.__FILE__);
            $sqlWhere = 'A.Auftragsnummer = '.$db->quote($ANR);
        } else {
            //die('#'.__LINE__.' '.__FILE__ . Zend_Debug::dump($ANR));
            
            $sqlWhere = 'A.Bearbeitungsstatus > 1 ';
            $search = JqGridSearch::getSqlBySearch($TblCnf, $opt);
            if ($search) $sqlWhere.= ' AND ' . $search;
        }
        
        $SelectDisponiert  = 
            'DV.tour_disponiert_am, '
           .'DA.auftrag_wiedervorlage_am, '
           .'DA.tour_neulieferungen_count, '
           .'DA.tour_dispo_count, '
           .'COUNT(DISTINCT(DV.tour_id)) tour_count, '
           .'MIN(DV.DatumVon) tour_date_first, '
           .'MAX(DV.DatumVon) tour_date_last, '
           .'CASE '
           .'WHEN DV.DatumVon is null ' . PHP_EOL
           .'THEN \'neu\' '             . PHP_EOL
           .'WHEN DV.DatumVon is not null  AND DA.auftrag_disponiert_am IS NULL  AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0) ' . PHP_EOL
           .'THEN \'beauftragt\' '      . PHP_EOL
           .'WHEN DV.zeiten_erfasst_am IS NOT NULL ' . PHP_EOL
           .'THEN \'fertig\' '          . PHP_EOL
           .'WHEN DA.auftrag_abgeschlossen_am IS NULL AND DA.tour_neulieferungen_count > 0 ' . PHP_EOL
           .'THEN \'neulieferung\' '    . PHP_EOL
           .'WHEN DA.auftrag_disponiert_am IS NULL  AND DA.tour_dispo_count IS NOT NULL AND DA.tour_dispo_count > 0 ' . PHP_EOL
           .'THEN \'teil\' '            . PHP_EOL
           .'WHEN DA.auftrag_disponiert_am IS NOT NULL ' . PHP_EOL
           .'THEN \'teil\' '            . PHP_EOL
           .'ELSE '                     . PHP_EOL
           .'-- nicht bestimmbar '      . PHP_EOL
           .'NULL '                     . PHP_EOL
           .'END AS dispoStatus '       . PHP_EOL
		   
		   
           ;
        
        $filterDateSQL = '';        
            
        $WhereDisponiert = ' DA.auftrag_abgeschlossen_am IS NULL ';
        if ($dispoStatus ) {
            switch ($dispoStatus) {
                case 'neu':
                $WhereDisponiert.= 
                    ' AND DV.DatumVon is null';
                    
                break;
                
                case 'beauftragt': 
                $WhereDisponiert.= 
                     ' AND DV.DatumVon is not null'
                    . ' AND DA.auftrag_disponiert_am IS NULL '
                    .' AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0) ' . PHP_EOL;
                break;
                case 'teil': 
                $WhereDisponiert.= 
                     ' AND DA.auftrag_disponiert_am IS NULL '
                    .' AND DA.tour_dispo_count IS NOT NULL AND DA.tour_dispo_count > 0 ' . PHP_EOL;
                break;
                case 'fertig': 
                $WhereDisponiert.= ' AND DA.auftrag_disponiert_am IS NOT NULL ' . PHP_EOL;
                break;
                case 'neulieferung': 
                $WhereDisponiert.= ' AND DA.auftrag_disponiert_am IS NULL AND DA.tour_neulieferungen_count > 0 ' . PHP_EOL;
                break;
            }
        }
        
        $WhereDisponiert.= "/*dispoStatusWV: $dispoStatusWV */";
        if ($dispoStatusWV ) {
            switch ($dispoStatusWV) {
                case 'disposition':
                $WhereDisponiert.= 
                    ' AND DA.auftrag_wiedervorlage_am IS NULL';
                    
                break;
                
                case 'zurueckgestellt': 
                $WhereDisponiert.= 
                    ' AND DA.auftrag_wiedervorlage_am IS NOT NULL'
//                   .' AND DATE_FORMAT(NOW(), "%Y-%m-%d") >= DATE_FORMAT(DA.auftrag_wiedervorlage_am, "%Y-%m-%d")'
                   ;
                break;
            }
        }

        // START IF !ANR
        if (!$ANR) {
            
            
            if ($opts['date']) {
                $val = $opts['date'];
                $filterTime = ( $val ) ? strtotime( $opts['date'] ) : time();

                if (!$filterTime) 
                $filterTime = time();

                $filterD   = date('Y-m-d', $filterTime);
                $filterY   = date('y', $filterTime);
                $filterW   = ltrim(date('W', $filterTime), '0');
                $chckMonat = date('n', $filterTime);

                if ($filterW > 50 && $chckMonat == 1) 
                $filterY= $filterY-1;

                $dateCompareOp   = $dateKwCompare;
                
                if ($opts['dateTo']) {
                    $dateCompareOp = '>=';
                    $dateCompareOpTo = '<=';
                    $filterTimeTo = strtotime($opts['dateTo']);
                    $filterDTo    = date('Y-m-d', $filterTimeTo);
                    $filterYTo    = date('y', $filterTimeTo);
                    $filterWTo    = ltrim(date('W', $filterTimeTo), '0');
                    $chckMonatTo  = date('n', $filterTimeTo);

                    if ($filterWTo > 50 && $chckMonatTo == 1) 
                    $filterYTo= $filterYTo-1;

                    $filterDateSQL = <<<EOT
                        -- filterTime: $filterTime
                        -- filterD:    $filterD
                        -- filterY:    $filterY
                        -- filterW:    $filterW
                        -- chckMonat:  $chckMonat
                        -- Terminfilter
                        -- Zeige Auftraege bzw. die Positionen enthalten die 
                        --   exakt fuer diesen Tag vorgesehen
                        --   oder vorher und noch nicht 100% gebucht sind
                        -- die fuer keinen Tag exakt vorgesehen, aber fuer diese Woche
                        --   oder vorher und noch nicht 100% gebucht sind
                        (    
                            (
                                (UNIX_TIMESTAMP( A.Liefertermin ) = 0 AND (A.Lieferwoche IS NULL OR A.Lieferwoche = 0) )
                                OR (DATE(A.Liefertermin) $dateCompareOp '$filterD' AND DATE(A.Liefertermin) $dateCompareOpTo '$filterDTo')
                                OR ( (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW)
                                    AND
                                    (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOpTo ($filterYTo * 100 + $filterWTo)
                                )
                            )
                        )
EOT;
                } else {

                    $filterDateSQL = <<<EOT
                    -- filterTime: $filterTime
                    -- filterD:    $filterD
                    -- filterY:    $filterY
                    -- filterW:    $filterW
                    -- chckMonat:  $chckMonat
                    -- Terminfilter
                    -- Zeige Auftraege bzw. die Positionen enthalten die 
                    --   exakt fuer diesen Tag vorgesehen
                    --   oder vorher und noch nicht 100% gebucht sind
                    -- die fuer keinen Tag exakt vorgesehen, aber fuer diese Woche
                    --   oder vorher und noch nicht 100% gebucht sind
                    (    
                        (
                            (UNIX_TIMESTAMP( A.Liefertermin ) = 0 AND (A.Lieferwoche IS NULL OR A.Lieferwoche = 0) )
                            OR (DATE(A.Liefertermin) $dateCompareOp '$filterD')
                            OR ( (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW) )
                        )
                    )
EOT;
                }
            }
        }
        // ENDE IF !ANR
        
        
        //SELECT A.*, $SelectDisponiert 
                
        $sqlFromWhere = <<<EOT

FROM $tblAK A 
LEFT JOIN mr_touren_dispo_auftraege DA
 ON 
 A.Mandant = DA.Mandant
 AND A.Auftragsnummer = DA.Auftragsnummer
LEFT JOIN mr_touren_dispo_vorgaenge DV
 ON 
 A.Mandant = DV.Mandant
 AND A.Auftragsnummer = DV.Auftragsnummer 
WHERE 1
EOT;
        if ($mandantid)
        $sqlFromWhere.= ' AND A.Mandant = ' . $db->quote($mandantid) . PHP_EOL;
        
        if ( $WhereDisponiert )
        $sqlFromWhere.= ' AND ' . $WhereDisponiert . PHP_EOL;
        
        if ($filterDateSQL)
        $sqlFromWhere.= ' AND ' . $filterDateSQL . PHP_EOL;
        
        if ($sqlWhere) 
        $sqlFromWhere.= ' AND ' . $sqlWhere . PHP_EOL;
        
        if (0) die(
            'SELECT COUNT( DISTINCT ( concat( A.Mandant, \'-\', A.Auftragsnummer ) ) ) '
           .$sqlFromWhere      
        );
        
        $re->total = $db->fetchOne(
            'SELECT COUNT( DISTINCT ( concat( A.Mandant, \'-\', A.Auftragsnummer ) ) ) '
           .$sqlFromWhere
        );

        if ($re->total > 0) {
            $re->total_pages = ceil($re->total / $limit);
        } else {
            $re->total_pages = 0;
        }
        if ($page > $re->total_pages)
            $page = $re->total_pages;
        
        $re->page = $page;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        $sql = 
            'SELECT '.$selectFromAuftrag.', ' . $SelectDisponiert . ' '
           .$sqlFromWhere
           .' GROUP BY A.Mandant, A.Auftragsnummer' . PHP_EOL
           .($sidx ? 'ORDER BY ' . $sidx . ' ' . $sord . PHP_EOL : '')
           .'LIMIT ' . $start . ', ' . $limit;
//        echo '#' . __LINE__ . ' sql: ' . $sql . '<br>' . PHP_EOL;
        $this->logQuery( $sql, __METHOD__ . ' #'. __LINE__, 'V-1');
        /* @var $result Zend_Db_Statement */
        $re->result = $db->query(
             $sql
        );
        
        $re->sql = $sql;
        $re->sqlFilterByDate = $filterDateSQL;
//        die(Zend_Debug::dump($re->result->fetchAll(), 'SQL-Fetch-All', false) );
        return $re;
    }
        
    /**
     *
     * @param array $filters
     * @param array $sort
     * @param array $limit
     * @param array $opts 
     * @return stdClass total, result Zend_Db_Statement_PDO 
     */
    public function query_old($filters, $sort, $limit, $opts)
    {        
        $re = new stdClass();
        $re->modelName = $this->_modelFilterName;
        $re->sql = '';
        $re->sqlFilterByDate = '';
        $re->total = 0;
        $re->total_pages = 0;
        $re->page = 1;
        $re->result = null;
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        $NAME = Zend_Db_Table::NAME;
        
        /* @var $model Model_Vorgaenge */
        $model = $this->_modelFilter;
               
        /* @var $storage Model_Db_vorgaenge */
        $storage      = $model->getStorage();
        $TblCnf       = $model->infoToTblConf();
        $mainTblAlias = $this->_mainTblAlias;
        
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDA = MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME);
                
        $opts = array_merge(array(
            'mandant' => 10,
            'date' => 1,
            'dateTo' => 1,
            'dateKwOnly' => '>=',
            'dispoStatus' => '',
        ), $opts);
        
        $sort = array_merge( array(
            'sidx' => null,
            'sord' => 'ASC',
        ), $sort                
        );
        
        $limit = array_merge( array(
            'page' => 1,
            'rows' => 100
        ), $limit);
        
        $page       = (int) $limit['page'];
        $limit      = (int) $limit['rows'];
        
        $sidx       = $sort['sidx'];
        $sord       = $sort['sord'];
        
        $dispoStatus = $opts['dispoStatus'];
        $mandantid  = (int) $opts['mandant'];
        $date       = $opts['date'];
        $dateTo     = $opts['dateTo'];
        $dateKwOnly = $opts['dateKwOnly'];
        
        $ANR = '';
        
        if ($dateKwOnly == 'false') 
            $dateKwOnly = false;
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) 
            $sord = 'ASC';
        
        $opt = array(
            "additionalFields" => array(),
            "tableNamespace" => "Vorgaenge"
        );
        
        $WhereDisponiert  = '';
        
        $SelectDisponiert  = array(
            'DV.tour_disponiert_am', 
            new Zend_Db_Expr('COUNT(DISTINCT(DV.tour_id)) tour_count'), 
            new Zend_Db_Expr('MIN(DV.DatumVon) tour_date_first'), 
            new Zend_Db_Expr('MAX(DV.DatumVon) tour_date_last'));

        $JoinDisponiert = 
            '(   '.$mainTblAlias.'.Mandant = DV.Mandant ' . PHP_EOL
            .'   AND '.$mainTblAlias.'.Auftragsnummer = DV.Auftragsnummer' . PHP_EOL
            .')';
            
        if ( $dispoStatus ) {
            $WhereDisponiert = '';
            switch ($dispoStatus) {
                case 'beauftragt': 
                $WhereDisponiert = 
                    'DA.auftrag_disponiert_am IS NULL AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0)' . PHP_EOL;
                break;
                case 'teil': 
                $WhereDisponiert = 
                    'DA.auftrag_disponiert_am IS NULL AND DA.tour_dispo_count IS NOT NULL AND DA.tour_dispo_count > 0' . PHP_EOL;
                break;
                case 'fertig': 
                $WhereDisponiert = 'DA.auftrag_disponiert_am IS NOT NULL' . PHP_EOL;
                break;
                case 'neulieferung': 
                $WhereDisponiert = 'DA.auftrag_disponiert_am IS NULL AND DA.tour_neulieferungen_count > 0' . PHP_EOL;
                break;
            }
        }
        // filter = {"groupOp":"AND","rules":[{"field":"Auftragsnummer","op":"null","data":"1047840"}]}
        
        // Anfrage untersuchen, ob Auftragsnummer abgefragt wird
        // Entscheidende Weiche fuer Datebankabfrage
        if ($filters) {
            $jsonFilter = json_decode($filters);
            //print_r($jsonFilter);
            if ($jsonFilter->rules) 
                foreach($jsonFilter->rules as $_rule) {
                    if ($_rule->field == 'Auftragsnummer' && $_rule->data) {
                        $ANR = trim($_rule->data);
                    }
                }
        }
        
        $filterOpts = array(
            'dateTo'           => $dateTo,
            'dateKwCompare'    => $dateKwOnly,
            'dispoStatus' => $dispoStatus,
        );
        
        // Wenn nicht gezielt nach Auftr-Nr gesucht wird initialisiere Komplexe Suche
        if (!$ANR) {
            $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
            $sqlVorgangeFilterFrom = new Zend_Db_Expr( "(\n".$this->filterByDate($date, 'date', $filterOpts )."\n)\n" );
        } else {
            $sqlWhere = '';
            $sqlVorgangeFilterFrom = new Zend_Db_Expr( "(\n".$this->filterByDate($ANR, 'Auftragsnummer', $filterOpts )."\n)\n" );
        }
        
        $tblVG = $this->getStorage()->info($NAME);
        
        // Pre-Select-Query: Count Records
        $select = $db->select();
        $select->from(array($mainTblAlias => $tblVG), new Zend_Db_Expr('COUNT(*) AS count'));
        
//        die('_useFilter: ' . ($this->_useFilter ? 'true' : 'false').'; mainTblAlias: ' . $mainTblAlias . '; table: ' . $model->getStorage()->info($NAME) );
        if ($this->_useFilter) {
            $select->joinLeft(
                array('Vorgaenge' => $tblVG ),
                $mainTblAlias . '.Mandant = Vorgaenge.Mandant '
                .'AND ' . $mainTblAlias . '.Auftragsnummer = Vorgaenge.Auftragsnummer',
                array() // SpaltenListe der JoinTable
            );
        }
        
        $select->joinLeft( 
            array('DV' => $tblDV), 
            $JoinDisponiert, 
            $SelectDisponiert );
        
        if ($WhereDisponiert) {            
            $select->joinLeft( 
                array('DA' => $tblDA), 
                $mainTblAlias . '.Mandant = DA.Mandant '
                .'AND ' . $mainTblAlias . '.Auftragsnummer = DA.Auftragsnummer',
                array() );
            
            $select->where( $WhereDisponiert );
        }
        
        $select->joinRight(
            array('Filter' => $sqlVorgangeFilterFrom ),
             'Filter.Mandant = Vorgaenge.Mandant '
            .'AND Filter.Auftragsnummer = Vorgaenge.Auftragsnummer',
            array() // SpaltenListe der JoinTable
        );
        
        if ($mandantid) $select->where( 
            $mainTblAlias . '.'.$db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid)
        );
        
        if ($sqlWhere)  
            $select->where ($sqlWhere);
//        die( '<pre>#' . __LINE__ . PHP_EOL .$select->assemble().'</pre>');
        
        $select->group(array(
                new Zend_Db_Expr($mainTblAlias . '.Mandant'),
                new Zend_Db_Expr( $mainTblAlias . '.Auftragsnummer'),
        ));
        
        $re->total = $db->fetchOne($select);

        if ($re->total > 0) {
            $re->total_pages = ceil($re->total / $limit);
        } else {
            $re->total_pages = 0;
        }
        if ($page > $re->total_pages)
            $page = $re->total_pages;
        
        $re->page = $page;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        // Main-Select-Query: Records
        $select = $db->select();
        $select->from( array($mainTblAlias => $TblCnf['Table']) );
        
        if ($this->_useFilter) {
            $select->joinLeft(
                array('Vorgaenge' => $tblVG ),
                $mainTblAlias.'.Mandant = Vorgaenge.Mandant '
                .'AND ' . $mainTblAlias . '.Auftragsnummer = Vorgaenge.Auftragsnummer',
                array() // SpaltenListe der JoinTable
            );
        }
        
        $select->joinLeft( 
            array('DV' => $tblDV), 
            $JoinDisponiert, 
            $SelectDisponiert );
        
        
        
        if ($WhereDisponiert) {            
            $select->joinLeft( 
                array('DA' => $tblDA), 
                $mainTblAlias . '.Mandant = DA.Mandant '
                .'AND ' . $mainTblAlias . '.Auftragsnummer = DA.Auftragsnummer',
                array() );
            
            $select->where( $WhereDisponiert );
        }
//        $selectDisponiert );
        
        $select->joinRight(
            array('Filter' => $sqlVorgangeFilterFrom ),
             'Filter.Mandant = Vorgaenge.Mandant '
            .'AND Filter.Auftragsnummer = Vorgaenge.Auftragsnummer',
            array('AnzahlPositionen', 'SumNichtDisponiert') // SpaltenListe der JoinTable
        ); 
        
        if ($mandantid) $select->where( 
            $mainTblAlias.'.'.$db->quoteIdentifier ('Mandant') .' = ' . $db->quote ($mandantid)
        );
        
        if ($sqlWhere) 
            $select->where ($sqlWhere);
        
        $select->group(array(
                new Zend_Db_Expr($mainTblAlias . '.Mandant'),
                new Zend_Db_Expr( $mainTblAlias . '.Auftragsnummer'),
        ));
        
        if ($sidx)
            $select->order( $sidx . ' ' . $sord );
        
        $select->limit($limit, $start);
        
        $re->sqlFilterByDate = $sqlVorgangeFilterFrom;
        $re->sql = $select->assemble();
        
        /* @var $result Zend_Db_Statement */
        $re->result = $db->query($select);
//        die(Zend_Debug::dump($re->result->fetchAll(), 'SQL-Fetch-All', false) );
        return $re;
    }
    
    public function filterByDate($val, $format = 'date', $opts)
    {
        $opts = array_merge(array(
            'dateTo' => '',
            'dateKwCompare'  => '>=',
            'dispoStatus' => '',
            'auftragStatus'  => '',
            ), 
            $opts
        );
        
        $dateKwCompare    = $opts['dateKwCompare'];
        $dispoStatus = $opts['dispoStatus'];
        
        $NAME = Zend_Db_Table::NAME;
        //$tblAK = $this->getStorage()->info($NAME);
        $tblAK = MyProject_Model_Database::loadStorage( $this->_modelFilterName )->info($NAME);
        $tblAP = MyProject_Model_Database::loadStorage('auftragspositionen')->info($NAME);
        //$tblBK = MyProject_Model_Database::loadStorage('bestellkoepfe')->info($NAME);
        $tblBK = MyProject_Model_Database::loadStorage('bestellkoepfeDispoFilter')->info($NAME);
        $tblBP = MyProject_Model_Database::loadStorage('bestellpositionen')->info($NAME);
        $tblDP = MyProject_Model_Database::loadStorage('tourenDispoPositionen')->info($NAME);
        $tblDV = MyProject_Model_Database::loadStorage('tourenDispoVorgaenge')->info($NAME);
        $tblDA = MyProject_Model_Database::loadStorage('tourenDispoAuftraege')->info($NAME);
               
        // Mapping von dispoStatus auf auftragStatus
        $mapDispoStatus = array(
          'beauftragt' => 'nichtDiponiert',
          'teil'       => 'teilDisponiert',
          'fertig'     => 'vollDisponiert'
        );
        
        if (empty($opts['auftragStatus']) && isset($mapDispoStatus[$dispoStatus])) 
        $opts['auftragStatus'] = $mapDispoStatus[$dispoStatus];
        
        // throw new Exception( 'Test tblDP Name: ' . $tblDP );
        // mr_touren_dispo_auftragspositionen

        $filterSQL = <<<EOT
SELECT 
-- Vereinbarter Ausliefertermin Geamtauftrag an Kunden
A.Mandant,
A.Auftragsnummer,
A.Lieferwoche AWoche,
A.Auftragswert,
A.AuftragswertListe,
CASE
    WHEN UNIX_TIMESTAMP( A.Liefertermin ) >0
    THEN A.Liefertermin
    WHEN A.Lieferjahr >=1 AND A.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL A.Lieferjahr YEAR ) , INTERVAL ((A.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS ALieferdatum, 
IF( UNIX_TIMESTAMP( A.Liefertermin ) >0, 'Datum', IF(A.Lieferjahr>=1 AND A.Lieferwoche>=1,'KW', NULL) ) ALieferGenauigkeit, 

-- Ankunftstermine gebuendelter Bestellungen lt. Lieferant
/*
CASE
    WHEN UNIX_TIMESTAMP( B.Liefertermin ) >0
    THEN B.Liefertermin
    WHEN B.Lieferjahr >=1 AND B.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL B.Lieferjahr YEAR ) , INTERVAL ((B.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS BLieferdatum, 
IF( UNIX_TIMESTAMP( B.Liefertermin ) >0, 'Datum', IF(B.Lieferjahr>=1 AND B.Lieferwoche>=1,'KW', NULL) ) BLieferGenauigkeit,
*/

-- Auftragspositionen
-- Vereinbarte Ausliefertermine Einzelpositionen an Kunden
AP.`Positionsnummer` AP_PosNr,
AP.`Liefertermin` AP_Liefertermin,
AP.Lieferwoche APWoche,
CASE
    WHEN UNIX_TIMESTAMP( AP.Liefertermin ) >0
    THEN AP.Liefertermin
    WHEN AP.Lieferjahr >=1 AND AP.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL AP.Lieferjahr YEAR ) , INTERVAL ((AP.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS APLieferdatum, 
IF( UNIX_TIMESTAMP( AP.Liefertermin ) >0, 'Datum', IF(AP.Lieferjahr>=1 AND AP.Lieferwoche>=1,'KW', NULL) ) APLieferGenauigkeit, 

-- Bestellkoepfe
-- MAX(B.ErwarteterEingang) MaxErwarteterEingang,
-- MAX(B.ErwarteterEingangJahr*100 + B.ErwarteterEingangWoche) MaxErwarteteWoche,

-- Bestellpositionen
-- Ankunftstermine einzelner Positionen lt. Lieferant
BP.`Positionsnummer` BP_PosNr,
BP.Liefertermin BP_Liefertermin, 
CASE
    WHEN UNIX_TIMESTAMP( BP.Liefertermin ) >0
    THEN BP.Liefertermin
    WHEN BP.Lieferjahr >=1 AND BP.Lieferwoche >=1
    THEN DATE_ADD( DATE_ADD( '2000-01-01', INTERVAL BP.Lieferjahr YEAR ) , INTERVAL ((BP.Lieferwoche) *7) DAY )
    ELSE
    -- nicht bestimmbar
    NULL
END AS BPLieferdatum,

-- DispoMengen
SUM(AP.Bestellmenge) GesamtBestellMenge,
D.DisponierteMenge,
CASE
    WHEN D.tour_id IS NOT NULL THEN
        IF(D.DisponierteMenge - D.AbschlussNLMenge <= AP.Bestellmenge, AP.Bestellmenge - D.DisponierteMenge + D.AbschlussNLMenge, 0)
    ELSE
        AP.Bestellmenge
END AS DispoRestMengen,
CASE
    WHEN D.tour_id IS NOT NULL THEN
        IF(D.DisponierteMenge - D.AbschlussNLMenge < AP.Bestellmenge, 1, 0)
    ELSE
        1
END AS IstNochNichtVollDisponiert,
COUNT(*) AnzahlPositionen,
SUM( 
    IF(D.tour_id IS NULL, 1, IF(D.DisponierteMenge - D.AbschlussNLMenge < AP.Bestellmenge, 1, 0) )
) SumNichtDisponiert

FROM $tblAK A
-- LEFT JOIN $tblBK B ON(
--     A.Mandant = B.Mandant
--     AND A.Auftragsnummer = B.Auftragsnummer)
        
LEFT JOIN $tblDA DA ON(
    A.Mandant = DA.Mandant 
    AND A.Auftragsnummer = DA.Auftragsnummer
)

        
LEFT JOIN $tblAP AP ON(
    A.Mandant = AP.Mandant
    AND A.Auftragsnummer = AP.Auftragsnummer
)
        
LEFT JOIN $tblDP D ON
(
    A.Mandant = D.Mandant
    AND A.Auftragsnummer = D.Auftragsnummer
    AND AP.Positionsnummer = D.Positionsnummer
)
LEFT JOIN $tblBP BP ON
(
    A.`Mandant`  = BP.`Mandant`
    AND A.`Auftragsnummer`  = BP.`Auftragsnummer` 
    AND AP.`Positionsnummer` = BP.`AuftragsPositionsnummer`
)

WHERE
-- Filter Bearbeitungsstatus
A.Bearbeitungsstatus = 2  
AND AP.Positionsart  = 1  
-- AND AP.Positionsnummer = D.Positionsnummer 
-- AND AP.`Positionsnummer` = BP.`AuftragsPositionsnummer`  
AND AP.AlternativPos <> 1 
EOT;
        
        switch($opts['auftragStatus']) {
            case 'vollDisponiert':
                $filterSQL.= 'AND DA.auftrag_disponiert_user IS NOT NULL' . PHP_EOL;
                break;

            case 'teilDisponiert':
                $filterSQL.= 'AND DA.auftrag_disponiert_user IS NULL '
                            .'AND DA.tour_dispo_count IS NOT NULL '
                            .'AND DA.tour_dispo_count > 0 ' . PHP_EOL;
                break;
            case 'nichtDisponiert':
                $filterSQL.= 'AND DA.auftrag_disponiert_user IS NULL'
                            .'AND (DA.tour_dispo_count IS NULL OR DA.tour_dispo_count = 0) ' . PHP_EOL;
                break;

            case 'vollAbgeschlossen':
                $filterSQL.= 'AND DA.auftrag_abgeschlossen_user IS NOT NULL' . PHP_EOL;
                break;

            case 'teilAbgeschlossen':
                $filterSQL.= 'AND DA.auftrag_abgeschlossen_user IS NULL '
                            .'AND DA.tour_abschluss_count IS NOT NULL '
                            .'AND DA.tour_abschluss_count > 0 ' . PHP_EOL;
                break;
            case 'nichtAbgeschlossen':
                $filterSQL.= 'AND DA.auftrag_abgeschlossen_user IS NULL'
                            .'AND (DA.tour_abschluss_count IS NULL OR DA.tour_abschluss_count = 0) ' . PHP_EOL;
                break;

            default:
                // Nothing
        }

        
        if ( 'Auftragsnummer' == $format) {
            $filterSQL.= PHP_EOL . "AND A.Auftragsnummer = ".((int)trim($val)) . PHP_EOL;
        }
        else {        
            $limit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : 200;
            $filterTime = ( $val ) ? strtotime( $val ) : time();
            if (!$filterTime) $filterTime = time();

            $filterD = date('Y-m-d', $filterTime);
            $filterY = date('y', $filterTime);
            $filterW = ltrim(date('W', $filterTime), '0');
            $chckMonat = date('n', $filterTime);
            if ($filterW > 50 && $chckMonat == 1) $filterY= $filterY-1;
            
            $dateCompareOp = $dateKwCompare;
            $dateCompareOpTo = '';
            
            if ($opts['dateTo']) {
                $dateCompareOp = '>=';
                $dateCompareOpTo = '<=';
                $filterTimeTo = strtotime($opts['dateTo']);
                $filterDTo = date('Y-m-d', $filterTimeTo);
                $filterYTo = date('y', $filterTimeTo);
                $filterWTo = ltrim(date('W', $filterTimeTo), '0');
                $chckMonatTo = date('n', $filterTimeTo);
                if ($filterWTo > 50 && $chckMonatTo == 1) $filterYTo= $filterYTo-1;
                
                    $filterSQL.= <<<EOT
-- filterTime: $filterTime
-- filterD: $filterD
-- filterY: $filterY
-- filterW: $filterW
-- chckMonat: $chckMonat
-- Terminfilter
-- Zeige Auftraege bzw. die Positionen enthalten die 
--   exakt fuer diesen Tag vorgesehen
--   oder vorher und noch nicht 100% gebucht sind
-- die fuer keinen Tag exakt vorgesehen, aber fuer diese Woche
--   oder vorher und noch nicht 100% gebucht sind
AND
(    
    (
        (UNIX_TIMESTAMP( A.Liefertermin ) = 0 AND (A.Lieferwoche IS NULL OR A.Lieferwoche = 0) )
        OR (DATE(A.Liefertermin) $dateCompareOp '$filterD' AND DATE(A.Liefertermin) $dateCompareOpTo '$filterDTo')
        OR ( (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW)
             AND
             (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOpTo ($filterYTo * 100 + $filterWTo)
           )
    )
    -- OR
    -- (
    --     (UNIX_TIMESTAMP( AP.Liefertermin ) = 0 AND (AP.Lieferwoche IS NULL OR AP.Lieferwoche = 0) )
    --     OR (DATE(AP.Liefertermin) $dateCompareOp '$filterD')
    --     OR ( (AP.Lieferjahr*100+AP.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW) )
    -- )
)
EOT;
            } else {

            $filterSQL.= <<<EOT
-- filterTime: $filterTime
-- filterD: $filterD
-- filterY: $filterY
-- filterW: $filterW
-- chckMonat: $chckMonat
-- Terminfilter
-- Zeige Auftraege bzw. die Positionen enthalten die 
--   exakt fuer diesen Tag vorgesehen
--   oder vorher und noch nicht 100% gebucht sind
-- die fuer keinen Tag exakt vorgesehen, aber fuer diese Woche
--   oder vorher und noch nicht 100% gebucht sind
AND
(    
    (
        (UNIX_TIMESTAMP( A.Liefertermin ) = 0 AND (A.Lieferwoche IS NULL OR A.Lieferwoche = 0) )
        OR (DATE(A.Liefertermin) $dateCompareOp '$filterD')
        OR ( (A.Lieferjahr*100+A.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW) )
    )
    -- OR
    -- (
    --     (UNIX_TIMESTAMP( AP.Liefertermin ) = 0 AND (AP.Lieferwoche IS NULL OR AP.Lieferwoche = 0) )
    --     OR (DATE(AP.Liefertermin) $dateCompareOp '$filterD')
    --     OR ( (AP.Lieferjahr*100+AP.Lieferwoche) $dateCompareOp ($filterY * 100 + $filterW) )
    -- )
)
EOT;
            }
        }
            $filterSQL.= <<<EOT
GROUP BY A.Mandant, A.Auftragsnummer
EOT;
//        die( $filterSQL );
        return $filterSQL;
    }
}
