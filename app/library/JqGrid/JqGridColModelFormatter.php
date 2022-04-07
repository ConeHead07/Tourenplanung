<?php

/**
 *
 * In jqGrid Vordefinierte Formatter-Objekte, deren Eigenschaften überschrieben werden können
 * @abstract Wenn als formatter ein vordefiniertes Format angebegen wird (integer, number, date etc.)<br>
 * können im Objekt colModel->formatoptions die Default-Vorgaben überschrieben werden.<br>
 * Abhängig vom eingebundenen Sprachpaket sind bereits die passende landestypischen Einstellungen gesetzt.<br>
 *
 * Column Specific Options<br>
 * Formatter options can be defined for particular columns, overwriting the defaults from the language file. 
 * This is accomplished by using the formatoptions array in colModel. For example:
 * <pre>
 * jQuery("#grid_id").jqGrid({
 * ...
 *    colModel : [
 *       ...
 *       {name:'myname', ...
 *        formatter:'currency',
 *        formatoptions:{
 *           decimalSeparator:",",
 *           thousandsSeparator: ",",
 *           decimalPlaces: 2,
 *           prefix: "$ "}
 *        },
 *        ...
 *    ],
 *    ...
 * });
 * </pre>
 * @author rybka
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter
 */
abstract class JqGridColModelFormatter extends JqGridBaseObject {

    protected $_formatterType = "";

    public function type() {
        return $this->_formatterType;
    }

}

/**
 *
 * @abstract  Überschreibbare Werte:
 * <li> thousandsSeparator determines the separator for the thousands
 * <li> defaultValue set the default value if nothing in the data
 */
class JqGridColModelFormatterInteger extends JqGridColModelFormatter {

    protected $_formatterType = "integer";

    /**
     * @param string $param
     */
    public function set_thousandsSeparator($param) {
        $this->thousandsSeparator = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_defaultValue($param) {
        $this->defaultValue = $param;
        return $this;
    }

}

/**
 * 
 * @abstract Überschreibbare Werte:
 * <li> thousandsSeparator determines the separator for the thousands
 * <li> decimalSeparator determines the separator for the decimals
 * <li> decimalPlaces determine how many decimal places we should have for the number
 * <li> defaultValue set the default value if nothing in the data
 */
class JqGridColModelFormatterNumber extends JqGridColModelFormatter {

    protected $_formatterType = "number";

    /**
     * @param string $param
     */
    public function set_decimalSeparator($param) {
        $this->decimalSeparator = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_thousandsSeparator($param) {
        $this->thousandsSeparator = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_decimalPlaces($param) {
        $this->decimalPlaces = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_defaulValue($param) {
        $this->defaulValue = $param;
        return $this;
    }

}

/**
 *
 * @abstract  The same as number, but we add aditional two options:
 * <li> prefix: text that is inserted before the number and 
 * <li> suffix: text that is added after the number
 * 
 * <li> thousandsSeparator determines the separator for the thousands
 * <li> decimalSeparator determines the separator for the decimals
 * <li> decimalPlaces determine how many decimal places we should have for the number
 * <li> defaultValue set the default value if nothing in the data
 */
class JqGridColModelFormatterCurrency extends JqGridColModelFormatter {

    protected $_formatterType = "currency";

    /**
     * @param string $param
     */
    public function set_decimalSeparator($param) {
        $this->decimalSeparator = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_thousandsSeparator($param) {
        $this->thousandsSeparator = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_decimalPlaces($param) {
        $this->decimalPlaces = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_defaulValue($param) {
        $this->defaulValue = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_prefix($param) {
        $this->prefix = $param;
        return $this;
    }

    /**
     * @param string $param
     */
    public function set_suffix($param) {
        $this->suffix = $param;
        return $this;
    }

}

/**
 *
 * @abstract  Überschreibbare Einstellungen:
 * <li> srcformat
 * <li> newformat
 * <br>
 * Folgende Einstellungen sind noch vordefiniert, aber die API gibt sie nicht als überschreibbare Werte an:
 * <li> dayNames [	"So", "Mo", "Di", "Mi", "Do", "Fr", "Sa",
 *   		"Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
 * <li> monthNames [ "Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez",
 *  		"Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"  	]
 * <li> AmPm ["am","pm","AM","PM"]
 * <li> S function (j) {return 'ter'},
 * <li> masks
 * <li> reformatAfterEdit
 */
class JqGridColModelFormatterDate extends JqGridColModelFormatter {

    protected $_formatterType = "date";

    /**
     * @param string $param
     */
    public function set_srcformat($param) {
        $this->srcformat = $param;
    }

    /**
     * @param string $param
     */
    public function set_newformat($param) {
        $this->newformat = $param;
    }

}

/**
 *
 * @abstract When a mail type is used we directly add a href with  mailto: before the  e-mail.<br>
 * Überschreibbare Einstellungen: Keine
 */
class JqGridColModelFormatterEmail extends JqGridColModelFormatter {

    protected $_formatterType = "email";

}

/**
 * @abstract The default value of the target options is null.<br>
 * When this options is set, we construct a link with the target property set 
 * and the cell value put in the href tag.<br>
 */
class JqGridColModelFormatterLink extends JqGridColModelFormatter {

    protected $_formatterType = "link";

}

/**
 * @abstract  Überschreibbare Einstellungen:
 * <li> baseLinkUrl is the link.<br>
 * <li> showAction is an additional value which is added after the baseLinkUrl.<br>
 * <li> addParam is an additional parameter that can be added after the idName property.<br>
 * <li> target, if set, is added as an additional attribute.<br>
 * <li> idName is the first parameter that is added after the showAction.<br>
 * By default, this is id,
 * @example colModel: [ { 		name:'myname', formatter:'showlink', formatoptions:{ baseLinkUrl:'someurl.php', addParam: '&action=edit'}, ...}<br>
 * colModel: [ {name:'myname', formatter:'showlink', formatoptions:{baseLinkUrl:'someurl.php', addParam: '&action=edit', idName:'myid'}, ...}
 */
class vJqGridColModelFormatterShowlink extends JqGridColModelFormatter {

    protected $_formatterType = "showlink";

    /**
     * @param string $param
     * @return JqGridColModelFormatterShowlink
     */
    public function set_baseLinkUrl($param) {
        $this->baseLinkUrl = $param;
        return $this;
    }

    /** 
     * wird an den Url-Pfad als weiteres Pfadelement angehängt
     * @param string $param
     */
    public function set_showAction($param) {
        $this->showAction = $param;
        return $this;
    }

    /**
     * @param string $param
     * @return JqGridColModelFormatterShowlink
     */
    public function set_addParam($param) {
        $this->addParam = $param;
        return $this;
    }

    /**
     * @param string $param
     * @return JqGridColModelFormatterShowlink
     */
    public function set_target($param) {
        $this->target = $param;
        return $this;
    }

    /**
     * @param string $param
     * @return JqGridColModelFormatterShowlink
     */
    public function set_idName($param) {
        $this->idName = $param;
        return $this;
    }

}

/**
 * The default value for the disabled is true
 * @abstract The default value for the disabled is true.<br>
 * This option determines if the checkbox can be changed.<br>
 * If set to false, the values in checkbox can be changed<br>
 * @param disabled
 */
class JqGridColModelFormatterCheckbox extends JqGridColModelFormatter {

    protected $_formatterType = "checkbox";

    /**
     * The default value for the disabled is true
     * @abstract The default value for the disabled is true.<br>
     * This option determines if the checkbox can be changed.<br>
     * If set to false, the values in checkbox can be changed<br>
     * @param disabled
     * @return JqGridColModelFormatterCheckbox
     */
    public function set_disabled($param) {
        $this->params->disabled = $param;
        return $this;
    }

}

/**
 * this is not a real select but a special case
 * @abstract this is not a real select but a special case.<br>
 * Überschreibbare Einstellungen: Keine<br>
 * Spezifizierende Einstellungen werden in colModel->editoptions gesetzt
 * colModel : [ {name:'myname', edittype:'select', editoptions:{value:"1:One;2:Two"}} ... ],<br>
 * colModel : [ {name:'myname', edittype:'select', formatter:'select', editoptions:{value:"1:One;2:Two"}} ... ]<br>
 */
class JqGridColModelFormatterSelect extends JqGridColModelFormatter {

    protected $_formatterType = "select";

}

/**
 * This type of formatter is a easy way to add a buttons at certain column for inli
 * @abstract This type of formatter is a easy way to add a buttons at certain column
 * for inline editing.<br>
 *  We add a two types of actions edit and delete.<br>
 *  When the editformbutton parameter is set to true the form editing dialogue
 *  appear instead of in-line edit.<br>
 *  <br>
 *  Überschreibbare Einstellungen:*
 * <li> keys: false
 * <li> editbutton : true
 * <li> delbutton : true
 * <li> editformbutton: false
 * <li> onEdit : null
 * <li> onSuccess: null
 * <li> afterSave:null
 * <li> onError: null
 * <li> afterRestore: null
 * <li> extraparam: {  oper:'edit'}
 * <li> url: null
 * <li> delOptions: { 	 }
 * <li> editOptions : {  }
 */
class JqGridColModelFormatterActions extends JqGridColModelFormatter {

    protected $_formatterType = "actions";

    /**
     * @abstract default: false
     * @param bool, mixed
     * @return JqGridColModelFormatterActions
     */
    public function set_keys($param) {
        $this->params->keys = $param;
        return $this;
    }

    /**
     * @abstract default: true
     * @param bool
     * @return JqGridColModelFormatterActions
     */
    public function set_editbutton($param) {
        $this->params->editbutton = $param;
        return $this;
    }

    /**
     * @abstract default: false
     * @param bool
     * @return JqGridColModelFormatterActions
     */
    public function set_editformbutton($param) {
        $this->params->editformbutton = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_onEdit($param) {
        $this->params->onEdit = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_onSuccess($param) {
        $this->params->onSuccess = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_afterSave($param) {
        $this->params->afterSave = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_onError($param) {
        $this->params->onError = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_afterRestores($param) {
        $this->params->afterRestore = $param;
        return $this;
    }

    /**
     * @abstract default: { oper:'edit' }
     * @param JsFunction
     * @return JqGridColModelFormatterActions
     */
    public function set_extraparam($param) {
        $this->params->extraparam = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param string
     * @return JqGridColModelFormatterActions
     */
    public function set_url($param) {
        $this->params->url = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param object, array
     * @return JqGridColModelFormatterActions
     */
    public function set_delOptions($param) {
        $this->params->delOptions = $param;
        return $this;
    }

    /**
     * @abstract default: null
     * @param object, array
     * @return JqGridColModelFormatterActions
     */
    public function set_editOptions($param) {
        $this->params->editOptions = $param;
        return $this;
    }

}

/**
 * myformatter ( cellvalue, options, rowObject ) return new_formated_value
 * @see
 * @abstract Bei Verwendung einer custom formatter Funktion sollte auch die custom-unformat
 * Funktion verwendet werden, damit für Bearbeitungsfunktionen wieder der Original-Wert zur Verfügung steht
 *
 * To the custom formatter are passed the following parameters:<br>
 * <pre>
 * function myformatter ( cellvalue, options, rowObject )
 * {
 *    // format the cellvalue to new format
 *    return new_formated_cellvalue;
 * }
 * </pre>
 *
 * Note the return in the function. This function should always return a value in order to work correctly.
 * <li> cellvalue : is the value to be formatted
 * <li> options : is an object containing the following element
 * <li> options : <pre>{
 *       rowId: rid,
 *       colModel: cm
 *      }</pre>
 * 		where rowId - is the id of the row colModel is the object of the properties
 *      for this column getted from colModel array of jqGrid
 * <li> rowObject - is a row data represented in the format determined from datatype option.
 *      If we have datatype: xml/xmlstring - the rowObject is xml node,
 *      provided according to the rules from xmlReader.<br>
 *      If we have datatype: json/jsonstring - the rowObject is array,
 *      provided according to the rules from jsonReader
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_formatter
 * @author rybka
 *
 */
class JqGridColModelFormatterCustom extends JsFunction {
    
}

/**
 * Unformatting
 * @abstract As mentioned in Predefined Formatter chapter all predefined types are 
 * compatible with the editing modules. This means that the numbers, links, e-mails, 
 * etc., are converted so that they can be edited correctly.<br>
 * Also the methods (like getRowData and getCell) that get data, 
 * used this unformat in order to get the original value.<br>
 * The question is:<br>
 * What to do if we use a custom formatter function and want to to have the original 
 * value back if we use editing or methods getRowData and getCell?<br>
 * The answer is:<br>
 * You can use your own custom unformatter function to do that. 
 * This function can be used in colModel<br>
 * 
 * Show image and edit image's path:<br>
 * <pre>
 * <script>
 * jQuery("#grid_id").jqGrid({
 * ...
 *    colModel: [
 *        ...
 *        {name:'price', index:'price', width:60, align:"center", editable: true, 
 *         formatter:imageFormat, 
 *         unformat:imageUnFormat
 *        },
 *        ...
 *    ]
 *    ...
 * });
 * function imageFormat( cellvalue, options, rowObject ){
 *    return '<img src="'+cellvalue+'" />';
 * }
 * function imageUnFormat( cellvalue, options, cell){
 *    return $('img', cell).attr('src');
 * }
 * </script>
 * </pre>
 * To the custom unformat function are passed the following parameters:<br>
 * <li> cellvalue - is the value to be unformated (pure text).
 * <li> options - is an object containing the following element
 *      options : <pre>{ 
 *      rowId: rid, 
 *      colModel: cm
 *      }</pre>
 *      where rowId - is the id of the row colModel is the object of the properties 
 *      for this column getted from colModel array of jqGrid
 * <li> cellobject - is a jQuery cell object. This object can be used to obtain 
 *      different things from the cell element - by example jQuery(cellobject).html() 
 *      can be used to get the html content instead of the text.
 * @param JsFunction, string Unformat
 * @author rybka
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_formatter
 *
 */
class JqGridColModelUnformatCustom extends JsFunction {
    
}
