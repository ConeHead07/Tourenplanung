<?php

class JsFunction {
	protected $_jsFunction = "";

	public function __construct($function) {
		$this->set($function);
	}

	public function get() {
		return $this->_jsFunction;
	}
	
	public function set($function) {
	$this->_jsFunction = (string)$function;
	}
	
	public function add($function) {
		$this->_jsFunction.= (string)$function;
	}

	public function __toString() {
		return $this->get();
	}
}

// Test
// $js = new JsFunction("function() { alert('hallo'); }");
// echo (string)$js; 