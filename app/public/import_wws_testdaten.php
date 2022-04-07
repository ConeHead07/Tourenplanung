<?php
set_time_limit(90);
error_reporting(E_ALL);
require 'test_index.php';
set_time_limit(90);
error_reporting(E_ALL);

$rgx_int = '/^-?[0-9]+$/';
$rgx_float_de = '/^(\d{1,3}(\.\d{3})+|\d+)(,\d+)?$/';
$rgx_float_db = '/^(\d{1,3}(,\d{3})+|\d+)(\.\d+)?$/';

// Um direkt auf die WWS-DB zuzugreifen und nicht auf die Tabellen-Kopien
// muss in der application.ini wws.db.useDefaultDb auf false gesetzt

$tablefiles = array(
    'AuftragsKoepfe' => APPLICATION_PATH . '/data/AuftragsKoepfe.txt',
    'AuftragsPositionen' => APPLICATION_PATH . '/data/AuftragsPositionen.txt',
    'BestellKoepfe' => APPLICATION_PATH . '/data/BestellKoepfe.txt',
    'Bestellpositionen' => APPLICATION_PATH . '/data/Bestellpositionen.txt',
    'Warenbewegungen' => APPLICATION_PATH . '/data/Warenbewegungen.txt'
);

/* @var $db Zend_Db_Adapter_Abstract */
$db = Zend_Registry::get('db');
/* @var $wwsdb Zend_Db_Adapter_Abstract */
$wwsdb = Zend_Registry::get('wwsdb');

class WwsTableAbstract extends Zend_Db_Table_Abstract {
    protected function _setupDatabaseAdapter() {
        $this->_db = Zend_Registry::get('wwsdb');   
    } 
}

class Auftragskoepfe extends WwsTableAbstract {
    protected $_name = 'Auftragskoepfe';
}
class Bestellkoepfe extends WwsTableAbstract {
    protected $_name = 'Bestellkoepfe';
}
class Bestellpositionen extends WwsTableAbstract {
    protected $_name = 'Bestellpositionen';
}
class Auftragspositionen extends WwsTableAbstract {
    protected $_name = 'Auftragspositionen';
}
class Warenbewegungen extends Zend_Db_Table_Abstract {
    protected $_name = 'Warenbewegungen';
}
Zend_Db_Table_Abstract::setDefaultAdapter($wwsdb);

$AK = new Auftragskoepfe();
$AP = new Auftragspositionen();
$BK = new Bestellkoepfe();
$BP = new Bestellpositionen();
$WB = new Warenbewegungen();

echo '<pre>' . PHP_EOL;

$select = $wwsdb->select()->from('Auftragskoepfe', 'COUNT(*) count')
->where('Mandant = 10')->where('Bearbeitungsstatus = ?',2)->where('AngelegtAm > DateAdd(d, 0, ?)', '2012-03-01T00:00:00');
echo $select->assemble() . PHP_EOL;
$row = $wwsdb->fetchAll($select);
$countANR = $row[0]['count'];
echo 'countANR: ' . $countANR . PHP_EOL;

$selectANR = $wwsdb->select()->from('Auftragskoepfe', 'Auftragsnummer')
->where('Mandant = 10')->where('Bearbeitungsstatus = ?',2)->where('AngelegtAm > DateAdd(d, 0, ?)', '2012-03-01T00:00:00')
->limit( min(100,$countANR) );
echo $selectANR->assemble() . PHP_EOL;


if ($NochNichtExportiert = 1) { // YES
    $select = $AK->select()->where('Mandant = 10')->where('Auftragsnummer IN ('.$selectANR->assemble().')'); // > DateAdd(d, 0, ?)', '2012-03-01T00:00:00')
    /* @var $stmt Zend_Db_Statement */
    $rows = $wwsdb->fetchAll( $select );
    file_put_contents($tablefiles['AuftragsKoepfe'], serialize($rows));
    echo 'Es wurden ' . count($rows). ' Zeilen (AK) aus dem WWS exportiert!' . PHP_EOL;
    echo $select->assemble() . PHP_EOL;
    unset($rows);
}

if ($NochNichtExportiert = 1) { // YES
    $select = $AP->select()->where('Mandant=?', 10)->where('Auftragsnummer in('.$selectANR->assemble().')');
    $rows = $wwsdb->fetchAll( $select );
    file_put_contents($tablefiles['AuftragsPositionen'], serialize($rows));
    echo 'Es wurden ' . count($rows). ' Zeilen (AP) aus dem WWS exportiert!' . PHP_EOL;
    echo $select->assemble();
    unset($rows);
}

if ($NochNichtExportiert = 1) { // YES
    $select = $BK->select()->where('Mandant=?', 10)->where('Auftragsnummer in('.$selectANR->assemble().')');
    $rows = $wwsdb->fetchAll( $select );
    file_put_contents($tablefiles['BestellKoepfe'], serialize($rows));
    echo 'Es wurden ' . count($rows). ' Zeilen (BK) aus dem WWS exportiert!' . PHP_EOL;
    echo $select->assemble();
    unset($rows);
}

if ($NochNichtExportiert = 1) { // YES
    $select = $BP->select()->where('Mandant=?', 10)->where('Auftragsnummer in('.$selectANR->assemble().')');
    $rows = $wwsdb->fetchAll( $select );
    file_put_contents($tablefiles['Bestellpositionen'], serialize($rows));
    echo 'Es wurden ' . count($rows). ' Zeilen (BP) aus dem WWS exportiert!' . PHP_EOL;
    echo $select->assemble();
    unset($rows);
}

if ($NochNichtExportiert = 1) { // YES
    $select = $WB->select()->where('Mandant=?', 10)->where('Auftragsnummer in('.$selectANR->assemble().')');
    $rows = $wwsdb->fetchAll( $select );
    file_put_contents($tablefiles['Warenbewegungen'], serialize($rows));
    echo 'Es wurden ' . count($rows). ' Zeilen (WB) aus dem WWS exportiert!' . PHP_EOL;
    echo $select->assemble();
    unset($rows);
}


//echo print_r(get_included_files(),1) . PHP_EOL;

if ($ShowWwsMeta = 0) {
    if (0) echo 'AK name: ' . print_r($AK->info(),1) . "<br>" . PHP_EOL;
    if (0) echo 'AP name: ' . print_r($AP->info(),1) . "<br>" . PHP_EOL;
    if (0) echo 'BK name: ' . print_r($BK->info(),1) . "<br>" . PHP_EOL;
    if (0) echo 'BP name: ' . print_r($BP->info(),1) . "<br>" . PHP_EOL;
    if (1) echo 'WB name: ' . print_r($WB->info(),1) . "<br>" . PHP_EOL;
}



if ($ImportTableFiles = 1) {
    echo '<pre>' . PHP_EOL;
    foreach($tablefiles as $_name => $_file) {
        $rows = unserialize(file_get_contents($_file));
        $fldConf = array();


        echo $_name . '<ol>';
        foreach($rows as $row) {
            echo '<li>';
            foreach($row as $_fld => $_val) {
                if (!isset($fldConf[$_fld]['max'])) $fldConf[$_fld]['max']   = 0;
                if (!isset($fldConf[$_fld]['typ'])) $fldConf[$_fld]['typ']   = '';
                if (!isset($fldConf[$_fld]['null'])) $fldConf[$_fld]['null'] = false;
                
                if (is_object($_val) && isset($_val->date)) {
                    $_val = $_val->date;
                    $fldConf[$_fld]['typ'] = (strlen($_val) > 10) ? 'datetime' : 'date';
                }
                elseif (is_scalar($_val)) {
                    $fldConf[$_fld]['max'] = (isset($fldConf[$_fld]['max'])) ? max($fldConf[$_fld]['max'], strlen($_val)) : strlen($_val);

                    if (!$fldConf[$_fld]['typ'] || $fldConf[$_fld]['typ'] == 'int') {
                        if (preg_match($rgx_int, $_val))
                            $fldConf[$_fld]['typ'] = 'int';
                        elseif (preg_match($rgx_float_db, $_val)) {
                            $fldConf[$_fld]['typ'] = 'float';
                        } elseif (strlen($_val)) {
                            $fldConf[$_fld]['typ'] = 'text';
                        }
                    } elseif ($fldConf[$_fld]['typ'] == 'float') {
                        if (!preg_match($rgx_float_db, $_val) && !preg_match($rgx_int, $_val)) {
                            $fldConf[$_fld]['typ'] = 'text';
                        }
                    }
                }

                if ($_val == '' || is_null($_val)) $fldConf[$_fld]['null'] = true;

                if (!is_scalar($_val) && !is_null($_val)) {
                    die('Debug val of '. $_fld . ': ' . Zend_Debug::dump($_val,1));
                }
            }
        }
        $i = 0;
        $cols = '';
        $insertColList = '';
        foreach($fldConf as $_fld => $_props) {
            if ($i) $cols.= ',' . PHP_EOL;
            if ($i) $insertColList.= ',';
            $insertColList.= $_fld;
            $cols.= '`'.$_fld.'` ';
            if (!isset($_props['typ'])) $_props['typ'] = 'text';
            switch($_props['typ']) {
                case 'int':
                    $cols.= 'int(11) ';
                    break;
                case 'float':
                    $cols.= 'float(9,2) ';
                    break;
                case 'date':
                case 'datetime':
                    $cols.= $_props['typ'].' ';
                    break;

                default:
                    if ($_props['max'] > 200) {
                        $cols.= 'text ';
                    } else {
                        $cols.= 'varchar('. ($_props['max']+20) . ') ';
                    }
            }
            $cols.= (!@empty($_props['null'])) ? 'NULL ' : 'NOT NULL ';
            $i++;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS '. $_name . ' (' . PHP_EOL
              .$cols
              .')';
        try {
            $db->query($sql);
        } catch(Zend_Db_Exception $e) {
            echo print_r($db->errorInfo(),1) . PHP_EOL;
            echo print_r($e,1). PHP_EOL;
            echo $sql . PHP_EOL;
        }
        echo "Successfull: ". $sql . PHP_EOL;

        $num_inserts = 0;
        foreach($rows as $row) {

            foreach($row as $_fld => $_val) {
                if (is_object($_val) && isset($_val->date)) {
                    $row[$_fld] = $_val->date;
                }
                $row[$_fld] = $db->quote($row[$_fld]);
            }
            $sql = 'INSERT INTO ' . $_name . '(' . implode(',', array_keys($row)) . ')';
            $sql.= ' VALUES (' . implode(',', array_values($row)) . ')';

            $dbh = mysql_connect('localhost', 'root', '');
            $dbuser = mysql_select_db('mt_rm');
            mysql_query($sql, $dbh);
            $num_inserts++;
    //        $db->execute($sql);
    //        $db->query($sql);
        }
        echo "Successfull: Inserted ".$num_inserts." ".$_name."-Data!" . PHP_EOL;
        unset($rows);
    }
}
