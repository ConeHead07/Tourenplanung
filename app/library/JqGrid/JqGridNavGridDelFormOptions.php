<?php

class JqGridNavGridDelFormOptions extends JqGridBaseObject
{
	/**
	* the initial top position of modal dialog
	* @abstract the initial top position of modal dialog.<br>
	*  The default value of 0 mean the top position from the upper left corner of the grid.<br>
	*  When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the top position from upper left corner of the window.<br>
	*
	* Default 0
	* @param
	* @return JqGridNavGridDelFormOptions 
	*/
	public function set_top($param)
	{
		$this->top = $param; return $this;
	}
	
	/**
	 * the initial left position of modal dialog
	 * @abstract the initial left position of modal dialog.<br>
	 *  The default value of 0 mean the left position from the upper left corner of the grid.<br>
	 *  When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the left position from upper left corner of the window.<br>
	 *
	 * Default 0
	 * @param
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
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
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_resize($param)
	{
		$this->resize = $param; return $this;
	}
	
	/**
	 *  url where to post data
	 * @abstract  url where to post data.<br>
	 *  If set, replaces the editurl
	 * Default null
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_url($param)
	{
		$this->url = $param; return $this;
	}
	
	/**
	 *
	 * @abstract Defines the type of request to make (“POST” or “GET”) when data is sent to the server
	 * Default POST
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_mtype($param)
	{
		$this->mtype = $param; return $this;
	}
	
	/**
	 *
	 * @abstract an array used to add content to the data posted to the server
	 * Default empty
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_delData($param)
	{
		$this->delData = $param; return $this;
	}
	
	/**
	 * If set to true uses jqModal plugin (if present) to creat the dialogs
	 * @abstract If set to true uses jqModal plugin (if present) to creat the dialogs.<br>
	 *  If set to true and the plugin is not present jqGrid uses its internal function to create dialog
	 * Default true
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_jqModal($param)
	{
		$this->jqModal = $param; return $this;
	}
	
	/**
	 *  array
	 * @abstract  array.<br>
	 *  Determines the icon of the submit button.<br>
	 *  The default value is [true,”left”,”ui-icon-delete”].<br>
	 *  The first item enables/disables the icon.<br>
	 *  The second item tells where to put the icon to left or to right of the text.<br>
	 *  The third item corresponds to valid ui icon from theme roller
	 * Default
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_delicon($param)
	{
		$this->delicon = $param; return $this;
	}
	
	/**
	 *  Array
	 * @abstract  Array.<br>
	 *  Determines the icon of the cancel button.<br>
	 *  The default values are [true,”left”,”ui-icon-cancel”].<br>
	 *  For description of these see delicon
	 * Default
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_cancelicon($param)
	{
		$this->cancelicon = $param; return $this;
	}
	
	/**
	 *
	 * @abstract reload grid data after posting
	 * Default true
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_reloadAfterSubmit($param)
	{
		$this->reloadAfterSubmit = $param; return $this;
	}
	
	/**
	 * When set to true the modal window can be closed with ESC key from the user
	 * @abstract When set to true the modal window can be closed with ESC key from the user.<br>
	 *
	 * Default false
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_closeOnEscape($param)
	{
		$this->closeOnEscape = $param; return $this;
	}
	
	/**
	 * This option allow to set global ajax settings for the form editiing when we dele
	 * @abstract This option allow to set global ajax settings for the form editiing when we delete the data to the server.<br>
	 *  Note that with this option is possible to overwrite all current ajax setting in the save request including the complete event.<br>
	 *
	 * Default empty object
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_ajaxDelOptions($param)
	{
		$this->ajaxDelOptions = $param; return $this;
	}
	
	/**
	 * The starting z-index for the dialog
	 * @abstract The starting z-index for the dialog.<br>
	 *  If you will see the dialog form under another elements or dialogs you should use the parameter with some value higher as default value 950.<br>
	 *  In the most cases it should be the value higher as 1000 - the default value of jQuery UI dialog.<br>
	 *
	 * Default 950
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_zIndex($param)
	{
		$this->zIndex = $param; return $this;
	}
	
	/**
	*  This event fires immediately after all actions and events are completed and the
	* @abstract  This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid.<br>
	*  <br>
	*  afterComplete : function (response, postdata, formid) {…} <br>
	*  where <br>
	*  <em>response</em> is the data returned from the server (if any) <br>
	*
	*  <em>postdata</em> an array, is the data sent to the server <br>
	*  <em>formid</em> is the jQuery object of form id, you can use formid[0].<br>
	* id to get form id.<br>
	*
	* @param
	* @return JqGridNavGridDelFormOptions 
	*/
	public function set_afterComplete($param)
	{
		$this->afterComplete = $param; return $this;
	}
	
	/**
	 * fires after showing the form; receives as Parameter the id of the constructed fo
	 * @abstract fires after showing the form; receives as Parameter the id of the constructed form.<br>
	 *  <br>
	 *
	 *  afterShowForm : finction (formid) {…}
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_afterShowForm($param)
	{
		$this->afterShowForm = $param; return $this;
	}
	
	/**
	 *  fires after response has been received from server
	 * @abstract  fires after response has been received from server.<br>
	 *  Typically used to display status from server (e.<br>
	 * g.<br>
	 * , the data is successfully deleted or the delete cancelled for server-side reasons).<br>
	 *  Receives as parameters the data returned from the request and an array of the posted values of type id=value1,value2.<br>
	 *  <br>
	 *  When used this event should return array with the following items [success, message] <br>
	 *  where <br>
	 *
	 *  <em>success</em> is a boolean value if true the process continues, if false a error <em>message</em> appear and all other processing is stoped.<br>
	 *  <br>
	 *  afterSubmit : function(response, postdata) <br>
	 *  { <br>
	 *  … <br>
	 *
	 *  return [succes,message] <br>
	 *  }
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_afterSubmit($param)
	{
		$this->afterSubmit = $param; return $this;
	}
	
	/**
	 * fires before showing the form with the new data; receives as Parameter the id of
	 * @abstract fires before showing the form with the new data; receives as Parameter the id of the constructed form.<br>
	 *  <br>
	 *  beforeShowForm : function(formid) {…}
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_beforeShowForm($param)
	{
		$this->beforeShowForm = $param; return $this;
	}
	
	/**
	 * fires before the data is submitted to the server
	 * @abstract fires before the data is submitted to the server.<br>
	 *  Recieves as parameter the posted data array and the formid.<br>
	 *  <br>
	 *  beforeSubmit : function(postdata, formid) { <br>
	 *  … <br>
	 *  return[success,message]; <br>
	 *
	 *  } <br>
	 *  When defined this event should return array with the following values <br>
	 *  <em>success</em> boolean indicating if the proccess should continue (true) or a error (false) <em>message</em> should appear to the user
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_beforeSubmit($param)
	{
		$this->beforeSubmit = $param; return $this;
	}
	
	/**
	 * fires after the submit button is clicked and the postdata is constructed
	 * @abstract fires after the submit button is clicked and the postdata is constructed.<br>
	 *  Parameters passed to this event is a options array of the method.<br>
	 *  The event should return array of type {} which extends the postdata array.<br>
	 *  <br>
	 *  onclickSubmit : function(params) { <br>
	 *  … <br>
	 *  return {add_data} <br>
	 *   }
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_onclickSubmit($param)
	{
		$this->onclickSubmit = $param; return $this;
	}
	
	/**
	 * This event is called just before closing the form and when a close icon is click
	 * @abstract This event is called just before closing the form and when a close icon is clicked, a cancel button is clicked, ESC key is pressed or click on overlay (if jqModal is present).<br>
	 *  The event can return (optionally) true or false.<br>
	 *  If the return value is true the form is closed, if false the form does not close.<br>
	 *  If nothing is returned the form is closed
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_onClose($param)
	{
		$this->onClose = $param; return $this;
	}
	
	/**
	 * The event (can) fire when error occurs from the ajax call and can be used for be
	 * @abstract The event (can) fire when error occurs from the ajax call and can be used for better formatting of the error messages.<br>
	 *  To this event is passed response from the server.<br>
	 *  The event should return single message (not array), which then is displayed to the user.<br>
	 *
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_errorTextFormat($param)
	{
		$this->errorTextFormat = $param; return $this;
	}
	
	/**
	 * If set this event can serialize the data passed to the ajax request when we save
	 * @abstract If set this event can serialize the data passed to the ajax request when we save a form data.<br>
	 *  The function should return the serialized data.<br>
	 *  This event can be used when a custom data should be passed to the server - e.<br>
	 * g - JSON string, <acronym title="Extensible Markup Language">XML</acronym> string and etc.<br>
	 *  To this event is passed the data which will be posted to the server.<br>
	 *
	 * @param
	 * @return JqGridNavGridDelFormOptions 
	 */
	public function set_serializeDelData($param)
	{
		$this->serializeDelData = $param; return $this;
	}
	
}