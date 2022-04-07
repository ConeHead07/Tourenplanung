<?php

class JqGridMethods
{
	protected $_gridID = "";
	
	/**
	 * @var JqGrid
	 */
	protected $_bindToJsObject = NULL;
	
	public function __construct($bindJsObj = NULL)
	{
		if (!is_null($bindJsObj) && is_string($bindJsObj)) $this->set_gridID($bindJsObj);
		$this->bindToJsObject( $bindJsObj ); 	
	}
	
	/** 
	 * 
	 * Interne Methoden-Funktion, keine JqGridMethode!
	 * @abstract Setzen GridId, an die die Methoden bei Bedarf gebunden werden.
	 * Kann für Chain-Aufrufe über bindToJsObj nach Bedarf an- und abgeschaltet werden
	 * @see JqGridMethods::bindToJsObj 
	 * @param string $id
	 * @return JqGridMethods
	 */
	public function set_gridID($id) {
		$this->_gridID = $id;
		return $this;
	}
	
	/**
	 * 
	 * Gibt die id des angebundenen Grids zurück
	 * @return string id
	 */
	public function get_gridID() {
		return $this->_gridID;
	}
	
	/**
	 * 
	 * Bindet generierte Methoden an ein bestimmtes Objekt
	 * oder lässt die Bindung offen, um es als Chain-Objekt
	 * hintereinander an ein jqGrid-Objekt anzuhängen
	 * @param mixed string|JqGrid|JsFunction $bindJsObj
	 */
	public function bindToJsObject( $bindJsObj )
	{
		if (is_string($bindJsObj)  && $bindJsObj !== '.'  && $bindJsObj !== '')
			$this->_bindToGrid = 'jQuery("'.$bindJsObj.'")';
		elseif ($bindJsObj instanceof JqGrid)
			$this->_bindToGrid = 'jQuery("'.$bindJsObj->get_id().'")';
		elseif ($bindJsObj instanceof JsFunction)
			$this->_bindToGrid = (string) $bindJsObj;
		elseif (is_bool($bindJsObj))
			$this->_bindToGrid = ($bindJsObj) ? 'jQuery("'.$this->_gridID.'")' : '';
		else
			$this->_bindToGrid = '';
		
		return $this;
	}
	
	public function navButtonAdd(JqGridNavGridButton $btnOptions, $pagerId)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("navButtonAdd","'.$pagerId.'",'
					.JqGridHelper::json_encode($btnOptions)
					.')');
	}
	
	/**
	* Populates a grid with the passed data (an array).
	* @abstract Populates a grid with the passed data (an array).
	* This method should be used this way - Example: <br>
	* Suppose we have data from a particular webservice (jsonresponse), then<br>
	* <pre>
	*  var mygrid = jQuery("#"+grid_id)[0];
	*  var myjsongrid = eval("("+jsonresponse.responseText+")");
	*  mygrid.addJSONData(myjsongrid);
	*  myjsongrid = null;
	*  jsonresponse =null;
	* </pre>
	*
	* will populate the data to the grid.
	* And, of course, the data in myjsongrid can be manipulated before being passed to addJSONData.<br>
	* 
	* Returns none
	* @param data
	* @return JsFunction
	*/
	public function addJSONData($data)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("addJSONData", '
					.JqGridHelper::json_encode($data).')');
	}
	
	/**
	 * Inserts a new row with id = rowid containing the data in data
	 * @abstract Inserts a new row with id = rowid 
	 * containing the data in data (an object) at the position specified 
	 * (first in the table, <br>
	 * last in the table or before or after the row specified in srcrowid).
	 * 
	 * The syntax of the data object is: {name1:value1,name2: value2…} 
	 * where name is the name of the column as described in the colModel and the value is the value.
	 *
	 *
	 * This method can insert multiple rows at once.<br>
	 * In this case the data parameter should be array defined as:<br>
	 *  [{name1:value1,name2: value2…}, {name1:value1,name2: value2…} ] <br>
	 * and the first option rowid should contain the name from data 
	 * object which should act as id of the row.<br>
	 * 
	 * It is not necessary that the name of the rowid in 
	 * this case should be a part from colModel.<br>
	 *
	 * Returns  true on success,<br>
	 *          false otherwise
	 * @param rowid
	 * @param data
	 * @param position
	 * @param srcrowid
	 * @return JsFunction
	 */
	public function addRowData($rowid, $data, $position, $srcrowid)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("addRowData", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($data).','
					.JqGridHelper::json_encode($position).','
					.JqGridHelper::json_encode($srcrowid).')');
	}
	
	/**
	 * Populates a grid with the passed data
	 * @abstract Populates a grid with the passed data.<br>
	 * This method should be used this way - Example:<br>
	 * 
	 * Suppose we have data from a particular webservice (xmlresponse), then  <br>
	 * var mygrid = jQuery("#"+grid_id)[0];<br>
	 *  mygrid.addXmlData(xmlresponse.responseXML);<br>
	 * will populate the data into the grid.<br>
	 * 
	 * And, of course, the data in xmlresponse can be manipulated before being passed to addXmlData.
	 * Returns none
	 * @param data
	 * @return JsFunction
	 */
	public function addXmlData($data)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("addXmlData", '
					.JqGridHelper::json_encode($data).')');
	}
	
	/**
	 * When called the method make it so that it is possible to select a row with Up and Down keys
	 * @abstract  When called the method make it so that it is possible 
	 * to select a row with Up and Down keys 
	 * - i.e we scroll the grid data with keys.
	 * It is possible to invoke additional events when the row is selected 
	 * and a key like enter, space, left or right are pressed.<br>
	 * To these events a selected id of the row is passed as parameter.<br>
	 * <pre>
	 * <li> bindKeys: JsObject {
	 * 	 onEnter: null,
	 *   onSpace: null,
	 *   onLeftKey: null,
	 *   onRightKey: null,
	 *   scrollingRows : true
	 * }
	 * </pre>
	 * 
	 * 
	 * Returns  jqGrid object
	 * @param object
	 * @return JsFunction
	 */
	public function bindKeys($bindKeysObject)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("bindKeys", '
					.JqGridHelper::json_encode($bindKeysObject).')');
	}
	
	/**
	 * Clears the currently loaded data from grid
	 * @abstract  Clears the currently loaded data from grid.<br>
	 * If the clearfooter parameter is set to true, 
	 * the method clears the data placed on the footer row.<br>
	 * 
	 * Returns jqGrid object
	 * @param bool clearfooter
	 * @return JsFunction
	 */
	public function clearGridData($clearfooter)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("clearGridData", '
					.JqGridHelper::json_encode($clearfooter).')');
	}
		
	/**
	 * Deletes the row with the id = rowid
	 * @abstract Deletes the row with the id = rowid.<br>
	 * This operation does not delete data from the server.<br>
	 * 
	 * Returns 
	 * - true on success,
	 * - false otherwise
	 * @param rowid
	 * @return JsFunction
	 */
	public function delRowData($rowid)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("delRowData", '
					.JqGridHelper::json_encode($rowid).')');
	}
	
	/**
	 * 
	 * InlineEditing EditRow
	 * @abstract     
	 * Passed Arguments:<br>
	 * <li> rowid: the id of the row to edit
	 * <li> keys: when set to true we can use [Enter] key to save the row and [Esc] to cancel editing.
	 * <li> oneditfunc: fires after successfully accessing the row for editing, 
	 *      prior to allowing user access to the input fields:<br>
	 *      - The row's id is passed as a parameter to this function.
	 * <li> succesfunc: if defined, this function is called immediately after the request is successful. 
	 *      This function is passed:<br>
	 *      - the data returned from the server. 
	 *      Depending on the data from server; this function should return true or false.
	 * <li> url: if defined, this parameter replaces the editurl parameter from the options array. 
	 *      If set to 'clientArray', the data is not posted to the server but rather is saved 
	 *      only to the grid (presumably for later manual saving).
	 * <li> extraparam: an array of type name: value. 
	 *      When set these values are posted along with the other values to the server.
	 * <li> aftersavefunc: if defined, this function is called after the data is saved to the server. 
	 *      Parameters passed to this function are the:<br>
	 *      - rowid and the<br> 
	 *      - response from the server request.<br> 
	 *      - Also the event is called too when the url is set to 'clientArray'.<br>
	 * <li> errorfunc: if defined, this function is called after the data is saved to the server. 
	 *      Parameters passed to this function are the:<br>
	 *      - rowid and the the <br>
	 *      - response from the server request.<br>
	 * <li> afterrestorefunc if defined, this function is called in restoreRow 
	 *      (in case the row is not saved with success) method after restoring the row. 
	 *      To this function we pass:<br>
	 *      - the rowid
	 *      
	 * @param mixed rowid
	 * @param bool keys (To use [Enter] for saving and [Esc] for canceling
	 * @param JsFunction oneditfunc (Passed Arg: rowid)
	 * @param JsFunction successfunc (Passed Arg: data returned from the server)
	 * @param string url (If set, replaces the general editurl, set to 'clientArray' for local storing and later saving)
	 * @param array extraparam (name:value pairs are sending to the the server onsave)
	 * @param JsFunction aftersavefunc (Passed Args: rowid, ServerResponse [, event if url = clientArray]
	 * @param JsFunction errorfunc (Passed Args: rowid, ServerResponse
	 * @param JsFunction afterrestorefunc (Passed Arg: rowid)
	 * @example 
	 * <pre>
	 * grid_id: is the already constructed grid
	 * jQuery("#grid_id").jqGrid(
	 * 	'editRow', rowid, keys, oneditfunc, succesfunc, url, 
	 *   extraparam, aftersavefunc, errorfunc, afterrestorefunc
	 * );
	 * </pre>
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing
	 */
	public function editRow($rowid, $keys, $oneditfunc, $successfunc, $url, $extraparam, $aftersavefunc, $errorfunc, $afterrestorefunc) 
	{
		$argc = func_num_args();
		
		$method = new JsFunction($this->_bindToGrid.'.jqGrid("editRow", '.JqGridHelper::json_encode($rowid));
		if ($argc > 1) $method->add( ', '.JqGridHelper::json_encode( $keys ) );
		if ($argc > 2) $method->add( ', '.JqGridHelper::json_encode( $oneditfunc ) );
		if ($argc > 3) $method->add( ', '.JqGridHelper::json_encode( $successfunc ) );
		if ($argc > 4) $method->add( ', '.JqGridHelper::json_encode( $url ) );
		if ($argc > 5) $method->add( ', '.JqGridHelper::json_encode( $extraparam ) );
		if ($argc > 6) $method->add( ', '.JqGridHelper::json_encode( $aftersavefunc ) );
		if ($argc > 7) $method->add( ', '.JqGridHelper::json_encode( $errorfunc ) );
		if ($argc > 8) $method->add( ', '.JqGridHelper::json_encode( $afterrestorefunc ) );
		$method->add( ')' );
		return $method;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param mixed rowid
	 * @param JsFunction afterrestorefunc (Passed Arg: rowid)
	 * @example
	 * <pre>
	 * jQuery("#grid_id").jqGrid('restoreRow',rowid, afterrestorefunc);
	 * </pre>
	 */
	public function restoreRow($rowid, $afterrestorefunc)
	{
		$argc = func_num_args();
		
		$method = new JsFunction($this->_bindToGrid.'.jqGrid("editRow", '.JqGridHelper::json_encode($rowid));
		if ($argc > 1) $method->add( ', '.JqGridHelper::json_encode( $afterrestorefunc ) );
		$method->add( ')' );
		return $method;
	}
	
	/**
	 * 
	 * InlineEditing saveRow
	 * @abstract Für Details zu den Argumenten siehe editRow<br>
	 * <b>What is posted to the server?</b><br>
	 * When the data is posted to the server we construct an object {} that contain(s):<br>
	 * <li> the name:value pair where the name is the name of the input element represented in the 
	 *      row (this is for all input elements)
	 * <li> additionally we add a pair id:rowid where the rowid is the id of the row
	 * <li> if the extraparam parameter is not empty we extend this data with the posted data
	 * 
	 * @see JqGridMethods::editRow
	 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing
	 * @param mixed rowid
	 * @param JsFunction successfunc (Passed Arg: data returned from the server)
	 * @param string url (If set, replaces the general editurl, set to 'clientArray' for local storing and later saving)
	 * @param array extraparam (name:value pairs are sending to the the server onsave)
	 * @param JsFunction aftersavefunc (Passed Args: rowid, ServerResponse [, event if url = clientArray]
	 * @param JsFunction errorfunc (Passed Args: rowid, ServerResponse
	 * @param JsFunction afterrestorefunc (Passed Arg: rowid)
	 * @example
	 * jQuery("#grid_id").jqGrid('saveRow',rowid, succesfunc, url, extraparam, aftersavefunc,errorfunc, afterrestorefunc);
	 */
	public function saveRow($rowid, $succesfunc, $url, $extraparam, $aftersavefunc, $errorfunc, $afterrestorefunc) 
	{
		
	}
	
	/**
	 * This method gets or sets data on footer
	 * @abstract This method gets or sets data on footer.<br>
	 * See footerrow in options array.
	 * 
	 * <li> action - can be 'get' or 'set'.<br>
	 *   The default is get.
	 *   'get' returns an object of type name:value, where the name is a name from colModel.<br>
	 *   This will return data from the footer.<br>
	 *   The other two options have no effect in this case.<br>
	 *   <br>
	 *  'set' takes a data array (object) and places the values in the footer.<br>
	 *  
	 * <li> data: The object should be in name:value pair, where the name is the name from colModel<br>
	 *  
	 * <li> format - default is true.<br>
	 *       This instruct the method to use the formatter (if set in colModel) when new values are set.<br>
	 *       A value of false will disable the using of formatter<br>
	 * 
	 * Returns jqGrid object
	 * @param action
	 * @param data
	 * @param format
	 * @return JsFunction
	 */
	public function footerData($action, $data, $format)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("footerData", '
					.JqGridHelper::json_encode($action).','
					.JqGridHelper::json_encode($data).','
					.JqGridHelper::json_encode($format).')');
	}
	
	/**
	 * Returns the content of the cell specified by id = rowid and column = <
	 * @abstract  Returns the content of the cell specified by id = rowid and column = iCol.<br>
	 * <li> rowid: id of the row
	 * <li> iCol can be either the column index or the name specified in colModel.<br>
	 *
	 * Do not use this method when you editing the row or cell.<br>
	 * This will return the cell content and not the actuall value of the input element
	 * Returns cell content
	 * @param rowid
	 * @param iCol
	 * @return JsFunction
	 */
	public function getCell($rowid, $iCol)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("getCell", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($iCol).')');
	}
	
	/**
	 * This method returns an array with the values from the column
	 * @abstract  This method returns an array with the values from the column.<br>
	 * <li> colname: can be either a number that represents the index of the column or a name from colModel.<br>
	 * <li> returntype: determines the type of the returned array.<br>
	 *      When set to false (default) the array contain only the values.<br>
	 *      When set to true the array contain a set of objects.<br>
	 *      The object is defined as {id:rowid, value:cellvalue} <br>
	 *      where the rowid is the id of the row and cellvalue is the value of the cell.<br>
	 *      For example, such output can be [ {id:1,value:1},{id:2,value:2} …]
	 * 
	 * <li> mathoperation: The valid options for mathoperation are - 'sum, 'avg', 'count'.<br>
	 *      If this parameter is set and is valid, the returned value is a scalar representing 
	 *      the operation applied to the all values in the column.<br>
	 *      If the parameter is not valid the returned value is empty array<br>
	 * <br>
	 * Returns array[] or value
	 * @param colname
	 * @param returntype
	 * @param mathoperation
	 * @return JsFunction
	 */
	public function getCol($colname, $returntype, $mathoperation)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("getCol", '
					.JqGridHelper::json_encode($colname).','
					.JqGridHelper::json_encode($returntype).','
					.JqGridHelper::json_encode($mathoperation).')');
	}
	
	/**
	 * This method returns an array of the id's in the current grid view<br>
	 * @abstract This method returns an array of the id's in the current grid view.<br>
	 * It returns an empty array if no data is available.<br>
	 * <br>
	 * Returns array[]
	 * @param none
	 * @return JsFunction
	 */
	public function getDataIDs()
	{
            return new JsFunction($this->_bindToGrid.'.jqGrid( "getDataIDs" )');
	}
	
	/**
	 * Returns the value of the requested parameter
	 * @abstract Returns the value of the requested parameter.<br>
	 * name is the name from the options array.<br>
	 * If the name is not set, the entry options are returned.<br>
	 * For available options, see options.
	 * Returns mixed value (Den Wert der Option oder wenn ohne name aufgerufen, werden alle Options zurückgegeben)
	 * @param name
	 * @return JsFunction
	 */
	public function getGridParam($name)
	{
            return new JsFunction($this->_bindToGrid.'.jqGrid("getGridParam", '
                .JqGridHelper::json_encode($name).')');
	}
	
	/**
	 * @abstract This method returns the index of the row in the grid table 
	 * specified by id=rowid when rowcontent set to false (default).<br>
	 * If rowcontent is set to true, it returns the entry row object.<br>
	 * If the rowid can not be found, the function returns false.<br>
	 * <br>
	 * Returns  mixed (Die Id des Datensatzes oder wenn rowcontent=true den ganzen Datensatz als Objekt)
	 * @param rowid
	 * @param rowcontent
	 * @return JsFunction
	 */
	public function getInd($rowid, $rowcontent)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("getInd", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($rowcontent).')');
	}
	
	/**
	 *
	 * @abstract Return the row data from the local array stored in data parameter 
	 * when the datatype is local<br>
	 * <br>
	 * Returns  row object
	 * @param rowid
	 * @return JsFunction
	 */
	public function getLocalRow($rowid)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("getLocalRow", '
					.JqGridHelper::json_encode($rowid).')');
	}
	
	/**
	 * Returns an array with data of the requested id = rowid. The returned ar
	 * @abstract Returns an array with data of the requested id = rowid. <br>
	 * The returned array is of type name:value, where the name is a name from colModel 
	 * and the value from the associated column in that row.<br>
	 * 
	 * It returns an empty array if the rowid can not be found.<br>
	 *
	 * 1.Do not use this method when you editing the row or cell.<br>
	 *   This will return the cell content and not the actuall value of the input element.<br>
	 * 2.the performance of this method becomes an issue.Do not use this method in the body of “for" and “when".<br>
	 *   (When calling this method, it will calculates the row datas one time.)<br>
	 * 
	 * If the rowid is not set the method return all the data from the grid in array
	 * Returns array{}
	 * @param rowid or none
	 * @return JsFunction
	 */
	public function getRowData ($rowid)
	{
		if ($rowid) 
			return new JsFunction($this->_bindToGrid.'.jqGrid("getRowData ", '
					   .JqGridHelper::json_encode($rowid or none).')');
		else 
			return new JsFunction($this->_bindToGrid.'.jqGrid("getRowData " )');
	}
	
	/**
	 * Given a single colname, it hides the column with that name
	 * @abstract Given a single colname, it hides the column with that name.<br>
	 * Given an array of colnames [“name1","name2"], it hides the columns with those names, 
	 * 'name1' and 'name2', in the example.<br>
	 * The names in colname or colnames must all be valid names from the colModel.
	 *
	 * NB: The width of the grid is not changed.
	 * Returns jqGrid object
	 * @param string colname or array [colnames]
	 * @return JsFunction
	 */
	public function hideCol($colname)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("hideCol", '
		           .JqGridHelper::json_encode($colname).')');
	}
	
	/**
	 * Reorder the grid columns based on the permutation array
	 * @abstract Reorder the grid columns based on the permutation array.<br>
	 * <li> permutation: The indexes of the permutation array are the current order, <br>
	 *      the values are the new order.<br>
	 *      By example if the array has values [1,0,2] <br>
	 *      after calling this method the first column will be reordered as second.
	 * <li> updateCells if set to true will reorder the cell data.
	 * <li> keepHeader if set to true will reorder the data above the header cells.<br>
	 * 
	 * Returns none
	 * @param permutation
	 * @param updateCells
	 * @param keepHeader
	 * @return JsFunction
	 */
	public function remapColumns($permutation, $updateCells, $keepHeader)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("remapColumns", '
					.JqGridHelper::json_encode($permutation).','
					.JqGridHelper::json_encode($updateCells).','
					.JqGridHelper::json_encode($keepHeader).')');
	}
	
	/**
	 * Resets (unselects) the selected row(s)
	 * @abstract Resets (unselects) the selected row(s).
	 * Also works in multiselect mode.<br>
	 * Returns jqGrid object
	 * @param none
	 * @return JsFunction
	 */
	public function resetSelection()
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("resetSelection" )');
	}
	
	/**
	 * Sets a new caption of the grid
	 * @abstract Sets a new caption of the grid.<br>
	 * If the Caption layer was hidden, it is shown.
	 * Returns jqGrid object
	 * @param string caption
	 * @return JsFunction
	 */
	public function setCaption($caption)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setCaption", '.JqGridHelper::json_encode($caption).')');
	}
	
	/**
	 * This method can change the content of particular cell and can set class or style
	 * @abstract This method can change the content of particular cell and can set class or style properties.<br>
	 * <br>
	 * Where:<br>
	 * <li> rowid the id of the row,<br>
	 *      colname the name of the column (this parameter can be a number (the index of the column) beginning from 0
	 * <li> data the content that can be put into the cell.<br>
	 *      If empty string the content will not be changed
	 * <li> class if class is string then we add a class to the cell using addClass; <br>
	 *       if class is an array we set the new css properties via css
	 * <li> properties sets the attribute properies of the cell
	 * <li> forceup If the parameter is set to true we perform update <br>
	 *      of the cell instead that the value is empty<br>
	 * 
	 * Returns jqGrid object
	 * @param rowid
	 * @param colname
	 * @param data
	 * @param class
	 * @param properties
	 * @param forceup
	 * @return JsFunction
	 */
	public function setCell($rowid, $colname, $data, $class, $properties, $forceup)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setCell", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($colname).','
					.JqGridHelper::json_encode($data).','
					.JqGridHelper::json_encode($class).','
					.JqGridHelper::json_encode($properties).','
					.JqGridHelper::json_encode($forceup).')');
	}
	
	/**
	 * Sets a particular parameter
	 * @abstract Sets a particular parameter.<br>
	 * Note - for some parameters to take effect a trigger(“reloadGrid") should be executed.<br>
	 * Note that with this method we can override events.<br>
	 * - The name (in the name:value pair) is the name from options array.
	 * For a particular options, see options.
	 * Returns jqGrid object
	 * @param object
	 * @return JsFunction
	 */
	public function setGridParam($object)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setGridParam", '.JqGridHelper::json_encode($object).')');
	}
	
	/**
	 * Sets the new height of the grid dynamically
	 * @abstract Sets the new height of the grid dynamically.<br>
	 * Note that the height is set only to the grid cells and not to the grid.<br>
	 * <li> new_height can be in pixels, percentage, or 'auto'.
	 * 
	 * Returns jqGrid object
	 * @param string,int new_height
	 * @return JsFunction
	 */
	public function setGridHeight($new_height)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setGridHeight", '
					.JqGridHelper::json_encode($new_height).')');
	}
	
	/**
	 * Sets a new width to the grid dynamically
	 * @abstract Sets a new width to the grid dynamically.<br><br>
	 * 
	 * The parameters are:<br>
	 * <li> new_width is the new width in pixels.
	 * <li> shrink (true or false) has the same behavior as shrinkToFit - see options.
	 *      If this parameter is not set we take the value of shrinkToFit.<br>
	 * 
	 * Returns jqGrid object
	 * @param new_width
	 * @param shrink
	 * @return JsFunction
	 */
	public function setGridWidth($new_width, $shrink)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setGridWidth", '
					.JqGridHelper::json_encode($new_width).','
					.JqGridHelper::json_encode($shrink).')');
	}
	
	/**
	 * Sets a new label in the header for the specified column; can also set attributes and classes
	 * @abstract Sets a new label in the header for the specified column; can also set attributes and classes.<br>
	 * <br>
	 * The parameters are:<br>
	 * <li> colname the name of the column (this parameter can be a number (the index of the column) beginning from 0<br>
	 * <li> data the content that can be put into the label.<br>
	 *      If empty string the content will not be changed<br>
	 * <li> class if class is string then we add a class to the label using addClass;<br>
	 *     if class is an array we set the new css properties via css<br>
	 * <li> properties sets the attribute properties of the label<br>
	 * 
	 * Returns jqGrid object
	 * @param string colname
	 * @param array data
	 * @param string class
	 * @param properties
	 * @return JsFunction
	 */
	public function setLabel($colname, $data, $class, $properties)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setLabel", '
					.JqGridHelper::json_encode($colname).','
					.JqGridHelper::json_encode($data).','
					.JqGridHelper::json_encode($class).','
					.JqGridHelper::json_encode($properties).')');
	}
	
	/**
	 * @abstract Updates the values (using the data array) in the row with rowiddata array) in the row with rowid.<br>
	 * The syntax of data array is: {name1:value1,name2: value2…} <br>
	 * where the name is the name of the column as described in the colModel and the value is the new value.<br>
	 *
	 * If the cssprop parameter is string we use addClass to add classes to the row.<br>
	 * If the parameter is object we use css to add css properties.<br>
	 * 
	 * Note that we can set properties and classes without data, 
	 * in this case we should set data to false
	 *
	 *  Do not use this method when you editing the row or cell.
	 * This will set content and and overwrite the input elements<br>
	 * 
	 * Returns 
	 * - true on success,
	 * - false otherwise
	 * @param int rowid
	 * @param data
	 * @param cssprop
	 * @return JsFunction
	 */
	public function setRowData($rowid, $data, $cssprop)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setRowData", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($data).','
					.JqGridHelper::json_encode($cssprop).')');
	}
	
	/**
	 *
	 * @abstract Toggles a selection of the row with id = rowid; <br>
	 * if onselectrow is true (the default) <br>
	 * then the event onSelectRow is launched, otherwise it is not.<br>
	 * 
	 * Returns jqGrid object
	 * @param int rowid
	 * @param bool onselectrow
	 * @return JsFunction
	 */
	public function setSelection($rowid, $onselectrow)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("setSelection", '
					.JqGridHelper::json_encode($rowid).','
					.JqGridHelper::json_encode($onselectrow).')');
	}
	
	/**
	 * Shows a column with a given colname
	 * @abstract Shows a column with a given colname.<br>
	 * If the colname is a string we show only the specified column.
	 * If colname is array of type [“name1","name2"] then the columns 
	 * with names 'name1' and 'name2' will be shown at the same time.<br>
	 * 
	 * The names in colname must be valid names from colModel.
	 * The width does not change.
	 * Returns jqGrid object
	 * @param colname
	 * @return JsFunction
	 */
	public function showCol($colname)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("showCol", '.JqGridHelper::json_encode($colname).')');
	}
	
	/**
	 * Reloads the grid with the current settings
	 * @abstract  Reloads the grid with the current settings.<br>
	 * This means that a new request is send to the server if datatype is xml or json.<br>
	 * This method should be applied to an already-constructed grid.<br>
	 * Pay attention that this method does not change HEADER information, 
	 * that means that any changes to colModel would not be affected.<br>
	 * 
	 * You should use gridUnload to reload new configuration with different colModel
	 * Returns none
	 * @param none
	 * @return JsFunction
	 */
	public function reloadGrid()
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("trigger", "reloadGrid" )');
	}
	
	public function trigger($param)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("trigger", "'.$param.'" )');
	}
	
	/**
	 *
	 * @abstract  unbind the events defined in bindKeys method
	 * Returns  jqGrid object
	 * @param none
	 * @return JsFunction
	 */
	public function unbindKeys()
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("unbindKeys")');
	}
	
	/**
	 * this synchronizes the width of the headers with the data
	 * @abstract this synchronizes the width of the headers with the data.<br>
	 * Useful when used with table drag and drop.<br>
	 * This method should be used this way - Example:<br>
	 *  var mygrid=jQuery("#grid_id")[0];<br>
	 *  mygrid.updateColumns();<br>
	 * 
	 * Returns none
	 * @param none
	 * @return JsFunction
	 */
	public function updateColumns()
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid("updateColumns")');
	}
	
	/** 
	 * Fügt dem Grid ein UI-Widget hinzu zum Auswählen der Spalten
	 * @param JqGridColumnChooserOptions $options
	 * @return JsFunction
	 */
	public function columnChooser(JqGridColumnChooserOptions $options = null)
	{
		return new JsFunction('function() { '.$this->_bindToGrid.'.jqGrid(\'columnChooser\', '.JqGridHelper::json_encode($options).')}');
	}
	
	/**
	 * Sortable Rows
	 * @abstract
	 * This method allows reordering (sorting) grid rows in a visual manner using a mouse.
	 * Calling convetions:
	 * <pre>
	 * jQuery("#list").sortableRows(options);
	 *
	 * or using the new API
	 *
	 * jQuery("#list").jqGrid('sortableRows', options);
	 * </pre>
	 * <p>
	 * where options is a object with properties listed below.<br>
	 * <p>
	 * The method is fully compatible with jQuery UI sortable widget.<br>
	 * This means that we can set any option and event available in this widget. <br>
	 * For more information on the options and events look <a href="http://jqueryui.com/demos/sortable/">here</a><br>
	 * <p>
	 * Used jQuery UI widget(s) and other plugins:<br>
	 * <li> jQuery UI core
	 * <li> jQuery UI sortable widget.
	 * <p>
	 * Known problems:<br>
	 * <li> Currently this method does not work correct in FireFox versions 3.0.x, Chrome and Safari browsers 
	 *      due to little problem in sortable widget, which we hope they will be corrected in future release 
	 *      of jQuery UI.
	 * <li> Method does not work currently when treeGrid is enabled - i.e. 
	 *      you can not use the method to reorder tree rows.
	 * 
	 * @link http://jqueryui.com/demos/sortable/
	 */
	public function sortableRows($jq_ui_sortable_options = null) 
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid(\'sortableRows\', '.JqGridHelper::json_encode($jq_ui_sortable_options).')');
	}
	
	/** 
	 * Resizable Grid: This method allow to re size the grid width and/or height in visual manner using a mouse.
	 * @abstract 
	 *
	 * Calling convetions:<br>
	 *<pre>
	 * jQuery("#list").gridResize(options);
	 * ... or using the new API
	 * jQuery("#list").jqGrid('gridResize', options);
	 * </pre>
	 * where options is a object with properties listed above.<br><br>
	 * <p>
	 * The method is fully compatible with jQuery UI resizable widget. <br>
	 * This means that we can set any option and event available in this widget. <br>
	 * For more information on the options and events look here <a href="http://jqueryui.com/demos/resizable/">[more]</a>
	 * <p>
	 * Used jQuery UI widget(s) and other plugins:
	 * <li> jQuery UI core
	 * <li> jQuery UI resizable widget.<br>
	 * <p>
	 * Known problems<br>
	 * Using the grid hide button (the button in the caption) does not hide the content created from the resizable widget. This cause appearing of borders of the grid after it is hidden.
	 * @link http://jqueryui.com/demos/resizable/
	 * @return JsFunction
	 * $param mixed array|object $options
	 */
	public function gridResize($jq_ui_resizable_options)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid(\'gridResize\', '.JqGridHelper::json_encode($jq_ui_resizable_options).')');
	}
	
	
	/**
	 * Drag and Drop rows between grids
	 * @abstract
	 * This method allow drag and drop rows between two or more grids using a mouse.
	 * <p>
	 * Calling convetions:<pre>	 *
	 * jQuery("#list").gridDnD(options);
	 * ... or using the new API
	 * jQuery("#list").jqGrid('gridDnD', options);</pre>
	 * <p>
	 * where options is a object with properties defined in php-class JqGridDnDOptions.
	 * @return JsFunction
	 * @param JqGridDnDOptions
	 * @see JqGridDnDOptions
	 */
	public function gridDnD(JqGridDnDOptions $options = null)
	{
		return new JsFunction($this->_bindToGrid.'.jqGrid(\'gridDnD\', '.JqGridHelper::json_encode($options).')');
	}

}