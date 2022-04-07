<?php

/**
 * 
 * Enter description here ...
 * @author rybka
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:colmodel_options
 * @abstract 
 * 
 * <h3>ColModel API</h3>
 * The colModel property defines the individual grid columns as an array of properties. This is the most important part of the jqGrid.
 * Syntax:<br>
 * <pre>
 * jQuery("#gridid").jqGrid({
 * ...
 * colModel: [ { name:'name1', index:'index1' ...}, { ... }, ... ],
 * ...
 * });
 * </pre>
 * <p>
 * The available colModel properties are listed here, in alphabetic order. 
 * The only required property is name. The colModel options can be get or set using getColProp 
 * and setColProp <sup><a href="#fn__1" name="fnt__1" id="fnt__1" class="fn_top">1)</a></sup> 
 * methods - See <a href="/jqgridwiki/doku.php?id=wiki:methods" title="wiki:methods">methods</a>.<br>
 * 
 * As mentioned above the options in colModel can be get or sfunction are: <br>
 * <li> rowId  - the id of the row <br>
 * <li> val  - the value which will be added in the cell <br>
 * <li> rawObject - the raw object of the data row - i.e if datatype is json - array, 
 * if datatype is xml xml node. <br>
 * <li> cm - all the properties of this coumn listed in the colModel <br>
 * <li> rdata - the data row which will be inserted in the row. <br>
 * This parameter is array of type name:value, where name is the name in colModel using template the 
 * methods getColProp and setColProp. Below are options which can not be changed 
 * dynamically when the grid is constructed (If changed they do not have effect 
 * or will cause the grid errors). For some of these options there are 
 * methods available to change the value.
 * </p>
 * <ul>
 * <li> name
 * <li> width
 * <li> resizable
 * <li> label (method avail.)
 * </ul>
 * 
 */
class JqGridColModel extends JqGridBaseObject {
	
	/** @var string */
	public $label = "";
	/** @var string */
	
	public $name = "";
	/** @var string */
	
	/** @var string */
	public $index = "";
	
	/** @var bool */
	public $editable = false;
	
	/** @var string */
	public $edittype = NULL;
	
	/** @var bool */
	public $hidden = false;

	/** @var JqGridModelEditOptions */
	public $editoptions = NULL;

	/** @var JqGridModelEditRules */
	public $editrules = NULL;

	
	/**
	* @var JqGridColModelFormatter
	*/
	public $formatoptions = NULL;
	
	public $formoptions = NULL;

	/**
	 *
	 * @abstract Als formatter kann eine JS-Funktion angegeben werden, <br>
	 * deren Rückgabewert in die Zelle eingesetzt wird.<br><br>
	 * Als Parameter erhält die benutzerdefiniert Formatterfunktion: <br>
	 * <li> cellvalue
	 * <li> options
	 * <li> rowObject
	 * @var string JS-Funktionsname mit Funktionsmarkern für korrekte json_enkodierung
	 */
	public $formatter = NULL;
	
	/**
	 * 
	 * @param string $name
	 * @param mixed [optional] array|object ColModelOptions
	 */
	public function __construct($name, $properties = NULL) {
		$this->name = $name;
		$this->index = $name;
		$this->label = $name;
		$this->editoptions = new JqGridColModelEditOptions();
		
		if (!is_null($properties)) {
			$this->setProperties($properties);
		}
	}
	
	/**
	 * 
	 * @return JqGridColModelEditOptions
	 */
	public function getInstanceEditOptions()
	{
		if (!isset($this->editoptions) || ($this->editoptions instanceof JqGridColModelEditOptions)) {
			$this->editoptions = new JqGridColModelEditOptions();
		}
		return $this->editoptions;
	}
	/**
	 * 
	 * @return JqGridColModelEditRules
	 */
	public function getInstanceEditRules()
	{
		if (!isset($this->editrules) || (!$this->editrules instanceof JqGridColModelEditRules)) {
			$this->editrules = new JqGridColModelEditRules();
		}
		return $this->editrules;
	}
	
	/**
	 * 
	 * @return JqGridColModelSearchOptions
	 */
	public function getInstanceSearchOptions()
	{
		if (!isset($this->searchoptions) || ($this->searchoptions instanceof JqGridColModelsearchOptions)) {
			$this->searchoptions = new JqGridColModelSearchOptions();
		}
		return $this->searchoptions;
	}
        
	/**
	 * 
	 * @return JqGridColModelSearchRules
	 */
	public function getInstanceSearchRules()
	{
		if (!isset($this->searchrules) || ($this->searchrules instanceof JqGridColModelsearchRules)) {
			$this->searchrules = new JqGridColModelSearchRules();
		}
		return $this->searchrules;
	}
	
	/**
	 * @return JqGridColModelFormatterActions
	 */
	public function getInstanceFormatterActions() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterActions)) {
			$this->formatoptions = new JqGridColModelFormatterActions();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterCurrency
	 */
	public function getInstanceFormatterCurrency() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterCurrency)) {
			$this->formatoptions = new JqGridColModelFormatterCurrency();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterInteger
	 */
	public function getInstanceFormatterInteger() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterInteger)) {
			$this->formatoptions = new JqGridColModelFormatterInteger();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterNumber
	 */
	public function getInstanceFormatterNumber() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterNumber)) {
			$this->formatoptions = new JqGridColModelFormatterNumber();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterDate
	 */
	public function getInstanceFormatterDate() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterDate)) {
			$this->formatoptions = new JqGridColModelFormatterDate();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterEmail
	 */
	public function getInstanceFormatterEmail() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterEmail)) {
			$this->formatoptions = new JqGridColModelFormatterEmail();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterCheckbox
	 */
	public function getInstanceFormatterCheckbox() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterCheckbox)) {
			$this->formatoptions = new JqGridColModelFormatterCheckbox();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterSelect
	 */
	public function getInstanceFormatterSelect() 
	{
		if (is_null($this->formatoptions) || ($this->formatoptions instanceof JqGridColModelFormatterSelect)) {
			$this->formatoptions = new JqGridColModelFormatterSelect();
			$this->formatter = $this->formatoptions->type();
		}
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModel
	 * @param JsFunction, string
	 */
	public function getInstanceFormatterCustom($param) 
	{
		/**
		 * @var JqGridColModelFormatterCustom
		 */
		$this->formatoptions = null;
		$this->formatter = new JqGridColModelFormatterCustom($param);
		return $this;
	}
	
	/**
	 * @return JqGridColModel
	 * @param JsFunction, string
	 */
	public function getInstanceUnformatCustom($param) 
	{
		/**
		 * @var JqGridColModelUnformatCustom
		 */
		$this->unformat = new JqGridColModelUnformatCustom($param);
		return $this;
	}
	
	/**
	 * @return JqGridColModelFormatterActions
	 */
	public function getInstanceFormatterLink() 
	{
		$this->formatoptions = new JqGridColModelFormatterLink();
		$this->formatter = $this->formatoptions->type();
		return $this->formatoptions;
	}
	
	/**
	 * @return JqGridColModelFormatterActions
	 */
	public function getInstanceFormatterShowlink() 
	{
		$this->formatoptions = new JqGridColModelFormatterShowlink();
		$this->formatter = $this->formatoptions->type();
		return $this->formatoptions;
	}
	
		
	/**
	* Possible values: left, center, right.
	* @abstract Defines the alignment of the cell in the Body layer, not in header cell.<br>
	* Possible values: left, center, right.
	*
	* Default left
	* @param string
	* @return JqGrid
	*/
	public function set_align($param)
	{
		$this->align = $param; return $this;
	}
	
	/**
	 * This function add attributes to the cell during the creation of the data - i
	 * @abstract This function add attributes to the cell during the creation of the data 
	 * - i.e dynamically.<br><br>
	 *  By example all valid attributes for the table cell can be used or 
	 *  a style attribute with different properties.<br>
	 *  The function should return string.<br>
	 *  <br>
	 *  Parameters passed to this function are:<br>
	 *
	 *	<li> <em>rowId</em>  - the id of the row <br>
	 *	<li> <em>val</em>  - the value which will be added in the cell <br>
	 *	<li> <em>rawObject</em> - the raw object of the data row 
	 *  - i.e if datatype is json - array, if datatype is xml xml node.<br>
	 *	<li> <em>cm</em> - all the properties of this column listed in the colModel <br>
	 *	<li> <em>rdata</em> - the data row which will be inserted in the row.<br>
	 *  This parameter is array of type name:value, where name is the name in colModel
	 * Default null
	 * @param function
	 * @return JqGrid
	 */
	public function set_cellattr($param)
	{
		$this->cellattr = $param; return $this;
	}
	
	/**
	 * This option allow to add classes to the column
	 * @abstract This option allow to add classes to the column.<br>
	 *  If more than one class will be used a space should be set.<br>
	 *  By example classes:'class1 class2' will set a class1 and class2 
	 *  to every cell on that column.<br>
	 *  In the grid css there is a predefined class ui-ellipsis 
	 *  which allow to attach ellipsis to a particular row.<br>
	 *  Also this will work in FireFox too.
	 *
	 * Default empty string
	 * @param string
	 * @return JqGrid
	 */
	public function set_classes($param)
	{
		$this->classes = $param; return $this;
	}
	
	/**
	 * Governs format of sorttype: date
	 * @abstract Governs format of sorttype:<br>
	 * date (when datetype is set to local) and editrules {date:true} fields.<br>
	 * Determines the expected date format for that column.<br>
	 * Uses a <acronym title="Hypertext Preprocessor">PHP</acronym>-like date formatting.
	 * Currently ”/”, ”-”, and ”.” are supported as date separators.<br>
	 * Valid formats are: <br>
	 * y,Y,yyyy for four digits year <br>
	 * YY, yy for two digits year <br>
	 * m,mm for months <br>
	 * d,dd for days.<br>
	 * <br>
	 * Default ISO Date (Y-m-d)
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#array_data Array Data
	 * @param string
	 * @return JqGrid
	 */
	public function set_datefmt($param)
	{
		$this->datefmt = $param; return $this;
	}
	
	/**
	 * The default value for the search field
	 * @abstract The default value for the search field.<br>
	 * This option is used only in Custom Searching and will be set as initial search.<br>
	 * Default empty
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_searching
	 * @param string
	 * @return JqGrid
	 */
	public function set_defval($param)
	{
		$this->defval = $param; return $this;
	}
	
	/**
	 * Defines if the field is editable
	 * @abstract Defines if the field is editable.<br>
	 * This option is used in cell, inline and form modules.<br>
	 * See editing<br>
	 * Default false
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_editable($param)
	{
		$this->editable = $param; return $this;
	}
	
	/**
	 * Array of allowed options (attributes) for edittype option <a href="/jqgridwiki/d
	 * @abstract Array of allowed options (attributes) for edittype option <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules#editable"> editing</a>
	 * Default empty array
	 * @param array
	 * @return JqGrid
	 */
	public function set_editoptions($param)
	{
		$this->editoptions = $param; return $this;
	}
	
	/**
	 * sets additional rules for the editable field <a href="/jqgridwiki/doku
	 * @abstract sets additional rules for the editable field <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules#editrules"> editing</a>
	 * Default empty array
	 * @param array
	 * @return JqGrid
	 */
	public function set_editrules($param)
	{
		$this->editrules = $param; return $this;
	}
	
	/**
	 * Defines the edit type for inline and form editing Possible values: text, textare
	 * @abstract Defines the edit type for inline and form editing Possible values: text, textarea, select, checkbox, password, button, image and file.
	 *  See also <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:common_rules#edittype" title="wiki:common_rules"> editing</a>
	 * Default text
	 * @param string
	 * @return JqGrid
	 */
	public function set_edittype($param)
	{
		$this->edittype = $param; return $this;
	}
	
	/**
	 * If set to asc or desc, the column will be sorted in that direction on first sort
	 * @abstract If set to asc or desc, the column will be sorted in that direction on first sort.
	 * Subsequent sorts of the column will toggle as usual
	 * Default null
	 * @param string
	 * @return JqGrid
	 */
	public function set_firstsortorder($param)
	{
		$this->firstsortorder = $param; return $this;
	}
	
	/**
	 *  If set to true this option does not allow recalculation of the width of the col
	 * @abstract  If set to true this option does not allow recalculation of the width of the column if shrinkToFit option is set to true.
	 *  Also the width does not change if a setGridWidth method is used to change the grid width.
	 *
	 * Default false
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_fixed($param)
	{
		$this->fixed = $param; return $this;
	}
	
	/**
	 * Defines various options for form editing
	 * @abstract Defines various options for form editing.
	 *  See <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:common_rules#formoptions" title="wiki:common_rules"> Form options</a>
	 * Default empty
	 * @param array
	 * @return JqGrid
	 */
	public function set_formoptions($param)
	{
		$this->formoptions = $param; return $this;
	}
	
	/**
	 * Format options can be defined for particular columns, overwriting the defaults for particular columns
	 * @abstract Format options can be defined for particular columns, 
	 * overwriting the defaults from the language file.<br>
	 * See Formatter for more details: <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter">[link]</a>.
	 *
	 * Default none
	 * @param array
	 * @return JqGrid
	 */
	public function set_formatoptions($param)
	{
		$this->formatoptions = $param; return $this;
	}
		
	/**
	 * The predefined types (string) or custom function name that controls the format of this field.
	 * @abstract 
	 * The predefined types (string) or custom function name that controls the format of this field.<br>
	 * See Formatter <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter">[link]</a> for more details.<br>
	 * <br>
	 * 
	 * To the custom formatter function are passed the following parameters: 
	 * <li> cellvalue - is the value to be formatted
	 * <li> options - is an object containing the following element
	 * options : { rowId: rid, colModel: cm} where rowId - is the id of the row colModel is the object of the properties for this column getted from colModel array of jqGrid
	 * <li> rowObject - is a row data represented in the format determined from datatype option. If we have datatype: xml/xmlstring - the rowObject is xml node,provided according to the rules from xmlReader If we have datatype: json/jsonstring - the rowObject is array, provided according to the rules from jsonReader
	 * 
	 *
	 * Default none
	 * @example
	 * <pre>
	 * function myformatter ( cellvalue, options, rowObject )
	 * {
	 *    // format the cellvalue to new format
	 * 	  return "< href=\"?pid="+rowObject.pid+"\">"+cellvalue+"</a>";
	 * }
	 * </pre>
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_formatter
	 * @link
	 * @param mixed
	 * @return JqGridColModel
	 */
	public function set_formatter($param)
	{
		if (is_string($param)) $this->formatter = $param; 
		elseif ($param instanceof JqGridCommonFormatter) $this->formatter = $param->get_name();
		elseif ($param instanceof JqGridColModelFormatter)  $this->formatter = $param->type();
		elseif ($param instanceof JsFunction)  $this->formatter = $param;
		else {
			throw new JqGridException('Ungültiger Formatierungsparameter: '.print_r($param,1).'!');
		}
		return $this;
	}
	
	/**
	 * If set to true this column will not appear in the modal dialog where users can c
	 * @abstract If set to true this column will not appear in the modal dialog where users can choose which columns to show or hide.
	 *  See <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:show_hide_columns" title="wiki:show_hide_columns">Show/Hide Columns</a>.
	 *
	 * Default false
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_hidedlg($param)
	{
		$this->hidedlg = $param; return $this;
	}
	
	/**
	 * Defines if this column is hidden at initialization
	 * @abstract Defines if this column is hidden at initialization.
	 *
	 * Default false
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_hidden($param)
	{
		$this->hidden = $param; return $this;
	}
	
	/**
	 * Set the index name when sorting
	 * @abstract Set the index name when sorting.
	 *  Passed as sidx parameter.
	 *
	 * Default empty string
	 * @param string
	 * @return JqGrid
	 */
	public function set_index($param)
	{
		$this->index = $param; return $this;
	}
	
	/**
	 * Defines the json mapping for the column in the incoming json string
	 * @abstract Defines the json mapping for the column in the incoming json string.
	 *  See <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:retrieving_data" title="wiki:retrieving_data">Retrieving Data</a>
	 * Default none
	 * @param string
	 * @return JqGrid
	 */
	public function set_jsonmap($param)
	{
		$this->jsonmap = $param; return $this;
	}
	
	/**
	 * In case if there is no id from server, this can be set as as id for the unique r
	 * @abstract In case if there is no id from server, this can be set as as id for the unique row id.
	 *  Only one column can have this property.
	 *  If there are more than one key the grid finds the first one and the second is ignored.
	 *
	 * Default false
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_key($param)
	{
		$this->key = $param; return $this;
	}
	
	/**
	 * When colNames array is empty, defines the heading for this column
	 * @abstract When colNames array is empty, defines the heading for this column.
	 *  If both the colNames array and this setting are empty, the heading for this column comes from the name property.
	 *
	 * Default none
	 * @param string
	 * @return JqGrid
	 */
	public function set_label($param)
	{
		$this->label = $param; return $this;
	}
	
	/**
	 * Set the unique name in the grid for the column
	 * @abstract Set the unique name in the grid for the column.
	 *  This property is required.
	 *  As well as other words used as property/event names, the reserved words (which cannot be used for names) include subgrid, cb and rn.
	 *
	 * Default Required
	 * @param string
	 * @return JqGrid
	 */
	public function set_name($param)
	{
		$this->name = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Defines if the column can be re sized
	 * Default true
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_resizable($param)
	{
		$this->resizable = $param; return $this;
	}
	
	/**
	 * When used in search modules, disables or enables searching on that column
	 * @abstract When used in search modules, disables or enables searching on that column.
	 *  <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:search_config" title="wiki:search_config"> Search Configuration</a>
	 * Default true
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_search($param)
	{
		$this->search = $param; return $this;
	}
	
	/**
	 * Defines the search options used searching <a href="/jqgridwiki/doku
	 * @abstract Defines the search options used searching <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:search_config" title="wiki:search_config"> Search Configuration</a>
	 * Default empty
	 * @param array
	 * @return JqGrid
	 */
	public function set_searchoptions($param)
	{
		$this->searchoptions = $param; return $this;
	}
	
	/**
	 * Defines is this can be sorted
	 * @abstract Defines is this can be sorted.
	 *
	 * Default true
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_sortable($param)
	{
		$this->sortable = $param; return $this;
	}
	
	/**
	 * Used when datatype is local
	 * @abstract Used when datatype is local.
	 * Defines the type of the column for appropriate sorting.
	 * Possible values: <br>
	 * <li> int/integer - for sorting integer <br>
	 * <li> float/number/currency - for sorting decimal numbers <br>
	 * <li> date - for sorting date <br>
	 * <li> text - for text sorting <br>
	 * <li> function - defines a custom function for sorting.
	 *  	To this function we pass the value to be sorted and it should return a value too.<br>
	 *  
	 * See Array Data: <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#array_data" title="wiki:retrieving_data"> [link]</a>
	 * Default text
	 * @param mixed
	 * @return JqGrid
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#array_data
	 */
	public function set_sorttype($param)
	{
		$this->sorttype = $param; return $this;
	}
	
	/**
	 *  Determines the type of the element when searching
	 * @abstract  Determines the type of the element when searching.
	 *  See <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:search_config" title="wiki:search_config"> Search Configuration</a>
	 * Default text
	 * @param string
	 * @return JqGrid
	 */
	public function set_stype($param)
	{
		$this->stype = $param; return $this;
	}
	
	/**
	 * Valid only in <a href="/jqgridwiki/doku
	 * @abstract Valid only in <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:custom_searching" title="wiki:custom_searching">Custom Searching</a> and edittype : 'select' and describes the url from where we can get already-constructed select element
	 * Default empty string
	 * @param string
	 * @return JqGrid
	 */
	public function set_surl($param)
	{
		$this->surl = $param; return $this;
	}
	
	/**
	 *  Set of valid properties for the colModel
	 * @abstract  Set of valid properties for the colModel.
	 *  This option can be used if you want to overwrite a lot of default values in the column model with easy.
	 *  See cmTemplate in grid options
	 * Default  null
	 * @param  object
	 * @return JqGrid
	 */
	public function set_template($param)
	{
		$this->template = $param; return $this;
	}
	
	/**
	 *
	 * @abstract If this option is false the title is not displayed in that column when we hover a cell with the mouse
	 * Default true
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_title($param)
	{
		$this->title = $param; return $this;
	}
	
	/**
	 * Set the initial width of the column, in pixels
	 * @abstract Set the initial width of the column, in pixels.
	 *  This value currently can not be set as percentage
	 * Default 150
	 * @param number
	 * @return JqGrid
	 */
	public function set_width($param)
	{
		$this->width = $param; return $this;
	}
	
	/**
	 * Defines the xml mapping for the column in the incomming xml file
	 * @abstract Defines the xml mapping for the column in the incomming xml file.
	 *  Use a CCS specification for this See <a href="http://www.trirand.com/jqgridwiki/doku.
	 * php?id=wiki:retrieving_data" title="wiki:retrieving_data">Retrieving Data</a>
	 * Default none
	 * @param string
	 * @return JqGrid
	 */
	public function set_xmlmap($param)
	{
		$this->xmlmap = $param; return $this;
	}
	
	/**
	 *  Custom function to “unformat” a value of the cell when used in editing See <a h
	 * @abstract  Custom function to “unformat” a value of the cell when used in editing See 
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_formatter">Custom Formatter</a>.
	 *  (Unformat is also called during sort operations.
	 *  The value returned by unformat is the value compared during the sort.
	 * )
	 * Default null
	 * @param function
	 * @return JqGrid
	 */
	public function set_unformat($param)
	{
		$this->unformat = $param; return $this;
	}
	
	/**
	 * This option is valid only when <a href="/jqgridwiki/doku
	 * @abstract This option is valid only when <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing#viewgridrow">viewGridRow</a> method is activated.
	 * When the option is set to false the column is does not appear in view Form
	 * Default true
	 * @param boolean
	 * @return JqGrid
	 */
	public function set_viewable($param)
	{
		$this->viewable = $param; return $this;
	}
	
}

