<?php

/**
 * Subgrid
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:subgrid
 * @abstract
 * There are times when we need to be able to easily display (or edit) records that are the children of a selected record in the parent grid. We would, of course, want to show only those records that are the children of the selected record in the grid, never the children of all records.
 *
 * jqGrid offers two ways of handling child records:<br>
 *
 * a subGrid<br>
 * a grid as a subGrid<br>
 *
 * Installation
 *
 * In order to use this module you should mark the Subgrid when you download the grid. For more information refer to Download.
 * For Developers - this is the grid.subgrid.js in the src directory.
 * Properties
 *
 * SubGrids use the following properties, events and methods of the parent grid - i.e. you should set these properties in the grid options
 * 
 * <b>Events</b>
 * <p>In these events,</p>
 * <ul>
 * <li class="level1"><div class="li"> pID is the unique id of the div element where we can put contents when <span class="search_hit">subgrid</span> is enabled,</div>
 * <li class="level1"><div class="li"> id is the id of the row</div>
 * <li class="level1"><div class="li"> sPostData - the data which is posted when a <span class="search_hit">subgrid</span> request is made</div>
 * </ul>
 * 
 */


class JqGridSubgrid extends JqGridBaseObject
{
	// PROPERTIES
	
	/**
	* If set to true this enables using a <span class="search_hit">subgrid</span>
	* @abstract If set to true this enables using a <span class="search_hit">subgrid</span>.<br>
	*  If the <span class="search_hit">subGrid</span> is enabled a additional column at left side is added to the basic grid.<br>
	*  This column contains a 'plus' image which indicate that the user can click on it to expand the row.<br>
	*  By default all rows are collapsed.<br>
	*
	* Default false
	* @param boolean
	* @return JqGridSubgrid
	*/
	public function set_subGrid($param)
	{
		$this->subGrid = $param; return $this;
	}
	
	/**
	 *  A set of options for the <span class="search_hit">subgrid</span>
	 * @abstract  A set of options for the <span class="search_hit">subgrid</span>.<br>
	 *  Below are all the options with their default values <br>
	 *  { <br>
	 *  plusicon : “ui-icon-plus”,<br>
	 *
	 *  minusicon : “ui-icon-minus”,<br>
	 *  openicon: “ui-icon-carat-1-sw”, <br>
	 *  expandOnLoad:  false, <br>
	 *  selectOnExpand : false, <br>
	 *  reloadOnExpand : true <br>
	 *  } <br>
	 *
	 *  <em>plusicon</em> and <em>minusicon</em> defies the icons when the grid is collapsed/expanded.<br>
	 *  A valid name of icon from Theme Roller should be set.<br>
	 *  <br>
	 *  <em>openicon</em> the icon bellow the minusicon when the <span class="search_hit">subgrid</span> row is expanded <br>
	 *  <em>expandOnLoad</em> when set to true make it so that all rows will be expanded automatically when a new set of data is loaded.<br>
	 *  <br>
	 *
	 *  <em>selectOnLoad</em> when set to true the row is selected when a plusicon is clicked with the mouse.<br>
	 *  <br>
	 *  <em>reloadOnExpand</em> If set to false the data in the <span class="search_hit">subgrid</span> is loaded only once and all other subsequent clicks just hide or show the data and no more ajax calls  are made.<br>
	 *
	 * Default
	 * @param  object
	 * @return JqGridSubgrid
	 */
	public function set_subGridOptions($param)
	{
		$this->subGridOptions = $param; return $this;
	}
	
	/**
	 * This property, which describes the model of the <span class="search_hit">subgrid
	 * @abstract This property, which describes the model of the <span class="search_hit">subgrid</span>, has an effect only if the <span class="search_hit">subGrid</span> property is set to true.<br>
	 *  It is an array in which we describe the column model for the <span class="search_hit">subgrid</span> data.<br>
	 *  The syntax is :<br>
	 *  <span class="search_hit">subGrid</span>Model : [ <br>
	 *
	 *  { name : ['name_1','name_2',…,'name_n'], <br>
	 *  width : [width_1,width_2,…,width_n] , <br>
	 *  align : ['left','center',…,'right'] , <br>
	 *  params : [param_1,…,param_n], <br>
	 *  mapping:['name_1_map','name_2_map',…,'name_n_map']} <br>
	 *  Where <br>
	 *
	 *  <em>name:</em> an array containing the labels of the columns of the <span class="search_hit">subgrid</span>.<br>
	 * <br>
	 *  <em>width:</em> an array containing the width of the columns.<br>
	 *  This array should have the same length as in name array.<br>
	 *  <br>
	 *  <em>align:</em> an array containing the alignment of the columns.<br>
	 *  The values can be left, center or right.<br>
	 *  If omited  the aliment is left.<br>
	 *  <br>
	 *
	 *  <em>params:</em> an array in which we can add a name from main grid's colModel to pass as additional parameter to the <span class="search_hit">subGrid</span>Url.<br>
	 *  <br>
	 *  <em>mapping:</em> the parameter is used only when repeatitems in <span class="search_hit">subgrid</span> is set to false.<br>
	 *  When defined in this case we use the names from this array to map it to the <span class="search_hit">subgrid</span> names.<br>
	 *  If not set and repeatitems is false we use the name option.<br>
	 *
	 * Default
	 * @param array
	 * @return JqGridSubgrid
	 */
	public function set_subGridModel($param)
	{
		$this->subGridModel = $param; return $this;
	}
	
	/**
	 * This option allow loading <span class="search_hit">subgrid</span> as a service
	 * @abstract This option allow loading <span class="search_hit">subgrid</span> as a service.<br>
	 *  If not set, the datatype parameter of the parent grid is used.<br>
	 *  See the example below
	 * Default null
	 * @param mixed
	 * @return JqGridSubgrid
	 */
	public function set_subgridtype($param)
	{
		$this->subgridtype = $param; return $this;
	}
	
	/**
	 * Determines the width of the <span class="search_hit">subGrid</span> column if <s
	 * @abstract Determines the width of the <span class="search_hit">subGrid</span> column if <span class="search_hit">subgrid</span> is enabled.<br>
	 *
	 * Default 20
	 * @param integer
	 * @return JqGridSubgrid
	 */
	public function set_subGridWidth($param)
	{
		$this->subGridWidth = $param; return $this;
	}
	
	/**
	 * This option has effect only if <span class="search_hit">subGrid</span> option is
	 * @abstract This option has effect only if <span class="search_hit">subGrid</span> option is set to true.<br>
	 *  This option points to the file from which we get the data for the <span class="search_hit">subgrid</span>.<br>
	 *  <span class="search_hit">jqGrid</span> adds the id of the row to this url as parameter.<br>
	 *  If there is a need to pass additional parameters, use the params option in <span class="search_hit">subGrid</span>Model
	 * Default empty string
	 * @param string
	 * @return JqGridSubgrid
	 */
	public function set_subGridUrl($param)
	{
		$this->subGridUrl = $param; return $this;
	}
	
	/**
	 * This option allow to set global ajax settings for the <span class="search_hit">s
	 * @abstract This option allow to set global ajax settings for the <span class="search_hit">subgrid</span> when we request data.<br>
	 *  Note that with this option is possible to overwrite all current ajax setting in the <span class="search_hit">subgrid</span> request including the complete event.<br>
	 *
	 * Default empty object
	 * @param object
	 * @return JqGridSubgrid
	 */
	public function set_ajaxSubgridOptions($param)
	{
		$this->ajaxSubgridOptions = $param; return $this;
	}
	
	
	// EVENTS
	/**
	* The event is raised just before expanding the grid
	* @abstract The event is raised just before expanding the grid.<br>
	*  When set, this event should return true or false.<br>
	*  If it returns false the <span class="search_hit">subgrid</span> row is not expanded and the <span class="search_hit">subgrid</span> is not opened.<br>
	*
	* @param pID, id
	* @return JqGridSubgrid
	*/
	public function set_subGridBeforeExpand($param)
	{
		$this->subGridBeforeExpand = $param; return $this;
	}
	
	/**
	 * This event is raised when the <span class="search_hit">subgrid</span> is enabled
	 * @abstract This event is raised when the <span class="search_hit">subgrid</span> is enabled and is executed when the user clicks on the plus icon of the grid.<br>
	 *  Can be used to put custom data in the <span class="search_hit">subgrid</span>.<br>
	 *
	 * @param pID, id
	 * @return JqGridSubgrid
	 */
	public function set_subGridRowExpanded($param)
	{
		$this->subGridRowExpanded = $param; return $this;
	}
	
	/**
	 * This event is raised when the user clicks on the minus icon
	 * @abstract This event is raised when the user clicks on the minus icon.<br>
	 *  The event should return true or false; when the returned value is false the row can not be collapsed.<br>
	 *
	 * @param pID, id
	 * @return JqGridSubgrid
	 */
	public function set_subGridRowColapsed($param)
	{
		$this->subGridRowColapsed = $param; return $this;
	}
	
	/**
	 * If set this event can serialize the data passed to the ajax request
	 * @abstract If set this event can serialize the data passed to the ajax request.<br>
	 *  The function should return the serialized data.<br>
	 *  This event can be used when a custom data should be passed to the server - e.<br>
	 * g - JSON string, <acronym title="Extensible Markup Language">XML</acronym> string and etc.<br>
	 *
	 * @param sPostData
	 * @return JqGridSubgrid
	 */
	public function set_serializeSubGridData($param)
	{
		$this->serializeSubGridData = $param; return $this;
	}
	
	
	// METHODEN
	/**
	*
	* @abstract dynamically expand the <span class="search_hit">subgrid</span> row with the id = rowid
	* Returns <span class="search_hit">jqGrid</span> object
	* @param rowid
	* @return JqGridSubgrid
	*/
	public function set_expandSubGridRow($param)
	{
		$this->expandSubGridRow = $param; return $this;
	}
	
	/**
	 *
	 * @abstract dynamically collapse the <span class="search_hit">subgrid</span> row with the id = rowid
	 * Returns <span class="search_hit">jqGrid</span> object
	 * @param rowid
	 * @return JqGridSubgrid
	 */
	public function set_collapseSubGridRow($param)
	{
		$this->collapseSubGridRow = $param; return $this;
	}
	
	/**
	 *
	 * @abstract dynamically toggle the <span class="search_hit">subgrid</span> row with the id = rowid
	 * Returns <span class="search_hit">jqGrid</span> object
	 * @param rowid
	 * @return JqGridSubgrid
	 */
	public function set_toggleSubGridRow($param)
	{
		$this->toggleSubGridRow = $param; return $this;
	}
	
	/**
	 * Add data in a <span class="search_hit">subgrid</span> row
	 * @abstract Add data in a <span class="search_hit">subgrid</span> row.<br>
	 *  json is a json object, rowid is the id of the row after which the data will be added
	 * Returns false
	 * @param json, rowid
	 * @return JqGridSubgrid
	 */
	public function set_subGridJson($param)
	{
		$this->subGridJson = $param; return $this;
	}
	
	/**
	 * Add data in a <span class="search_hit">subgrid</span> row
	 * @abstract Add data in a <span class="search_hit">subgrid</span> row.<br>
	 *  xml is a xml dom element, rowid is the id of the row after which the data will be added
	 * Returns false
	 * @param xml, rowid
	 * @return JqGridSubgrid
	 */
	public function set_subGridXml($param)
	{
		$this->subGridXml = $param; return $this;
	}
	
	
	
}