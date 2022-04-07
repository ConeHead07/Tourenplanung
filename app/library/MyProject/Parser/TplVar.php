<?php

class MyProject_Parser_TplVar {

	//const pattern = '!\{\{([a-zA-Z0-9._-]+)\}\}!';
	/**
	 * regex pattern Ausdruck
	 * @var string
	 */
	protected $pattern = '!\{\{([^}]+)\}\}!';
	
	protected $pattern_pct = '!%%(.+)%%!U';
	/**
	 * Text mit zu ersertzenden Template-Vars
	 * @var string
	 */
	protected $subject = "";

	/**
	 * Array mit gefunden Template-Vars
	 * @var array
	 */
	protected $matches = "";

	/**
	 * Schalter-Variable, ob Template-Vars ohne Übereinstimmung mit registrierten Valid-Vars entfernt werden sollen
	 * @var boolean default true
	 */
	protected $cleanInvalidVars = true;

	/**
	 * Array mit registrierten Variablen als Ersetzungsquelle
	 * @var array
	 */
	protected $ValidVars = array();

	function __construct($ValidVars = array()) {
		$this->setValidVars($ValidVars);
	}

	/**
	 * Registrierung zulässiger Ersetzungsvariablen
	 * @param array $ValidVars
	 * @return TplVarParser
	 */
	function setValidVars($ValidVars) {
		$this->ValidVars = $ValidVars;
		return $this;
	}

	/**
	 * @return int|false the number of full pattern matches (which might be zero), or false if an error occurred.
	 * @see preg_match_all
	 */
	function match_vars() {
		return preg_match_all($this->pattern, $this->subject, $this->matches);
	}

	/**
	 * Ersetzt Template-Vars in $subject durch registrierte Ersetzungs-Variablen (ValidVars)
	 * @param string $subject
	 * @return string
	 */
	function parse($subject) {

		$this->subject = $subject;
		$this->matches = array();
		if (!$this->match_vars()) return $this->subject;

		for($i = 0; $i < count($this->matches[1]); $i++) {
			$parts = explode(".", $this->matches[1][$i]);
			if ($parts[0] == "Now") {
				$value = "";
				if (substr($parts[1], 0, 7) == "Format(") {
					$format = substr($parts[1], 7, -1);
					$value = date($format);
				} else {
					$value = date("Y-m-d H:i:s");
				}
				$this->subject = str_replace($this->matches[0][$i], $value, $this->subject);
				continue;
			}
			$found = false;
			
			if (count($parts) == 1) {

				if (isset($this->ValidVars[$parts[0]]) && is_scalar($this->ValidVars[$parts[0]])) {
					$found = true;
					$this->subject = str_replace(
					$this->matches[0][$i],
					$this->ValidVars[$parts[0]],
					$this->subject
					);
				}
			} else {
				$found = false;
				$SELF = __CLASS__;
				
				$value = self::array_path_value(array_slice($parts, 1), $this->ValidVars[$parts[0]], $found);
				if ($found && is_scalar($value)) {
					$this->subject = str_replace($this->matches[0][$i], $value, $this->subject);
				}
			}
			if (!$found && $this->cleanInvalidVars) {
				$this->subject = str_replace($this->matches[0][$i], "", $this->subject);
				//echo "!error".$this->matches[1][$i]."<br>\n";
			}
		}
		return $this->subject;
	}

	/**
	 * Gibt im Erfolgsfall den Wert an der Pfadstelle des Arrays zurück
	 * @param array $path
	 * @param array $arr
	 * @param boolean $found
	 * @return mixed
	 * @abstract kann auch statisch ohne Instanzierung verwendet werden
	 */
	static function array_path_value($path, &$arr, &$found = false) {

		if (count($path) == 1) {
			if (is_array($arr) && array_key_exists($path[0], $arr)) {
				$found = true;
				return $arr[$path[0]];
			}
			if (is_object($arr) && isset($arr->{$path[0]})) {
				$found = true;
				return $arr->{$path[0]};
			}
			$found = false;
			return null;
		}
		if (is_array($arr) && array_key_exists($path[0], $arr))
		return self::array_path_value(array_shift($path), $arr[$path[0]], $found);

		if (is_object($arr) && isset($arr->{$path[0]}))
		return self::array_path_value(array_shift($path), $arr->{$path[0]}, $found);

		$found = false;
		return null;

	}
}

class MyProject_Parser_TplVarDblPct extends MyProject_Parser_TplVar {
	protected $pattern = '!%%(.+)%%!U';
}

class MyProject_Parser_TplVarDblBraces extends MyProject_Parser_TplVar {
	protected $pattern = '!{{(.+)}}!U';
}

class MyProject_Parser_TplVarBracePct extends MyProject_Parser_TplVar {
	protected $pattern = '!{%(.+)%}!U';
}