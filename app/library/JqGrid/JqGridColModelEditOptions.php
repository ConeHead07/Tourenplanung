<?php

class JqGridColModelEditOptions extends JqGridBaseObject
{

	/**
	 *
	 * Mögliche Auswahlwerte als einfaches oder assoziatives Array
	 * @var array
	 */
	
	/**
	 * Übernimmt die Generierung der Cell-Ausgabe, wenn edittype == custom
	 * @abstract Wenn im ColModel der edittype auf custom gesetzt wurde, 
	 * muss die in custom_element übergebene Funktion eine HTML-Objekt für die Anzeige zurückliefern.
	 * colModel: [
	 *  ...
	 *  {name:'price', ..., editable:true, edittype:'custom',
	 *     editoptions:{custom_element: myelem, custom_value:myvalue} }<br><br>
	 * 		function myelem(value, options) {
	 * 			var el = jQuery("<span/>");
	 * 			jQuery(el).text(value);
	 * 			return el;
	 * 		}
	 * @param JsFunction
	 * @return JqGridColModelEditOptions
	 * @see jqgrid_helper::custom_readable_element
	 */
	public function set_custom_element($param)
	{
		$this->custom_element = ($param instanceof JsFunction) ? $param : (new JsFunction($param));
		return $this;
	}

	/**
	 * Übernimmt Setter und Getter-Funktion für die Zelle/Column, wenn edittype ist custom
	 * @abstract Die user-Funktion wird sowohl zum Setzen und Abfragen des Zellwertes aufgerufen,<br>
	 * wenn ColModel->edittype = custom
	 * colModel: [
	 *  ...
	 *  {name:'price', ..., editable:true, edittype:'custom',
	 *     editoptions:{custom_element: myelem, custom_value:myvalue} }<br><br>
	 * 
	 * function myvalue(elem, operation, value) {
	 * 		if(operation === 'get') {
	 * 			return jQuery(elem).text();
	 * 		} else if(operation === 'set') {
	 * 			jQuery(elem).text(value);
	 * 		}
	 * 	}
	 * @param JsFunction
	 * @return JqGridColModelEditOptions
	 * @see jqgrid_helper::custom_readable_value
	 */
	public function set_custom_value($param)
	{
		$this->custom_value = ($param instanceof JsFunction) ? $param : (new JsFunction($param));
		return $this;
	}
	
	/**
	* When set for edittype checkbox this value should be a string with two possible v
	* @abstract When set for edittype checkbox this value should be a string with two possible values separated with a colon (:) - Example editoptions:{value:“Yes:No”} where the first value determines the checked property.<br>
	*  <br>
	*  When set for edittype select value can be a string, object or function.<br>
	* <br>
	*
	*  If the option is a string it must contain a set of value:label pairs with the value separated from the label with a colon (:) and ended with(;).<br>
	*  The string should not ended with a (;)- editoptions:{value:“1:One;2:Two”}.<br>
	*  <br>
	*  If set as object it should be defined as pair name:value - editoptions:{value:{1:'One';2:'Two'}} <br>
	*  When defined as function - the function should return either formated string or object.<br>
	*  <br>
	*  In all other cases this is the value of the input element if defined.<br>
	*
	* @param mixed
	* @return JqGridModelEditOptions
	*/
	public function set_value($param)
	{
		$this->value = $param; return $this;
	}
	
	/**
	 * This option is valid only for elements of type select - i
	 * @abstract This option is valid only for elements of type select - i.<br>
	 * e.<br>
	 * , edittype:select and should be the <acronym title="Uniform Resource Locator">URL</acronym> to get the <acronym title="Asynchronous JavaScript and XML">AJAX</acronym> data for the select element.<br>
	 *  The data is obtained via an <acronym title="Asynchronous JavaScript and XML">AJAX</acronym> call and should be a valid <acronym title="HyperText Markup Language">HTML</acronym> select element with the desired options &lt;select&gt;&lt;option value='1'&gt;One&lt;/option&gt;…&lt;/select&gt;.<br>
	 *  You can use option group.<br>
	 *  <br>
	 *
	 *  The <acronym title="Asynchronous JavaScript and XML">AJAX</acronym> request is called only once when the element is created.<br>
	 *  <br>
	 *  In the inline edit or the cell edit module it is called every time when you edit a new row or cell.<br>
	 *  In the form edit module only once.<br>
	 *
	 * @param string
	 * @return JqGridModelEditOptions
	 */
	public function set_dataUrl($param)
	{
		$this->dataUrl = $param; return $this;
	}
	
	/**
	 * This option is relevant only if the dataUrl parameter is set
	 * @abstract This option is relevant only if the dataUrl parameter is set.<br>
	 *  When the server response can not build the select element, you can use your own function to build the select.<br>
	 *  The function should return a string containing the select and options value(s) as described in dataUrl option.<br>
	 *  Parameter passed to this function is the server response
	 * @param JsFunction
	 * @return JqGridModelEditOptions
	 */
	public function set_buildSelect($param)
	{
		$this->buildSelect = ($param instanceof JsFunction) ? $param : JsFunction($param); 
		return $this;
	}
	
	/**
	 *  We pass the element object to this function, if defined
	 * @abstract  We pass the element object to this function, if defined.<br>
	 *  This function is called only once when the element is created.<br>
	 *  Example : <br>
	 *  …editoptions: { dataInit : function (elem) { <br>
	 *  $(elem).<br>
	 * autocomplete(); <br>
	 *   } <br>
	 *
	 *  } <br>
	 *  The event is called only once when the element is created.<br>
	 *  <br>
	 *  In the inline edit or the cell edit module it is called every time when you edit a new row or cell.<br>
	 *  In the form edit module only once if the recreateForm option is set to false, or every time if the same option is set to true .<br>
	 *  <br>
	 *  <span style="color: red;">Note: Some plugins require the position of the element in the <acronym title="Document Object Model">DOM</acronym> and since this event is raised before inserting the element into the <acronym title="Document Object Model">DOM</acronym> you can use a setTimeout function to accomplish the desired action.<br>
	 *  This is especially valid for jQuery UI datepicker (1.<br>
	 * 7.<br>
	 * x and up releases)</span>
	 * @param JsFunction
	 * @return JqGridModelEditOptions
	 */
	public function set_dataInit($param)
	{
		$this->dataInit = ($param instanceof JsFunction) ? $param : JsFunction($param); 
		return $this;
	}
	
	/**
	 * list of events to apply to the data element; uses $("#id")
	 * @abstract list of events to apply to the data element; uses $('#id').<br>
	 * bind(type, [data], fn) to bind events to data element.<br>
	 *  Should be described like this: <br>
	 *  … editoptions: { dataEvents: [ <br>
	 *  { type: 'click', data: { i: 7 }, fn: function(e) { console.<br>
	 * log(e.<br>
	 * data.<br>
	 * i); } }, <br>
	 *  { type: 'keypress', fn: function(e) { console.<br>
	 * log('keypress'); } } <br>
	 *
	 *  ] <br>
	 *  }
	 * @param array
	 * @return JqGridModelEditOptions
	 */
	public function set_dataEvents($param)
	{
		$this->dataEvents = $param; return $this;
	}
	
	/**
	 *  The option can be string or function
	 * @abstract  The option can be string or function.<br>
	 *  This option is valid only in Form Editing module when used with editGridRow method in add mode.<br>
	 *  If defined the input element is set with this value if only element is empty.<br>
	 *  If used in selects the text should be provided and not the key.<br>
	 *  Also when a function is used the function should return value.<br>
	 *
	 * @param mixed
	 * @return JqGridModelEditOptions
	 */
	public function set_defaultValue($param)
	{
		$this->defaultValue = $param; return $this;
	}
	
	/**
	 *
	 * @abstract  If set to true a string 'null' is send to the server 
	 * when the data in that field is empty
	 * @param bool
	 * @return JqGridModelEditOptions
	 */
	public function set_NullIfEmpty($param)
	{
		$this->NullIfEmpty = $param; return $this;
	}
	
	/**
	 * In this case you can set any other valid attribute for the editable element
	 * @abstract In this case you can set any other valid attribute for the editable element.<br>
	 *  For example, if the element is edittype:'text', we can set size, maxlength, etc.<br>
	 *  attributes.<br>
	 *  Refer to the valid attributes for the element
	 * @param string
	 * @param mixed
	 * @return JqGridModelEditOptions
	 */
	public function set_others($name, $param)
	{
		$this->{$name} = $param; return $this;
	}
}
