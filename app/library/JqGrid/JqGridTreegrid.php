<?php

class JqGridTreegrid extends JqGridBaseObject
{
	
	
	// PROPERTIES
	/**
	*
	* @abstract when true, the tree is expanded and/or collapsed when we click 
	* on the text of the expanded column, not only on the image
	* Default true
	* @param boolean
	* @return JqGridTreegrid
	*/
	public function set_ExpandColClick($param)
	{
		$this->ExpandColClick = $param; return $this;
	}
	
	/**
	 * indicates which column (name from colModel) should be used to expand the tree gr
	 * @abstract indicates which column (name from colModel) should be used to expand 
	 * the tree grid.<br>
	 * If not set the first one is used.<br>
	 * Valid only when <span class="search_hit">treeGrid</span> option is set to true.<br>
	 *
	 * Default null
	 * @param string
	 * @return JqGridTreegrid
	 */
	public function set_ExpandColumn($param)
	{
		$this->ExpandColumn = $param; return $this;
	}
	
	/**
	 * Determines the initial datatype (see datatype option)
	 * @abstract Determines the initial datatype (see datatype option).<br>
	 *  Usually this should not be changed.<br>
	 *  During the reading process this option is equal to the datatype option.<br>
	 *
	 * Default null
	 * @param mixed
	 * @return JqGridTreegrid
	 */
	public function set_treedatatype($param)
	{
		$this->treedatatype = $param; return $this;
	}
	
	/**
	 * Enables (disables) the tree grid format
	 * @abstract Enables (disables) the tree grid format.<br>
	 *
	 * Default false
	 * @param boolean
	 * @return JqGridTreegrid
	 */
	public function set_treeGrid($param)
	{
		$this->treeGrid = $param; return $this;
	}
	
	/**
	 * Deteremines the method used for the <span class="search_hit">treeGrid</span>
	 * @abstract Deteremines the method used for the <span class="search_hit">treeGrid</span>.<br>
	 *  Can be nested or adjacency.<br>
	 *
	 * Default nested
	 * @param string
	 * @return JqGridTreegrid
	 */
	public function set_treeGridModel($param)
	{
		$this->treeGridModel = $param; return $this;
	}
	
	/**
	 * This array set the icons used in the tree
	 * @abstract This array set the icons used in the tree.<br>
	 *  The icons should be a valid names from UI theme roller images.<br>
	 *  The default values are: {plus:'ui-icon-triangle-1-e',minus:'ui-icon-triangle-1-s',leaf:'ui-icon-radio-off'}
	 * Default
	 * @param array
	 * @return JqGridTreegrid
	 */
	public function set_treeIcons($param)
	{
		$this->treeIcons = $param; return $this;
	}
	
	/**
	 * extends the colModel defined in the basic grid
	 * @abstract extends the colModel defined in the basic grid.<br>
	 *  The fields described here are added to end of the colModel array and are hidden.<br>
	 *  This means that the data returned from the server should have values for these fields.<br>
	 *  For a full description of all valid values see below.<br>
	 *
	 * Default
	 * @param array
	 * @return JqGridTreegrid
	 */
	public function set_treeReader($param)
	{
		$this->treeReader = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Determines the level where the root element begins when <span class="search_hit">treeGrid</span> is enabled
	 * Default 0
	 * @param numeric
	 * @return JqGridTreegrid
	 */
	public function set_tree_root_level($param)
	{
		$this->tree_root_level = $param; return $this;
	}
	
	// METHODEN
	/**
	*  Add a node in the tree according the value of the parentid parameter
	* @abstract  Add a node in the tree according the value of the parentid parameter.<br>
	*  The nodeid is the unique values in the row.<br>
	*  If set to empty string the method gets the next max number + 1 from the data.<br>
	*  if parendid is null the node is added as root.<br>
	*  If the parentid is valid id of existing row the data is added as child of the that row.<br>
	*  Data is a data to be inserted.<br>
	*
	* @param  nodeid, parentid, data
	* @return JqGridTreegrid
	*/
	public function set_addChildNode($param)
	{
		$this->addChildNode = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Collapse the node at specified record
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_collapseNode($param)
	{
		$this->collapseNode = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Collapse the current row
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_collapseRow($param)
	{
		$this->collapseRow = $param; return $this;
	}
	
	/**
	 * Where rowid is the id of the row
	 * @abstract Where rowid is the id of the row.<br>
	 *  Deletes the specified node and all child nodes of that node.<br>
	 *  Does not delete the node at server
	 * @param rowid
	 * @return JqGridTreegrid
	 */
	public function set_delTreeNode($param)
	{
		$this->delTreeNode = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Expand the node at the specified record
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_expandNode($param)
	{
		$this->expandNode = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Expand the current row
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_expandRow($param)
	{
		$this->expandRow = $param; return $this;
	}
	
	/**
	 *
	 * @abstract returns array of the ancestors of the specified record
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_getNodeAncestors($param)
	{
		$this->getNodeAncestors = $param; return $this;
	}
	
	/**
	 *
	 * @abstract returns the depth of the specified record
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_getNodeDepth($param)
	{
		$this->getNodeDepth = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Returns the parent node of the specified record
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_getNodeParent($param)
	{
		$this->getNodeParent = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Returns array of child nodes of the specified record; 
	 * returns empty array if none
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_getNodeChildren($param)
	{
		$this->getNodeChildren = $param; return $this;
	}
	
	/**
	 * Returns an array of the current root nodes
	 * @abstract Returns an array of the current root nodes.<br>
	 *
	 * @param none
	 * @return JqGridTreegrid
	 */
	public function set_getRootNodes($param)
	{
		$this->getRootNodes = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Returns true if the node is already loaded
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_isNodeLoaded($param)
	{
		$this->isNodeLoaded = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Returns true or false if the node is visible or not
	 * @param record
	 * @return JqGridTreegrid
	 */
	public function set_isVisibleNode($param)
	{
		$this->isVisibleNode = $param; return $this;
	}
	
	/**
	 *
	 * @abstract  The same as setRowData
	 * @param rowid, data
	 * @return JqGridTreegrid
	 */
	public function set_setTreeRow($param)
	{
		$this->setTreeRow = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Direction is 1 (meaning ascending) or -1 (meaning descending); 
	 * sorts the tree with the currently set sortname (sortname is from grid option)
	 * @param direction
	 * @return JqGridTreegrid
	 */
	public function set_SortTree($param)
	{
		$this->SortTree = $param; return $this;
	}
	
}