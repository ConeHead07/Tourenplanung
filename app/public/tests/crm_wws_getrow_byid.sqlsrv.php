<?php

//die('#'.__LINE__ . ' ' . __FILE__);
require_once(dirname(__FILE__) . "/class/wwssync.sqlsrv.class.php");
//die('#'.__LINE__ . ' ' . __FILE__);
WWW_DB::check_mssql($errno);

//$w = new WWW_DB;
//die( print_r($w->fetchRow('SELECT * FROM AuftragsKoepfe'),1));
//die('#'.__LINE__ . ' ' . __FILE__ . ' errno: ' . $errno);
$TplAjaxXmlResult = "<" . "?xml version=\"1.0\" encoding=\"UTF-8\" ?" . ">\n";
$TplAjaxXmlResult.= "<result type=\"{ResultType}\">\n";
$TplAjaxXmlResult.= "	<Msg/>\n";
$TplAjaxXmlResult.= "	<Err/>\n";
$TplAjaxXmlResult.= "	<JScript/>\n";
$TplAjaxXmlResult.= "	<MoreFields/>\n";
$TplAjaxXmlResult.= "</result>\n";
$aXmlValues["ResultType"] = "error";

$aFitTxt4Xml = array(
    "&" => "&#" . ord("&") . ";",
    "\r" => "&#" . ord("\r") . ";",
    "\n" => "&#" . ord("\n") . ";",
    "\"" => "&#" . ord("\"") . ";",
    "<" => "&#" . ord("<") . ";",
    ">" => "&#" . ord(">") . ";"
);

// echo "ord(LEERZEICHEN):".ord(" ")."<br>\n";
function encodeXmlTxt($XmlText) {
    global $aFitTxt4Xml;
    $XmlText = decodeXmlTxt($XmlText);

    $newXmlText = "";
    for ($i = 0; $i < strlen($XmlText); $i++) {
        $encChr = $XmlText[$i];
        if (ord($XmlText[$i]) < 33) { // || ord($XmlText[$i]) > 125) {
            switch (ord($XmlText[$i])) {
                case 130: // Komma ,
                case 32:  // Leerzeichen
                case 13:  // Zeilenumbruch
                case 10:  // Zeilenumbruch
                    break;

                default:
                    $encChr = "&#" . ord($XmlText[$i]) . ";";
            }
            //echo "#".__LINE__." $i:".ord($XmlText[$i]).":".chr(ord($XmlText[$i])).":".htmlentities($encChr)."<br>\n";
        } else {
            $encChr = strtr($XmlText[$i], $aFitTxt4Xml);
            //echo "#".__LINE__." $i:".ord($XmlText[$i]).":".chr(ord($XmlText[$i])).":".htmlentities($encChr)."<br>\n";
        }

        $newXmlText.= $encChr;
    }
    return $newXmlText;
}

function decodeXmlTxt($XmlText) {
    global $aFitTxt4Xml;
    $XmlText = strtr($XmlText, array_flip($aFitTxt4Xml));
    $p = strpos($XmlText, "&#");
    $i = 0;
    $aRpl = array();
    while (is_int($p)) {
        if ($i++ > 2550)
            break;
        $p2 = strpos($XmlText, ";", $p);
        if (is_int($p2)) {
            $start = $p + 2;
            $length = $p2 - $start;
            $ord = substr($XmlText, $start, $length);
            if (strval(intval($ord)) === $ord) {
                $aRpl["&#" . $ord . ";"] = chr($ord);
            }
            $p = strpos($XmlText, "&#", $p2);
            continue;
        }
        break;
    }
    return strtr($XmlText, $aRpl);
}

//die('#'.__LINE__ . ' ' . __FILE__ . ' errno: ' . $errno);
$wid = (!empty($_GET["wid"])) ? $_GET["wid"] : "";
$wid_int = strval(intval($wid));
if ($wid_int == $wid && strlen($wid) <= 7 && strlen($wid) >= 5) {
    if (!$errno) {
        if ($wid) {
            //die('#'.__LINE__ . ' ' . __FILE__ . ' wid: ' . $wid);
            try {
                $wws = new WWW_DB();
            } catch (Exception $e) {
                die('#' . __LINE__ . ' ' . __FILE__ . ' exception: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
            if ($wws && $wws->connid) {
                $aRows = $wws->get_RowsById($wid, (isset($_GET["AllFields"])));
                if (count($aRows)) {
                    // echo "#".__LINE__." Angebotsstatus <pre>".print_r($aData,true)."</pre>\n";
                    $aXmlValues["MoreFields"] = "";
                    for ($i = 0; $i < count($aRows); $i++) {
                        $aXmlValues["MoreFields"].= '<Row nr="' . $i . '">' . PHP_EOL;
                        foreach ($aRows[$i] as $k => $v) {
                            if (is_object($v)) {
                                /* @var $v DateTime */
                                if ($v instanceof DateTime) {
                                    $v = $v->format('Y-m-d H:i:s');
                                }
                            }
                            
                            if (!is_null($v) && !is_scalar($v)) {
                                //echo '#' . __LINE__ . ' value of field ' . $k . ' is not a scalar: ' . gettype($v). ' ' . print_r($v, 1) . ' cast toString: ' . strval($v) . '<br/>' . PHP_EOL;
                                $v = print_r($v,1);
                            }
                            $aXmlValues["MoreFields"].= "\t<" . $k . "><![CDATA[" . encodeXmlTxt($v) . "]]></" . $k . ">\n";
                        }
                        $aXmlValues["MoreFields"].= '</Row>' . PHP_EOL;
                    }
                    $aXmlValues["ResultType"] = "success";
                } else {
                    $aXmlValues["Err"] = "Es wurde kein Datensatz mit der Vorgangsnr gefunden! " . print_r(sqlsrv_errors(), 1);
                }
            } else {
                $aXmlValues["Err"] = "Es konnte keine DB-Verbindung hergestellt werden!";
            }
        } else {
            $aXmlValues["Err"] = "Fehlende WWS-ID für Anfrage";
        }
    } else {
        $aXmlValues["Err"] = $aErrno2Txt[$errno] . " " . $enableNotice;
    }
} else {
    $aXmlValues["Err"] = "Ungültige oder fehlende WWS-ID (5-6 Numerische Zeichen): " . intval($wid) . ":" . $wid;
}

$AjaxXmlResult = $TplAjaxXmlResult;
foreach ($aXmlValues as $k => $v) {
    switch ($k) {
        case "ResultType":
            $AjaxXmlResult = str_replace("{" . $k . "}", $v, $AjaxXmlResult);
            break;

        case "MoreFields":
            $AjaxXmlResult = str_replace("<" . $k . "/>", $v, $AjaxXmlResult);
            break;

        default:
            $AjaxXmlResult = str_replace("<" . $k . "/>", "<" . $k . ">" . $v . "</" . $k . ">", $AjaxXmlResult);
    }
}

header("Content-Type: text/xml; charset=utf-8");
echo utf8_encode($AjaxXmlResult);

// Oliver Kowalski: 0177 9699439

