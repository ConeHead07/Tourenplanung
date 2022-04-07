<?php

class JqGridColModelSearchRules extends JqGridBaseObject 
{

	/**
	* 
	* @abstract  (true or false) if set to true, the value will be checked and if empty, an error message will be displayed.
	* @param boolean $required
	* @return JqGridColModelSearchRules 
	*/
	public function set_required($required)
	{
		$this->required = $required;
		return $this;
	}

	/**
	* 
	* @abstract  (true or false) if set to true, the value will be checked and if this is not a number, an error message will be displayed.
	* @param boolean $number
	* @return JqGridColModelSearchRules 
	*/
	public function set_number($number)
	{
		$this->number = $number;
		return $this;
	}

	/**
	* 
	* @abstract (true or false) if set to true, the value will be checked and if this is not a integer, an error message will be displayed.
	* @param boolean $integer
	* @return JqGridColModelSearchRules 
	*/
	public function set_integer($integer)
	{
		$this->integer = $integer;
		return $this;
	}

	/**
	* 
	* @abstract if set, the value will be checked and if the value is less than this, an error message will be displayed.
	* @param number(integer) $minValue
	* @return JqGridColModelSearchRules 
	*/
	public function set_minValue($minValue)
	{
		$this->minValue = $minValue;
		return $this;
	}

	/**
	* 
	* @abstract if set, the value will be checked and if the value is more than this, an error message will be displayed.
	* @param number(integer) $maxValue
	* @return JqGridColModelSearchRules 
	*/
	public function set_maxValue($maxValue)
	{
		$this->maxValue = $maxValue;
		return $this;
	}

	/**
	* 
	* @abstract if set to true, the value will be checked and if this is not valid e-mail, an error message will be displayed
	* @param boolean $email
	* @return JqGridColModelSearchRules 
	*/
	public function set_email($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	* 
	* @abstract if set to true, the value will be checked and if this is not valid url, an error message will be displayed
	* @param boolean $url
	* @return JqGridColModelSearchRules 
	*/
	public function set_url($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	* 
	* @abstract  if set to true a value from datefmt option is get (if not set <acronym title="International Organization for Standardization">ISO</acronym> date is used) and the value will be checked and if this is not valid date, an error message will be displayed
	* @param boolean $date
	* @return JqGridColModelSearchRules 
	*/
	public function set_date($date)
	{
		$this->date = $date;
		return $this;
	}

	/**
	* if set to true, the value will be checked and if this is not valid time, an erro
	* @abstract if set to true, the value will be checked and if this is not valid time, an error message will be displayed.<br>
	* Currently we support only hh:mm format and optional am/pm at the end
	* @param boolean $time
	* @return JqGridColModelSearchRules 
	*/
	public function set_time($time)
	{
		$this->time = $time;
		return $this;
	}

	/**
	* if set to true allow definition of the custom checking rules via a custom funct
	* @abstract  if set to true allow definition of the custom checking rules via a custom function.<br>
	* See below
	* @param boolean $custom
	* @return JqGridColModelSearchRules 
	*/
	public function set_custom($custom)
	{
		$this->custom = $custom;
		return $this;
	}

	/**
	* this function should be used when a custom option is set to true
	* @abstract  this function should be used when a custom option is set to true.<br>
	* Parameters passed to this function are the value, which should be checked and the name - the property from colModel.<br>
	* The function should return array with the following parameters: first parameter - true or false.<br>
	* The value of true mean that the checking is successful false otherwise; the second parameter have sense only if the first value is false and represent the error message which will be displayed to the user.<br>
	* Typically this can look like this [false,?Please enter valid value?]
	* @param function $custom_func
	* @return JqGridColModelSearchRules 
	*/
	public function set_custom_func($custom_func)
	{
		$this->custom_func = (is_string($custom_func) ? new JsFunction($custom_func) : $custom_func);
		return $this;
	}

}

