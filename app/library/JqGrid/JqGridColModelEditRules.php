<?php

class JqGridColModelEditRules extends JqGridBaseObject {

	public $required = false;
	public $minValue = NULL;
	public $maxValue = NULL;

	/** @var bool
	 * ob Feld eine E-Mail repräsentiert und daraufhin geprüft werden soll
	 */
	public $email = NULL;

	/** @var bool
	 * ob Feld eine Ganzzahl repräsentiert und daraufhin geprüft werden soll
	 */
	public $integer = NULL;

	/** @var bool
	 * ob Feld eine Zahl repräsentiert und daraufhin geprüft werden soll
	 */
	public $number = NULL;

	/** @var bool
	 * ob Feld ein Datum repräsentiert und daraufhin geprüft werden soll
	 */
	public $date = Null;

	public $readonly = NULL;
	public $edithidden = NULL;
	        
	/**
	* This option is valid only in form editing module
	* @abstract This option is valid only in form editing module.<br>
	*  By default the hidden fields are not editable.<br>
	*  If the field is hidden in the grid and edithidden is set to true, the field can be edited when add or edit methods are called.<br>
	*
	* @param bool $param
	* @return JqGridColModelEditRules
	*/
	public function set_edithidden($param)
	{
		$this->edithidden = $param; return $this;
	}
	
	/**
	 *  (true or false) if set to true, the value will be checked and if empty, an erro
	 * @abstract  (true or false) if set to true, the value will be checked and if empty, an error message will be displayed.<br>
	 *
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_required($param)
	{
		$this->required = $param; return $this;
	}
	
	/**
	 *  (true or false) if set to true, the value will be checked and if this is not a
	 * @abstract  (true or false) if set to true, the value will be checked and if this is not a number, an error message will be displayed.<br>
	 *
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_number($param)
	{
		$this->number = $param; return $this;
	}
	
	/**
	 * (true or false) if set to true, the value will be checked and if this is not a i
	 * @abstract (true or false) if set to true, the value will be checked and if this is not a integer, an error message will be displayed.<br>
	 *
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_integer($param)
	{
		$this->integer = $param; return $this;
	}
	
	/**
	 * if set, the value will be checked and if the value is less than this, an error m
	 * @abstract if set, the value will be checked and if the value is less than this, an error message will be displayed.<br>
	 *
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_minValue($param)
	{
		$this->minValue = $param; return $this;
	}
	
	/**
	 * if set, the value will be checked and if the value is more than this, an error m
	 * @abstract if set, the value will be checked and if the value is more than this, an error message will be displayed.<br>
	 *
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_maxValue($param)
	{
		$this->maxValue = $param; return $this;
	}
	
	/**
	 *
	 * @abstract if set to true, the value will be checked and if this is not valid e-mail, an error message will be displayed
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_email($param)
	{
		$this->email = $param; return $this;
	}
	
	/**
	 *
	 * @abstract if set to true, the value will be checked and if this is not valid url, an error message will be displayed
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_url($param)
	{
		$this->url = $param; return $this;
	}
	
	/**
	 *
	 * @abstract  if set to true a value from datefmt option is get (if not set <acronym title="International Organization for Standardization">ISO</acronym> date is used) and the value will be checked and if this is not valid date, an error message will be displayed
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_date($param)
	{
		$this->date = $param; return $this;
	}
	
	/**
	 * if set to true, the value will be checked and if this is not valid time, an erro
	 * @abstract if set to true, the value will be checked and if this is not valid time, an error message will be displayed.<br>
	 *  Currently we support only hh:mm format and optional am/pm at the end
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_time($param)
	{
		$this->time = $param; return $this;
	}
	
	/**
	 *  if set to true allow definition of the custom checking rules via a custom function
	 * @abstract  if set to true allow definition of the custom checking rules via a custom function.<br>
	 *  See below
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_custom($param)
	{
		$this->custom = $param; return $this;
	}
	
	/**
	 *  this function should be used when a custom option is set to true
	 * @abstract  this function should be used when a custom option is set to true.<br>
	 *  Parameters passed to this function are the value, which should be checked and the name - the property from colModel.<br>
	 *  The function should return array with the following parameters: first parameter - true or false.<br>
	 *  The value of true mean that the checking is successful false otherwise; the second parameter have sense only if the first value is false and represent the error message which will be displayed to the user.<br>
	 *  Typically this can look like this [false,”Please enter valid value”]
	 * @param
	 * @return JqGridColModelEditRules
	 */
	public function set_custom_func($param)
	{
		$this->custom_func = $param; return $this;
	}

}
