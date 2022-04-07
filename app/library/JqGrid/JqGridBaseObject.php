<?php

abstract class JqGridBaseObject
{
	/**
	*
	* @param mixed [optional] array|object ColModelOptions
	*/
	public function __construct($properties = NULL) {
		if (!is_null($properties)) {
			$this->setProperties($properties);
		}
	}
	
	/**
	 * 
	 * Properties setzen
	 * @param mixed $properties
	 * @return $this
	 */
	public function setProperties($properties)
	{
		if (!is_null($properties)) foreach($properties as $k => &$v) {
			
			if (method_exists($this, 'set_'.$k)) {
				$this->{'set_'.$k}($v);
				//echo 'method found: SetProperties set_'.$k."(".print_r($v,1).")<br>\n";
			}
			elseif (method_exists($this, $k)) {
				$this->{$k}($v);
				//echo 'method found: SetProperties '.$k."(".print_r($v,1).")<br>\n";
			}
			else {
				$this->$k = $v;
				//echo 'no method found: SetProperties '.$k." = ".print_r($v,1)."<br>\n";
			}
		}
		return $this;
	}
	
	/**
	*
	* Property setzen
	* @param string name of the property
	* @param mixed value of the property
	* @return $this
	*/
	public function setProperty($name, $value)
	{
		if (method_exists($this, 'set_'.$name)) $this->{'set_'.$name}($value);
		if (method_exists($this, $name)) $this->{$name}($value);
		else $this->$name = $value;
		
		return $this;
	}
	
	public function getProperty($property)
	{
		if (method_exists($this, 'get_'.$property)) 
			return $this->{'get_'.$property}();
		
		if (property_exists($property)) 
			return $this->$property;
		
		throw new JqGridException('Die Eigenschaft '.__CLASS__.'::'.$property.' existiert nicht oder wurde noch nicht gesetzt!');
	}
}