<?php

/**
 * Creating common formatter function<br>
 *
 * There are times where you maybe want to use your custom format/unformat functions in many places into the code. This of course can be done defining the functions as example above. We have designed the formatter module so that it can be easy extended from the developer and doing it so make the development process easy. Below we will discuss how to make your own formatter functions to be visible all into the code.
 *
 * After loading the jqGrid Java Script files you can define in script tag the following (or simple create your own file and include it into the head section)
 * <pre>
 * <script type="text/javascript">
 * jQuery.extend($.fn.fmatter , {
 *     currencyFmatter : function(cellvalue, options, rowdata) {
 *         return "$"+cellvalue;
 *     }
 * });
 * jQuery.extend($.fn.fmatter.currencyFmatter , {
 *     unformat : function(cellvalue, options) {
 *         return cellvalue.replace("$","");
 *     }
 * });
 * </script>
 * </pre>
 *
 * Then in your code you just need to do:<br>
 * <pre>
 * <script>
 * jQuery("#grid_id").jqGrid({
 *     ...
 *     colModel: [
 *     ...
 *     {name:'price', index:'price', width:60, align:"center", editable: true,
 *        formatter:'currencyFmatter'},
 *        ...
 *     ]
 *     ...
 * });
 * </script>
 * </pre>
 * Note that in this case you will not need to specify the unformat function.
 *
 */
class JqGridCommonFormatter extends JsFunction
{
	protected $_name = '';
	protected $_formatter = null;
	protected $_unformat = null;

	/**
	 * Eigene Erweiterung der vordefinierten jqGrid-Formatier-Methoden
	 * @param string $name (Ohne Sonderzeichen wie eine Javascript-Variable)
	 * 
	 * @param JsFunction|string $formatter function(cellvalue, options, rowdata) { return new_cell_value }
	 * 
	 * @param JsFunction|string $unformat  function(cellvalue, options) { return unformatted_cellvalue; }
	 */
	public function __construct($name, $formatter, $unformat)
	{
			
		/**
		 * @var string functionname (Ohne Sonderzeichen!)
		 */
		$this->_name = $name;
			
		/**
		 * @var JsFunction
		 */
		$this->_formatter = new JsFunction('');
			
		/**
		 * @var JsFunction
		 */
		$this->_unformat = new JsFunction('');
			
		// Set Formattername and Formatterfunction
		$this->set_formatter($formatter);
			
		// Add Unformat-Function
		$this->set_unformat($unformat);
	}

	/**
	 * Der Name kann als String-Variable an JqGridColModel::set_formatter übergeben werden
	 * @return string name of FormatterMethod
	 */
	public function get_name()
	{
		return (string)$this->_name;
	}

	/**
	 * @param string name of FormatterMethod
	 * @return JqGridCommonFormatter
	 */
	public function set_name()
	{
		return $this->_name = $name;
		return $this;
	}

	/**
	 * Set Formatter-Function
	 * @param JsFunction, string $function function(cellvalue, options, rowdata) { return new_cell_value }
	 * @return JqGridCommonFormatter
	 */
	public function set_formatter($function)
	{
		$this->_formatter->set('jQuery.extend($.fn.fmatter , {'."\n");
		$this->_formatter->add($this->_name.':'.$function."\n");
		$this->_formatter->add('});'."\n");
		$this->set(''.$this->_formatter.$this->_unformat);
		return $this;
	}

	/**
	 * Set Unformat-Function
	 * @param JsFunction, string function(cellvalue, options) { return unformatted_cellvalue; }
	 * @return JqGridCommonFormatter
	 */
	public function set_unformat($function)
	{
		// Add Unformat-Function
		$this->_unformat->set('jQuery.extend($.fn.fmatter'.$this->_name.', {'."\n");
		$this->_unformat->add('unformat:'.$function."\n");
		$this->_unformat->add('});'."\n");
		$this->set(''.$this->_formatter.$this->_unformat);
		return $this;
			
	}
}