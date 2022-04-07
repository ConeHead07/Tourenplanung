<?php

require_once dirname(__FILE__).'/../JsFunction.php';
require_once dirname(__FILE__).'/../JqGridHelper.php';

$obj = new stdClass();
$obj->numarray = array("eins","zwei","drei","vier","fünf");
foreach($obj->numarray as $k => $v) echo "$k:$v<br>\n";

$obj->assocarray = array("step1"=>"eins", "step 2"=>"zwei","step3"=>"drei","step4"=>"vier","step5"=>"fünf");
$obj->strHallo = "Hallo";
$obj->intFuenf = 5;
$obj->float3_4 = 3.4;
$obj->boolTrue = true;
$obj->nullElm = null;
$obj->JsFunction = new JsFunction("function(){alert('i m a function on top');}");
$obj->sub->assocarray = array("step1"=>"eins", "step 2"=>"zwei","step3"=>"drei","step4"=>"vier","step5"=>"fünf");
$obj->sub->strHallo = "Hallo";
$obj->sub->intFuenf = 5;
$obj->sub->float3_4 = 3.4;
$obj->sub->boolTrue = true;
$obj->sub->nullElm = null;
$obj->sub->JsFunction = new JsFunction("function(){alert('i m a function in sub');}");

$jsonCode = "";
JqGridHelper::json_encode_object($obj, $jsonCode);
echo "<pre>".htmlentities($jsonCode)."</pre>\n";
echo "<script>
var json = $jsonCode; 
alert(json.numarray[4]);
json.JsFunction();
json.sub.JsFunction();
</script>\n";