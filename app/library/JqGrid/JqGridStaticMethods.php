<?php

class JqGridStaticMethods
{
	/**
	* This option allow to set global ajax settings for all ajax requests used in the
	* @abstract This option allow to set global ajax settings for all ajax requests used in the grid.<br>
	* Note that with this option is possible to overwrite all current ajax setting for the particular module.<br>
	* Starting from version 3.6 we have a 3 level ajax settings.<br>
	* <br>
	
	*  The first level is specific for the module when we use ajax.<br>
	* <br>
	*  The second level is determined from this option, and the <br>
	*  third level can be passed as additional parameter for the particular method.<br>
	* The third level ajax setting has a higher priority.<br>
	* This means that we use this global rule for all ajax settings: <br>
	*
	*  jQuery.extend(jQuery.ajax({method specific options}, ajaxOptions, ThirdLevelajaxSettinds)); <br>
	*  Additional to this we have a possibility to define on every ajax request serialize function which allow to convert the parameters passed to the server.
	* Returns none
	* @param empty object
	* @return JsFunction
	*/
	public function ajaxOptions($object)
	{
		return new JsFunction('ajaxOptions('.JqGridHelper::json_encode($object).')');
	}
	
	/**
	 *
	 * @abstract Escapes the special string characters with two backslashes (\) so that a single backslash will be put into the string and can be interpreted correct when used in jQuery selector.
	 * Returns parsed string
	 * @param string
	 * @return JsFunction
	 */
	public function jqID($string)
	{
		return new JsFunction('jqgrid.jqID('.JqGridHelper::json_encode($string).')');
	}
	
	/**
	 * The htmlDecode function decodes an <acronym title="HyperText Markup Language">H
	 * @abstract  The htmlDecode function decodes an <acronym title="HyperText Markup Language">HTML</acronym> encoded string back into the original html code.<br>
	
	 *
	 * Returns decoded_string
	 * @param string
	 * @return JsFunction
	 */
	public function htmlDecode($string)
	{
		return new JsFunction('jgrid.htmlDecode('.JqGridHelper::json_encode($string).')');
	}
	
	/**
	 *
	 * @abstract  The htmlEncode function encodes an <acronym title="HyperText Markup Language">HTML</acronym> string - opposite to htmlDecode
	 * Returns encoded_string
	 * @param string
	 * @return JsFunction
	 */
	public function htmlEncode($string)
	{
		return new JsFunction('jgrid.htmlEncode('.JqGridHelper::json_encode($string).')');
	}
	
	/**
	 * Simple string-templating
	 * @abstract  Simple string-templating.<br>
	 * Accepts a string template as the first argument.<br>
	 * The second is optional: If specified, it is used to replace placeholders in the first argument.<br>
	 *  Example jQuery.jqgformat("Please enter a value  between {0} and {1}.", 4, 8)<br>
	 *  result : "Please enter a value between 4 and 8."
	 * Returns formated_string
	 * @param string
	 * @return JsFunction
	 */
	public function format($string)
	{
		return new JsFunction('jgrid.format('.JqGridHelper::json_encode($string).')');
	}
	
	/**
	 * This method is used to correct the bug in Internet Explorer versions <- 7 where
	 * @abstract  This method is used to correct the bug in Internet Explorer versions <- 7 where CellIndex is calculated in Display Order and not in real one.<br>
	 * <em>cell</em> is the cell content (i.e.) td element
	 * Returns  index
	 * @param cell
	 * @return JsFunction
	 */
	public function getCellIndex($cell)
	{
		return new JsFunction('jgrid.getCellIndex('.JqGridHelper::json_encode($cell).')');
	}
	
	/**
	 * Convert <em>xmlstring</em> to dom document
	 * @abstract  Convert <em>xmlstring</em> to dom document.<br>
	
	 * Return <em>xmlDoc</em> - dom document.
	 * Returns xmlDoc
	 * @param xmlstring
	 * @return JsFunction
	 */
	public function stringToDoc($xmlstring)
	{
		return new JsFunction('jgrid.stringToDoc('.JqGridHelper::json_encode($xmlstring).')');
	}
	
	/**
	 *
	 * @abstract Function for stripping out <acronym title="HyperText Markup Language">HTML</acronym> tags from a given content.
	 * Returns new_content
	 * @param content
	 * @return JsFunction
	 */
	public function stripHtml($content)
	{
		return new JsFunction('jgrid.stripHtml('.JqGridHelper::json_encode($content).')');
	}
	
	/**
	 * This function parses a jsonString (JSON text) to produce an object or array.<br>
	 * @abstract This function parses a jsonString (JSON text) to produce an object or array.<br>
	 *
	 *  To prevent JavaScript hijacking attacks, web application authors are encouraged to use this function.<br>
	 * This returns a JSON representation of the variable.<br>
	 *  Cyclic structures are detected and safely interrupted, but could not be further restored.<br>
	 *  1.<br>
	
	 * The while(1); construct, located at the beginning of JSON text, <br>
	 *  2.<br>
	 * Comments at the beginning and end of the text.<br>
	 * <br>
	 *  JSON data providers are encouraged to use one or both of these <span class="search_hit">methods</span> to prevent data execution.<br>
	 * Such JSON response may then look like this <br>
	 *
	 *  while(1);{[ <br>
	
	 *  {"name":"safe value 1"}, <br>
	 *  {"name":"safe value 2"}, <br>
	 *  ]} <br>
	*  <span class="search_hit">jqGrid</span> uses this function when obtaining a data with datatype:'json'
	* Returns object
	* @param jsonString
	* @return JsFunction
	*/
	public function parse($jsonString)
	{
		return new JsFunction('jgrid.parse('.JqGridHelper::json_encode($jsonString).')');
	}
}