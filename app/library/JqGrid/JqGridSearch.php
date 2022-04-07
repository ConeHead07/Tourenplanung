<?php
/**
 * Description of JqGridSearch
 *
 * @author rybka
 */
class JqGridSearch extends JqGridBaseObject {
    //put your code here
    
    public static $db = Null;
    
    
    public static function quote($value) 
    {
        if (self::$db || (class_exists('Zend_Registry') && self::$db = Zend_Registry::get('db')))
            return self::$db->quote($value);
        
       switch(true) {
           case is_numeric($value): return $value;
           case is_null($value): return 'NULL';
           case is_string($value): return "'" . addslashes($value) . "'";
       }
    }
    
    public static function quoteIdentifier($identifier)
    {
        if (self::$db || (class_exists('Zend_Registry') && self::$db = Zend_Registry::get('db')))
            return self::$db->quoteIdentifier($identifier);
        
        return $identifier;
    }
    
    public static function createSearchForm_byConf($RowedID, &$Conf, $options) {

        $searchForm = "";
        $searchFlds = "<option value=\"*\">Alle Felder</option>\n";
        foreach ($Conf["Fields"] as $fN => $fCnf) {
            $searchFlds.= "<option value=\"" . $fN . "\">" . $fCnf["label"] . "</option>\n";
        }

        $aCompare = array("*", ">", ">=", "<", "<=", "=", "Not", "Empty");
        $searchCmp = "";
        for ($i = 0; $i < count($aCompare); ++$i) {
            $searchCmp.= "<option value=\"" . $aCompare[$i] . "\">{$aCompare[$i]}</option>\n";
        }

        $SearchFields = "<form method=\"get\">\n";
        $SearchFields.= "<select name=\"searchFields\">" . $searchFlds . "</select>";
        $SearchFields.= "<select name=\"searchCmp\">" . $searchCmp . "</select>";
        $SearchFields.= "<input type=\"text\" name=\"searchTerm\" value=\"\">";
        $SearchFields.= "<input type=\"hidden\" name=\"_searchQ2\" value=\"true\">";
        $SearchFields.= "</form>\n";

        return <<<EOT
		<div id="searchform_{$RowedID}" title="Suche" style="display:none;">
			<div id="search-form-msgbox"></div>
			<div id="search-form-content">$SearchFields</div>
			<div id="search-form-nav">
				<table style="width:100%" border=0 class="EditTable">
					<tr>
						<td colspan="2"><hr style="margin: 1px;" class="ui-widget-content"></td>
					</tr>
					<tr id="Act_Buttons">
						<td class="EditButton" style="text-align:right" align=right>
							<a id="sQuery" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)">Suchen<span class="ui-icon ui-icon-disk"></span></a>
							<a id="cQuery" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)">Abbrechen<span class="ui-icon ui-icon-close"></span></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<script>
		$(".fm-button:not(.ui-state-disabled)","#searchform_{$RowedID}").hover(
		   function(){ $(this).addClass('ui-state-hover');}, 
		   function(){ $(this).removeClass('ui-state-hover');}
		);
		$("#sQuery:not(.ui-state-disabled)","#searchform_{$RowedID}").click(function() { 
			my_jqgrid_search("#$RowedID", $("form:eq(0)", "#searchform_{$RowedID}").serialize());
			//$("form:eq(0)", "#search-form-dialog-modal").submit() 
		});
		$("#cQuery:not(.ui-state-disabled)","#searchform_{$RowedID}").click(function() { $( "#searchform_{$RowedID}" ).dialog('close'); });
		</script>
EOT;
    }
    
    
    public static function fit_cmp_op_byterm(&$term, &$cmp_op) {
            $oper = "";
            $term = trim($term);
            if (!$term) return '';
            switch($term[0]) {
                    case "<":
                    case ">":
                    if (isset($term[1]) && $term[1]=="=") {
                            $oper = substr($term, 0, 2);
                            $term = trim(substr($term,2));
                    } else {
                            $oper = $term[0];
                            $term = trim(substr($term,1));
                    }
                    break;

                    case "=":
                            $oper = $term[0];
                            $term = trim(substr($term,1));
                    break;

                    case "!":
                            $oper = "NOT LIKE";
                            $term = trim(substr($term,1));
                    break;
            }
            
            if (!$oper) {
                $term = str_replace('*', '%', $term);
                if ($cmp_op == 'bw') $term.= '%';
                $oper = 'LIKE';                
            }
            $cmp_op = $oper;
                
    }
    
    public static function getSearchOperationByTerm($term)
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        $cmp_op = 'bw';
        self::fit_cmp_op_byterm($term, $cmp_op);
        
        return $cmp_op . ' ' . $db->quote($term);
    }
    
    /**
     *
     * @param string $term
     * @param string $oper
     * @return string 
     */
    public static function getSearchOperation($term, $oper) 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        header('X-Debug-SearchQ2-143: getSearchOperation=' . $term . ', ' . $oper);
        switch($oper) {
            case "gt": // groesser</option>
                $oper = '>';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;
            
            case "ge": // groesser gleich</option>
                $oper = '>=';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;
            
            case "lt": // kleiner</option>
                $oper = '<';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;
            
            case "le": // kleiner gleich</option>
                $oper = '<=';
                $term = is_numeric($term) ? $term : $db->quote($term);
                break;
            
            case "eq": // gleich</option>
                $oper = '=';
                $term = $db->quote($term);
                break;
                
            case "ne": // ungleich</option>
                $oper = '<>';
                $term = $db->quote($term);
                break;
            
            case "null":
            case "bw": // beginnt mit</option>
                $oper = 'LIKE';
                $term = $db->quote($term . '%');
                break;
            
            case "bn": // beginnt nicht mit</option>
                $oper = 'NOT LIKE';
                $term = $db->quote($term . '%');
                break;
            
            case "ew": // endet mit</option>
                $oper = 'LIKE';
                $term = $db->quote('%' . $term);
                break;
            
            case "en": // endet nicht mit</option>
                $oper = 'Not LIKE';
                $term = $db->quote('%' . $term);
                break;
            
            case "cn": // enthält</option>
                $oper = 'LIKE';
                $term = $db->quote('%' . $term . '%');
                break;
            
            case "nc": // enthält nicht</option>
                $oper = 'NOT LIKE';
                $term = $db->quote('%' . $term . '%');
                break;
            
            case "nu": // is null</option>
                $oper   = 'IS';
                $term = 'NULL';
                break;
            
            case "nn": // is not null</option>
                $oper   = 'IS NOT';
                $term = 'NULL';
                break;
            
            case "in": // ist in</option>
                $oper   = 'LIKE';
                $term = $db->quote($term);
                break;
            
            case "ni": // ist nicht in</option>
                $oper   = 'NOT LIKE';
                $term = $db->quote($term);
                break;
        }
        header('X-Debug-SearchQ2-225: getSearchOperation=' . trim($oper . ' ' . $term) );
        return trim($oper . ' ' . $term);
        // ['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
    }

    
    /**
     *
     * @global type $db
     * @param array $_CONF Tabellen-Felder-Conf
     * @param array $opt => $opt["additionalFields"]
     * @return string sql-Where-Part
     */
    public static function getSqlBySearch($_CONF, $opt = array()) 
    {
        $r = Zend_Controller_Front::getInstance()->getRequest();

        $_search    = ($r->getParam('_search',    "false") == "true");
        $_searchAll = ($r->getParam('_searchAll', "false") == "true");
        $_searchQ2  = ($r->getParam('_searchQ2',  "false") == "true");
        $filters    = ($r->getParam('filters',    "false")!== "false");
//        die();
        // Suche über Suchformular
        if ($_searchQ2 && $r->getParam("searchTerm", "")) {
            return self::getSqlBySearchQ2($_CONF, $opt);
        }

        // Suche mit einem Term in allen Feldern
        if ($_searchAll && $r->getParam('term', "")) {
            return self::getSqlBySearchAll($_CONF, $opt);
        }
        
        // Suche über FilterToolbar
        if ($_search) {
            if ($r->getParam("searchField")) {// || isset($_REQUEST['searchField']))
                return self::getSqlBySearchQ2($_CONF, $opt);
            }
            return self::getSqlBySearchFilter($_CONF, $opt);
            // Bsp-Request
            // _REQUEST[filters] => {"groupOp":"AND","rules":[{"field":"Mitarbeiter","op":"bw","data":"admin"},{"field":"krank","op":"bw","data":"Nein"},{"field":"urlaub","op":"bw","data":"Nein"},{"field":"von","op":"bw","data":"00:30"},{"field":"bis","op":"bw","data":"00:00"},{"field":"Dauer","op":"bw","data":"23:30"},{"field":"stundensaldo","op":"bw","data":"15:30"},{"field":"Leistung","op":"bw","data":"Aquise"},{"field":"Belasted","op":"bw","data":"Nein"}]}
        }
        return "";
    }

    public static function getSqlBySearchQ2($_CONF, $opt = array()) 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        $r = Zend_Controller_Front::getInstance()->getRequest();
        $tableNamespace = (array_key_exists('tableNamespace', $opt)) ? $opt['tableNamespace'] : '';
        //die('#'.__LINE__.' '.__METHOD__);
        $_search = ($r->getParam('_search', "false") == "true");
        $_searchAll = ($r->getParam('_searchAll', "false") == "true");
        
        $searchField = ($r->getParam("searchField") ? $r->getParam("searchField") : $r->getParam("searchFields", "*") );
        $searchCmp = $r->getParam("searchCmp", $r->getParam("searchOper", "*"));
        $term = $r->getParam("searchTerm", $r->getParam("searchString", ""));
        $term = str_replace('%', '\\'. '%', $term);

//        die('<pre>#'.__LINE__.' '.__METHOD__.print_r($opt,1).PHP_EOL.print_r($_CONF,1) . PHP_EOL);
        if (0) die('<pre>#'.__LINE__.' '.__METHOD__.' searchField:'.$searchField . PHP_EOL
                .'searchCmp:'.$searchCmp . PHP_EOL
                .'term:'.$term . PHP_EOL);

        $sql_search_cmd = '';
        $SearchableConfFields = array();
        $SearchableAddFields  = array();
        $additionalFields = (array_key_exists('additionalFields',$opt) ? $opt['additionalFields'] : array());

        if ($searchField == "*") {
            $SearchableConfFields = $_CONF["Fields"];
            $SearchableAddFields  = $additionalFields;
        } elseif (array_key_exists($searchField, $_CONF["Fields"])) {
            $SearchableConfFields[$searchField] = $_CONF["Fields"][$searchField];
        } elseif (in_array($searchField, $additionalFields)) {
            $SearchableAddFields[] = $searchField;
        }
        
        $searchOperation = self::getSearchOperation($term, $searchCmp);
                
//        header('X-Debug-SearchQ2-01: ' . json_encode($SearchableConfFields));
        header('X-Debug-SearchQ2-02: searchOperation=' . $searchOperation);
        header('X-Debug-SearchQ2-03: searchField=' . json_encode($searchField));
        header('X-Debug-SearchQ2-04: searchOper=' . json_encode($searchCmp));
        header('X-Debug-SearchQ2-05: searchTerm=' . json_encode($term));
        $i = 0;
        
        foreach ($SearchableConfFields as $fKey => $fCnf) {
//            if ($fCnf["key"] == "PRI") continue;
            switch ($fKey) {
                case "Datum":
                case "Mitarbeiter":
                default:
                    if (strpos($fCnf["type"], "int") !== false) {
                        if (!is_numeric($term) && $searchCmp != "nu") continue;
                        $term = intval($term);
                    } elseif (strpos($fCnf["type"], "float") !== false) {
                        if (!is_numeric(str_replace(",", ".", $term)) && $searchCmp != "nu")
                            continue;
                        $term = str_replace(",", ".", $term);
                    }                    
                    $sql_search_cmd.= ($i ? ' OR ' : '') .($tableNamespace?$tableNamespace.'.':'') . $db->quoteIdentifier($fCnf['dbField']). ' ' . $searchOperation;
                    ++$i;
            }
        }
        
        foreach($SearchableAddFields as $fKey) {                
            $sql_search_cmd.= ($i ? ' OR ' : '') 
                           . $db->quoteIdentifier($fKey). ' ' 
                           . $searchOperation;
            ++$i;                
        }
        
        if ($sql_search_cmd) return '(' . $sql_search_cmd . ')';
        else return '';
    }

    public static function getSqlBySearchAll($_CONF, $opt = array())
    {
        $r = Zend_Controller_Front::getInstance()->getRequest();

        $term = $r->getParam('term', "");
        $sql_search_cmd = "";
        $tableNamespace = (array_key_exists('tableNamespace', $opt)) ? $opt['tableNamespace'] : '';

        $cmp_op = "LIKE";
        self::fit_cmp_op_byterm($term, $cmp_op);
        //die('#'.__LINE__.' '.__METHOD__);
        $i = 0;
        $sql_search_cmd.= "(\n";
        foreach ($_CONF["Fields"] as $fKey => $fCnf) {
//            if ($fCnf["key"] == "PRI") continue;
            switch ($fKey) {
                case "Datum":
                case "Mitarbeiter":
                default:
                    if (strpos($fCnf["type"], "int") !== false) {
                        if (!is_numeric($term))
                            continue;
                        $term = intval($term);
                    } elseif (strpos($fCnf["type"], "float") !== false) {
                        if (!is_numeric(str_replace(",", ".", $term)))
                            continue;
                        $term = str_replace(",", ".", $term);
                    }
                    $sql_search_cmd.= ($i ? ($cmp_op != "NOT LIKE" ? " /*$i*/OR " : " /*$i*/AND ") : "") . ($tableNamespace?$tableNamespace.'.':''). "`" . $fCnf["dbField"] . "` $cmp_op \"" . str_replace("*", "%", mysql_real_escape_string($fTerm)) . ($cmp_op != "=" ? "%" : "") . "\"\n";
                    ++$i;
            }
        }
        $sql_search_cmd.= ")\n";
        return $sql_search_cmd;
    }

    public static function getSqlBySearchFilter($_CONF, $opt = array())
    {
        $r = Zend_Controller_Front::getInstance()->getRequest();
        $tableNamespace = (array_key_exists('tableNamespace', $opt)) ? $opt['tableNamespace'] : '';
        //die('#'.__LINE__.' '.__METHOD__);
        
        // Bsp-Request
        // _REQUEST[filters] => 
        //   {
        //      "groupOp":"AND",
        //      "rules":[
        //          {"field":"Mitarbeiter","op":"bw","data":"admin"},
        //          {"field":"krank","op":"bw","data":"Nein"},
        //          {"field":"urlaub","op":"bw","data":"Nein"},
        //          {"field":"von","op":"bw","data":"00:30"},
        //          {"field":"bis","op":"bw","data":"00:00"},
        //          {"field":"Dauer","op":"bw","data":"23:30"},
        //          {"field":"stundensaldo","op":"bw","data":"15:30"},
        //          {"field":"Leistung","op":"bw","data":"Aquise"},
        //          {"field":"Belasted","op":"bw","data":"Nein"}
        //     ]
        //  }

        $additionalFields = (isset($opt["additionalFields"])) ? $opt["additionalFields"] : array();

        //foreach($_REQUEST as $k => $v) echo "#".__LINE__." _REQUEST[$k] => ".print_r($v,1)."\n";

        $SearchFields = array();
        $sql_search_cmd = "";
        $filters  = $r->getParam("filters", "");
        
        if (!is_object($filters)) {
            $prmEncoding = mb_detect_encoding($filters);
            if ($prmEncoding != 'UTF-8') $filters = utf8_encode($filters);
            $filters = json_decode($filters);
        }        
        
        header('X-Debug-SearchFilter-01: filter=' . json_encode($filters));
        
        if (is_object($filters) && property_exists($filters, 'rules')) {
            foreach ($filters->rules as $k => $v) {
                $SearchFields[$v->field] = array(
                    'oper'=>$v->op, 
                    'term'=> utf8_decode($v->data));
            }
        } else {
            return '';
        }
        
        $GroupOp = (isset($filters->groupOp) && in_array($filters->groupOp, array('AND','OR')) ? $filters->groupOp : "OR");
        
        header('X-Debug-SearchFilter-03: SearchFields=' . json_encode($SearchFields));
        
        foreach ($SearchFields as $_field => $v) {
            if (!empty($v['term'])) {
                $_term = $v['term'];
                $_oper = $v['oper'];
                $_tbl = '';
                
//                if (strpos($_field, '.')) {
//                    $_t = explode('.', $_field);
//                    $_field = array_pop($_t);
//                    $_tbl = array_pop($_t);
//                }
                if (array_key_exists($_field, $_CONF["Fields"])) {
                    $fCnf = $_CONF["Fields"][$_field];
                    $dbfield = ($_tbl ? $_tbl.'.': '') . $fCnf["dbField"];
                    header('X-Debug-SearchFilter-04: case='.$_field.' Is In _CONF!');
                } elseif (array_key_exists($_field, $additionalFields)) {
                    $dbfield = $additionalFields[$_field];
                    header('X-Debug-SearchFilter-04: case='.$_field.' Is In Associative Array AdditionalFields!');
                } elseif (($k = array_search($_field, $additionalFields)) !== false) {
                    $dbfield = $additionalFields[$k];
                    header('X-Debug-SearchFilter-04: case='.$_field.' Is In Numerative Array AdditionalFields!');
                } else {
                    header('X-Debug-SearchFilter-04: case='.$_field.' Is Not Valid!');
                    continue;
                }
                
                if (!$_oper || ($_oper == 'bw' && !isset($_REQUEST['searchField']))) {
                    $searchOperation = self::getSearchOperationByTerm($_term);
                } else {
                    $searchOperation = self::getSearchOperation($_term, $_oper);
                }

                $sql_search_cmd.= 
                         (!empty($sql_search_cmd) 
                         ?
                         " $GroupOp " // AND|OR
                         : "")
                         .($tableNamespace?$tableNamespace.'.':''). $dbfield . ' ' . $searchOperation;
            } else {
                header('X-Debug-SearchFilter-04: '.$_field.': Term (' . $v['term'] . ') is empty!');
            }
        }
        return $sql_search_cmd;
    }
}
