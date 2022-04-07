<?php

/**
 * 
 * Enter description here ...
 * @author rybka
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:navigator#parameters
 * @abstract 
 * jQuery("#grid_id").jqGrid('navGrid','#gridpager',<br>
 * {parameters},<br>
 * prmEdit, <br>
 * prmAdd, <br>
 * prmDel, <br>
 * prmSearch, <br>
 * prmView);
 * <p>
 * Parameter werden in dieser Klasse definiert.<br>
 * Die spezifischen Formular-Parameter für Edit, Add, Del, Search und View<br>
 * können in den nachfolgenden Parametern spezifiziert werden:<br>
 * - prmEdit: Parameter für das Edit-Formular-Funktionen: class JqGridFormPrmEdit<br>
 * - prmAdd: Parameter für das Edit-Formular-Funktionen: class JqGridFormPrmEdit<br>
 * - prmDel: Parameter für das Edit-Formular-Funktionen: class JqGridFormPrmDel<br>
 * - prmSearch: Parameter für das Edit-Formular-Funktionen: class JqGridFormPrmSearch<br>
 * - prmView: Parameter für das Edit-Formular-Funktionen: class JqGridFormPrmView<br>
 * </p>
 */
class JqGridNavGrid extends JqGridBaseObject {
	protected $_RowedID = "";
	protected $_id = "";
        protected $_navButtons = array();
	
	/**
	 * @var JqGridNavGridEditFormOptions
	 */
	protected $_EditFormOptions = NULL;
	
	/**
	* @var JqGridNavGridAddFormOptions
	*/
	protected $_AddFormOptions = NULL;
	
	/**
	* @var JqGridNavGridDelFormOptions
	*/
	protected $_DelFormOptions = NULL;
	
	/**
	* @var JqGridNavGridSearchFormOptions
	*/
	protected $_SearchFormOptions = NULL;
	
	/**
	* @var JqGridNavGridViewFormOptions
	*/
	protected $_ViewFormOptions = NULL;
	
	public function get_id()
	{
		return $this->_id;
	}
        
        public function addNavButton(JqGridNavGridButton $button)
        {
            $this->_navButtons[] = $button;
        }
        
        public function getNavButtons()
        {
            /* @var $button JqGridNavGridButton */
            foreach($this->_navButtons as $button) {
                $button->set_pagerID($this->_id);
            }
            return $this->_navButtons;
        }
	/**
	 * 
	 * Enter description here ...
	 * @param array, object with $properties
	 * @return JqGridNavGridEditFormOptions
	 */
	function getInstanceEditFormOptions($properties = NULL)
	{
		if (!is_object($this->_EditFormOptions) || !($this->_EditFormOptions instanceof JqGridNavGridEditFormOptions)) 
			$this->_EditFormOptions = new JqGridNavGridEditFormOptions($properties);
		
                return $this->_EditFormOptions;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array, object with $properties
	 * @return JqGridNavGridEditFormOptions
	 */
	function getInstanceAddFormOptions($properties = NULL)
	{
		if (!is_object($this->_AddFormOptions) || !($this->_AddFormOptions instanceof JqGridNavGridEditFormOptions)) 
			$this->_AddFormOptions = new JqGridNavGridEditFormOptions($properties);
		
		return $this->_AddFormOptions;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array, object with $properties
	 * @return JqGridNavGridDelFormOptions
	 */
	function getInstanceDelFormOptions($properties = NULL)
	{
		if (!is_object($this->_DelFormOptions) || !($this->_DelFormOptions instanceof JqGridNavGridDelFormOptions)) 
			$this->_DelFormOptions = new JqGridNavGridDelFormOptions($properties);
		
		return $this->_DelFormOptions;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array, object with $properties
	 * @return JqGridNavGridSearchFormOptions
	 */
	function getInstanceSearchFormOptions($properties = NULL)
	{
		if (!is_object($this->_SearchFormOptions) || !($this->_SearchFormOptions instanceof JqGridNavGridSearchFormOptions)) 
			$this->_SearchFormOptions = new JqGridNavGridSearchFormOptions($properties);
		
		return $this->_SearchFormOptions;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array, object with $properties
	 * @return JqGridNavGridViewFormOptions
	 */
	function getInstanceViewFormOptions($properties = NULL)
	{
		if (!is_object($this->_ViewFormOptions) || !($this->_ViewFormOptions instanceof JqGridNavGridViewFormOptions)) 
			$this->_ViewFormOptions = new JqGridNavGridViewFormOptions($properties);
		
		return $this->_ViewFormOptions;
	}

	// 	The full list of the parameters is as follow:
	// 	Property	Type	Description	Default1)

	
	// 	If the id of the button is not set we use the following rule:
	// 	For the add button we use “add_” + the id of the grid
	// 	For the edit button we use “edit_” + the id of the grid
	// 	For the delete button we use “del_” + the id of the grid
	// 	For the view button we use “view_” + the id of the grid
	// 	For the search button we use “search_” + the id of the grid
	// 	For the refresh button we use “refresh_” + the id of the grid
	
	public function __construct($RowedID, $PagerID, $properties = NULL)
	{
		$this->_RowedID = $RowedID;
		$this->_id = $PagerID;
		
		if (!is_null($properties)) $this->setProperties($properties);
	}
	/**
	 * add	boolean	Enables or disables the add action in the Navigator.
	 * @abstract When the button is clicked a editGridRow with parameter new method is executed	true
	 * @param bool
	 * @return JqGridNavGrid
	 */
	public function set_add($param)
	{
		$this->add = (bool)$param;
		return $this;
	}

	/**
	 * addicon	string
	 * @abstract addicon string Set a icon (Css-ClassName) to be displayed if the add action is enabled. Note that currently only icons from UI theme images can be added
	 * @default ui-icon-plus
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_addicon($param)
	{
		$this->addicon = (string)$param;
		return $this;
	}


	/**
	 * @abstract addtext	string	The text than can be set in the add button. Default	empty
	 * @default empty
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_addtext($param)
	{
		$this->addtext = (string)$param;
		return $this;
	}

	/**
	 * @abstract The title that appear when we mouse over to the add button (if enabled)
	 * @default Add new row
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_addtitle($param)
	{
		$this->addtitle = (string)$param;
		return $this;
	}

	/**
	 * @abstract The header of the message that appear when we try to edit,delete or view a row without to select it
	 * @default Warning
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_alertcap($param)
	{
		$this->alertcap = (string)$param;
		return $this;
	}

	/**
	 * @abstract The message text that appear when we try to edit,delete or view a row without to select it
	 * @default Please, select row
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_alerttext($param)
	{
		$this->alerttext = (string)$param;
		return $this;
	}

	/**
	 * Clones all the actions from the bottom pager to the top pager if defined. Note that the navGrid can be applied to the top pager only. The id of the top pager is a combination of grid id and “_toppager”
	 * @default false
	 * @param boolean
	 * @return JqGridNavGrid
	 */
	public function set_cloneToTop($param)
	{
		$this->cloneToTop = (bool)$param;
		return $this;
	}

	/**
	 * Determine if the alert dialog can be closed if the user pres ESC key
	 * @default bool
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_closeOnEscape($param)
	{
		$this->closeOnEscape = (bool)$param;
		return $this;
	}

	/**
	 * Enables or disables the delete action in the Navigator. When the button is clicked a delGridRow method is executed.
	 * @abstract Default true
	 * @default true
	 * @param bool
	 * @return JqGridNavGrid
	 */
	public function set_del($param)
	{
		$this->del = (string)$param;
		return $this;
	}

	/**
	 * Set a icon to be displayed if the delete action is enabled. Note that currently only icons from UI theme images can be used
	 * @abstract Default 'ui-icon-trash'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_delicon($param)
	{
		$this->delicon = (string)$param;
		return $this;
	}

	/**
	 * The text than can be set in the delete button
	 * @abstract Default empty
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_deltext($param)
	{
		$this->deltext = (string)$param;
		return $this;
	}

	/**
	 * The title that appear when we mouse over to the delete button (if enabled)
	 * @abstract Default 'Delete selected row'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_deltitle($param)
	{
		$this->deltitle = (string)$param;
		return $this;
	}

	/**
	 * Enables or disables the edit action in the Navigator.
	 * @abstract Enables or disables the edit action in the Navigator.<br>When the button is clicked a editGridRow method is executed with parameter the - current selected row.<br>Default true
	 * @param bool
	 * @return JqGridNavGrid
	 */
	public function set_edit($param)
	{
		$this->edit = (bool)$param;
		return $this;
	}

	/**
	 * Set a icon to be displayed if the edit action is enabled.
	 * @abstract Set a icon to be displayed if the edit action is enabled.<br>
	 * Note that currently only icons from UI theme images can be used<br>
	 * Default 'ui-icon-pencil'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_editicon($param)
	{
		$this->editicon = (string)$param;
		return $this;
	}

	/**
	 * The text than can be set in the edit button
	 * @abstract Default empty
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_edittext($param)
	{
		$this->edittext = (string)$param;
		return $this;
	}

	/**
	 * The title that appear when we mouse over to the edit button (if enabled)
	 * @abstract Default 'Edit selected row'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_edittitle($param)
	{
		$this->edittitle = (string)$param;
		return $this;
	}

	/**
	 * Determines the position of the Navigator buttons in the pager. Can be left, center and right.
	 * @abstract Default 'left'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_position($param)
	{
		$this->position = (string)$param;
		return $this;
	}

	/**
	 * Enables or disables the refresh button in the pager.
	 * @abstract Enables or disables the refresh button in the pager.<br>
	 * When the button is clicked a trigger(“reloadGrid”) is executed and the search parameters are cleared<br>
	 * Default true
	 * @param bool
	 * @return JqGridNavGrid
	 */
	public function set_refresh($param)
	{
		$this->refresh = (bool)$param;
		return $this;
	}

	/**
	 * Set a icon to be displayed if the refresh action is enabled.
	 * @abstract Set a icon to be displayed if the refresh action is enabled.<br>
	 * Note that currently only icons from UI theme images can be used<br>
	 * Default 'ui-icon-refresh'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_refreshicon($param)
	{
		$this->refreshicon = (string)$param;
		return $this;
	}

	/**
	 * The text than can be set in the refresh button. Default empty
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_refreshtext($param)
	{
		$this->refreshtext = (string)$param;
		return $this;
	}

	/**
	 * The title that appear when we mouse over to the refresh button (if enabled). Default 'Reload Grid'
	 * @abstract addicon string Set a icon (Css-ClassName) to be displayed if the add action is enabled. Note that currently only icons from UI theme images can be added
	 * @default ui-icon-plus
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_refreshtitle($param)
	{
		$this->refreshtitle = (string)$param;
		return $this;
	}

	/**
	 * Determines how the grid should be reloaded
	 * @abstract Determines how the grid should be reloaded:<br>
	 * - firstpage - the grid reload the data from the first page.<br>
	 * - current - the reloading should save the current page and current selection<br>
	 * Default 'firstpage'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_refreshstate($param)
	{
		$this->refreshstate = (string)$param;
		return $this;
	}


	/**
	 * If defined this event fire after the refresh button is clicked. Default null
	 * @param JsFunction|string
	 * @return JqGridNavGrid
	 */
	public function set_afterRefresh($param)
	{
		$this->afterRefresh = ($param instanceof JsFunction) ? $param : (string)(new JsFunction($param));
		return $this;
	}

	/**
	 * If defined this event fire before the refresh button is clicked	null
	 * @param JsFunction|string
	 * @return JqGridNavGrid
	 */
	public function set_beforeRefresh($param)
	{
		$this->beforeRefresh = ($param instanceof JsFunction) ? $param : (string)(new JsFunction($param));
		return $this;
	}

	/**
	 * Enables or disables the search button in the pager. When the button is clicked a searchGrid method is executed	true
	 * @param boolean|string|JsFunction $param
         * @throw JqGridException if invalid Param
	 * @return JqGridNavGrid
	 */
	public function set_search($param)
	{
		if ($param instanceof JsFunction || is_bool($param)) $this->search = $param;
                elseif(is_string($var)) $this->search = new JsFunction ($param);
                else throw new JqGridException ('Invalid Type for search-Param, expected string, bool or JsFunction-Object!');
		return $this;
	}

	/**
	 * Set a icon to be displayed if the search action is enabled. Note that currently only icons from UI theme images can be used	ui-icon-search
	 * @abstract Set a icon to be displayed if the search action is enabled.<br>
	 * Note that currently only icons from UI theme images can be used<br>
	 * Default 'ui-icon-search'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_searchicon($param)
	{
		$this->searchicon = (string)$param;
		return $this;
	}

	/**
	 * The text than can be set in the search button. Default empty
	 * @abstract addicon string Set a icon (Css-ClassName) to be displayed if the add action is enabled. Note that currently only icons from UI theme images can be added
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_searchtext($param)
	{
		$this->searchtext = (string)$param;
		return $this;
	}

	/**
	 * The title that appear when we mouse over to the search button (if enabled). Default 'Find records'
	 * @abstract addicon string Set a icon (Css-ClassName) to be displayed if the add action is enabled. Note that currently only icons from UI theme images can be added
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_searchtitle($param)
	{
		$this->searchtitle = (string)$param;
		return $this;
	}

	/**
	 * Enables or disables the view button in the pager.
	 * @abstract Enables or disables the view button in the pager.<br>
	 * When the button is clicked a viewGridRow method is executed<br>
	 * Default false
	 * @param bool
	 * @return JqGridNavGrid
	 */
	public function set_view($param)
	{
		$this->view = (bool)$param;
		return $this;
	}

	/**
	 * Set a icon to be displayed if the search action is enabled. Note that currently only icons from UI theme images can be used	ui-icon-document
	 * @abstract Set a icon to be displayed if the search action is enabled.<br>
	 * Note that currently only icons from UI theme images can be used<br>
	 * Default 'ui-icon-document'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_viewicon($param)
	{
		$this->viewcon = (string)$param;
		return $this;
	}

	/**
	 * The text that can be set in the view button. Default empty
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_viewtextn($param)
	{
		$this->viewtext = (string)$param;
		return $this;
	}

	/**
	 * The title that appear when we mouse over to the view button (if enabled). Default 'View selected row'
	 * @param string
	 * @return JqGridNavGrid
	 */
	public function set_viewtitle($param)
	{
		$this->viewtitle = (string)$param;
		return $this;
	}

	/**
	 * If defined replaces the build in add function. No parameters are passed to this function. Default null
	 * @param JsString|string
	 * @return JqGridNavGrid
	 */
	public function set_addfunc($param)
	{
		$this->addfunc = ($param instanceof JsFunction) ? $param : (string)(new JsFunction($param));
		return $this;
	}

	/**
	 * If defined replaces the build in edit function.
	 * @abstract If defined replaces the build in edit function.<br>
	 * Parameter passed to this function is the id of the edited row<br>
	 * Default null
	 * @param JsString|string
	 * @return JqGridNavGrid
	 */
	public function set_editfunc($param)
	{
		$this->editfunc = ($param instanceof JsFunction) ? $param : (string)(new JsFunction($param));
		return $this;
	}

	/**
	 * If defined replaces the build in del function.
	 * @abstract If defined replaces the build in del function.<br>
	 * Parameter passed to this function is the id of the edited row<br>
	 * null
	 * @param JsString|string
	 * @return JqGridNavGrid
	 */
	public function set_delfunc($param)
	{
		$this->delfunc = ($param instanceof JsFunction) ? $param : (string)(new JsFunction($param));
		return $this;
	}

}

// Beispiel
// prmSearch = {
// 	multipleSearch: true,
// 	overlay: false,
// 	beforeShowSearch: function() {
// 		$("#rowedp_entries")[0].toggleToolbar();
// 	},
// 	onClose: function() {
// 		$("#rowedp_entries")[0].toggleToolbar();
// 	}
// };
// prmEdit = {
// 	afterShowForm: function(){
// 		//#358 get_jsFormHelper_byConfName
// 		// Register Datepicker fuer Datum
// 		jQuery('[name="Datum"]').datepicker({
// 			dateFormat:"yy-mm-dd",showWeek:true,changeYear:true
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer fahrtzeit
// 		jQuery('[name="fahrtzeit"]').timepicker({
// 			hourText: 'Stunde', minuteText: 'Minuten',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false, minuteText: 'Min'
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer von
// 		jQuery('[name="von"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});
			
// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer bis
// 		jQuery('[name="bis"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer pause
// 		jQuery('[name="pause"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});
			
// 		//#345 get_jsFormHelper_byConfName
// 		// Register Autocomplete fuer Leistung
// 		register_jq_autocomplete(jQuery('[name="Leistung"]'),
// 					"http://localhost/mertens_crm/autocomplete_byconf.php?cnfKey=p_entries&fKey=Leistung", 
// 					"single", null);
// 	}
// };
// prmAdd = {
// 	afterShowForm: function(){
// 		//#358 get_jsFormHelper_byConfName
// 		// Register Datepicker fuer Datum
// 		jQuery('[name="Datum"]').datepicker({
// 			dateFormat:"yy-mm-dd",showWeek:true,changeYear:true
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer fahrtzeit
// 		jQuery('[name="fahrtzeit"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer von
// 		jQuery('[name="von"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer bis
// 		jQuery('[name="bis"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});

// 		//#363 get_jsFormHelper_byConfName
// 		// Register Timepicker fuer pause
// 		jQuery('[name="pause"]').timepicker({
// 			hourText: 'Stunde',
// 			minutes: {
// 				interval: 15 },
// 				showPeriodLabels: false,
// 				minuteText: 'Min'
// 		});

// 		//#345 get_jsFormHelper_byConfName
// 		// Register Autocomplete fuer Leistung
// 		register_jq_autocomplete(jQuery('[name="Leistung"]'),
// 				"http://localhost/mertens_crm/autocomplete_byconf.php?cnfKey=p_entries&fKey=Leistung", 
// 				"single", null);
// 	}
// };

// jQuery("#rowedp_entries").jqGrid('navGrid',"#prowedp_entries", {
// 	edit:true,
// 	add:true,
// 	del:true,
// 	view:true,
// 	search:false,
// 	addfunc: function() {
// 		user_func_addForm_p_entries("#rowedp_entries","#prowedp_entries")
// 	},
// 	editfunc: function(id) {
// 		user_func_editForm_p_entries(id, "#rowedp_entries","#prowedp_entries")
// 	},
// },
// prmEdit, prmAdd, prmDel, prmSearch, prmView);

// // Make Grid Resizable, requires jquery ui components
// // handles: 'e, w'	jQuery("#t_rowedp_entries").height(25).hide().jqGrid('filterGrid',"#rowedp_entries",{gridModel:true,gridToolbar:true});
// jQuery("#rowedp_entries").jqGrid('gridResize', {
// 	minWidth:800,});
// 	jQuery("#sg_Datum").datepicker({
// 		dateFormat:"yy-mm-dd",showWeek:true,changeYear:true
// 	});

// 	jQuery("#rowedp_entries").jqGrid('filterToolbar',{
// 		stringResult: true,searchOnEnter : false
// 	});

// 	// DEMO: Eigener Button toggle FilterToolbar
// 	$("#rowedp_entries").jqGrid('navButtonAdd', '#prowedp_entries',{
// 		caption: "Filter",
// 		title: "Toggle Searching Toolbar",
// 		buttonicon: 'ui-icon-pin-s',
// 		onClickButton: function () {
// 			$( this )[0].toggleToolbar();
// 		}
// 	});

// 	$(document).ready(function() {
// 		$("#rowedp_entries")[0].toggleToolbar();
// 	});

// 	jQuery("#rowedp_entries").jqGrid('navButtonAdd','#prowedp_entries',{
// 		caption:"",
// 		buttonicon:'ui-icon-search',
// 		onClickButton:function(){
// 			$( "#searchform_rowedp_entries" ).dialog({
// 				height: 'auto',
// 				width: 'auto',
// 				minHeight:100,
// 				modal: false,
// 				open:function()  {
// 					$(this).data("FilterToolbarStatusBefore", $("tr.ui-search-toolbar", "#gview_rowedp_entries").css("display"));
						
// 					if (true && $("tr.ui-search-toolbar", "#gview_rowedp_entries").css("display")!="none")
// 					$("#rowedp_entries")[0].toggleToolbar();
// 				},

// 				close:function() {
// 					if (true && $(this).data("FilterToolbarStatusBefore") != "none" && $("tr.ui-search-toolbar", "#gview_rowedp_entries").css("display")=="none")
// 					$("#rowedp_entries")[0].toggleToolbar();
// 				}
// 			});
// 		}
// 	});

// 	// DEMO: Eigener Button toggle FilterToolbar
// 	$("#rowedp_entries").jqGrid('navButtonAdd', '#prowedp_entries',{
// 		caption: "Filter",
// 		title: "Toggle Searching Toolbar",
// 		buttonicon: 'ui-icon-pin-s',
// 		onClickButton: function () {
// 			$( this )[0].toggleToolbar();
// 		}
// 	});
