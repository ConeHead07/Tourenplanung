<?php

class JqGridNavGridViewFormOptions extends JqGridBaseObject
{

	/**
	* the initial top position of modal dialog
	* @abstract the initial top position of modal dialog.<br>
	*  The default value of 0 mean the top position from the upper left corner of the grid.<br>
	*  When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the top position 
	*  from upper left corner of the window.<br>
	*
	* Default 0
	* @param
	* @return JqGridNavGridViewFormOptions
	*/
	public function set_top($param)
	{
		$this->top = $param; return $this;
	}
	
	/**
	 * the initial left position of modal dialog
	 * @abstract the initial left position of modal dialog.<br>
	 *  The default value of 0 mean the left position from the upper left corner of the grid.<br>
	 *  When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean 
	 *  the left position from upper left corner of the window.<br>
	 *
	 * Default 0
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_left($param)
	{
		$this->left = $param; return $this;
	}
	
	/**
	 *
	 * @abstract the width of confirmation dialog
	 * Default 300
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_width($param)
	{
		$this->width = $param; return $this;
	}
	
	/**
	 *
	 * @abstract the entry height of confirmation dialog
	 * Default auto
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_height($param)
	{
		$this->height = $param; return $this;
	}
	
	/**
	 * the parameter control the scrolling content - i
	 * @abstract the parameter control the scrolling content - i.<br>
	 * e between the modal header and modal footer.<br>
	 *
	 * Default auto
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_dataheight($param)
	{
		$this->dataheight = $param; return $this;
	}
	
	/**
	 * determines if the dialog will be modal
	 * @abstract determines if the dialog will be modal.<br>
	 *  Also works only if jqModal plugin is present
	 * Default false
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_modal($param)
	{
		$this->modal = $param; return $this;
	}
	
	/**
	 * Determines if the dialog is dragabale
	 * @abstract Determines if the dialog is dragabale.<br>
	 *  Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UI
	 * Default  true
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_drag($param)
	{
		$this->drag = $param; return $this;
	}
	
	/**
	 * determines if the dialog can be resized
	 * @abstract determines if the dialog can be resized.<br>
	 *  Works only is jqDnR plugin is available or resizable widget is present from jQuery UI
	 * Default true
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_resize($param)
	{
		$this->resize = $param; return $this;
	}
	
	/**
	 * If set to true uses jqModal plugin (if present) to creat the dialogs
	 * @abstract If set to true uses jqModal plugin (if present) to creat the dialogs.<br>
	 *  If set to true and the plugin is not present jqGrid uses its internal function to create dialog
	 * Default true
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_jqModal($param)
	{
		$this->jqModal = $param; return $this;
	}
	
	/**
	 *
	 * @abstract When set this information is placed just after the modal header as additional row
	 * Default empty string
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_topinfo($param)
	{
		$this->topinfo = $param; return $this;
	}
	
	/**
	 *
	 * @abstract When set this information is placed just after the buttons of the form as additional row
	 * Default empty string
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_bottominfo($param)
	{
		$this->bottominfo = $param; return $this;
	}
	
	/**
	 *  Array
	 * @abstract  Array.<br>
	 *  Determines the icon of the cancel button.<br>
	 *  The default values are [true,”left”,”ui-icon-close”].<br>
	 *  For description of these see saveicon
	 * Default
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_closeicon($param)
	{
		$this->closeicon = $param; return $this;
	}
	
	/**
	 *  array
	 * @abstract  array.<br>
	 *  This option works only in edit mode and  add keyboard navigation, which allow us to navigate through the records while in form editing pressing certain keys.<br>
	 *  The default state is disabled.<br>
	 *  The first item enables/disables the navigation.<br>
	 *  The second item corresponds to reccord up and by default is the  the key code for Up key.<br>
	 *  The third item corresponds to reccord down and by default is the  key code for Down key
	 * Default [false,38,40]
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_navkeys($param)
	{
		$this->navkeys = $param; return $this;
	}
	
	/**
	 * When set to true the modal window can be closed with ESC key from the user
	 * @abstract When set to true the modal window can be closed with ESC key from the user.<br>
	 *
	 * Default false
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_closeOnEscape($param)
	{
		$this->closeOnEscape = $param; return $this;
	}
	
	/**
	 * Since we construct the view with table element it is difficult to calculate, in
	 * @abstract Since we construct the view with table element it is difficult to calculate, in this case, how much width is needed for the labels.<br>
	 *  Depending on the needs this value can be increased or decreased
	 * Default 30%
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_labelswidth($param)
	{
		$this->labelswidth = $param; return $this;
	}
	
	/**
	 *
	 * @abstract  This option enable or disable the appearing of the previous and next buttons (pager buttons) in the form
	 * Default true
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_viewPagerButtons($param)
	{
		$this->viewPagerButtons = $param; return $this;
	}
	
	/**
	 * The starting z-index for the dialog
	 * @abstract The starting z-index for the dialog.<br>
	 *  If you will see the dialog form under another elements or dialogs you should use the parameter with some value higher as default value 950.<br>
	 *  In the most cases it should be the value higher as 1000 - the default value of jQuery UI dialog.<br>
	 *
	 * Default 950
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function set_zIndex($param)
	{
		$this->zIndex = $param; return $this;
	}
	
	/**
	* This event is called just before closing the form and when a close icon is click
	* @abstract This event is called just before closing the form and when a close icon is clicked, a cancel button is clicked, ESC key is pressed or click on overlay (if jqModal is present).<br>
	*  The event can return (optionally) true or false.<br>
	*  If the return value is true the form is closed, if false the form does not close.<br>
	*  If nothing is returned the form is closed
	* @param
	* @return JqGridNavGridViewFormOptions
	*/
	public function onClose($param)
	{
		$this->onClose = $param; return $this;
	}
	
	/**
	 * fires before showing the form with the new data; receives as Parameter the id of
	 * @abstract fires before showing the form with the new data; receives as Parameter the id of the constructed form.<br>
	 *  <br>
	 beforeShowForm : function(formid) { …}
	 * @param
	 * @return JqGridNavGridViewFormOptions
	 */
	public function onbeforeShowForm($param)
	{
		$this->beforeShowForm = $param; return $this;
	}
	
}