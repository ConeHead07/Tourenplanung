<?php
function __autoload($class_name) {
	$tryfile = dirname(__FILE__).'/../'.$class_name.'.php';
	$file = realpath($tryfile);
	require_once $file;
}
require_once dirname(__FILE__).'/../JsFunction.php';
require_once dirname(__FILE__).'/../JqGridHelper.php';

require_once dirname(__FILE__).'/../JqGrid.php';
require_once dirname(__FILE__).'/../JqGridColModel.php';
require_once dirname(__FILE__).'/../JqGridColModelEditOptions.php';
require_once dirname(__FILE__).'/../JqGridColModelEditRules.php';
require_once dirname(__FILE__).'/../JqGridColModelFormatter.php';
require_once dirname(__FILE__).'/../JqGridPager.php';

$bindKeys = new stdClass();
$bindKeys->onEnter = new JsFunction("function(id) { alert('onEnter id:'+id); }");
$bindKeys->onSpace = new JsFunction("function(id) { alert('onSpace id:'+id); }");
$bindKeys->onLeftKey = new JsFunction("function(id) { alert('onLeftKey id:'+id); }");
$bindKeys->onRightKey = new JsFunction("function(id) { alert('onRightKey id:'+id); }");
$bindKeys->scrollingRows = new JsFunction("function(id) { alert('scrollingRows id:'+id); }");

$grid = new JqGrid("Grid1");
$gridMethods = $grid->getInstanceMethods();
$grid->addCode( $gridMethods->bindKeys( $bindKeys ) );

$grid->width(300);
$grid->colModel()->addFields(array(
	"name", "vorname", "strasse"
));

$cmEditOpts = new JqGridColModelEditOptions();

/**
 * @var JqGridColModel
 */
$cm = $grid->colModel()->getItem("name");

$cm->set_editable(true);
$cm->set_edittype("custom");

$cmEO = new JqGridColModelEditOptions();
$cmEO->set_custom_element("function(){alert('hallo');}");
$cmEO->set_custom_value("function(){alert('hallo');}");
$cm->set_editoptions($cmEO);
$cm->getInstanceFormatterCurrency()->set_decimalSeparator(',');

/**/

echo JqGridHelper::json_encode($grid);

echo $grid->getCode();
