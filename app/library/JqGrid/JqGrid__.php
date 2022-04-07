<?php

$JG = new JqGrid__();

class JqGrid__ {

	protected $RowedID = "GridRowedID";
	protected $PagerID = "GridPager";
	static $_uniqueID = 0;

	protected $Conf = stdClass;
	protected $JsPreloadCode = array();
	protected $JsOnloadCode = array();
	
	public function getObject() 
	{
		return getObject();
	}
	public function toJson() {
		
	}
		
	public function __construct() {
		$this->Conf = new stdClass();
	}

	public function json_encode_utf8() {
		function jqgrid_get_jsonModel(&$gridModelObj, &$JQGrid_JSON_MODEL) {
			myutf8_autoencode_array($gridModelObj);

			$JQGrid_JSON_MODEL = "";
			$gridIter = 0;
			foreach($gridModelObj as $k => $v) {
				if ($v !== null) $JQGrid_JSON_MODEL.= ($gridIter?",\n\t":"").$k.":".json_encode_utf8($v);
				++$gridIter;
			}

			$JQGrid_JSON_MODEL = str_replace("\"[%", "", $JQGrid_JSON_MODEL);
			$JQGrid_JSON_MODEL = str_replace("%]\"", "", $JQGrid_JSON_MODEL);
			return $JQGrid_JSON_MODEL;
		}
	}

	/**
	 * Set Grid-Url (Data-List-Source)
	 * @param string
	 * @return JqGrid
	 */
	public function url($url)
	{
		$this->Conf->url = $url; return $this;
	}

	/**
	 * Set Grid-Datatype (Data-List-Source)
	 * @param string options:json, xml, ...
	 * @return JqGrid
	 */
	public function datatype($datatype)
	{
		$this->Conf->datatype = $datatype; return $this;
	}

	/**
	 * Set Grid-Height (Data-List-Source)
	 * @param string|int
	 * @return JqGrid
	 */
	public function height($height)
	{
		$this->Conf->height = $height; return $this;
	}

	/**
	 * Set Grid-Width
	 * @param string|int
	 * @return JqGrid
	 */
	public function width($width)
	{
		$this->Conf->width = $width; return $this;
	}

	/**
	 * Set Grid-rownumbers, Zeilennummerierung
	 * @param bool
	 * @return JqGrid
	 */
	public function rowNum($rownumbers)
	{
		$this->Conf->rownumbers = $rownumbers; return $this;
	}

	/**
	 * Set Grid-rowList, Array mit Listengrößen
	 * @param array mit int-Werten
	 * @return JqGrid
	 * @abstract Ermöglicht dem User dynamisches Ändern der Anzahl an Records in der Liste
	 */
	public function rowList($rowList)
	{
		$this->Conf->rowList = $rowList; return $this;
	}

	/**
	 * Set Grid-pager
	 * @param
	 * @return JqGrid
	 * @abstract
	 */
	public function pager($pager)
	{
		$this->Conf->pager = $pager; return $this;
	}

	/**
	 * Set Grid-viewrecords
	 * @param bool
	 * @return JqGrid
	 * @abstract
	 */
	public function viewrecords($rowList)
	{
		$this->Conf->viewrecords = $rowList; return $this;
	}


	/**
	 * Set Grid-rowList, Array mit Listengrößen
	 * @param string optiona: asc, desc
	 * @return JqGrid
	 * @abstract Default-Sortierrichtung
	 */
	public function sortorder($sortorder)
	{
		$this->Conf->sortorder = $sortorder; return $this;
	}

	/**
	 * Set Grid-rowList, Array mit Listengrößen
	 * @param string optiona: asc, desc
	 * @return JqGrid
	 * @abstract Default-Sortierrichtung
	 */
	public function editurl($editurl)
	{
		$this->Conf->editurl = $editurl; return $this;
	}
	


	/**
	 * Set Grid-repeatitems
	 * @param bool
	 * @return JqGrid
	 * @abstract Feldzuordnung aus Record-Liste (Datalist) an Hand des Feldnamens
	 *
	 * array (
	 *         "root" => $opts["rows"],  // object-Root-Name of Datalist
	 *         "page" => 'rows'		     // Name des Objekts mit den Ergebnis-Daten
	 *        "total" => 'total',	     // Name des Objekts, dass die absolute Trefferzahl enthält
	 *      "records" => 'records'       // Name des Objekts, dass die Anzahl der Rows im Ergebnis enthält
	 *  "repeatitems" => bool            // Angabe ob, Felder wiederholt ausgegeben werden
	 */
	public function jsonReader($opts)
	{
		$this->Conf->jsonReader = array(
	      "root" => $opts["rows"],					// object-Root-Name of Datalist
	      "page" => $opts["page"],
	      "total" => $opts["total"],
	      "records" => $opts["records"],
	      "repeatitems" => $opts["repeatitems"]		// bool
		);
		return $this;
	}

	/**
	 * @param bool $grouping
	 * @param array $options
	 * @abstract options:
	 * - groupField
	 * - groupCollapse
	 * - groupColumnShow
	 * - groupOrder
	 * - groupSummary
	 * - groupSummaryOnHide
	 * - groupDataSorted
	 * - groupText
	 */
	public function grouping($grouping, $opts = array())
	{
		// bool
		$this->Conf->grouping = $grouping;

		if ($grouping) {
			$this->Conf->groupingView = array();
				
			// array mit Feldnamen
			$k = "groupField";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];
				
			// array mit bool-werten
			$k = "groupColumnShow";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // array(false), // vorher false
				
			// bool
			$k = "groupCollapse";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // true, // vorher true
				
			// array mit string-werten asc|desc
			$k = "groupOrder";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // array('asc'),
				
			// array mit bool-Werten
			$k = "groupSummary";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // array(true)
				
			// bool
			$k = "showSummaryOnHide";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // true,
				
			// bool
			$k = "groupDataSorted";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // true
				
			// array(' <span class="jqgroupVal" groupVal="{0}"><b>{0}</b></span>') // {1}
			$k = "groupText";
			if (array_key_exists($k, $opts))
			$this->Conf->groupingView[$k] = $opts[$k];  // array(' <span class="jqgroupVal" groupVal="{0}"><b>{0}</b></span>') // {1}
		} else {
			$this->Conf->groupingView = null;
		}
		return $this;
	}

	/**
	 *
	 * Einfaches Array mit Feldnamen, die Felder (Typ,Title,visible,editType etc) selbst werden im colModel konfiguriert
	 * @param array $colNames
	 * @return JqGrid
	 */
	public function colNames($colNames)
	{
		$this->Conf->colNames = $colNames; return $this;
	}

	public function colModel($colModel)
	{
		$this->Conf->colModel = $colModel; return $this;
	}

	/**
	 * Schalter, ob footerRow angezeigt werden soll
	 * @param bool
	 * @return JqGrid
	 */
	public function footerRow($footerRow)
	{
		$this->Conf->footerRow = $footerRow; return $this;
	}

	/**
	 * Schalter, ob userDataOnFooter angezeigt werden soll
	 * @param bool
	 * @return JqGrid
	 */
	public function userDataOnFooter($userDataOnFooter)
	{
		$this->Conf->userDataOnFooter = $userDataOnFooter; return $this;
	}


	/**
	 * Toolbar einbetten
	 * @param array [false] | [true,"bottom"]
	 * @return JqGrid
	 * @abstract Es muss ein Array übergeben werden, auch wenn nur false angeben wird => array(false)
	 * Um die die Toolbar anzuzeigen: => array(true, "bottom"|"top")
	 */
	public function toolbar($opt)
	{
		$this->Conf->toolbar = $opt; return $this;
	}

	/**
	 * Spaltenbreite optimal anpassen
	 * @param bool
	 * @return JqGrid
	 */
	public function forceFit($opt)
	{
		$this->Conf->forceFit = $opt; return $this; // : true,
	}

	/**
	 * Festlegen, ob Grid-Größe und -Breite durch User skaliert werden kann
	 * @param bool
	 * @return JqGrid
	 */
	public function resizable($opt)
	{
		$this->Conf->resizable = $opt; return $this; // true,
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function onafterShowForm($jsFunction)
	{
		$this->Conf->afterShowForm = (string)(new JsFunction($jsFunction)); return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function onSelectRow($jsFunction)
	{
		if ($jsFunction) $this->Conf->onSelectRow = (string)(new JsFunction($jsFunction));
		else $this->Conf->onSelectRow = NULL;
		return $this;
	}

	/**
	 * @param string RowedID
	 * @param string Js-Helper-Funktion EditRowFunction, wenn Row inline zum Bearbeiten geöffnet wird
	 * @return JqGrid
	 */
	public function onSelectRow_Edit($EditRowFunction='')
	{
		if (!$EditRowFunction) $EditRowFunction = 'null';
		return <<<EOT
			function(id)
			{					
				if(id && id!==lastsel){
					jQuery( this ).jqGrid('editRow', id, true, {$EditRowFunction});
					jQuery( this ).jqGrid('saveRow', lastsel);
					jQuery( this ).jqGrid('restoreRow', lastsel);
					jQuery( this ).jqGrid('editRow',id,true);
					lastsel=id;
				}
			}
EOT;
	}


	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function ondblClickRow($jsFunction)
	{
		if ($jsFunction) $this->Conf->ondblClickRow = (string)(new JsFunction($jsFunction));
		else $this->Conf->ondblClickRow = NULL;
		return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function ondblClickRow_Edit($jsFunction)
	{
		if ($jsFunction) $this->Conf->ondblClickRow = (string)(new JsFunction("function(id) {
			if(id && id==lastsel && $OnSelectRowEditEnabled){
				jQuery( this ).jqGrid('editRow', id, true, {$EditRowFunction});
				lastsel=id;
			}
		}"));
		else $this->Conf->ondblClickRow = NULL;
		return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function onloadComplete($jsFunction)
	{
		if ($jsFunction) $this->Conf->loadComplete = (string)(new JsFunction($jsFunction));
		else $this->Conf->loadComplete = NULL;
		return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function ongridComplete($jsFunction)
	{
		if ($jsFunction) $this->Conf->gridComplete = (string)(new JsFunction($jsFunction));
		else $this->Conf->gridComplete = NULL;
		return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function onloadError($jsFunction)
	{
		if ($jsFunction) $this->Conf->loadError = (string)(new JsFunction($jsFunction));
		else $this->Conf->loadError = NULL;
		return $this;
	}

	/**
	 * @param string jsFunction
	 * @return JqGrid
	 */
	public function onloadError_Alert()
	{
		$this->Conf->loadError = (string)(new JsFunction("function(xhr, status, error) {
			alert('status:'+status+'\n'+error);
		}"));
	}

	/**
	 * @param string FormatType/DataType (integer, number, currency, date)
	 * @param array  FormatOptions
	 * @return JqGrid
	 */
	public function formatter($type, $opts)
	{
		if (!isset($this->Conf->formatter)) $this->Conf->formatter = new stdClass();
		if (!isset($this->Conf->formatter->{$type})) $this->Conf->formatter->{$type} = array();

		foreach($opts as $k => $v) $this->Conf->formatter->{$type}->{$k} = $v;
		return $this;
	}

	/**
	 * @return JqGrid
	 */
	public function formatter_default()
	{
		if (!isset($this->Conf->formatter)) $this->Conf->formatter = new stdClass();
		$this->Conf->formatter->integer = array(
			'thousandsSeparator'=> '', 
			'defaultValue'=> "0");

		$this->Conf->number = array(
			'decimalSeparator'=>',', 
			'thousandsSeparator'=> '', 
			'decimalPlaces'=> 2, 
			'defaultValue'=> '0.00');

		$this->Conf->currency = array(
			'decimalSeparator'=> ',', 
			'thousandsSeparator'=> '', 
			'decimalPlaces'=> 2, 
			'prefix'=> '', 
			'suffix'=> '', 
			'defaultValue'=> '0.00');

		$this->Conf->date = array(
			'dayNames'=> array(
					      "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa",
					      "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"
		),
			'monthNames'=> array(
					      "Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez",
					      "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"
		),
			'AmPm'=> array("am","pm","AM","PM"),
			'S' => (string)(new JsFunction('function (j) {
						  return j < 11 || j > 13 ? [\'st\', \'nd\', \'rd\', \'th\'][Math.min((j - 1) % 10, 3)] : \'th\'}')),
			'srcformat'=> 'Y-m-d',
			'newformat'=> 'd.m.Y',
			'masks'=> array(
				  'ISO8601Long'=> 'Y-m-d H:i:s',
				  'ISO8601Short'=> 'Y-m-d',
				  'ShortDate'=> 'n/j/Y',
				  'LongDate'=> 'l, F d, Y',
				  'FullDateTime'=> 'l, F d, Y g:i:s A',
				  'MonthDay'=> 'F d',
				  'ShortTime'=> 'g:i A',
				  'LongTime'=> 'g:i:s A',
				  'SortableDateTime'=> 'Y-m-d\\TH:i:s',
				  'UniversalSortableDateTime'=> 'Y-m-d H:i:sO',
				  'YearMonth'=> 'F, Y'
		),
			'reformatAfterEdit'=> false
		);
		return $this;
	}
	
	
	
	
	/**
	* This option allow to set global ajax settings for the grid when we request data.
	* @abstract This option allow to set global ajax settings for the grid when we request data.
	* Note that with this option is possible to overwrite all current ajax setting
	* in the grid including the error, complete and beforeSend events.
	* Default empty object
	* Changeable Yes
	* @param object
	* @return JqGrid
	*/
	public function ajaxGridOptions($param)
	{
		$this->param->ajaxSelectOptions = $param; return $this;
	}
	
	/**
	 * This option allow to set global ajax settings for
	 * @abstract This option allow to set global ajax settings for the select element
	 * when the select is obtained via dataUrl option in editoptions or searchoptions objects
	 * Default empty object
	 * Changeable Yes
	 * @param object
	 * @return JqGrid
	 */
	public function ajaxSelectOptions($param)
	{
		$this->param->ajaxSelectOptions = $param; return $this;
	}
	
	
	/**
	 * The class that is used for alternate rows.
	 * @abstract The class that is used for alternate rows.
	 * You can construct your own class and replace this value.
	 * This option is valid only if altRows options is set to true
	 * Default ui-priority-secondary
	 * Changeable Yes. Requires reload
	 * @param string
	 * @return JqGrid
	 */
	public function altclass($param)
	{
		$this->params->altclass = $param; return $this;
	}
	
	/**
	 * Set a zebra-striped grid
	 * @abstract Set a zebra-striped grid<br>
	 * Schalter, ob Zeilen abwechselnd gestalter werden sollen, 
	 * wird aktuell mit opacity umgesetzt, ist nicht so schön, aber OK.
	 * Individuelle Gestaltung ermöglicht die Option JqGrid::altclass
	 * 
	 * Default  false
	 * Changeable  Yes. After reload
	 * @see JqGrid::altclass
	 * @param boolean
	 * @return JqGrid
	 */
	public function altRows($param)
	{
		$this->params->altRows = $param; return $this;
	}
	
	/**
	 * When set to true encodes (html encode) the incoming (from server) and posted data (from editing modules).
	 * @abstract When set to true encodes (html encode) the incoming (from server) and posted data (from editing modules).
	 * By example &lt; will be converted to &amp;lt;
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function autoencode($param)
	{
		$this->params->autoencode = $param; return $this;
	}
	
	/**
	 * When set to true, the grid width is recalculated
	 * @abstract  When set to true, the grid width is recalculated automatically to the width of the parent element.
	 * This is done only initially when the grid is created.
	 * In order to resize the grid when the parent element changes width you should apply custom code
	 * and use a setGridWidth method for this purpose
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function autowidth($param)
	{
		$this->params->autowidth = $param; return $this;
	}
	
	/**
	 * Defines the Caption layer for the grid. This capti
	 * @abstract Defines the Caption layer for the grid. This caption appears above the Header layer.
	 * If the string is empty the caption does not appear.
	 * Default empty string
	 * Changeable  No.Method avail.
	 * @param string
	 * @return JqGrid
	 */
	public function caption($param)
	{
		$this->params->caption = $param; return $this;
	}
	
	/**
	 * This option determines the padding + border width of the cell.
	 * @abstract This option determines the padding + border width of the cell.
	 * Usually this should not be changed, but if custom changes to td element are made
	 * in the grid css file this will need to be changed. The initial value of 5 means paddingLef?
	 * 2+paddingRight?2+borderLeft?1=5
	 * Default 5
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function cellLayout($param)
	{
		$this->params->cellLayout = $param; return $this;
	}
	
	/**
	 * Enables (disables) cell editing. See Cell Editing
	 * @abstract Enables (disables) cell editing. See Cell Editing for more details
	 * Default false
	 * Changeable  Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function cellEdit($param)
	{
		$this->params->cellEdit = $param; return $this;
	}
	
	/**
	 * Determines where the contents of the cell are save
	 * @abstract Determines where the contents of the cell are saved: 'remote' or 'clientArray'.
	 * See Cell Editing for more details
	 * Default 'remote'
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function cellsubmit($param)
	{
		$this->params->cellsubmit = $param; return $this;
	}
	
	/**
	 * the url where the cell is to be saved. See Cell Ed
	 * @abstract the url where the cell is to be saved. See Cell Editing for more details
	 * Default null
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function cellurl($param)
	{
		$this->params->cellurl = $param; return $this;
	}
	
	/**
	 *  Defines a set of properties which overwrite the d
	 * @abstract  Defines a set of properties which overwrite the default values in column Model.
	 * By example if you want to make all columns not sortable,
	 * only one propery here can be writen instead in all columns in column model
	 * Default  null
	 * Changeable  No
	 * @param  object
	 * @return JqGrid
	 */
	public function cmTemplate($param)
	{
		$this->params->cmTemplate = $param; return $this;
	}
	
	/**
	 * Array which describes the parameters of the column
	 * @abstract Array which describes the parameters of the columns.
	 * This is the most important part of the grid.
	 * For a full description of all valid values see colModel API
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:colmodel_options">[link]</a>.
	 * Default empty array
	 * Changeable  No
	 * @param array
	 * @return JqGrid
	 */
	public function colModel($param)
	{
		$this->params->colModel = $param; return $this;
	}
	
	/**
	 * An array in which we place the names of the columns.
	 * @abstract An array in which we place the names of the columns.
	 * This is the text that appears in the head of the grid (Header layer).
	 * The names are separated with commas. Note that the number of element
	 * in this array should be equal of the number elements in the colModel array.
	 * Default empty array[]
	 * Changeable No
	 * @param array[]
	 * @return JqGrid
	 */
	public function colNames($param)
	{
		$this->params->colNames = $param; return $this;
	}
	
	/**
	 * A array that store the local data passed to the gr
	 * @abstract A array that store the local data passed to the grid.
	 * You can directly point to this variable in case you want to load a array data.
	 * It can replace addRowData method which is slow on relative big data
	 * Default  empty array
	 * Changeable  Yes
	 * @param array
	 * @return JqGrid
	 */
	public function data($param)
	{
		$this->params->data = $param; return $this;
	}
	
	/**
	 * The string of data when datatype parameter is set
	 * @abstract The string of data when datatype parameter is set to xmlstring
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#xml_string">[link]</a>
	 * or jsonstring
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#json_string">[link]</a>
	 * Default null
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function datastr($param)
	{
		$this->params->datastr = $param; return $this;
	}
	
	/**
	 * Defines what type of information to expect to represent data in the grid.
	 * @abstract Defines what type of information to expect to represent data in the grid.
	 * Valid options are:
	 * xml - we expect xml data;
	 * xmlstring - we expect xml data as string;
	 * json - we expect JSON data;
	 * jsonstring - we expect JSON data as string;
	 * local - we expect data defined at client side (array data);
	 * javascript - we expect javascript as data;
	 * function - custom defined function for retrieving data.
	 * See <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:colmodel_options"> colModel API</a>
	 * and <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data"> Retrieving Data</a>
	 * Default xml
	 * Changeable  Yes
	 * @param string
	 * @return JqGrid
	 */
	public function datatype($param)
	{
		$this->params->datatype = $param; return $this;
	}
	
	/**
	 * This option should be set to true if a event or a
	 * @abstract This option should be set to true if a event or a plugin is attached to the table cell.
	 * The option uses jQuery empty for the the row and all its children elements.
	 * This have of course speed overhead, but prevent memory leaks
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function deepempty($param)
	{
		$this->params->deepempty = $param; return $this;
	}
	
	/**
	 * Applicable only when we use datatype : local. Dese
	 * @abstract Applicable only when we use datatype : local.
	 * Deselects currently-selected row(s) when a sort is applied.
	 * Default true
	 * Changeable  Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function deselectAfterSort($param)
	{
		$this->params->deselectAfterSort = $param; return $this;
	}
	
	/**
	 * Determines the language direction in grid. The def
	 * @abstract Determines the language direction in grid. The default is “ltr” (Left To Right language).
	 * When set to “rtl”  (Right To Left) the grid automatically do the needed.
	 * It is important to note that in one page we can have two (or more) grids where the one can
	 * have direction “ltr” while the other can have direction “rtl”. This option work only in FireFox 3.x versions
	 * and Internet Explorer versions &gt;=6. Currently Safai and Google Chrome does not support fully direction “rtl”.
	 * Also the same apply to Opera browsers. The most common problem in FireFox is that the default settings
	 * of the browser does not support RTL.
	 *
	 * In order change this see HOW TO section in this chapter . <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:howto_grid_base">[link]</a>.
	 * Default ltr
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function direction($param)
	{
		$this->params->direction = $param; return $this;
	}
	
	/**
	 * Defines the url for inline and form editing.
	 * @abstract Defines the url for inline and form editing.
	 * Default null
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function editurl($param)
	{
		$this->params->editurl = $param; return $this;
	}
	
	/**
	 *  Display the information when the returned (or the
	 * @abstract  Display the information when the returned (or the current) number of records is zero.
	 * This option is valid only if viewrecords option is set to true.
	 * Default see lang file
	 * Changeable Yes
	 * @param  string
	 * @return JqGrid
	 */
	public function emptyrecords($param)
	{
		$this->params->emptyrecords = $param; return $this;
	}
	
	/**
	 * when true, the treeGrid is expanded and/or collaps
	 * @abstract when true, the treeGrid is expanded and/or collapsed when we click on the
	 * text of the expanded column, not only on the image
	 * Default true
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function ExpandColClick($param)
	{
		$this->params->ExpandColClick = $param; return $this;
	}
	
	/**
	 * indicates which column (name from colModel) should
	 * @abstract indicates which column (name from colModel) should be used to expand the tree grid.
	 * If not set the first one is used. Valid only when treeGrid option is set to true.
	 * Default null
	 * Changeable  No
	 * @param string
	 * @return JqGrid
	 */
	public function ExpandColumn($param)
	{
		$this->params->ExpandColumn = $param; return $this;
	}
	
	/**
	 * If set to true this will place a footer table with
	 * @abstract If set to true this will place a footer table with one row below the gird records
	 * and above the pager. The number of columns equal of these from colModel
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function footerrow($param)
	{
		$this->params->footerrow = $param; return $this;
	}
	
	/**
	 * If set to true, and resizing the width of a column
	 * @abstract If set to true, and resizing the width of a column, the adjacent column (to the right)
	 * will resize so that the overall grid width is maintained (e.g., reducing the width of column 2 by 30px
	 * will increase the size of column 3 by 30px). In this case there is no horizontal scrolbar.<br>
	 * Note: this option is not compatible with shrinkToFit option - i.e if shrinkToFit is set to false, forceFit is ignored.
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function forceFit($param)
	{
		$this->params->forceFit = $param; return $this;
	}
	
	/**
	 * Determines the current state of the grid (i.e. whe
	 * @abstract Determines the current state of the grid (i.e. when used with hiddengrid, hidegrid and caption options).
	 * Can have either of two states: 'visible' or 'hidden'
	 * Default visible
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function gridstate($param)
	{
		$this->params->gridstate = $param; return $this;
	}
	
	/**
	 * In the previous versions of jqGrid including 3.4.X
	 * @abstract In the previous versions of jqGrid including 3.4.X,reading a relatively big data
	 * sets (Rows &gt;=100 ) caused speed problems. The reason for this was that as every cell
	 * was inserted into the grid we applied about 5-6 jQuery calls to it. Now this problem is
	 * resolved; we now insert the entry row at once with a jQuery append. The result is impressive - about 3-5 times faster.
	 * What will be the result if we insert all the data at once? Yes, this can be done with a help of gridview option
	 * when set to true. The result is a grid that is 5 to 10 times faster. Of course when this option is set
	 * to true we have some limitations. If set to true we can not use treeGrid, subGrid, or afterInsertRow event.
	 * If you do not use these three options in the grid you can set this option to true and enjoy the speed.
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function gridview($param)
	{
		$this->params->gridview = $param; return $this;
	}
	
	/**
	 *  Enables grouping in grid. Please refer <a href="/
	 * @abstract  Enables grouping in grid. Please refer grouping page
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:grouping">[link]</a>.
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function grouping($param)
	{
		$this->params->grouping = $param; return $this;
	}
	
	/**
	 * If the option is set to true the title attribute i
	 * @abstract If the option is set to true the title attribute is added to the column headers
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function headertitles($param)
	{
		$this->params->headertitles = $param; return $this;
	}
	
	/**
	 * The height of the grid. Can be set as number (in t
	 * @abstract The height of the grid. Can be set as number (in this case we mean pixels) or as percentage (only 100% is acceped) or value of auto is acceptable.
	 * Default 150
	 * Changeable No.Method avail.
	 * @param mixed
	 * @return JqGrid
	 */
	public function height($param)
	{
		$this->params->height = $param; return $this;
	}
	
	/**
	 * If set to true the grid initially is hidden. The d
	 * @abstract If set to true the grid initially is hidden. The data is not loaded (no request is sent) and only the caption layer is shown. When the show/hide button is clicked the first time to show grid, the request is sent to the server, the data is loaded, and grid is shown. From this point we have a regular grid. This option has effect only if the caption property is not empty and the hidegrid property (see below) is set to true.
	 * Default false
	 * Changeable  No
	 * @param boolean
	 * @return JqGrid
	 */
	public function hiddengrid($param)
	{
		$this->params->hiddengrid = $param; return $this;
	}
	
	/**
	 * Enables or disables the show/hide grid button, whi
	 * @abstract Enables or disables the show/hide grid button, which appears on the right side of the Caption layer. Takes effect only if the caption property is not an empty string.
	 * Default true
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function hidegrid($param)
	{
		$this->params->hidegrid = $param; return $this;
	}
	
	/**
	 * When set to false the mouse hovering is disabled i
	 * @abstract When set to false the mouse hovering is disabled in the grid data rows.
	 * Default true
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function hoverrows($param)
	{
		$this->params->hoverrows = $param; return $this;
	}
	
	/**
	 * When set this string is added as prefix to the id
	 * @abstract When set this string is added as prefix to the id of the row when it is build
	 * Default empty
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function idPrefix($param)
	{
		$this->params->idPrefix = $param; return $this;
	}
	
	/**
	 * By default the local searching is case sensitive.
	 * @abstract By default the local searching is case sensitive.
	 * To make the local search and sorting not case sensitive set this options to true
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function ignoreCase($param)
	{
		$this->params->ignoreCase = $param; return $this;
	}
	
	/**
	 * an array used to add content to the data posted to
	 * @abstract an array used to add content to the data posted to the server when we are in inline editing.
	 * Default {}
	 * Changeable Yes
	 * @param empty object
	 * @return JqGrid
	 */
	public function inlineData($param)
	{
		$this->params->inlineData = $param; return $this;
	}
	
	/**
	 * Array which describes the structure of the expecte
	 * @abstract Array which describes the structure of the expected json data.
	 * For a full description and default setting, see Retrieving Data JSON Data <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#json_data"> [link]</a>
	 * Default
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function jsonReader($param)
	{
		$this->params->jsonReader = $param; return $this;
	}
	
	/**
	 * Readonly property. Determines the total number of
	 * @abstract Readonly property. Determines the total number of pages returned from the request.
	 * Default 0
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function lastpage($param)
	{
		$this->params->lastpage = $param; return $this;
	}
	
	/**
	 * Readonly property. Determines the index of last so
	 * @abstract Readonly property. Determines the index of last sorted column beginning from 0
	 * Default 0
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function lastsort($param)
	{
		$this->params->lastsort = $param; return $this;
	}
	
	/**
	 * If this flag is set to true, the grid loads the da
	 * @abstract If this flag is set to true, the grid loads the data from the server
	 * only once (using the appropriate datatype). After the first request the datatype
	 * parameter is automatically changed to local and all further manipulations are done
	 * on the client side. The functions of the pager (if present) are disabled.
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function loadonce($param)
	{
		$this->params->loadonce = $param; return $this;
	}
	
	/**
	 * The text which appear when requesting and sorting
	 * @abstract The text which appear when requesting and sorting data.
	 * This parameter is located in language file
	 * Default Loading…
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function loadtext($param)
	{
		$this->params->loadtext = $param; return $this;
	}
	
	/**
	 *  This option controls what to do when an ajax oper
	 * @abstract  This option controls what to do when an ajax operation is in progress.<br>
	 * <strong>disable</strong> - disables the jqGrid progress indicator. This way you can use your own indicator.<br>
	 * <strong>enable</strong> (default) - enables “Loading” message in the center of the grid. <br>
	 *
	 * <strong>block</strong> - enables the “Loading” message and blocks all actions
	 * in the grid until the ajax request is finished. Note that this disables paging,
	 * sorting and all actions on toolbar, if any.
	 * Default enable
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function loadui($param)
	{
		$this->params->loadui = $param; return $this;
	}
	
	/**
	 * Defines the type of request to make (“POST” or “GE
	 * @abstract Defines the type of request to make (“POST” or “GET”)
	 * Default GET
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function mtype($param)
	{
		$this->params->mtype = $param; return $this;
	}
	
	/**
	 * This parameter have sense only multiselect option
	 * @abstract This parameter have sense only multiselect option is set to true.
	 * Defines the key which will be pressed when we make multiselection.
	 * The possible values are:<br>
	 * <li> <strong>shiftKey</strong> - the user should press Shift Key
	 * <li> <strong>altKey</strong> - the user should press Alt Key
	 * <li> <strong>ctrlKey</strong> - the user should press Ctrl Key<br>
	 * Default empty string
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function multikey($param)
	{
		$this->params->multikey = $param; return $this;
	}
	
	/**
	 * This option works only when multiselect = true. Wh
	 * @abstract This option works only when multiselect = true. When multiselect is set to true,
	 * clicking anywhere on a row selects that row; when multiboxonly is also set to true,
	 * the multiselection is done only when the checkbox is clicked (Yahoo style).
	 * Clicking in any other row (suppose the checkbox is not clicked) deselects
	 * all rows and the current row is selected.
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function multiboxonly($param)
	{
		$this->params->multiboxonly = $param; return $this;
	}
	
	/**
	 * If this flag is set to true a multi selection of r
	 * @abstract If this flag is set to true a multi selection of rows is enabled.
	 * A new column at left side is added. Can be used with any datatype option.
	 * Default false
	 * Changeable  No. see HOWTO
	 * @param boolean
	 * @return JqGrid
	 */
	public function multiselect($param)
	{
		$this->params->multiselect = $param; return $this;
	}
	
	/**
	 * Determines the width of the multiselect column if
	 * @abstract Determines the width of the multiselect column if multiselect is set to true.
	 * Default 20
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function multiselectWidth($param)
	{
		$this->params->multiselectWidth = $param; return $this;
	}
	
	/**
	 * Set the initial number of page when we make the re
	 * @abstract Set the initial number of page when we make the request.
	 * This parameter is passed to the url for use by the server routine retrieving the data
	 * Default 1
	 * Changeable Yes
	 * @param integer
	 * @return JqGrid
	 */
	public function page($param)
	{
		$this->params->page = $param; return $this;
	}
	
	/**
	 * Defines that we want to use a pager bar to navigat
	 * @abstract Defines that we want to use a pager bar to navigate through the records.
	 * This must be a valid html element; in our example we gave the div the id of “pager”,
	 * but any name is acceptable. Note that the Navigation layer (the “pager” div) can be
	 * positioned anywhere you want, determined by your html; in our example we specified
	 * that the pager will appear after the Table Body layer. The valid calls can be
	 * (using our example) 'pager', '#pager', jQuery('#pager'). I recommend to use the second one.
	 *
	 * See <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:pager" class="wikilink1" title="wiki:pager">Pager</a>
	 * Default empty string. Currently only one pagebar is possible.
	 * Changeable No
	 * @param mixed
	 * @return JqGrid
	 */
	public function pager($param)
	{
		$this->params->pager = $param; return $this;
	}
	
	/**
	 *  Determines the position of the pager in the grid.
	 * @abstract  Determines the position of the pager in the grid.
	 * By default the pager element when created is divided in 3 parts
	 * (one part for pager, one part for navigator buttons and one part for record information)
	 * Default center
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function pagerpos($param)
	{
		$this->params->pagerpos = $param; return $this;
	}
	
	/**
	 * Determines if the Pager buttons should be shown if
	 * @abstract Determines if the Pager buttons should be shown if pager is available.
	 * Also valid only if pager is set correctly. The buttons are placed in the pager bar.
	 * Default true
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function pgbuttons($param)
	{
		$this->params->pgbuttons = $param; return $this;
	}
	
	/**
	 * Determines if the input box, where the user can ch
	 * @abstract Determines if the input box, where the user can change the number of requested page,
	 * should be available. The input box appear in the pager bar.
	 * Default true
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function pginput($param)
	{
		$this->params->pginput = $param; return $this;
	}
	
	/**
	 * Show information about current page status. The fi
	 * @abstract Show information about current page status.
	 * The first value is the current loaded page.
	 * The second value is the total number of pages
	 * Default See lang file
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function pgtext($param)
	{
		$this->params->pgtext = $param; return $this;
	}
	
	/**
	 *  Default values prmNames: <br>
	
	 {page:“page”,ro
	 * @abstract  Default values prmNames: <br>
	
	 {page:“page”,rows:“rows”, sort: “sidx”, order: “sord”, search:“_search”, nd:“nd”, id:“id”, oper:“oper”, editoper:“edit”, addoper:“add”, deloper:“del”, subgridid:“id”, npage: null, totalrows:“totalrows”} <br>
	 Customizes names of the fields sent to the server on a Post. For example, with this setting, you can change the sort order element from “sidx” to “mysort”: prmNames: {sort: “mysort”}. The string that will be posted to the server will then be myurl.php?page=1&amp;rows=10&amp;mysort=myindex&amp;sord=asc  rather than myurl.php?page=1&amp;rows=10&amp;sidx=myindex&amp;sord=asc <br>
	 When some parameter is set to null they will be not sended to the server. By example if we set prmNames: { nd:null} the nd parameter will not be sended.  For npage option see scroll option. <br>
	 The options meaning the following <br>
	
	 page - the the requested page - default value page, <br>
	 rows - the number of rows requested - default value rows, <br>
	 sort - the sorting column - default value sidx, <br>
	 order - the sort order default value sord, <br>
	 search - the search indicator - default value _search, <br>
	 nd - the time passed to the request (for <acronym title="Internet Explorer">IE</acronym> browsers
	 not to cache the request) - default value nd, <br>
	
	 id - the name of the id when post data in editing modules - default value id, <br>
	 oper - the operation parameter - default value oper, <br>
	 editoper - the name of operation when the data is posted in edit mode - default value edit, <br>
	 addoper - the name of operation when the data is posted in add mode - default value add,<br>
	 deloper - the name of operation when the data is posted in delete mode - default value del <br>
	 totalrows - the name of the total rows to be obtained from server - see rowTotal - default value totalrows <br>
	
	 subgridid - the name passed when we click to load data in subgrid - default value id
	 * Default  none
	 * Changeable Yes
	 * @param array
	 * @return JqGrid
	 */
	public function prmNames($param)
	{
		$this->params->prmNames = $param; return $this;
	}
	
	/**
	 * This array is passed directly to the url. This is
	 * @abstract This array is passed directly to the url.
	 * This is associative array and can be used this way:
	 * {name1:value1…}.
	 * See <acronym title="Application Programming Interface">API</acronym> methods for manipulation.
	 * Default empty array
	 * Changeable Yes
	 * @param array
	 * @return JqGrid
	 */
	public function postData($param)
	{
		$this->params->postData = $param; return $this;
	}
	
	/**
	 * Readonly property. Determines the exactly number o
	 * @abstract Readonly property. Determines the exactly number of rows in the grid.
	 * Do not mix this with records parameter. Instead that in most cases they are equal
	 * there is a case where this is not true. By example you define rowNum parameter 15,
	 * but you return from server records parameter = 20, then the records parameter will be 20,
	 * the reccount parameter will be 15, and in the grid you will have 15 records.
	 * Default 0
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function reccount($param)
	{
		$this->params->reccount = $param; return $this;
	}
	
	/**
	 * Determines the position of the record information
	 * @abstract Determines the position of the record information in the pager.
	 * Can be left, center, right
	 * Default right
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function recordpos($param)
	{
		$this->params->recordpos = $param; return $this;
	}
	
	/**
	 * Readonly property. Determines the number of return
	 * @abstract Readonly property. Determines the number of returned records in grid from the request
	 * Default none
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function records($param)
	{
		$this->params->records = $param; return $this;
	}
	
	/**
	 * Represent information that can be shown in the pag
	 * @abstract Represent information that can be shown in the pager.
	 * Also this option is valid if viewrecords option is set to true.
	 * This text appear only if the tottal number of recreds is greater then zero.
	 * In order to show or hide some information the items in {} mean the following:
	 * {0} the start position of the records depending on page number and number of requested records;
	 * {1} - the end position {2} - total records returned from the data
	 * Default  see lang file
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function recordtext($param)
	{
		$this->params->recordtext = $param; return $this;
	}
	
	/**
	 * Assigns a class to columns that are resizable so t
	 * @abstract Assigns a class to columns that are resizable so that we can show a resize handle
	 * only for ones that are resizable
	 * Default empty string
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function resizeclass($param)
	{
		$this->params->resizeclass = $param; return $this;
	}
	
	/**
	 * An array to construct a select box element in the
	 * @abstract An array to construct a select box element in the pager in which
	 * we can change the number of the visible rows. When changed during the execution,
	 * this parameter replaces the rowNum parameter that is passed to the url.
	 * If the array is empty the element does not appear in the pager.
	 * Typical you can set this like [10,20,30]. If the rowNum parameter is set to 30
	 * then the selected value in the select box is 30.
	 * Default empty array - []
	 * Changeable No
	 * @param array[]
	 * @return JqGrid
	 */
	public function rowList($param)
	{
		$this->params->rowList = $param; return $this;
	}
	
	/**
	 *  If this option is set to true, a new column at le
	 * @abstract  If this option is set to true, a new column at left of the grid is added.
	 * The purpose of this column is to count the number of available rows, beginning from 1.
	 * In this case colModel is extended automatically with new element with name - 'rn'.
	 * Also, be careful not to use the name 'rn' in colModel
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function rownumbers($param)
	{
		$this->params->rownumbers = $param; return $this;
	}
	
	/**
	 * Sets how many records we want to view in the grid.
	 * @abstract Sets how many records we want to view in the grid.
	 * This parameter is passed to the url for use by the server routine retrieving the data.
	 * Note that if you set this parameter to 10 (i.e. retrieve 10 records) and your server return 15 then only 10 records will be loaded.
	 * <del>Set this parameter to <strong>-1</strong>  (unlimited) to disable this checking.</del>
	 * Default 20
	 * Changeable Yes
	 * @param integer
	 * @return JqGrid
	 */
	public function rowNum($param)
	{
		$this->params->rowNum = $param; return $this;
	}
	
	/**
	 *  When set this parameter can instruct the server t
	 * @abstract  When set this parameter can instruct the server to load the total number of rows needed to work on.
	 * Note that rowNum determines the total records displayed in the grid, while rowTotal the total rows on
	 * which we operate. When this parameter is set we send a additional parameter to server named totalrows.
	 * You can check for this parameter and if it is available you can replace the rows parameter with this one.
	 * Mostly this parameter can be combined wit loadonce parameter set to true.
	 * Default null
	 * Changeable  Yes
	 * @param  integer
	 * @return JqGrid
	 */
	public function rowTotal($param)
	{
		$this->params->rowTotal = $param; return $this;
	}
	
	/**
	 * Determines the width of the row number column if r
	 * @abstract Determines the width of the row number column if rownumbers option is set to true.
	 * Default 25
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function rownumWidth($param)
	{
		$this->params->rownumWidth = $param; return $this;
	}
	
	/**
	 *  This is read only property and is used in Inline
	 * @abstract  This is read only property and is used in Inline and cell editing modules to store the data,
	 * before editing the row or cell.<br>
	 * See <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:cell_editing">Cell editing</a>
	 * and <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing">Inline editing</a>.
	 * Default empty array
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function savedRow($param)
	{
		$this->params->savedRow = $param; return $this;
	}
	
	/**
	 * <del>This property contain the searched data in pa
	 * @abstract <del>This property contain the searched data in pair name:value.</del>
	 * Default <del>empty array{}</del>
	 * Changeable <del>Yes</del>
	 * @param <del>array {}</del>
	 * @return JqGrid
	 */
	public function <del>searchdata</del>($param)
	{
		$this->params-><del>searchdata</del> = $param; return $this;
	}
	
	/**
	 * Creates dynamic scrolling grids. When enabled, the
	 * @abstract Creates dynamic scrolling grids. When enabled, the pager elements are disabled
	 * and we can use the vertical scrollbar to load data. When set to true the grid will always
	 * hold all the items from the start through to the latest point ever visited. <br>
	 When scroll is set to value  (eg 1), the grid will just hold the visible lines. This allow us
	 to load the data at portions whitout to care about the memory leaks. Additionally this we have
	 optional extension to the server protocol: npage (see prmNames array). If you set the npage
	 option in prmNames, then the grid will sometimes request more than one page at a time,
	 if not it will just perform multiple gets.
	 * Default false
	 * Changeable No
	 * @param boolean or <br>
	
	 integer
	 * @return JqGrid
	 */
	public function scroll($param)
	{
		$this->params->scroll = $param; return $this;
	}
	
	/**
	 * Determines the width of the vertical scrollbar.
	 * @abstract Determines the width of the vertical scrollbar. Since different browsers interpret
	 * this width differently (and it is difficult to calculate it in all browsers) this can be changed.
	 * Default 18
	 * Changeable No.Method avail.
	 * @param integer
	 * @return JqGrid
	 */
	public function scrollOffset($param)
	{
		$this->params->scrollOffset = $param; return $this;
	}
	
	/**
	 * This control the timeout handler when scroll is set to 1.
	 * @abstract This control the timeout handler when scroll is set to 1.
	 * Default 200
	 * Changeable Yes
	 * @param integer <br>
	 (milliseconds)
	 * @return JqGrid
	 */
	public function scrollTimeout($param)
	{
		$this->params->scrollTimeout = $param; return $this;
	}
	
	/**
	 * When enabled, selecting a row with setSelection sc
	 * @abstract When enabled, selecting a row with setSelection scrolls the grid so that the selected row is visible.
	 * This is especially useful when we have a verticall scrolling grid and we use form editing with
	 * navigation buttons (next or previous row). On navigating to a hidden row, the grid scrolls so the
	 * selected row becomes visible.
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function scrollrows($param)
	{
		$this->params->scrollrows = $param; return $this;
	}
	
	/**
	 * This options is read only. Determines the currentl
	 * @abstract This options is read only. Determines the currently selected rows when multiselect is set to true.
	 * This is one dimensional array and the values in the array correspond to the selected id's in the grid.
	 * Default empty array []
	 * Changeable No
	 * @param array-[]
	 * @return JqGrid
	 */
	public function selarrrow($param)
	{
		$this->params->selarrrow = $param; return $this;
	}
	
	/**
	 * This option is read only. Contain the id of the la
	 * @abstract This option is read only. Contain the id of the last selected row.
	 * If you sort or apply a pagging this options is set to null
	 * Default null
	 * Changeable No
	 * @param string
	 * @return JqGrid
	 */
	public function selrow($param)
	{
		$this->params->selrow = $param; return $this;
	}
	
	/**
	 * This option describes the type of calculation of t
	 * @abstract This option describes the type of calculation of the initial width
	 * of each column against with the width of the grid. If the value is true and
	 * the value in width option is set then: Every column width is scaled according
	 * to the defined option width. Example: if we define two columns with a width
	 * of 80 and 120 pixels, but want the grid to have a 300 pixels -
	 * then the columns are recalculated as follow:<br>
	 * 1- column = 300(new width)/200(sum of all width)*80(column width) = 120
	 * and 2 column = 300/200*120 = 180. The grid width is 300px.
	 * If the value is false and the value in width option is set then:
	 * The width of the grid is the width set in option. The column width are not recalculated and
	 * have the values defined in colModel. If integer is set, the width is calculated according to it.
	 * Default true
	 * Changeable No
	 * @param boolean or <br>
	
	 integer
	 * @return JqGrid
	 */
	public function shrinkToFit($param)
	{
		$this->params->shrinkToFit = $param; return $this;
	}
	
	/**
	 * When enabled this option allow column reordering w
	 * @abstract When enabled this option allow column reordering with mouse.
	 * Since this option uses jQuery UI sortable widget, be a sure that this widget
	 * and the related to widget files are loaded in head tag. Also be a sure too
	 * that you mark the grid.jqueryui.js when you download the jqGrid.
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function sortable($param)
	{
		$this->params->sortable = $param; return $this;
	}
	
	/**
	 * The initial sorting name when we use datatypes xml
	 * @abstract The initial sorting name when we use datatypes xml or json (data returned from server).
	 * This parameter is added to the url. If set and the index (name) match the name from colModel
	 * then to this column by default is added a image sorting icon, according to the parameter sortorder (below).
	 * See prmNames.
	 * Default empty string
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function sortname($param)
	{
		$this->params->sortname = $param; return $this;
	}
	
	/**
	 * The initial sorting order when we use datatypes xm
	 * @abstract The initial sorting order when we use datatypes xml or json (data returned from server).This parameter is added to the url - see prnNames. Two possible values - asc or desc.
	 * Default asc
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function sortorder($param)
	{
		$this->params->sortorder = $param; return $this;
	}
	
	/**
	 * If set to true this enables using a subgrid. If th
	 * @abstract If set to true this enables using a subgrid. If the subGrid is enabled a additional column at left side is added to the basic grid. This column contains a 'plus' image which indicate that the user can click on it to expand the row. By default all rows are collapsed. See <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid" class="wikilink1" title="wiki:subgrid">Subgrid</a>
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function subGrid($param)
	{
		$this->params->subGrid = $param; return $this;
	}
	
	/**
	 *  A set of additional options for the subgrid. For
	 * @abstract  A set of additional options for the subgrid.
	 * For more information and default values see Subgrid <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid">[link]</a>.
	 * Default Subgrid
	 * Changeable  Yes
	 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid
	 * @param  object
	 * @return JqGrid
	 */
	public function subGridOptions($param)
	{
		$this->params->subGridOptions = $param; return $this;
	}
	
	/**
	 * This property, which describes the model of the su
	 * @abstract This property, which describes the model of the subgrid, has an effect only if the subGrid property is set to true. It is an array in which we describe the column model for the subgrid data. For more info see <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid" class="wikilink1" title="wiki:subgrid">Subgrid</a>.
	 * Default empty array
	 * Changeable No
	 * @param array-[]
	 * @return JqGrid
	 */
	public function subGridModel($param)
	{
		$this->params->subGridModel = $param; return $this;
	}
	
	/**
	 * This option allow loading subgrid as a service. If
	 * @abstract This option allow loading subgrid as a service.
	 * If not set, the datatype parameter of the parent grid is used.
	 * Default null
	 * Changeable Yes
	 * @param mixed
	 * @return JqGrid
	 */
	public function subGridType($param)
	{
		$this->params->subGridType = $param; return $this;
	}
	
	/**
	 * This option has effect only if subGrid option is s
	 * @abstract This option has effect only if subGrid option is set to true.
	 * This option points to the file from which we get the data for the subgrid.
	 * jqGrid adds the id of the row to this url as parameter.
	 * If there is a need to pass additional parameters, use the params option in subGridModel.
	 * See Subgrid <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid">[link]</a>
	 * Default empty string
	 * Changeable Yes
	 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid
	 * @param string
	 * @return JqGrid
	 */
	public function subGridUrl($param)
	{
		$this->params->subGridUrl = $param; return $this;
	}
	
	/**
	 *  Determines the width of the subGrid column if sub
	 * @abstract  Determines the width of the subGrid column if subgrid is enabled.
	 * Default 20
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function subGridWidth($param)
	{
		$this->params->subGridWidth = $param; return $this;
	}
	
	/**
	 * This option defines the toolbar of the grid. This
	 * @abstract This option defines the toolbar of the grid.
	 * This is array with two values in which the first value enables the toolbar and the second defines
	 * the position relative to body Layer.<br>
	 * Possible values “top”,”bottom”, “both”. <br>
	 * When we set toolbar: [true,”both”] two toolbars are created –
	 * one on the top of table data and one of the bottom of the table data.
	 * When we have two toolbars then we create two elements (div).
	 * The id of the top bar is constructed like “t_”+id of the grid and the bottom toolbar the id is “tb_”+id of the grid.
	 * In case when only one toolbar is created we have the id as “t_” + id of the grid,
	 * independent of where this toolbar is created (top or bottom)
	 * Default [false,'']
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function toolbar($param)
	{
		$this->params->toolbar = $param; return $this;
	}
	
	/**
	 * When enabled this option place a pager element at
	 * @abstract When enabled this option place a pager element at top of the grid below the caption (if available).
	 * If another pager is defined both can coexists and are refreshed in sync.
	 * The id of the new created pager is a combination of the gridid+“_toppager”.
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function toppager($param)
	{
		$this->params->toppager = $param; return $this;
	}
	
	/**
	 * Readonly parameter. Measure the loading time of th
	 * @abstract Readonly parameter. Measure the loading time of the records -
	 * currently available only when we load xml or json data. Also the check begin
	 * when the request is complete and we begin to insert data into the grid and ends when the last row is added.
	 * Default 0
	 * Changeable No
	 * @param integer
	 * @return JqGrid
	 */
	public function totaltime($param)
	{
		$this->params->totaltime = $param; return $this;
	}
	
	/**
	 * Determines the initial datatype (see datatype opti
	 * @abstract Determines the initial datatype (see datatype option).
	 * Usually this should not be changed. During the reading process this option is equal to the datatype option.
	 * Default null
	 * Changeable No
	 * @param mixed
	 * @return JqGrid
	 */
	public function treedatatype($param)
	{
		$this->params->treedatatype = $param; return $this;
	}
	
	/**
	 * Enables (disables) the tree grid format. For more
	 * @abstract Enables (disables) the tree grid format.
	 * For more details see Tree Grid <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid">[link]</a>
	 * Default false
	 * Changeable No
	 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid
	 * @param boolean
	 * @return JqGrid
	 */
	public function treeGrid($param)
	{
		$this->params->treeGrid = $param; return $this;
	}
	
	/**
	 *  Deteremines the method used for the treeGrid. Can
	 * @abstract  Deteremines the method used for the treeGrid. Can be nested or adjacency.
	 * See Tree Grid <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid">[link]</a>
	 * Default nested
	 * Changeable No
	 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid
	 * @param string
	 * @return JqGrid
	 */
	public function treeGridModel($param)
	{
		$this->params->treeGridModel = $param; return $this;
	}
	
	/**
	 * This array set the icons used in the tree. The ico
	 * @abstract This array set the icons used in the tree.
	 * The icons should be a valid names from UI theme roller images.
	 * The default values are:<br>
	 * {plus:'ui-icon-triangle-1-e', minus:'ui-icon-triangle-1-s', leaf:'ui-icon-radio-off'}
	 * Default
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function treeIcons($param)
	{
		$this->params->treeIcons = $param; return $this;
	}
	
	/**
	 * extends the colModel defined in the basic grid.
	 * @abstract extends the colModel defined in the basic grid.
	 * The fields described here are added to end of the colModel array and are hidden.
	 * This means that the data returned from the server should have values for these fields.
	 * For a full description of all valid values see Tree Grid <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid"> [link]</a>.
	 * Default
	 * Changeable No
	 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:treegrid
	 * @param array
	 * @return JqGrid
	 */
	public function treeReader($param)
	{
		$this->params->treeReader = $param; return $this;
	}
	
	/**
	 * Determines the level where the root element begins
	 * @abstract Determines the level where the root element begins when treeGrid is enabled
	 * Default 0
	 * Changeable No
	 * @param numeric
	 * @return JqGrid
	 */
	public function tree_root_level($param)
	{
		$this->params->tree_root_level = $param; return $this;
	}
	
	/**
	 * The url of the file that holds the request
	 * @abstract The url of the file that holds the request
	 * Default null
	 * Changeable Yes
	 * @param string
	 * @return JqGrid
	 */
	public function url($param)
	{
		$this->params->url = $param; return $this;
	}
	
	/**
	 * This array contain custom information from the req
	 * @abstract This array contain custom information from the request. Can be used at any time.
	 * Default empty array
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function userData($param)
	{
		$this->params->userData = $param; return $this;
	}
	
	/**
	 * When set to true we directly place the user data a
	 * @abstract When set to true we directly place the user data array userData at footer.
	 * The rules are as follow: If the userData array contain name which is equal to those of colModel
	 * then the value is placed in that column.If there are no such values nothing is palced.
	 * Note that if this option is used we use the current formatter options (if available) for that column
	 * Default false
	 * Changeable Yes
	 * @param boolean
	 * @return JqGrid
	 */
	public function userDataOnFooter($param)
	{
		$this->params->userDataOnFooter = $param; return $this;
	}
	
	/**
	 * If true, jqGrid displays the beginning and ending record number in the grid,
	 * out of the total number of records in the query.
	 * @abstract If true, jqGrid displays the beginning and ending record number in the grid,
	 * out of the total number of records in the query.
	 * This information is shown in the pager bar (bottom right by default)in this format:
	 * “View X to Y out of Z”.
	 * If this value is true, there are other parameters that can be adjusted,
	 * including 'emptyrecords' and 'recordtext'.
	 * Default false
	 * Changeable No
	 * @param boolean
	 * @return JqGrid
	 */
	public function viewrecords($param)
	{
		$this->params->viewrecords = $param; return $this;
	}
	
	/**
	 *  The purpose of this parameter is to define differ
	 * @abstract  The purpose of this parameter is to define different look and behavior of sorting icons
	 * that appear near the header. This parameter is array with the following default options viewsortcols :
	 * [false,'vertical',true].
	 * <li> The first parameter determines if all icons should be viewed at the same time when all columns
	 * have sort property set to true.
	 * The default of false determines that only the icons of the current sorting column should be viewed.
	 * Setting this parameter to true causes all icons in all sortable columns to be viewed.<br>
	 * <li> The second parameter determines how icons should be placed - vertical means that the sorting icons
	 * are one under another. 'horizontal' means that the icons should be one near other. <br>
	 * <li> The third parameter determines the click functionality. If set to true the columns are sorted
	 * if the header is clicked. If set to false the columns are sorted only when the icons are clicked. <br>
	 * Important note: When set a third parameter to false be a sure that the first parameter is set to true,
	 * otherwise you will loose the sorting.
	 * Default
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function viewsortcols($param)
	{
		$this->params->viewsortcols = $param; return $this;
	}
	
	/**
	 * If this option is not set, the width of the grid i
	 * @abstract If this option is not set, the width of the grid is a sum of the widths of the columns
	 * defined in the colModel (in pixels). If this option is set, the initial width of each column is set
	 * according to the value of shrinkToFit option.
	 * Default none
	 * Changeable No. Method avail.
	 * @param number
	 * @return JqGrid
	 */
	public function width($param)
	{
		$this->params->width = $param; return $this;
	}
	
	/**
	 * Array which describes the structure of the expected xml data.
	 * @abstract Array which describes the structure of the expected xml data.
	 * For a full description refer to Retrieving Data XML Data
	 * <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:retrieving_data#xml_data"> [link]</a>.
	 * Default
	 * Changeable No
	 * @param array
	 * @return JqGrid
	 */
	public function xmlReader($param)
	{
		$this->params->xmlReader = $param; return $this;
	}
}
