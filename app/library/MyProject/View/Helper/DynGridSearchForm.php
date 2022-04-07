<?php 

class MyProject_View_Helper_DynGridSearchForm extends Zend_View_Helper_Abstract
{  
    protected $_fields = null;
    protected $_opers = null;
    protected $_sopt = null;
    protected $_soptconf = array(
        'gt' => array('groesser', '>'),
        'ge' => array('groesser gleich', '>='),
        'lt' => array('kleiner', '<'),
        'le' => array('kleiner gleich', '<='),
        'eq' => array('gleich', '='),
        'ne' => array('ungleich', '<>'),
        'bw' => array('beginnt mit', 'LIKE'),
        'bn' => array('beginnt nicht mit', 'NOT LIKE'),
        'ew' => array('endet mit', 'LIKE'),
        'en' => array('endet nicht mit', 'Not LIKE'),
        'cn' => array('enthält', 'LIKE'),
        'nc' => array('enthält nicht', 'NOT LIKE'),
        'nu' => array('is null', 'IS NULL'),
        'nn' => array('is not null', 'IS NOT NULL'),
        'in' => array('ist in', 'LIKE'),
        'ni' => array('ist nicht in', 'NOT LIKE'),
    );
    protected $_searchFieldsIni = '';
    static $currId = 0;

    public function DynGridSearchForm(array $opt = NULL) 
    {
        self::$currId += 1;
        $view = Zend_Layout::getMvcInstance()->getView();
//        $bU = $view->getHelper('baseUrl')->getBaseUrl();
        $bU = $view->baseUrl();
        
        $this->_fields = array();
        $this->_searchFieldsIni = '';
        $gridId = null;
        $gridSearchUrl = '';
        $gridModelUrl  = '';
        $elementId = null;
        $showOpers = true;
        $defaultOpers  = null;
        $staticFields  = array();
        $defaultFields = array();
        $liveSearchUrl = '';
        $categoryTreeFields = array();
        $searchFieldsFormat = array();
        if (is_array($opt) && count($opt)) {
            if (array_key_exists('sopt', $opt))
                $this->setOpers ($opt['sopt']);
            if (array_key_exists('showOperators', $opt))
                $showOpers = (bool) $opt['showOperators'];
            if (array_key_exists('defaultOpers', $opt))
                $defaultOpers = $opt['defaultOpers'];
            if (array_key_exists('staticFields', $opt))
                $staticFields = (array)$opt['staticFields'];
            if (array_key_exists('defaultFields', $opt))
                $defaultFields = $opt['defaultFields'];
            if (array_key_exists('elementId', $opt))
                $elementId = $opt['elementId'];
            if (array_key_exists('gridId', $opt))
                $gridId = $opt['gridId'];
            if (array_key_exists('gridSearchUrl', $opt))
                $gridSearchUrl = $opt['gridSearchUrl'];
            if (array_key_exists('gridModelUrl', $opt))
                $gridModelUrl = $opt['gridModelUrl'];
            if (array_key_exists('searchFieldsIni', $opt))
                $this->_searchFieldsIni = $opt['searchFieldsIni'];
            if (array_key_exists('liveSearchUrl', $opt))
                $liveSearchUrl = $opt['liveSearchUrl'];
            if (array_key_exists('categoryTreeFields', $opt))
                $categoryTreeFields = $opt['categoryTreeFields'];            
            if (array_key_exists('searchFieldsFormat', $opt))
                $searchFieldsFormat = $opt['searchFieldsFormat'];
            
        }
        if (empty($elementId)) $elementId = '#SearchForm' . self::$currId;
        
        $jsonOpers          = json_encode( $this->getOpers() );
        $jsonFields         = json_encode( $this->getFields() );
        $jsonShowOpers      = json_encode( $showOpers);
        $jsonDefaultOpers   = json_encode( $defaultOpers);
        $jsonGridId         = json_encode( $gridId);
        $jsonFieldsFormat   = json_encode( $searchFieldsFormat);
        $jsonGridSearchUrl  = json_encode( $gridSearchUrl);
        $jsonGridModelUrl   = json_encode( $gridModelUrl);
        $jsonLiveSearchUrl  = json_encode( $liveSearchUrl);
        $jsonCategoryFields = json_encode( $categoryTreeFields);
        $prerenderedFields  = '';
        
        if (is_array($defaultFields) && count($defaultFields) ) {
            foreach($defaultFields as $_f) {
                if (!in_array($_f, $staticFields))
                    $prerenderedFields.= '
                    <tr>
                        <td class="columns"><select name="field" default="' . $_f . '"></select></td>
                        <td class="operators"><select name="op"></select></td>
                        <td class="data"><input name="data" type="text" class="sQuery" /></td>
                        <td class="del-rule"><button class="delete-rule ui-del" title="Delete rule">-</button></td>
                    </tr>' . PHP_EOL;
                else 
                    $prerenderedFields.= '
                    <tr>
                        <td class="columns"><select name="field" default="' . $_f . '" readonly="readonly">
                        </select></td>
                        <td class="operators"><select name="op">
                        </select></td>
                        <td class="data"><input name="data" type="text" class="sQuery" /></td>
                        <td class="del-rule"></td>
                    </tr>' . PHP_EOL;
            }
        }
        
        $ExtraSearchFields = '';
        $ExtraScript = '';
        if (1 && $elementId == '#sFormVorgaenge') {
            $ExtraSearchFields = '<tr><td><td><td><td></tr>'
                .'<tr class="simpleInput">'
                .'<td colspan=4>Geplante Auslieferung</td></tr>'
                .'<tr class="simpleInput"><td>'
                .'<input type="hidden" name="date" id="inputDate"/>'
                .'<input type="text" id="vorgaengeDate" size=10 style="width:99%;padding-left:0;padding-right:0;" /></td>'                 
                .'<td align=center style="text-align:center">bis</td>'
                .'<td><input type="hidden" name="dateTo" id="inputDateTo"/><input type="text" id="vorgaengeDateTo" size=10 style="width:99%;padding-left:0;padding-right:0;" /></td>'
                .'<td></td>'
                .'</tr>' . PHP_EOL;
            
            $ExtraScript = "
                $(\"#vorgaengeDate, #vorgaengeDateTo\", \"$elementId\").button().datepicker({
                    dateFormat: \"D dd.mm.yy\",
                    changeMonth: true,
                    showWeek: true,
                    onSelect: function(d) {
                        var sel = '#input'+$(this).attr('id').substr(9);
                        $( sel ).val( 
                           $.datepicker.formatDate('yy-mm-dd', $.datepicker.parseDate('D dd.mm.yy', d) )
                        );
                    }
                });
            ";
        }
        
        if ($this)
        return <<<EOT
            <!-- Start Output DynGridSeearchForm -->
            <div id="{$elementId}" class="touren-widget searchFilter">
            
            <table class="group" cellpadding=0 border=0 cellspacing=0 style="border-collapse:collapse;border-spacing:0">
            <tbody>
                $ExtraSearchFields
                $prerenderedFields
                <tr>
                <th colspan="3" style="text-align:left">
                <select name="groupOp">
                    <option value="AND">und</option>
                    <option value="OR">oder</option>
                </select>
                <input class="add-rule ui-add" type="button" title="Add rule" value="+">
                <input class="switch-op ui-add" type="button" title="Suchoperatoren" value="Details">
                <button name="sendQuery" style="float:right">suchen</button>
                <button name="resetQuery" style="float:right">reset</button>
                <span style="clear:both;display:block;visibility:hidden;">.</span></th>
                </tr>                
            </tbody>
            </table>
            <div>
            </div>
            </div>
            <script>
            
            (function($) {
                $(function() {
                    var renderMultiSearchBox = function() {
                        $("$elementId").fbMultiSearchBox({
                            showOpers:     $jsonShowOpers,
                            searchOpers:   $jsonOpers,
                            defaultOpers:  $jsonDefaultOpers,
                            searchFields:  $jsonFields,
                            liveSearchUrl: $jsonLiveSearchUrl,
                            categoryTreeFields: $jsonCategoryFields,
                            searchFieldsFormat: $jsonFieldsFormat,
                            jqGridID: $jsonGridId,
                            onselect: null //function(selectbox, input, selboxValue) {}
                        });
                    }
                    if ($($jsonGridId).length && $($jsonGridId)[0].grid) {
                        renderMultiSearchBox.call();
                    } else if ($jsonGridModelUrl) {
                        $.getScript($jsonGridModelUrl, renderMultiSearchBox);
                    }
                    $ExtraScript
                });
            })(jQuery);

            </script>
            <!-- Ende Output DynGridSeearchForm -->
EOT;
    }

    public function _loadFields($options = NULL) {
        if (!$this->_searchFieldsIni) return;
        
        if (file_exists( $this->_searchFieldsIni )) {
            /* @var $config Zend_Config_Ini */
            $config = new Zend_Config_Ini( $this->_searchFieldsIni );
            if (@empty($config->Fields)) throw new Exception("No Fields-Section in ConfigFile:".$this->_searchFieldsIni);
            foreach($config->Fields as $k => $v) {
                if ($v == 1) {                    
                    $this->_fields[$k] = (@isset($config->FieldAlias->$k)) ? $config->FieldAlias->$k : $k;
                }
            }
        }
    }
    
    public function getFields()
    {
        if ( !$this->_fields ) $this->_loadFields ();
        return $this->_fields;
    }

    public function setOpers($opers) {
        $this->_opers = $opers;
    }

    public function getOpers() {
        if ($this->_opers == NULL) {
            foreach($this->_soptconf as $k => $v) {
                $this->_opers[$k] = $v[0];
            }
        }
        return $this->_opers;
    }
}

