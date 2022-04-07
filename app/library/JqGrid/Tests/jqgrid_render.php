<?php
require_once '../JqGridColModelFormatter.php';
function __autoload($class_name) {
	//echo ' autoload class: '.$class_name."<br>\n";
	$tryfile = dirname(__FILE__).'/../'.$class_name.'.php';
	$file = realpath($tryfile);
	require_once $file;
}

// Eigene Zell-Formatierer für Bildinhalte definieren
$ComFormatterUrl = new JqGridCommonFormatter(
	'customImg', 
	'function(cellvalue, options, rowdata) { return (typeof(cellvalue)=="string"?"<a href=\'http://"+cellvalue+"\' rel=\'"+cellvalue+"\'>"+cellvalue+"</a>":""); }', 
	'function(cellvalue, options) { return (cellvalue?$(cellvalue).attr("rel"):""); }'
);

// Grid-Options definieren
$grid = new JqGrid("#grid1");
$grid->height('auto')
->width(800)
->resizable(false)
->sortable(true)
->datatype('local')
->data(new JsFunction('mydata'))
->caption("Test-Grid")
->shrinkToFit(true)
->pager('#grid1_pager')
->colModel(array('addFieldsProperties' => array(
	"invdate" => array("width" => 90, 'formatter' => 'date'),
	     "id" => array('width' => 50, 'key' => true, 'sorttype' => 'int'),
	   "name" => array('width' => 50, 'editable' => true),
	 "amount" => array('width' => 50, 'sortable' => false),
	    "tax" => array('width' => 50),
	  "total" => array('width' => 50),
	   "note" => array('width' => 50, 'editable' => true),
	    'url' => array('width' => 150,'editable' => true, 'formatter' => $ComFormatterUrl)
)));

// TESTS MIT SELECT und CHECKBOXEN
// "1:One;2:Two"}
// $eo = $grid->colModel()->getItem('note')->getInstanceFormatterCheckbox(false);
// $eo = $grid->colModel()->getItem('note')
// ->set_edittype('checkbox')
// ->getInstanceEditOptions()
// //->set_value(array("1"=>"One","2"=>"Two","3"=>"Three"))
// ;

// Grid-Options definieren
$grid2 = new JqGrid("#grid2");
$grid2->height('auto')
->width(800)
->resizable(false)
->sortable(true)
->datatype('local')
->data(new JsFunction('mydata'))
->caption("Test-Grid2")
->shrinkToFit(true)
->colModel(array('addFieldsProperties' => array(
	"invdate" => array("width" => 90, 'formatter' => 'date'),
	     "id" => array('width' => 50, 'key' => true, 'sorttype' => 'int'),
	   "name" => array('width' => 50, 'editable' => true),
	 "amount" => array('width' => 50, 'sortable' => false),
	    "tax" => array('width' => 50),
	  "total" => array('width' => 50),
	   "note" => array('width' => 50, 'editable' => true),
	    'url' => array('width' => 150,'editable' => true, 'formatter' => $ComFormatterUrl)
)));

// Hole Grid-Methoden Instanz. Parameter '.' für Javascript-Chaining
$gMeth = $grid->getInstanceMethods('.');

// Hole NavGrid Instanz.
$gNav = $grid->getInstanceNavGrid($grid->pager);

// Nav-Grid Options festlegen
$gNav->set_add(true)
->set_edit(true)
->set_del(true)
->set_search(false)
->set_position('right');

// Buttons definieren
$navBtnHello = new JqGridNavGridButton();
$navBtnHello
->onClickButton(New JsFunction('function() { alert("Who clicked me?");}'))
->set_caption('ClickCaptain')
->set_id('ClickID1')
->set_position('first')
->set_title('ClickTitle')
->set_cursor('progress');

$navBtnColChooser = new JqGridNavGridButton();
$navBtnColChooser
->onClickButton( $gMeth->bindToJsObject(true)->columnChooser( new JqGridColumnChooserOptions(null)) )
//->onClickButton(New JsFunction('function() { alert("Who clicked me?");}'))
->set_caption('Cols')
->set_id('btnColCh')
->set_position('first')
->set_title('Spalten auswaehlen')
->set_cursor('progress');

// Buttons hinzufügen
$grid
->addChainMethod( $gMeth->bindToJsObject(false)->navButtonAdd($navBtnHello, $grid->pager ) )
->addChainMethod( $gMeth->navButtonAdd($navBtnColChooser, $grid->pager ) )

// Addon-Funktionen hinzufügen
->addChainMethod( $gMeth->gridResize(null) )
->addChainMethod( $gMeth->sortableRows(null) )
->addChainMethod( $gMeth->gridDnD( new JqGridDnDOptions( array("connectWith"=>'#grid2') ) ))
;


// Füge für Grid benötigte Funktionen, Erweiterungen etc. hinzu
$grid->addCode($ComFormatterUrl);
$grid->enableInlineEditingDefaults();

$m = $grid->getInstanceMethods()->bindToJsObject( true );
$grid->addCode( "alert(".$m->getDataIDs().")");

?>
<html>
<head>
<?php
include( '../jqgrid.local.headerfiles.php' );
?>
<script>

// LocalData-Array zum Testen
// Muss für Edit-Funktionen extra berücksichtigt werden in dem url auf 'clientArray' gesetzt wird
var mydata = [ 
	{id:"1",invdate:"2007-10-01",name:"test",note:"1",amount:"200.00",tax:"10.00",total:"210.00",url:"www.heise.de"}
//	,{id:"2",invdate:"2007-10-02",name:"test2",note:"2",amount:"300.00",tax:"20.00",total:"320.00",url:"www.heise.de"},
//	{id:"3",invdate:"2007-09-01",name:"test3",note:"3",amount:"400.00",tax:"30.00",total:"430.00",url:"www.heise.de"}
//	,{id:"4",invdate:"2007-10-04",name:"test",note:"1",amount:"200.00",tax:"10.00",total:"210.00",url:"www.heise.de"},
//	{id:"5",invdate:"2007-10-05",name:"test2",note:"2",amount:"300.00",tax:"20.00",total:"320.00",url:"www.heise.de"},
//	{id:"6",invdate:"2007-09-06",name:"test3",note:"3",amount:"400.00",tax:"30.00",total:"430.00",url:"www.heise.de"},
//	{id:"7",invdate:"2007-10-04",name:"test",note:"1",amount:"200.00",tax:"10.00",total:"210.00",url:"www.heise.de"},
//	{id:"8",invdate:"2007-10-03",name:"test2",note:"2",amount:"300.00",tax:"20.00",total:"320.00",url:"www.heise.de"},
//	{id:"11",invdate:"2007-09-01",name:"test3",note:"3",amount:"400.00",tax:"30.00",total:"430.00",url:"www.heise.de"}
];

// Grid ausgeben
jQuery(function() {
<?php 
echo str_replace("{", "\n{", $grid->getJsCode() );
echo $grid->getCode();

echo str_replace("{", "\n{", $grid2->getJsCode() );
?>
});
</script>

</head>
<body>

<table id="grid1"></table> 
<div id="grid1_pager"></div> 


<table id="grid2"></table> 
</body>
</html>