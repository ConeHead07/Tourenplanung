<?php

class JqGridNavGridEditFormOptions extends JqGridBaseObject
{
	//Properties
	
	/**
	* the initial top position of modal dialog. 
	* @param int
	* @abstract the initial top position of modal dialog.<br>
	* The default value of 0 mean the top position from the upper left corner of the grid.<br>
	* When jqModal option is true (see below) and jqModal plugin is present any value different 
	* from 0 mean the top position from upper left corner of the window.<br>
	* Default 0
	* @return JqGridNavGridEditFormOptions
	*/
	public function set_top($param)
	{
		$this->top = $param;  return $this;
	}
	
	/**
	 *the initial left position of modal dialog. 
	 * @param int
	 * @abstract the initial left position of modal dialog.<br>
	 * The default value of 0 mean the left position from the upper left corner of the grid.<br>
	 * When jqModal option is true (see below) and jqModal plugin is present any value different 
	 * from 0 mean the left position from upper left corner of the window.<br>
	 * Default 0
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_left($param)
	{
		$this->left = $param;  return $this;
	}
	
	/**
	 * the width of confirmation dialogDefault. Default 300
	 * @param int|string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_width($param)
	{
		$this->width = $param;  return $this;
	}
	
	/**
	 * the entry height of confirmation dialogDefault. Default auto
	 * @param int|string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_height($param)
	{
		$this->height = $param;  return $this;
	}
	
	/**
	 * the parameter control the scrolling content - i.e between the modal header and modal footer. Default auto
	 * @param int|string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_dataheight($param)
	{
		$this->dataheight = $param;  return $this;
	}
	
	/**
	 * determines if the dialog will be modal. Also works only if jqModal plugin is present. Default false
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_modal($param)
	{
		$this->modal = $param;  return $this;
	}
	
	/**
	 * Determines if the dialog is dragabale. Default true
	 * @param bool
	 * @abstract Determines if the dialog is dragabale.<br>
	 * Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UIDefault  true
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_drag($param)
	{
		$this->drag = $param;  return $this;
	}
	
	/**
	 * determines if the dialog can be resized. Default true
	 * @param bool
	 * @abstract determines if the dialog can be resized.<br>
	 * Works only is jqDnR plugin is available or resizable widget is present from jQuery UI.<br>
	 * Default true
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_resize($param)
	{
		$this->drag = $param;  return $this;
	}
	
	
	/**
	 * url where to post data. If set, replaces the editurlDefault null
	 * @param string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_url($param)
	{
		$this->url = $param;  return $this;
	}
	
	/**
	 * Defines the type of request to make (“POST” or “GET”) when data is sent to the server. Default POST
	 * @param string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_mtype($param)
	{
		$this->mtype = $param;  return $this;
	}
	
	/**
	 * an array used to add content to the data posted to the server. Default empty
	 * @param array
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_editData($param)
	{
		$this->editData = $param;  return $this;
	}
	
	/**
	 * Recreate Form on everytime (useful if colModel can change). Default false
	 * @param bool
	 * @abstract when set to true the form is recreated every time the dialog is activated<br>
	 *  with the new options from colModel (if they are changed)<br>
	 *  Default false
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_recreateForm($param)
	{
		$this->recreateForm = $param;  return $this;
	}
	
	/**
	 * If set to true uses jqModal plugin (if present) to creat the dialogs. Default true
	 * @param bool
	 * @abstract If set to true uses jqModal plugin (if present) to creat the dialogs.<br>
	 * If set to true and the plugin is not present jqGrid uses its internal function to create dialog<br>
	 * Default true
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_jqModal($param)
	{
		$this->jqModal = $param;  return $this;
	}
	
	/**
	 * Controls where the row just added is placed: 'first', 'last'. Default first
	 * @param string
	 * @abstract Controls where the row just added is placed:<br>
	 * - 'first' at the top of the gird<br>
	 * - 'last' at the bottom.<br>
	 * Where the new row is to appear in its natural sort order, set reloadAfterSubmit: true<br>
	 * Default 'first'
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_addedrow($param)
	{
		$this->addedrow = $param;  return $this;
	}
	
	/**
	 * When set this information is placed just after the modal header as additional row. Default empty
	 * @param string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_topinfo($param)
	{
		$this->topinfo = $param;  return $this;
	}
	
	/**
	 * When set this information is placed just after the buttons of the form as additional row. Default empty
	 * @param string
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_bottominfo($param)
	{
		$this->bottominfo = $param;  return $this;
	}
	
	/**
	 * Determines the icon of the submit button. 
	 * @param array
	 * @abstract array. Determines the icon of the submit button.<br>
	 * The default value is [true,”left”,”ui-icon-disk”].<br>
	 * The first item enables/disables the icon.<br>
	 * The second item tells where to put the icon to left or to right of the text.<br>
	 * The third item corresponds to valid ui icon from theme roller
	 * Default [true,”left”,”ui-icon-disk”]
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_saveicon($param)
	{
		$this->saveicon = $param;  return $this;
	}
	
	/**
	 * Determines the icon of the cancel button. Default [true,”left”,”ui-icon-close”]
	 * @param array
	 * @abstract Determines the icon of the cancel button.<br>
	 * The default values are [true,”left”,”ui-icon-close”].<br>
	 * For description of these see saveiconDefault
	 * @see JqGridNavGridEditFormOptions::init_saveicon
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_closeicon($param)
	{
		$this->closeicon = $param;  return $this;
	}
	
	/**
	 * Determines the possibility to save the form with pressing a certain key.
	 * @param array
	 * @abstract Determines the possibility to save the form with pressing a certain key.<br>
	 * The first item enables/disables saving with pressing certain key.<br>
	 * The second item corresponds to key code for saving.<br>
	 * If enabled the default value for saving is [Enter]. <br><br>
	 * <b>Note</b> that this binding should be used for both adding and editing a row. 
	 * Since the binding is for the form, there is no possibility to have one key in add and another in edit mode.<br>
	 * Default [false,13]
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_savekey($param)
	{
		$this->savekey = $param;  return $this; 
	}
	
	/**
	 * This option works only in edit mode and add keyboard navigation (through the records while in form editing)
	 * @param array
	 * @abstract This option works only in edit mode and  add keyboard navigation, 
	 * which allow us to navigate through the records while in form editing pressing certain keys.<br>
	 * The default state is disabled.<br>
	 * The first item enables/disables the navigation.<br>
	 * The second item corresponds to reccord up and by default is the  the key code for Up key. <br>
	 * The third item corresponds to reccord down and by default is the  key code for Down key.<br>
	 * Default [false,38,40]
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_navkeys($param)
	{
		$this->navkeys = $param;  return $this;
	}
	
	/**
	 * Only for editing mode.
	 * @param bool
	 * @abstract This option work only in editing mode. 
	 * If Set to true this option will work only when a submit button is clicked 
	 * and if any data is changed in the form.<br>
	 * If the data is changed a dialog message appear where the user is asked to confirm the changes or cancel it.<br>
	 * Pressing cancel button of the new dialog will return to the form, but does not set the values to its original state.<br>
	 * Default false
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_checkOnSubmit($param)
	{
		$this->checkOnSubmit = $param;  return $this;
	}
	
	
	/**
	 * Only for editing mode.
	 * @abstract This option is applicable in add and edit mode. 
	 * When this option is set to true the behaviour as follow: <br>
	 * 	when something is changed in the form and the user click on Cancel button, 
	 *  navigator buttons, close button (on upper right corner of the form), 
	 *  in overlay (if available) or press Esc key (if set) a message box appear asking 
	 *  the user to save the changes, not to save the changes or go back in the grid cancel all changes 
	 *  (this will close the modal form)<br>
	 *  Default false
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_checkOnUpdate($param)
	{
		$this->checkOnUpdate = $param;  return $this;
	}
	
	/**
	 * when add mode, close the dialog after add record. Default false
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_closeAfterAdd($param)
	{
		$this->closeAfterAdd = $param;  return $this;
	}
	
	/**
	 * when add mode, clear the data after adding data. Default true
	 * @param
	 * @abstract
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_clearAfterAdd($param)
	{
		$this->clearAfterAdd = $param;  return $this;
	}
	
	/**
	 * when in edit mode, close the dialog after editing. Default false
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_closeAfterEdit($param)
	{
		$this->closeAfterEdit = $param;  return $this;
	}
	
	/**
	 * reload grid data after posting. Default true
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_reloadAfterSubmit($param)
	{
		$this->reloadAfterSubmit = $param;  return $this;
	}
	
	/**
	 * When set to true the modal window can be closed with ESC key from the user. Default false
	 * @param bool
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_closeOnEscape($param)
	{
		$this->closeOnEscape = $param;  return $this;
	}
	
	/**
	 * Global ajax settings for the form editing
	 * @param JsFunction|array Javascript-Objekt
	 * @abstract This option allow to set global ajax settings for the form editing when we save the data to the server.<br>
	 * Note that with this option is possible to overwrite all current ajax setting in the save request including the complete event.<br>
	 * Default empty object
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_ajaxEditOptions($param)
	{
		$this->ajaxEditOptions = $param;  return $this;
	}
	
	/**
	 * This option enable or disable the appearing of the previous and next buttons. Default true
	 * @param bool
	 * @abstract This option enable or disable the appearing of the previous and next buttons (pager buttons) in the form.<br>
	 * Default true
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_viewPagerButtons($param)
	{
		$this->viewPagerButtons = $param;  return $this;
	}
	
	/**
	 * The starting z-index for the dialog. Default 950
	 * @param int
	 * @abstract The starting z-index for the dialog.<br>
	 * If you will see the dialog form under another elements or dialogs 
	 * you should use the parameter with some value higher as default value 950.
	 * In the most cases it should be the value higher as 1000 - the default value of jQuery UI dialog.<br>
	 * Default 950
	 * @return JqGridNavGridEditFormOptions
	 */
	public function set_zIndex($param)
	{
		$this->zIndex = $param;  return $this;
	}
	

	// Events
	/**
	* afterclickPgButtons : function(whichbutton, formid, rowid) {…}
	* @param JsFunction, string
	* @abstract This event can be used only when we are in edit mode and the navigator buttons are enabled; 
	* it fires after the data for the new row is loaded from the grid, allowing modification of the data or 
	* form before the form is redisplayed. <br>
	* afterclickPgButtons : function(whichbutton, formid, rowid) {…} <br>
	* where <br>
	* 		<em>whichbutton</em> is either 'prev' or 'next' <br>
	* 		<em>formid</em> is the jQuery object of form id, you can use formid[0].id to get form id. <br>
	* 		<em>rowid</em> is the id of the current row
	* @return JqGridNavGridEditFormOptions
	*/
	public function onafterclickPgButtons($fn)
	{
		$this->afterclickPgButtons = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}
	
	/**
	 * function (response, postdata, formid) { … }
	* @param JsFunction, string
	* @abstract This event fires immediately after all actions and events are completed 
	* and the row is inserted or updated in the grid. <br>
	* afterComplete : function (response, postdata, formid) { … } <br>
	* where <br>
	* - <em>response</em> is the data returned from the server (if any) <br>
	* - <em>postdata</em> an array, is the data sent to the server <br>
	* - <em>formid</em> is the jQuery object of form id, you can use formid[0].id to get form id.
	* @return JqGridNavGridEditFormOptions
	*/
	public function onafterComplete($fn)
	{
		$this->afterComplete = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	
	/**
	 * function (formid) { … }
	* @param JsFunction, string
	* @abstract  fires after showing the form; 
	* receives as Parameter the id of the constructed form. <br>
	* afterShowForm : function (formid) { … }
	* @return JqGridNavGridEditFormOptions
	*/
	public function onafterShowForm($fn)
	{
		$this->afterShowForm = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	 * function(response, postdata) { return [success,message,new_id] }
	* @param JsFunction, string
	* @abstract  fires after response has been received from server. 
	* Typically used to display status from server (e.g., the data is successfully saved 
	* or the save cancelled for server-side editing reasons). 
	* Receives as parameters the data returned from the request and an array of the posted values of type id=value1,value2. <br>
	* When used this event should return array with the following items [success, message, new_id] <br>
	* where <br>
	* - <em>success</em> is a boolean value if true the process continues, if false a error <em>message</em> appear and all other processing is stopped. (message is ignored if success is true). <br>
	* - <em>new_id</em> can be used to set the new row id in the grid when we are in add mode. <br>
	* afterSubmit : function(response, postdata) {<br>
	* 	return [success,message,new_id] <br>
	* }
	* @return JqGridNavGridEditFormOptions
	*/
	public function onafterSubmit($fn)
	{
		$this->afterSubmit = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	 * function(postdata, formid, mode) { … return object; }
	* @param JsFunction, string
	* @abstract  This event fires before checking the values (if checking is defined in colModel via editrules option). <br>
	* To this event we pass the following parameters: <br>
	* 1. <em>posdata</em> - the array of values which should be chaced in name:value pair, 
	* where the name is the name from colModel. <br>
	* 2. <em>formid</em> - the form id object. <br>
	* 3. <em>mode</em> - the current mode in which we are - can be add or edit. <br>
	* In all cases the event should return object in name value pair which then will be posted to the server.<br>
	* beforeCheckValues: function(postdata, formid, mode) { … return object; }
	* @return JqGridNavGridEditFormOptions
	*/
	public function onbeforeCheckValues($fn)
	{
		$this->beforeCheckValues = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	 * function(formid) { … }
	* @param JsFunction, string
	* @abstract  fires before initialize the new form data. 
	* Receives, as parameter, the id of the constructed form. <br>
	* beforeInitData : function(formid) { … }
	* @return JqGridNavGridEditFormOptions
	*/
	public function onbeforeInitData($fn)
	{
            $this->beforeInitData = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
            return $this;
	}
	
	/**
	 * function(formid) { … }
	* @abstract  fires before showing the form with the new data; 
	* receives as Parameter the id of the constructed form. <br>
	* beforeShowForm : function(formid) { … }
	* @param JsFunction|string $beforeShowForm
	* @return JqGridNavGridEditFormOptions
	*/
	public function onbeforeShowForm($fn)
	{
            $this->beforeShowForm = ($fn instanceof JsFunction) ? $fn : (new JsFunction($fn));
            return $this;
	}
	
	/**
	 * function(postdata, formid) { return[success,message]; }
	* @param JsFunction, string
	* @abstract  fires before the data is submitted to the server. 
	* Recieves as parameter the posted data array and the formid. <br>
	* beforeSubmit : function(postdata, formid) {<br>
	* … <br>
	* return[success,message]; <br>
	* } <br>
	* When defined this event should return array with the following values <br>
	* <em>success</em> boolean indicating if the proccess should continue (true) or a error (false) <em>message</em> 
	* should appear to the user
	* @return JqGridNavGridEditFormOptions
	*/
	public function onbeforeSubmit($fn)
	{
		$this->beforeSubmit = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}	

	/**
	 * function (whichbutton, formid, rowid)
	* @param JsFunction, string
	* @abstract  This event can be used only when we are in edit mode; 
	* it fires immediately after the previous or next button is clicked, 
	* before leaving the current row, allowing working with (e.g., saving) the currently loaded values in the form.<br>
	* onclickPgButtons : function (whichbutton, formid, rowid)  {
	* …} <br>
	* where <br>
	* 	<em> - whichbutton</em> is either 'prev' or 'next' <br>
	* 	<em> - formid</em> is the jQuery object of form id, you can use formid[0].id to get form id. <br>
	* 	<em> - rowid</em> is the id of the current row
	* @return JqGridNavGridEditFormOptions
	*/
	public function onclickPgButtons($fn)
	{
		$this->onclickPgButtons = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	 * function(params, posdata) { return {add_data} }
	* @param JsFunction, string
	* @abstract fires after the submit button is clicked and the postdata is constructed. 
	* Parameters passed to this event are: a options array of the method and the posted data array. 
	* The event should return array of type {} which extends the postdata array. <br>
	* onclickSubmit : function(params, posdata) { 
	*  <br>…<br>
	*  return {add_data} <br>
	*  }
	* @return JqGridNavGridEditFormOptions
	*/
	public function onclickSubmit($fn)
	{
		$this->onclickSubmit = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}
				
	/**
	 * function(formid) {	…}
	* @param JsFunction, string
	* @abstract fires only once when creating the data for editing and adding. 
	* Receives, as parameter, the id of the constructed form. <br>
	* onInitializeForm : function(formid) {	…}
	* @return JqGridNavGridEditFormOptions
	*/
	public function onInitializeForm($fn)
	{
		$this->onInitializeForm = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}
	
	/**
	* @param JsFunction, string
	* @abstract This event is called just before closing the form <br>
	* and when a close icon is clicked, <br>
	* a cancel button is clicked, <br>
	* ESC key is pressed <br>
	* or click on overlay (if jqModal is present).<br><br>
	* The event can return (optionally) true or false. 
	* If the return value is true the form is closed,<br>
	* if false the form does not close.<br>
	* If nothing is returned the form is closed
	* @return JqGridNavGridEditFormOptions
	*/
	public function onClose($fn)
	{
		$this->onClose = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	* @param JsFunction, string
	* @abstract The event (can) fire when error occurs from the ajax call and can be used 
	* for better formatting of the error messages. 
	* To this event is passed response from the server. 
	* The event should return single message (not array), which then is displayed to the user.
	* @return JqGridNavGridEditFormOptions
	*/
	public function onerrorTextFormat($fn)
	{
		$this->errorTextFormat = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	/**
	* @param JsFunction, string
	* @abstract If set this event can serialize the data passed to the ajax request when we save a form data.<br>
	* The function should return the serialized data. 
	* This event can be used when a custom data should be passed to the server - e.g - JSON string, 
	* <acronym title="Extensible Markup Language">XML</acronym> string and etc. 
	* To this event is passed the data which will be posted to the server
	* @return JqGridNavGridEditFormOptions
	*/
	public function onserializeEditData($fn)
	{
		$this->serializeEditData = ($fn instanceof JsFunction) ? $fn : (string)(new JsFunction($fn));
		return $this;
	}

	
}