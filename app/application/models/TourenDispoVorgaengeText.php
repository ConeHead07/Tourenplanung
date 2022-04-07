<?php

/**
 * Description of User
 * @author rybka
 */
class Model_TourenDispoVorgaengeText extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'tourenDispoVorgaengeText';
    
    protected $_storage = null;
    protected $_db = null;
    protected $_tbl = null;
    
    
    public function __construct() {
        /* @var $this->_storage Model_Db_TourenDispoVorgaenge */
        $this->_storage = $this->getStorage();
        
        /* @var $this->_db Zend_Db_Adapter_Abstract */        
        $this->_db = $this->_storage->getAdapter();
        
        /* @var $this->_tbl string */
        $this->_tbl = $this->_storage->info(Zend_Db_Table::NAME);
    }
    
    public function getBemerkungen($tour_id)
    {
        $db = $this->_db;
        $storage = $this->_storage;
        $storageDV = MyProject_Model_Database::loadStorage("tourenDispoVorgaenge");
        
        $select = $db->select();
        $select->from( array('T'=>$storageDV->info(Zend_Db_Table::NAME)), 'tour_id' );
        $select->joinLeft(
                array( 'TX' => $storage->info(Zend_Db_Table::NAME)), 
                'T.tour_id = TX.tour_id',
                array('bemerkung', new Zend_Db_Expr('TX.tour_id txtid') ) );
        $select->where('T.tour_id = :tour_id');

        return $db->fetchRow($select, array( ':tour_id' => $tour_id) );
    }
    
    public function getPrintBemerkungen($tour_id)
    {
        $row =  $this->getBemerkungen($tour_id);
        $xml = $row['bemerkung'];
        
        
        if ( strtolower(substr( $xml, 0, 6) != '<?xml '))
        $xml_str = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL
        .'<entries>' . PHP_EOL
        .$row['bemerkung']
        .'</entries>' . PHP_EOL
        ;
        else $xml_str = $xml;
                
        /* @var $xml SimpleXMLElement */
        $xml = simplexml_load_string( $xml_str );
        
        $bemerkungen = '';
        
        $result = $xml->xpath('//div[@class="entry" and @print="1"]');
        while(list( , $node) = each($result)) {
            $bemerkungen.= $node->asXML();
        }
        return $bemerkungen;
    }
    
    public function updatePrintFlag($tour_id, $data) 
    {
        $row =  $this->getBemerkungen($tour_id);
        $xml = $row['bemerkung'];
        
        if ( strtolower(substr( $xml, 0, 6) != '<?xml '))
        $xml_str = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL
        .'<entries>' . PHP_EOL
        .$row['bemerkung']
        .'</entries>' . PHP_EOL
        ;
        else $xml_str = $xml;
                
        /* @var $xml SimpleXMLElement */
        $xml = simplexml_load_string( $xml_str );
        
//        echo '#'.__LINE__ . ' data:' . print_r($data,1) . PHP_EOL;
        
        foreach($xml->div as $_div) {
            $_attr = $_div[0]->attributes();
            $_div[0]['test1'] = 'one';
            $_div['test2'] = 'two';
            
            $_id = (!empty($_attr['id'])) ? (string)$_attr['id'] : '';
            
            $printFlag = 0;
            if ( $_id 
                 && array_key_exists($_id, $data) 
                 && array_key_exists('print', $data[$_id])
                 && $data[$_id]['print']) {                
                $printFlag = 1;       
            }
            $_div[0][ 'print' ] = $printFlag;
        }
        $row['bemerkung'] = $xml->asXML();
        $this->update($row, $row['txtid']);
        die($xml->asXML());
    }
    
    public function updateAttributes($tour_id, $data)
    {
        $row =  $this->getBemerkungen($tour_id);
        $xml = $row['bemerkung'];
        
        $xml_str = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL
        .'<entries>' . PHP_EOL
        .$row['bemerkung']
        .'</entries>' . PHP_EOL
        ;
//        echo '<pre>#' . __LINE__ . ' ' . __METHOD__ . ' ' . htmlentities( $xml_str ) . PHP_EOL;
        
        /* @var $xml SimpleXMLElement */
        $xml = simplexml_load_string( $xml_str );
        
        foreach($xml->div as $_div) {
            $_attr = $_div[0]->attributes();
            $_div[0]['test1'] = 'one';
            $_div['test2'] = 'two';
            
            $_id = (!empty($_attr['id'])) ? (string)$_attr['id'] : '';
            
            if ( $_id && array_key_exists($_id, $data) ) {                
                foreach( $data[ $_id ] as $_k => $_v) {
                    $_div[0][ $_k ] = $_v;
                }             
            }
        }
        $row['bemerkung'] = $xml->asXML();
        $this->update($data, $row['txtid']);
//        $xml = new SimpleXMLElement();
//        die('#'.__LINE__. ' newXML: ' . $xml->asXML());
    }
    
    /**
     *
     * @param type $tour_id
     * @param type $bemerkung
     * @return type bool
     */
    public function saveBemerkung($tour_id, $bemerkung, $attribs = array())
    {
        
        if (!$bemerkung) return true;
        
        $db = $this->_db;
        $storage = $this->_storage;
        $storageDV = MyProject_Model_Database::loadStorage("tourenDispoVorgaenge");
        
        $attribs = array_merge(
                array( 'print' => 1),
                (array) $attribs
        );
        
        $uname = MyProject_Auth_Adapter::getUserName();
        $str_attribs = '';
        foreach($attribs as $k => $v) {
            $str_attribs.= ' ' . $k . '="'.$v.'"';
        }
        
        $entryTime = date("Y-m-d H:i:s");
        $entryId = md5($uname . $entryTime);
        
        $entry = '<div class="entry" id="' . $entryId . '"'.$str_attribs.'>'
                .'<div class="bemerkung-meta"><span class="user">'.$uname . '</span>, '
                .'<span class="datetime">' . $entryTime . '</span></div>'
                .'<div class="bemerkung">' . $bemerkung . '</div>'
                .'</div>';
        
        $row = $this->getBemerkungen($tour_id);
        $bmkg = $row['bemerkung'];
        
        $p1 = strpos($row['bemerkung'], '<entries>');
        $p2 = (false !== $p1) ? strrpos($row['bemerkung'], '</entries>', $p1+9) : false;
        if (is_int($p1) && is_int($p2)) {
            $bmkg = substr($row['bemerkung'], $p1+9, $p2-($p1+9));
        }
        
        $data = array(
            "tour_id" => $row["tour_id"],
            "bemerkung" => '<?xml version="1.0" encoding="UTF-8"?>'
                         . '<entries>'
                         . $bmkg . PHP_EOL . $entry
                         . '</entries>'
        );
        
        if (!$row['txtid']) {
            $this->insert($data);
        } else {
            $this->update($data, $row['txtid']);
        }
        return true;
    }
}
