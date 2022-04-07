<?php

class JqGridDnDOptions extends JqGridBaseObject
{

	/**
	 * Determines the target grid(s) to which the row should be dropped
	 * @abstract Determines the target grid(s) to which the row should be dropped.
	 * The option is a string.
	 * In case of more than one grid the ids should be delemited with comma - i.e "#grid1, #grid2"
	 * Default empty string
	 * @param string $connectWith
	 * @return JqGridDnDOptions
	 */
	public function set_connectWith($connectWith)
	{
		$this->connectWith = $connectWith;
		return $this;
	}

	/**
	 * This event raises when we start drag a row from the source grid (i.e
	 * @abstract This event raises when we start drag a row from the source grid (i.e.
	 * to which this method is applied).
	 * Parameters passed to this event are the event handler and a prepared ui object.
	 * For more information refer to jQuery UI draggable events
	 * Default null
	 * @param JsFunction $onstart
	 * @return JqGridDnDOptions
	 */
	public function set_onstart($onstart)
	{
		$this->onstart = ($onstart instanceof JsFunction) ? $onstart : (new JsFunction($onstart));
	}

	/**
	 * This event is triggered when dragging stops
	 * @abstract This event is triggered when dragging stops.
	 * Parameters passed to this even are the event handler and a prepared ui object.
	 * For more information refer to jQuery UI draggable events
	 * Default null
	 * @param JsFunction $onstop
	 * @return JqGridDnDOptions
	 */
	public function set_onstop($onstop)
	{
		$this->onstop = ($onstop instanceof JsFunction) ? $onstop : (new JsFunction($onstop));
	}

	/**
	 * This event raises before droping the row to the grid specified in connectWith op
	 * @abstract This event raises before droping the row to the grid specified in connectWith option.
	 * Parameters passed to this event are the event handler, prepared ui object, data which will be inserted into the grid in name value pair, source grid object and target(this) grid object.
	 *
	 *  If the event return object in value name pair this object will be inserted into the target grid.
	 * Default null
	 * @param JsFunction $beforedrop
	 * @return JqGridDnDOptions
	 */
	public function set_beforedrop($beforedrop)
	{
		$this->beforedrop = ($beforedrop instanceof JsFunction) ? $beforedrop : (new JsFunction($beforedrop));
	}

	/**
	 * This event raises after the droping the row to the grid specified in connectWith
	 * @abstract This event raises after the droping the row to the grid specified in connectWith option.
	 * Parameters passed to this event are the event handler, prepared ui object, data which is inserted into the grid in name value pair.
	 * For more information refer to jQuery UI droppable events
	 * Default null
	 * @param JsFunction $ondrop
	 * @return JqGridDnDOptions
	 */
	public function set_ondrop($ondrop)
	{
		$this->ondrop = ($ondrop instanceof JsFunction) ? $ondrop : (new JsFunction($ondrop));
	}

	/**
	 * Predefined options which can be applied to the droppable grid (specified with co
	 * @abstract Predefined options which can be applied to the droppable grid (specified with connectWith option above).
	 * Also you can set any option and event (except drop event which is replaced with ondrop event listed above).
	 * For more information refer to jQuery UI droppable
	 * Default  { 
	 * activeClass : "ui-state-active",
	 * 	hoverClass : "ui-state-hover"
	 * }
	 * @param object $drop_opts
	 * @return JqGridDnDOptions
	 */
	public function set_drop_opts($drop_opts)
	{
		$this->drop_opts = $drop_opts;
		return $this;
	}

	/**
	 * Predefined options which can be applied to the draggable grid (i.e
	 * @abstract Predefined options which can be applied to the draggable grid (i.e.
	 * to which this method is applied).
	 * Also you can set any option and event (except start and stop events which are replaced with onstart  and onstop events listed above).
	 * For more information refer to jQuery UI draggable
	 * Default  { 
	 *      revert: "invalid",
	 * 	    helper: "clone",
	 * 	    cursor: "move",
	 * 	 appendTo : "#jqgrid_dnd",
	 * 	    zIndex: 5000
	 *  }
	 * @param object $drag_opts
	 * @return JqGridDnDOptions
	 */
	public function set_drag_opts($drag_opts)
	{
		$this->drag_opts = $drag_opts;
		return $this;
	}

	/**
	 * If set to true this means that only fields that have equal names will be added t
	 * @abstract If set to true this means that only fields that have equal names will be added to the target grid.
	 * Note that we use addRowData to insert new row, which means that if some field with name "a" on source grid is hidden they can appear on the target grid.
	 * The default value of false mean that the grid data will be added to the target counted from the first column from source.
	 * Default false
	 * @param boolean $dropbyname
	 * @return JqGridDnDOptions
	 */
	public function set_dropbyname($dropbyname)
	{
		$this->dropbyname = $dropbyname;
		return $this;
	}

	/**
	 * Determines where to add the new row
	 * @abstract Determines where to add the new row.
	 * Can be first which mean as first row of the grid and last - as last row in the target grid.
	 *
	 * Default first
	 * @param string $droppos
	 * @return JqGridDnDOptions
	 */
	public function set_droppos($droppos)
	{
		$this->droppos = $droppos;
		return $this;
	}

	/**
	 * This option determines how the new row id should be generated
	 * @abstract This option determines how the new row id should be generated.
	 * If this option is true we generate a id wich begin with string setted with the option autoidprefix (see below) and a random number.
	 * If this option is false the id can be either the the next record count or value determined by key property in colModel.
	 *
	 *  If the parameter is defined as function this function should return value which will act as id to the target grid.
	 * Parameters passed in this case is the data array which will be inserted into the target grid row
	 * Default true
	 * @param boolean $autoid
	 * @return JqGridDnDOptions
	 */
	public function set_autoid($autoid)
	{
		$this->autoid = $autoid;
		return $this;
	}

	/**
	 * This option have sense only if the option autoid is set to true and determines t
	 * @abstract This option have sense only if the option autoid is set to true and determines the prefix of the new genearted id.
	 *
	 * Default dnd_
	 * @param string $autoidprefix
	 * @return JqGridDnDOptions
	 */
	public function set_autoidprefix($autoidprefix)
	{
		$this->autoidprefix = $autoidprefix;
		return $this;
	}

}
