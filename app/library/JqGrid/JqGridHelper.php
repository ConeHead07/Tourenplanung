<?php
require_once 'JsFunction.php';

class JqGridHelper {
	
	public static function utf8_autoencode($string) {
	    return $string;
	    // Disable utf8-encoding
//		if (!is_string($string)) return $string;
                if ( function_exists("mb_detect_encoding") ) {
			if ( mb_detect_encoding($string,"UTF-8",true) != "UTF-8") 
			return utf8_encode($string);
		}
		if ( !preg_match("/".utf8_encode("(�)|(�)|(�)|(�)|(�)|(�)|(�)")."/", $string) )
		return utf8_encode($string);
		
		// Wenn bereits UTF-8 nicht ver�ndern!
		return $string;
	}
	
	public static function json_encode($object) 
	{
		$jsonCode = "";
		self::json_encode_object($object, $jsonCode);
		return $jsonCode;
	}
	
	
	public static function json_encode_object(&$object, &$jsonCode) {
            if (is_scalar($object)) {
                // $jsonCode.= json_encode( (is_string($object) ? self::utf8_autoencode($object) : $object) );
                $jsonCode.= json_encode( $object );
                return;
            }
            if (is_null($object)) {
                $jsonCode.= json_encode($object);
                return;
            }
            $use_as_object = is_object($object);
            if (is_array($object) && !$use_as_object) {
                $j=0;
                foreach($object as $i => $_) {
                    if ($i !== $j++) {$use_as_object=true; break;}
                }
            }

            $jsonCode.= ($use_as_object) ? "{" : "[";

            $i = -1;
            $jsKey = '';
            $quoteAnyway  = true;
            try {
                foreach($object as $k => $v) {
                    if ($use_as_object) $jsKey = ($quoteAnyway || preg_match("/[^a-z0-9_]/i", $k))  ? '"'.$k.'"': $k;
                    ++$i;
                    if (is_scalar($v) || is_null($v)) {
                            $jsonCode.= ($i?',':'').($use_as_object ? "$jsKey:":'').json_encode((!is_string($v)?$v:self::utf8_autoencode($v)));
                            continue;
                    }
                    if ($v instanceof JsFunction) {
                            $jsonCode.= ($i?',':'').($use_as_object ? "$jsKey:":'').self::utf8_autoencode((string)$v);
                            continue;
                    }

                    $jsonCode.= ($i?',':'').($use_as_object ? "$jsKey:":'');
                    self::json_encode_object($v, $jsonCode);
                }
            } catch (Exception $e) {
                echo "ERROR:".$e."<br>\n";
                echo "k:$k, v:".print_r($v,1)."<br>\n";
            }
            $jsonCode.= ($use_as_object) ? "}" : "]";
	}
	
	/**
	 * @param string RowedID
	 * @param string Js-Helper-Funktion EditRowFunction, wenn Row inline zum Bearbeiten ge�ffnet wird
	 * @return JqGrid
	 */
	public function getEditRow_onSelectRow($RowedID, $EditRowFunction)
	{
		if (!$EditRowFunction) $EditRowFunction = 'null';
		return <<<EOT
		function(id){
				
			if(id && id!==lastsel){
				jQuery('#{$RowedId}').jqGrid('editRow', id, true, {$EditRowFunction});
				jQuery('#{$RowedId}').jqGrid('saveRow', lastsel);
				jQuery('#{$RowedId}').jqGrid('restoreRow', lastsel);
				jQuery('#{$RowedId}').jqGrid('editRow',id,true);
				lastsel=id;
			}
		}
EOT;
	}
	/**
	 *
	 * Javascript-Funktion f�r selbsgebaute readonly-Felder bzw. userdefined Grid-Feld-Typen
	 * @param string $functionName
	 * @return string js-Funktion mit Namen $functionName
	 * @abstract Js-Funktion, die die Darstellung bzw. Formatierung des Feldwertes �bernimmt
	 *
	 * Verwendungsbeispiel in jqgrid->colModel
	 * %custom_readable_elem% muss durch den Namen der Funktion ersetzt werden (ohne Funktionsk�rper!)
	 * jQuery("#grid_id").jqGrid({
	 * colModel: [
	 * {name:'price', ..., editable:true, edittype:'custom', editoptions:{
	 * custom_element: "%custom_readable_elem%",
	 * custom_value:"%custom_readable_value%"} }
	 */
	public function custom_readable_elem($fncName) {
		// Javascript-Beispiel f�r selbsgebaute readonly-Felder
		return <<<EOT
		function {$fncName}(value, options) {
			var el = jQuery("<span/>");
			jQuery(el).text(value);
			return el;
		}
EOT;
	}

	/**
	 *
	 * Javascript-Funktion f�r selbsgebaute readonly-Felder
	 * @param string $functionName
	 * @return string js-Funktion mit Namen $functionName
	 * @abstract Js-Funktion, Grid-Schnittstellen-Funktion zum Setzen und Lesen des Wertes eigener Grid-Feld-Typen
	 *
	 * Verwendungsbeispiel in jqgrid->colModel
	 * %custom_readable_value% muss durch den Namen der Funktion ersetzt werden (ohne Funktionsk�rper!)
	 * jQuery("#grid_id").jqGrid({
	 * colModel: [
	 * {name:'price', ..., editable:true, edittype:'custom', editoptions:{
	 * custom_element: "%custom_readable_elem%",
	 * custom_value:"%custom_readable_value%"} }
	 */
	public function custom_readable_value($functionName)
	{
		return <<<EOT
		function {$functionName}(elem, operation, value) {
			if(operation === 'get') {
				return jQuery(elem).text();
			} else if(operation === 'set') {
				jQuery(elem).text(value);
			}
		}
EOT;
	}

	/**
	 * @param $functionName
	 * @return $Js-Funktions-Code
	 * @abstract $Js-Funktionsname kann im colModel als formatter angegeben werden.
	 * Der zur�ckgegebene Funktionscode muss als externer js-Code-Block eingebettet werden
	 * bzw. ausserhalb des jqgrid-Objekt-Codes<br>
	 * An Funktion �bergebene Argumente:<br>
	 * <li> cellvalue
	 * <li> options
	 * <li> rowObject
	 */
	public function custom_formatter_time_hmm($functionName)
	{
	// time_formatter_hhmm
	return <<<EOT
	// START user-time-functions START
	function {$functionName}(cellvalue, options, rowObject) {
		//alert("{$functionName}("+cellvalue+")");
		if (cellvalue && typeof(cellvalue)=="string") {
			if (cellvalue.split(":").length == 2) return cellvalue;
			else if (cellvalue.split(":").length > 2) return cellvalue.split(":")[0]+":"+cellvalue.split(":")[1];
			else return cellvalue.split(":")[0]+":00";
		}
		return cellvalue;
	}
	// ENDE user-time-functions ENDE
EOT;
}

/**
 * @param JS-funktionsname
 * @return JS-funktionscode f�r time-summenberechnung
 */
public function custom_time_summary($functionName = "my_sumTime")
{
	return <<<EOT
	function {$functionName}(val, name, record) {
		//alert(val+","+record[name]);
		var i=0,
		t1 = ((val||'0')+":0").split(":"),
		t2 = ((record[name]||'0')+":0").split(":");
	
		for (i = 0; i < t1.length; ++i) {
			while(t1[i].charAt(0)=='0' || isNaN(t1[0])) t1[i] = t1[i].substr(1); if (t1[i]=='' || isNaN(t1[i])) t1[i]=0;
		}
		for (i = 0; i < t2.length; ++i) {
			while(t2[i].charAt(0)=='0' || isNaN(t2[0])) t2[i] = t2[i].substr(1); if (t2[i]=='' || isNaN(t2[i])) t2[i]=0;
		}
	
		var mintotal = (t1[0]*60)+(t2[0]*60)+(1*t1[1])+(1*t2[1]);
		var h = (Math.floor(mintotal/60)).toString();
		var m = (mintotal % 60).toString();
	
		if (h.length < 2) h = ("00"+h).substr(("00"+h).length-2);
		if (m.length < 2) m = ("00"+m).substr(("00"+m).length-2);
		return h+":"+m;
	}
EOT;
}

	public function filterToolbar($RowedID, $PagerID, $opts = array())
	{
	
		$opts = array_merge(
		array(
		// defaults
					"searchOnEnter" => false, // Wartet nicht erst auf Enter, sondern sucht direkt bei Eingabe Live-Search
					"caption" => 'Filter',
					"title" => 'Toggle Searching Toolbar',
					"buttonicon" => 'ui-icon-pin-s',
					"showOnStart" => false // Wird per Default erst auf Anforderung eingeblendet
		), $opts );
		$opts['searchOnEnter'] = ($opts['searchOnEnter']) ? 'true' : 'false';
		$opts['showOnStart'] = ($opts['showOnStart']) ? 'true' : 'false';
	
		return <<<EOT
				jQuery("#t_{$RowedID}").height(25).hide().jqGrid('filterGrid',"#{$RowedID}",{gridModel:true,gridToolbar:true});
				jQuery("#sg_Datum").datepicker({dateFormat:"yy-mm-dd",showWeek:true,changeYear:true});
				
				jQuery("#{$RowedID}").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : {$opts['searchOnEnter']} }); 
				
				// Eigener Button toggle FilterToolbar
				$("#{$RowedID}").jqGrid('navButtonAdd', '#{$PagerID}',{ 
					caption: "{$opts['caption']}", 
					title: "{$opts['title']}",
					buttonicon: '{$opts['buttonicon']}',
					onClickButton: function () { $( this )[0].toggleToolbar(); }
				});
				if ( {$opts['showOnStart']} ) $(document).ready(function() {
					$("#{$RowedID}")[0].toggleToolbar();
				});
EOT;
	}
}
