<?php

// DB-Field-Comment: Bearbeitungsstauts
// 0=erfasst,
// 1=angeboten,
// 2=bestätigt,
// 3=teilgeliefert,
// 4=geliefert,
// 5=teilberechnet,
// 9=erledigt, (ENUM=AuftragsBearbeitungsStatus), 7,8,20 für Anbindung woodProcess

// DB-Field-Comment: UnterBearbeitung
// ergänzender Unter-Bearbeitungsstatus (enum=UnterBearbeitungsStatusId)
// Siehe Tabelle UnterbearbeitungsStatus


// DB-Field-Comment: AbschlussStatus
// 0-offen (undefiniert), 1-gewonnen, 2-verloren (enum=AbschlussStatus)

// Angebotsstatus 
// [Bearbeitungsstatus] => 1
// [AbschlussStatus] => 0
// Bestätigt: 
// [Bearbeitungsstatus] => 2
// [AbschlussStatus] => 0
// Geliefert: 
// [Bearbeitungsstatus] => 4
// [AbschlussStatus] => 0
// Teilberechnet: 
// [Bearbeitungsstatus] => 5
// [AbschlussStatus] => 0
// Erledigt: 
// [Bearbeitungsstatus] => 9
// [AbschlussStatus] => 1

$aErrno2Txt[0] = "MS-SQLSRV-Connector wurde geladen!";
$aErrno2Txt[1] = "MS-SQLSRV-Connector wurde nicht geladen: die Funktion dl() zum Nachladen ist deaktiviert!";
$aErrno2Txt[2] = "MS-SQLSRV-Connector wurde nicht geladen: die Erweiterung php_sqlsrv wurde nicht gefunden!";
$enableNotice = "Aktivieren Sie die Erweiterung extension=php_sqlsrv_54_ts.dll in xampp/php/php.ini indem Sie das Semikolon am Zeilenanfang entfernen. Danach muss der Webserver neu gestartet werden.";

class WWW_DB {

    public $connid;
    protected $Servername = "10.30.2.110";
    protected $Benutzername = "CO6_RO"; // string 
    protected $Passwort = 'q6T7Ag.@Z'; // string
    protected $NeueVerbindung = true; // bool
    protected $DB = "scoffice6";

    function __construct() {
        if (!$this->check_mssql($errno)) {
            throw new Exception($errno);
        }

        $connectionInfo = array(
            "Database" => $this->DB,
            "UID" => $this->Benutzername,
            "PWD" => $this->Passwort);

        $this->connid = @sqlsrv_connect(
                        $this->Servername, $connectionInfo);
        // echo "#".__LINE__." MS_Conn: $this->connid <br>\n";

        $this->Result = @sqlsrv_query($this->connid, "USE " . $this->DB);
    }

    public function fetchRow($sql, $params = array()) {
        $stmt = sqlsrv_query(
                $this->connid, $sql, $params, array(
            'scrollable' => SQLSRV_CURSOR_STATIC)
        );
        if ($stmt) {

            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            echo print_r(sqlsrv_errors(), 1) . PHP_EOL;
        }
        return null;
    }

    static function check_sqlsrv(&$errno) {
        if (function_exists("sqlsrv_connect"))
            return true;


        if (!function_exists("dl")) {
            $errno = 1;
            return false;
        }

        if (!dl("php_sqlsrv_54_ts.dll")) {
            $errno = 2;
            return false;
        }
        return true;
    }

    static function check_mssql(&$errno) {
        return self::check_sqlsrv($errno);

        if (function_exists("sqlsrv_connect"))
            return true;


        if (!function_exists("dl")) {
            $errno = 1;
            return false;
        }

        if (!dl("php_mssql.dll") && !dl("php_mssql.so")) {
            $errno = 2;
            return false;
        }
        return true;
    }

    function __destruct() {
        if (is_resource($this->connid))
            sqlsrv_close($this->connid);
    }

    function get_RowById($WwsId, $mid = 0) {
        $row = array();

        if (!is_resource($this->connid))
            return $row;
        if (!function_exists("sqlsrv_connect") && !@dl("php_mssql.dll")) {
            return false;
        }

        $mandant = ($mid > 1) ? $mid : "";

        $int1 = $WwsId; // "192161";
        // [Bearbeitungsstatus] 
        // [UnterBearbeitungsstatus]
        // [AbschlussStatus]
        $Query = "SELECT 
                    A.Bearbeitungsstatus,
                    A.UnterBearbeitungsstatus,
                    A.AbschlussStatus,,
                    A.Auftragswert,
                    A.Auftragsnummer as vorgangsnr,
                    A.Vorgangstitel as Vorgangstitel,
                    A.RechnungName as firmenname,
                    A.RechnungPostleitzahl as firmenplz,
                    A.RechnungOrt as firmenort,
                    A.RechnungPostleitzahl + ' ' + A.RechnungOrt as firmenplzort,
                    A.RechnungStrassePostfach as firmenstr,
                    A.AngelegtDurch as verkaeufer
		  FROM  AuftragsKoepfe AS A
		  WHERE A.Auftragsnummer=" . (int)$int1;
if ($mandant) $Query.= " AND A.Mandant = " . (int)$mandant;

        // WHERE A.Bearbeitungsstatus!=\"9\"";

        $this->Result = sqlsrv_query($this->connid, $Query, array(), array('scrollable' => SQLSRV_CURSOR_STATIC));

        if ($this->Result) {
            $n = sqlsrv_num_rows($this->Result);
            if ($n) {
                $row = sqlsrv_fetch_array($this->Result, SQLSRV_FETCH_ASSOC);
                foreach($row as $k => $v) {
                    if (is_object($v)) {
                        if ($v instanceof DateTime) {
                            $row[$k] = $v->format('Y-m-d H:i:s');
                        }
                    }
                }
            }
            // sqlsrv_free_result($this->Result);
        } else {
            die('#' . __LINE__ . ' ' . __FILE__ . PHP_EOL . __METHOD__ . PHP_EOL . print_r(sqlsrv_errors(), 1) . PHP_EOL . $Query);
        }

        return $row;
    }

    function get_RowsById($WwsId, $mid = 0, $AllFlds = false) {
        $row = array();

        if (!is_resource($this->connid))
            return $row;
        if (!function_exists("sqlsrv_connect") && !@dl("php_mssql.dll")) {
            return false;
        }

        $rows = array();
        $mandant = ($mid > 1) ? $mid : "";
        if (func_num_args() == 2) {
		$AllFlds = ($mid < 2) ? $mid : 0;
	   }
        $int1 = $WwsId; // "192161";
        // [Bearbeitungsstatus] 
        // [UnterBearbeitungsstatus]
        // [AbschlussStatus]
        if (!$AllFlds) {
            $Query = "SELECT 
				A.Bearbeitungsstatus,
				A.UnterBearbeitungsstatus,
				A.AbschlussStatus,
                                A.Auftragswert,
				A.Auftragsnummer as vorgangsnr,
				A.Vorgangstitel as Vorgangstitel,
				A.RechnungName as firmenname,
				A.RechnungPostleitzahl as firmenplz,
				A.RechnungOrt as firmenort,
				A.RechnungPostleitzahl + ' ' + A.RechnungOrt as firmenplzort,
				A.RechnungStrassePostfach as firmenstr,
                                A.AngelegtDurch as verkaeufer";
        } else {
            $Query = "SELECT * ";
        }
        $Query.= PHP_EOL 
              . " FROM  AuftragsKoepfe AS A " . PHP_EOL
              . " WHERE A.Auftragsnummer=" . (int)$int1 . "";
if ($mandant) $Query.= " AND A.Mandant = " . (int)$mandant;

        $this->Result = sqlsrv_query($this->connid, $Query, array(), array('scrollable' => SQLSRV_CURSOR_STATIC));
        // echo "#".__LINE__." MS_Result: $this->Result <br>\n";

        if ($this->Result) {
            $n = sqlsrv_num_rows($this->Result);
            if ($n) {
                for ($i = 0; $i < $n; $i++) {
                    $row = sqlsrv_fetch_array($this->Result, SQLSRV_FETCH_ASSOC);
                    foreach($row as $k => $v) {
                        if (is_object($v)) {
                            if ($v instanceof DateTime) {
                                $row[$k] = $v->format('Y-m-d H:i:s');
                            }
                        }
                    }
                    $rows[] = $row;
                }
                // echo "#".__LINE__." row: ".print_r($row, true)."<br>\n";
            }
            // sqlsrv_free_result($this->Result);
        } else {
            die('#' . __LINE__ . ' ' . __FILE__ . PHP_EOL . __METHOD__ . PHP_EOL . print_r(sqlsrv_errors(), 1) . PHP_EOL . $Query);
        }


        return $rows;
    }

    function get_projectsStatus($aWwsIds) {

        if (!function_exists("sqlsrv_connect") && !@dl("php_mssql.dll")) {
            return false;
        }

        if (!is_array($aWwsIds) || !count($aWwsIds)) {
            return false;
        }
        // [Bearbeitungsstatus] 
        // [UnterBearbeitungsstatus]
        // [AbschlussStatus]
        $Query = "SELECT 
				A.Bearbeitungsstatus,
				A.AbschlussStatus,
				A.Auftragsnummer as vorgangsnr,
				A.Auftragsnummer,
				A.Mandant
		  		FROM  AuftragsKoepfe AS A
		        WHERE \n";

        for ($i = 0; $i < count($aWwsIds); $i++) {
            $Query.= ($i ? "\n OR " : "") . " A.Auftragsnummer=" . ((int)$aWwsIds[$i]);
        }

        $this->Result = sqlsrv_query($this->connid, $Query, array(), array('scrollable' => SQLSRV_CURSOR_STATIC));
        
        $rows = array();
        if ($this->Result) {
            $n = sqlsrv_num_rows($this->Result);
            if ($n) {
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = sqlsrv_fetch_array($this->Result, SQLSRV_FETCH_ASSOC);
                }
            }
        } else {
            die('#' . __LINE__ . ' ' . __FILE__ . PHP_EOL . __METHOD__ . PHP_EOL . print_r(sqlsrv_errors(), 1) . PHP_EOL . 'Query: ' . $Query);
        }

        return $rows;
    }

}

if (basename(__FILE__) == basename($_SERVER["PHP_SELF"])) {
    $wws = new WWW_DB();
    echo "#" . __LINE__ . " Angebotsstatus <pre>" . print_r($wws->get_RowById("193401"), true) . "</pre>\n";

    $wws_cache_file = dirname(__FILE__) . "/../cache/lastsync.cache.phs";
    $aWwsIds = array("193401", "193155", "190590", "186327", "191254");
    $aWwsStatien = $wws->get_projectsStatus($aWwsIds);
    file_put_contents($wws_cache_file, serialize($aWwsStatien));
    echo "#" . __LINE__ . " Angebotsstatus <pre>" . print_r($aWwsStatien, true) . "</pre>\n";
}
// $wws->get_projectsStatus($aWwsIds);