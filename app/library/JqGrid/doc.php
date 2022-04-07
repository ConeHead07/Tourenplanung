<?php
header('Content-Type: text/plain');

if (!defined('EOL')) define('EOL', "\n");

echo '<pre>'.EOL;


$pattern = "/<tr.*?>\s*"
		 ."<td[^>]*?>(?P<property>.*)<\/td>\s*"
		 ."<td[^>]*?>(?P<type>.*)<\/td>\s*"
 		 ."<\/tr>"
		 ."/Uis";


$pattern = '/<tr[^>]*>[^<]*'
		.'<td[^>]*>(?P<property>.*)?<\/td>[^<]*'
		.'<td[^>]*>(?P<description>.*)?<\/td>[^<]*'
		.'<td[^>]*>(?P<default>.*)?<\/td>[^<]*'
		.'/Usi';


$pattern = '/<tr[^>]*>[^<]*'
.'<td[^>]*>(?P<method>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<JqGridMethodParams>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<return>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<description>.*)?<\/td>[^<]*'
.'/Usi';

$pattern = '/<tr[^>]*>[^<]*'
.'<td[^>]*>(?P<property>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<JsEventParams>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<description>.*)?<\/td>[^<]*'
.'/Usi';

$pattern = '/<tr[^>]*>[^<]*'
.'<td[^>]*>(?P<property>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<type>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<description>.*)?<\/td>[^<]*'
.'<td[^>]*>(?P<default>.*)?<\/td>[^<]*'
.'/Usi';


$class = 'JqGridColModelSearchOptions';
$extends = 'JqGridBaseObject';
// Name	Type	Description	Default
$doc = <<<EOT
	<tr class="row1">
		<td class="col0">connectWith</td><td class="col1">string</td><td class="col2">Determines the target grid(s) to which the row should be dropped. The option is a string. In case of more than one grid the ids should be delemited with comma - i.e ”#grid1, #grid2” </td><td class="col3">empty string</td>

	</tr>
	<tr class="row2">
		<td class="col0">onstart</td><td class="col1">function</td><td class="col2">This event raises when we start drag a row from the source grid (i.e. to which this method is applied). Parameters passed to this event are the event handler and a prepared ui object. For more information refer to <a href="http://jqueryui.com/demos/draggable/#event-start" class="urlextern" title="http://jqueryui.com/demos/draggable/#event-start" rel="nofollow">jQuery UI draggable events</a> </td><td class="col3">null</td>
	</tr>
	<tr class="row3">
		<td class="col0">onstop</td><td class="col1">function</td><td class="col2">This event is triggered when dragging stops. Parameters passed to this even are the event handler and a prepared ui object. For more information refer to <a href="http://jqueryui.com/demos/draggable/#event-stop" class="urlextern" title="http://jqueryui.com/demos/draggable/#event-stop" rel="nofollow">jQuery UI draggable events</a></td><td class="col3">null</td>

	</tr>
	<tr class="row4">
		<td class="col0">beforedrop</td><td class="col1">function</td><td class="col2">This event raises before droping the row to the grid specified in connectWith option. Parameters passed to this event are the event handler, prepared ui object, data which will be inserted into the grid in name value pair, source grid object and target(this) grid object. <br>
 If the event return object in value name pair this object will be inserted into the target grid.</td><td class="col3">null</td>
	</tr>
	<tr class="row5">

		<td class="col0">ondrop</td><td class="col1">function</td><td class="col2">This event raises after the droping the row to the grid specified in connectWith option. Parameters passed to this event are the event handler, prepared ui object, data which is inserted into the grid in name value pair. For more information refer to <a href="http://jqueryui.com/demos/droppable/#event-drop" class="urlextern" title="http://jqueryui.com/demos/droppable/#event-drop" rel="nofollow">jQuery UI droppable events</a></td><td class="col3">null</td>
	</tr>
	<tr class="row6">
		<td class="col0">drop_opts</td><td class="col1">object</td><td class="col2 leftalign">Predefined options which can be applied to the droppable grid (specified with connectWith option above). Also you can set any option and event (except drop event which is replaced with ondrop event listed above). For more information refer to <a href="http://jqueryui.com/demos/droppable/" class="urlextern" title="http://jqueryui.com/demos/droppable/" rel="nofollow">jQuery UI droppable</a>  </td><td class="col3"> { activeClass : “ui-state-active”, <br>

 hoverClass : “ui-state-hover” <br>
 } </td>
	</tr>
	<tr class="row7">
		<td class="col0">drag_opts</td><td class="col1">object</td><td class="col2">Predefined options which can be applied to the draggable grid (i.e. to which this method is applied). Also you can set any option and event (except start and stop events which are replaced with onstart  and onstop events listed above). For more information refer to <a href="http://jqueryui.com/demos/draggable/" class="urlextern" title="http://jqueryui.com/demos/draggable/" rel="nofollow">jQuery UI draggable</a> </td><td class="col3"> { revert: “invalid”, <br>

 helper: “clone”, <br>
 cursor: “move”, <br>
 appendTo : ”#<span class="search_hit">jqgrid</span>_dnd”, <br>
 zIndex: 5000 <br>
 } </td>

	</tr>
	<tr class="row8">
		<td class="col0">dropbyname</td><td class="col1">boolean</td><td class="col2">If set to true this means that only fields that have equal names will be added to the target grid. Note that we use addRowData to insert new row, which means that if some field with name “a” on source grid is hidden they can appear on the target grid. The default value of false mean that the grid data will be added to the target counted from the first column from source.</td><td class="col3">false</td>
	</tr>
	<tr class="row9">
		<td class="col0">droppos</td><td class="col1">string</td><td class="col2">Determines where to add the new row. Can be first which mean as first row of the grid and last - as last row in the target grid. </td><td class="col3">first</td>

	</tr>
	<tr class="row10">
		<td class="col0">autoid</td><td class="col1">boolean</td><td class="col2">This option determines how the new row id should be generated. If this option is true we generate a id wich begin with string setted with the option autoidprefix (see below) and a random number. If this option is false the id can be either the the next record count or value determined by key property in colModel. <br>
 If the parameter is defined as function this function should return value which will act as id to the target grid. Parameters passed in this case is the data array which will be inserted into the target grid row</td><td class="col3">true</td>
	</tr>
	<tr class="row11">

		<td class="col0">autoidprefix</td><td class="col1">string</td><td class="col2">This option have sense only if the option autoid is set to true and determines the prefix of the new genearted id. </td><td class="col3">dnd_</td>
	</tr>
EOT;

preg_match_all($pattern, $doc, $m, PREG_SET_ORDER);

echo "<pre>\n";
echo 'class '.$class.($extends ? ' extends '.$extends.' '.EOL : '');
echo '{'.EOL.EOL;

for ($i = 0; $i < count($m); ++$i) {
		
	if (array_key_exists("JsEventParams", $m[$i])) {
		$m[$i]["description"].= "\nArguments passed to the Function:\n";
		$params = explode(",", $m[$i]["JsEventParams"]);
		foreach ($params as $_p)
			$m[$i]["description"].= "- ".trim(strip_tags($_p))."\n";
	}
	echo "/**\n";
	echo "* ".trim(substr($m[$i]["description"], 0, min(strpos($m[$i]["description"],'. '), 80) ))."\n";
	echo "* @abstract ".strtr($m[$i]["description"], array(". "=>".<br>\n* ", "\n"=>"\n* ", "href=\"/"=>"href=\"/http://http://www.trirand.com/" ))."\n";
	if (array_key_exists("default", $m[$i])) echo "* Default ".$m[$i]["default"]."\n";
	if (array_key_exists("return", $m[$i])) echo "* Returns ".$m[$i]["return"]."\n";
	if (array_key_exists("type", $m[$i])) echo "* @param ".$m[$i]["type"].(isset($m[$i]['property']) ? ' $'.$m[$i]['property']:'')."\n";
	
	if (array_key_exists("JsEventParams", $m[$i])) echo "* @param JsFunction\n";
	
	if (array_key_exists("params", $m[$i])) {
		$params = explode(",", $m[$i]["params"]);
		foreach ($params as $param)
			echo "* @param ".trim(strip_tags($param))."\n";
	}
	
	if (array_key_exists("JqGridMethodParams", $m[$i])) {
		$params = explode(",", strip_tags($m[$i]["JqGridMethodParams"]));
		$param = "";
		$paramsAsJsArgs = "";
		foreach ($params as $_p) {
			$_p = trim( strip_tags( $_p ) );
			echo "* @param ".$_p."\n";
			$param.= ($param?', ':'').'$'.$_p;
			$paramsAsJsArgs.= ($paramsAsJsArgs? '.\',\'' : '' )."\n\t\t.".'JqGridHelper::json_encode($'.$_p.')';
		}
	}
	
	echo '* @return ' . $class . ' ' . PHP_EOL;
	echo '*/' . PHP_EOL;
	echo "public function set_".strip_tags($m[$i]["property"]).'($'.$m[$i]['property'].')'."\n";
	echo "{\n";
	echo "	\$this->".$m[$i]['property'].' = $'.$m[$i]['property'].";\n";
	echo '	return $this'.";\n";
	//echo '	return new JsFunction(\'jQuery("\'.$this->gridID.\'").jqGrid("'. $m[$i]["method"].'"'. ($param? ', \''.$paramsAsJsArgs:'').'.\')\');'."\n";
	//echo "	\$this->params->".strip_tags($m[$i]["property"])." = \$param; return \$this;\n";
	echo "}\n\n";
}
echo '}'.EOL;
