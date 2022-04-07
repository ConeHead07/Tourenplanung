<?php

/**
*
* Klasse mit voreingestellen Optionen (regex-Pattern und ValidVars) und Methoden (parse_conf) zum Parsen von Tpl-Vars in Table-Conf-Objekten
* @author rybka
* @uses TplVarParser
*/
class MyProject_Parser_TableConf extends MyProject_Parser_TplVar {
	protected $pattern = '!{%(.+)%}!U';
        protected static $instance = null;

        function __construct() {
		global $_CONF;
		global $_TABLE;
		global $db_name;
		global $user;

		$this->ValidVars = array(
			"CONF" => &$_CONF, 
			"TABLE" => &$_TABLE, 
			"DATABASE" => $db_name, 
			"USER" => &$user
		);
                self::$instance = $this;
	}
        
        /**
         * @return MyProject_Parser_TableConf
         */
        public static function getInstance()
        {
            if (self::$instance == null)
                self::$instance = new self;
            
            return self::$instance;
        }

	function parse_conf(&$Conf) {
            if (!is_array($Conf)) return;
            if (is_array($Conf)) foreach($Conf as $k => $v) {
			switch($k) {
				case "Title":
				case "Db":
				case "Table":
				case "FormInput":
				case "FormPreview":
				case "FormRead":
					$Conf[$k] = $this->parse($v);
					break;
			}
		}

		if (!array_key_exists("Fields", $Conf)) return;

		foreach($Conf["Fields"] as $field => $props) {
			if (!is_array($props)) continue;
				
			foreach($props as $k => $v) {
				switch($k) {
					case "dbField":
					case "label":
					case "listlabel":
					case "size":
					case "sql":
					case "default":
					case "optionsAsJSON":
					case "inputRegExMask":
					case "inputRepeatField":
					case "inputAttribute":
					case "readAttribute":
						$Conf["Fields"][$field][$k] = $this->parse($v);
				}
			}
		}
	}
}