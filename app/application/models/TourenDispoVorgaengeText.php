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
                array('bemerkung',  'bemerkung_json', new Zend_Db_Expr('TX.tour_id txtid') ) );
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

    public function updateBemerkungen(int $tour_id, array $aProperties = [])
    {
        $row =  $this->getBemerkungen($tour_id);
        $aList = json_decode($row['bemerkung_json'] ?: '[]', true);
        $iFoundChanges = 0;

//        print_r($aProperties);
//        exit;

        $iListSize = count($aList);
        for($i = 0; $i < $iListSize; $i++) {

            $_itm = &$aList[ $i ];
            $_id = $_itm[ 'id'];

            if (empty($_upd = $aProperties[ $_id ] ?? false)) {
                continue;
            }

            foreach($_upd as $_k => $_v) {

                if ($_k === "remove") {
                    $_k = 'removed';
                }

                if (!isset($_itm[$_k]) || $_itm[ $_k ] != $_v) {


                    $_itm[ $_k ] = $_v;
                    ++$iFoundChanges;
                }
            }
        }
        $sXmlBmkg = $this->bemerkungenToXml( $aList );

        if ($iFoundChanges > 0) {
            $this->update([
                'bemerkung' => $sXmlBmkg,
                'bemerkung_json' => json_encode( $aList, JSON_PRETTY_PRINT)
            ], $tour_id);
            return true;
        }

        return false;

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

    public function bemerkungenconvertAll() {
        $db = $this->_db;

        $sql = 'SELECT tour_id, bemerkung '
            . ' FROM ' . $this->_tbl
            . ' WHERE bemerkung_json IS NULL OR bemerkung_json = "[]" ORDER BY modified DESC'
            . ' LIMIT 1000';

        $rows = $db->fetchAll($sql, [], Zend_Db::FETCH_ASSOC);

        $iUpdated = 0;

        foreach($rows as $_row) {

            $stmt = $db->query(
                'UPDATE ' . $this->_tbl . ' SET bemerkung_json = :json WHERE tour_id = :tour_id LIMIT 1',
                [
                    'json' => $this->bemerkungToJson($_row['bemerkung']),
                    'tour_id' => $_row['tour_id']
                ]
            );

            $iUpdated+= $stmt->rowCount();

        }

        return [
            'iNumRows' => count($rows),
            'iUpdated' => $iUpdated,
        ];
    }

    public function xmlBemerkungToList(string $xmlString): array {

        $aEntries = [];
        if (preg_match_all(
            '#<div class="entry".*?</div>\s*</div>#s',
            $xmlString, $m)) {

            $iNumMatches = count($m[0]);
            for($i = 0; $i < $iNumMatches; $i++ ) {
                $entity = $m[0][$i];

                $e = [];
                if (preg_match('#id="(.*?)"#s', $entity, $m2)) {
                    $e['id'] = $m2[1];
                }
                if (preg_match('#print="(.*?)"#s', $entity, $m2)) {
                    $e['print'] = $m2[1];
                }
                if (preg_match('#remove="(.*?)"#s', $entity, $m2)) {
                    $e['remove'] = $m2[1];
                }
                if (preg_match('#<span class="user">(.*?)</span>#s', $entity, $m2)) {
                    $e['user'] = $m2[1];
                }
                if (preg_match('#<span class="datetime">(.*?)</span>#s', $entity, $m2)) {
                    $e['datetime'] = $m2[1];
                }
                if (preg_match('#<div class="bemerkung">(.*?)</div>#s', $entity, $m2)) {
                    $e['bemerkung'] = $m2[1];
                }
                $aEntries[] = $e;
            }
            return $aEntries;
        }


    }


    public function bemerkungtoJson(string $xmlString)
    {

        $aBList = $this->xmlBemerkungToList($xmlString);
        return json_encode($aBList, JSON_PRETTY_PRINT);

        $arr = $this->bemerkungToArray($xmlString);

        $aEntries = [];
        $iChildren = count($arr['children']);
        for($i = 0; $i < $iChildren; $i++) {
            $_n = $arr['children'][$i];
            $_e =  [];
            $_e['id'] = $_n['id'];
            $_e['print'] = $_n['print'];
            $iChildren2 = count($_n['children']);
            for($i2 = 0; $i2 < $iChildren2; $i2++) {

                $_n2 = $_n['children'][$i2];

                switch($_n2['class']) {
                    case 'bemerkung-meta':
                        $iChildren3 = count($_n2['children']);
                        for($i3 = 0; $i3 < $iChildren3; $i3++) {
                            $_n3 = $_n2['children'][$i3];
                            $_key = $_n3['class'];
                            $_e[ $_key ] = $_n3['value'];
                        }
                        break;

                    case 'bemerkung':
                        $_e['bemerkung'] = $_n2['value'];
                        break;
                }
            }
            $aEntries[] = $_e;
        }

        return json_encode($aEntries, JSON_PRETTY_PRINT);
    }

    public function bemerkungToArray(string $xmlString)
    {
        $xmlParser = xml_parser_create();
        $isValid = xml_parse($xmlParser, $xmlString);
        if (!$isValid) {
            $xErrCode = xml_get_error_code( $xmlParser );
            $xErrMsg = xml_error_string($xErrCode);
            $xErrLine = xml_get_current_line_number($xmlParser);
            $xErrCol = xml_get_current_column_number($xmlParser);
            return [
                'error_code' => $xErrCode,
                'error_message' =>$xErrMsg,
                'error_line' => $xErrLine,
                'error_col' => $xErrCol,
                'XML' => $xmlString,
            ];

            MyProject_Response_Json::send([
                'error_code' => $xErrCode,
                'error_message' =>$xErrMsg,
                'error_line' => $xErrLine,
                'error_col' => $xErrCol,
                'XML' => $xmlString,
            ]);
        }

        try {
            /** @var SimpleXMLElement $xml */
            $xml = simplexml_load_string($xmlString);
        } catch(Exception $e) {
            echo __LINE__;
            exit;
            MyProject_Response_Json::send([
                'ExceptionStack' => $e->getTraceAsString(),
                'ExceptionMsg' => $e->getMessage(),
                'xml' => $xmlString,
            ]);
        }

        if (!$xml) {
            echo '<pre>' . htmlentities($xmlString) . '</pre>';
            return [];
        }

        $nodeToArray = function(SimpleXMLElement $node) use(&$nodeToArray) {

            $a = [];
            $attributes = [];
            $children = [];
            $a['tag'] = $node->getName();
            $class = '';
            $id = '';
            $value = '';

            foreach($node->attributes() as $k => $v) {
                switch($k) {
                    case 'class':
                        $class = (string)$v;
                        break;
                    case 'id':
                        $id = (string)$v;
                        break;
                    case 'attribute':
                    case 'children':
                    case 'value':
                        $attributes[ $k ] = (string)$v;
                        break;

                    default:
                        $a[ $k ] = (string)$v;
                }
            }

            if ($node->count()) {
                foreach($node->children() as $child) {
                    $children[] = $nodeToArray($child);
                }
            } else {
                $value = (string)$node;
            }

            if ($id) {
                $a['id'] = $id;
            }

            if ($class) {
                $a['class'] = $class;
            }

            if (count($children)) {
                $a['children'] = $children;
            }

            if (count($attributes)) {
                $a['attribute'] = $attributes;
            }

            if ($value) {
                $a['value'] = $value;
            }

            return $a;
        };



        return $nodeToArray( $xml );
        return json_encode($nodeToArray( $xml ), JSON_PRETTY_PRINT);
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

    public function bemerkungenToXml(array $aBemerkungen): string
    {
        $sXmlBmkg = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sXmlBmkg.= "<entries>\n";
        foreach($aBemerkungen as $_b) {
            if (!empty($_b['remove']) || !empty($_b['removed'])) {
                continue;
            }

            $sXmlBmkg.= '
                <div class="entry" id="'. $_b['id'] ?? '' . '" print="' . $_b['print'] ?? 1 . '">
                <div class="bemerkung-meta">
                <span class="user">' . $_b['user'] ?? '' . '</span>, 
                <span class="datetime">' . $_b['datetime'] ?? '' . '</span>
                </div>
                <div class="bemerkung">' . $_b['bemerkung'] ?? '' . '</div>
                </div>' . "\n";
        }
        $sXmlBmkg.= "</entries>\n";

        return $sXmlBmkg;
    }

    public function saveBemerkungJson($tour_id, $bemerkung, $attribs = array())
    {

        if (!$bemerkung) return true;

        $attribs = array_merge(
            array( 'print' => 1, 'removed' => 0),
            (array) $attribs
        );

        $userName = MyProject_Auth_Adapter::getUserName();

        $entryTime = date("Y-m-d H:i:s");
        $entryId = md5($userName . $entryTime);

        $aEntry = [
            'id' => $entryId,
            'print' => $attribs['print'],
            'removed' => $attribs['removed'],
            'user' => $userName,
            'datetime' => $entryTime,
            'bemerkung' => $bemerkung,
        ];

        $row = $this->getBemerkungen($tour_id);
        $bmkg = (array)json_decode($row['bemerkung_json'], true);
        $bmkg[] = $aEntry;

        $sXmlBmkg = $this->bemerkungenToXml($bmkg);

        $data = array(
            "tour_id" => $row["tour_id"],
            'bemerkung' => $sXmlBmkg,
            'bemerkung_json' => json_encode($bmkg),
        );

        if (!$row['txtid']) {
            $this->insert($data);
        } else {
            $this->update($data, $row['txtid']);
        }
        return true;
    }
}
