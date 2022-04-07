<?php

class JqGridAddOnMethods
{
	/**
	* This method can be called to construct an custom search form for the grid and sh
	* @abstract This method can be called to construct an custom search form for the grid and should be not applied to the grid but to a valid <acronym title="HyperText Markup Language">HTML</acronym> element.<br>
	* <br>
	
	*  <em>grid_id</em> is the id of the grid to which the search will be applied.<br>
	* <br>
	*  <em>parms</em> is an array of parameters (see below).<br>
	* For more details refer to <a href="/http://http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_searching" class="wikilink1" title="wiki:custom_searching">custom searching</a>
	* Returns <acronym title="HyperText Markup Language">HTML</acronym> object
	* @param grid_id
	* @param params
	* @return JsFunction
	*/
	public function filterGrid($grid_id, $params)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("filterGrid", '
		.JqGridHelper::json_encode($grid_id).','
		.JqGridHelper::json_encode($params).')');
	}
	
	/**
	 * This method is the same as filterGrid, except that the search input elements are
	 * @abstract This method is the same as filterGrid, except that the search input elements are placed in the grid just below the header elements.<br>
	
	 * When the header elements are resized the input search elements are also resized according to the new width.<br>
	 * Another difference to the filterGrid is that the filter toolbar uses definitions from colModel.<br>
	 * For more information refer to <a href="/http://http://www.trirand.com/jqgridwiki/doku.php?id=wiki:toolbar_searching" class="wikilink1" title="wiki:toolbar_searching">Toolbar searching</a>
	 * Returns <span class="search_hit">jqGrid</span> object
	 * @param params
	 * @return JsFunction
	 */
	public function filterToolbar($params)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("filterToolbar", '
		.JqGridHelper::json_encode($params).')');
	}
	
	/**
	 *
	 * @abstract Return an array of the properties of the given column name from colModel
	 * Returns array{}
	 * @param colname
	 * @return JsFunction
	 */
	public function getColProp($colname)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("getColProp", '
		.JqGridHelper::json_encode($colname).')');
	}
	
	/**
	 *
	 * @abstract Destroys the entry grid with id= <em>grid_id</em> from the <acronym title="Document Object Model">DOM</acronym> (clears all the html associated with the grid and unbinds all events)
	 * Returns true on success, <br>
	
	 false otherwise
	 * @param grid_id
	 * @return JsFunction
	 */
	public function GridDestroy($grid_id)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("GridDestroy", '
		.JqGridHelper::json_encode($grid_id).')');
	}
	
	/**
	 *
	 * @abstract The only difference to previous method is that the grid is destroyed, but the table element and pager (if any) are left ready to be used again.
	 * Returns true on success, <br>
	 false otherwise
	 * @param grid_id
	 * @return JsFunction
	 */
	public function GridUnload($grid_id)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("GridUnload", '
		.JqGridHelper::json_encode($grid_id).')');
	}
	
	/**
	 * Show or hide the grid depending and state parameter
	 * @abstract Show or hide the grid depending and state parameter.<br>
	 * When the state is set to 'visible' the grid will be shown.<br>
	 * When the parameter is set to 'hidden' the grid will be hidden.<br>
	 * Note that the method does not call onHeaderClick event and the caption of the grid is allway visible
	 * Returns jGrid object
	 * @param state
	 * @return JsFunction
	 */
	public function setGridState($state)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("setGridState", '
		.JqGridHelper::json_encode($state).')');
	}
	
	/**
	 * Sets new properties in colModel
	 * @abstract Sets new properties in colModel.<br>
	 * This method is ideal for dynamically changing properties of the column.<br>
	 * Note that some properties - have no effect see colModel options <a href="/http://http://www.trirand.com/jqgridwiki/doku.php?id=wiki:colmodel_options">[link]</a>.<br>
	
	 * For example: <br>
	 *
	 *  jQuery("#grid_id").setColProp('colname',{editoptions:{value:"True:False"}}); <br>
	 *  will change the editoptions values.
	 * Returns jGrid object
	 * @param colname
	 * @param properties
	 * @return JsFunction
	 */
	public function setColProp($colname, $properties)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("setColProp", '
		.JqGridHelper::json_encode($colname).','
		.JqGridHelper::json_encode($properties).')');
	}
	
	/**
	 * Sorts the given colname and shows the appropriate sort icon
	 * @abstract Sorts the given colname and shows the appropriate sort icon.<br>
	 * The same (without sorting icon) can be done using setGridParam({sortname:'myname'}).trigger('reloadGrid').<br>
	 * If the reload is set to true, the grid reloads with the current page and sortorder settings.
	 * Returns <span class="search_hit">jqGrid</span> object
	 * @param colname
	 * @param reload
	 * @return JsFunction
	 */
	public function sortGrid($colname, $reload)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("sortGrid", '
		.JqGridHelper::json_encode($colname).','
		.JqGridHelper::json_encode($reload).')');
	}
	
	/**
	 * This method update the existing data in the grid by given <em>rowidname</em>
	 * @abstract This method update the existing data in the grid by given <em>rowidname</em>.<br>
	 * <br>
	 * 
	 * <em>data</em> is a array of data in format <br>
	 * [{name:value,name1:value1…}, {name:value,name2:value2…}] <br>
	 * where the name is the name from colModel and value is the actuall value.<br>
	 * It is not neccessary that all columns are present in the data item object (the same as setRowData method)<br>
	 *  <em>rowidname</em> (string) - is the name of the row which should acts as id If not set the name "id" is used.<br>
	 *  	
	 * <br>
	 *  <em>jsonreader</em> (boolean) default false.<br>
	 * If set to true a jsonReader definition is used to set the data.<br>
	 * Note that this work only if the item in jsonReader object 'repeatitems' is set to true.In this case the data should be not in pair name:value, but only values where the number of columns should be equal of those in colModel.
	 * Returns true on success, <br>
	 false otherwise
	 * @param data
	 * @param rowidname
	 * @param jsonreader
	 * @return JsFunction
	 */
	public function updateGridRows($data, $rowidname, $jsonreader)
	{
		return new JsFunction('jQuery("'.$this->gridID.'").jqGrid("updateGridRows", '
		.JqGridHelper::json_encode($data).','
		.JqGridHelper::json_encode($rowidname).','
		.JqGridHelper::json_encode($jsonreader).')');
	}
}