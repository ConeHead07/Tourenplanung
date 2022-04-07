<?php 
require_once __DIR__ . '/../library/MyProject/Db/Sqlsrv.php';


$dbParams = array(
    'host'     => '10.30.2.110',
    'dbname'   => 'scoffice6', 
    'username' => 'CO6_RO', 
    'password' => 'q6T7Ag.@Z',
);

require_once __DIR__ . '/../library/MyProject/Db/Sqlsrv.php';

function rowsToTbl(&$rows ) 
{
    $reTable = "<table border=1 cellspacing=0 cellpadding=1>\n";
    if (count($rows)) {
        $colNames = array();
        foreach($rows[0] as $k => $v) $colNames[] = $k;
        $reTable.= "<thead><tr><td width=20>#</td><td>".implode("&nbsp;</td><td>", $colNames)."</td></tr></thead>\n";

        $reTable.= "<tbody>\n";
        for($i = 0; $i < count($rows); $i++) {
				foreach($rows[$i] as $k => $v) {
					if ($v instanceof DateTime) {
						$rows[$i][$k] = $v->format('Y-m-d H:i:s');
					}
				}
                $reTable.= "<tr><td align=right>".($i+1)."</td><td>".implode("&nbsp;</td><td>", $rows[$i])."&nbsp;</td></tr>\n";
        }
        $reTable.= "</tbody>\n";
    } else {
        $reTable.= "Keine Daten!<br>\n";
    }
    $reTable.= "</table>\n";
    return $reTable;
}

function sqlToTable($SQL, $wws) {
    $aRows = $wws->get_RowsBySql($SQL );
    $htmlTable = '<a href="#" onclick="(function(){var o = document.getElementById(\'SqlLog\').style; o.display = (o.display == \'none\' ? \'block\' : \'none\');})();">toggle SQL</a> (<pre id="SqlLog" style="display:none">' . $SQL . '</pre>' . PHP_EOL;
    $htmlTable.= rowsToTbl( $aRows );
    return $htmlTable;
}

$SQL = (isset($_REQUEST['SQL']) ? stripslashes($_REQUEST['SQL']) : '');
?>
<form>
Query<br/>
<textarea name="SQL" style="width:100%;min-height:150px;"><?php echo htmlentities($SQL); ?></textarea><br/>
<input type="submit" value="Abfrage senden" />
</form>
<?php

if  ($SQL) {
    try {
        $wws = MyProject_Db_Sqlsrv::factory($dbParams); //::getInstance($dbParams['host'], $dbParams['user'], $dbParams['pwd'], $dbParams['dbname']);
        $wws->setFetchMode(SQLSRV::FETCH_ASSOC);
        $wws->setScrollableCursor(SQLSRV_CURSOR_KEYSET);
        $rows = $wws->fetchAll($SQL, array('name' => 'Assm%'));
        echo rowsToTbl( $rows );
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

?>