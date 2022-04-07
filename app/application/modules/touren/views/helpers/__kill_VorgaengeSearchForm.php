<?php 

class Touren_View_Helper_VorgaengeSearchForm {

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
    
    /* @var $view Zend_View */
    protected $view = null;

    public function vorgaengeSearchForm(array $opt = NULL) 
    {
        $view = Zend_Layout::getMvcInstance()->getView();
//        $bU = $view->getHelper('baseUrl')->getBaseUrl();
        $bU = $view->baseUrl();
        
        $showOpers = true;
        if (is_array($opt) && count($opt)) {
            if (array_key_exists('sopt', $opt))
                $this->setOpers ($opt['sopt']);
            if (array_key_exists('showOperators', $opt))
                $showOpers = (bool) $opt['showOperators'];
        }
        
        $elementId = '#sFormVorgaenge';
        $jsonOpers = json_encode( $this->getOpers() );
        $jsonFields= json_encode( $this->getFields() );
        $jsonShowOpers = json_encode($showOpers);
        return <<<EOT
            <div class="touren-vorgaenge-widget searchFilter">
            <table class="group" cellpadding=0 border=0 cellspacing=0 style="border-collapse:collapse;border-spacing:0">
            <tbody>
                <tr>
                    <td class="columns"><select name="field" default="LieferungPostleitzahl"></select></td>
                    <td class="operators"><select name="op">
                    </select></td>
                    <td class="data"><input  name="data" type="text" class="sQuery" /></td>
                    <td class="del-rule"><button class="delete-rule ui-del" title="Delete rule">-</button></td>
                </tr>
                <tr>
                    <td class="columns"><select name="field" default="Auftragsnummer"></select></td>
                    <td class="operators"><select name="op">
                    </select></td>
                    <td class="data"><input  name="data" type="text" class="sQuery" /></td>
                    <td class="del-rule"><button class="delete-rule ui-del" title="Delete rule">-</button></td>
                </tr>
                <tr>
                    <td class="columns"><select name="field" default="LieferungName"></select></td>
                    <td class="operators"><select name="op">
                    </select></td>
                    <td class="data"><input  name="data" type="text" class="sQuery" /></td>
                    <td class="del-rule"><button class="delete-rule ui-del" title="Delete rule">-</button></td>
                </tr>
                <tr>
                    <td class="columns"><select name="field" default="Lieferwoche"></select></td>
                    <td class="operators"><select name="op">
                    </select></td>
                    <td class="data"><input  name="data" type="text" class="sQuery" /></td>
                    <td class="del-rule"><button class="delete-rule ui-del" title="Delete rule">-</button></td>
                </tr>
                
                <tr>
                <th colspan="3" style="text-align:left">
                <select name="groupOp">
                    <option value="AND">und</option>
                    <option value="OR">oder</option>
                </select>
                <input class="add-rule ui-add" type="button" title="Add rule" value="+"></th>
                </tr>                
            </tbody>
            </table>
            <div>
                <button name="sendQuery" style="float:right">suchen</button>
                <span style="clear:both;display:block;visibility:hidden;">.</span>
            </div>
            </div>
            <script>
            if (typeof(TVG)=='undefined') var TVG = {};
            TVG.showOpers    = $jsonShowOpers;
            TVG.searchOpers  = $jsonOpers;
            TVG.searchFields = $jsonFields;
            jQuery("head").append('<link href="{$bU}/jquery/combobox/jquery.ui.combobox.css" type="text/css" rel="Stylesheet" />');
            jQuery.getScript("{$bU}/jquery/combobox/jquery.ui.combobox.js", function(data, textStatus){
                jQuery.getScript("{$bU}/touren/jquery.crm.plugins/searchvorgaenge.js");
            });

            </script>
EOT;
    }

    public function _loadFields($options = NULL) {
        $f = APPLICATION_PATH
                . '/modules/touren/configs/vorgaenge_suchfelder.ini';
        
        if (file_exists($f)) {
            /* @var $config Zend_Config_Ini */
            $config = new Zend_Config_Ini( $f );
            foreach($config->Fields as $k => $v) {
                $this->_fields[] = $k;
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

