<html>
<head>
<?php
//readfile( '../jqgrid.cdn.headerfiles.html' );
readfile( '../jqgrid.local.headerfiles.html' );
?>
<style>
.blackandwhite {
	background-color:#000;
	background:#000 !important;
	color:#fff;
}
</style>
</head>
<body>

<table id="list4"></table> 
<div id="pager4"></div> 
Java Scrpt code 
<script>

var mydata = [ 
	{id:"1",invdate:"2007-10-01",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"}, 
	{id:"2",invdate:"2007-10-02",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"}, 
	{id:"3",invdate:"2007-09-01",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"}, 
	{id:"4",invdate:"2007-10-04",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"}, 
	{id:"5",invdate:"2007-10-05",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"}, 
	{id:"6",invdate:"2007-09-06",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"}, 
	{id:"7",invdate:"2007-10-04",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"}, 
	{id:"8",invdate:"2007-10-03",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"}, 
	{id:"9",invdate:"2007-09-01",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"} 
];

function doItRed(val, name, row) {
	//alert("name:"+name+"; Amount:"+row.amount);
	return "<span style='color:#f00;'>"+val+"</span>";
}
jQuery("#list4").jqGrid({ 
	datatype: "local", 
	height: 250, 
	pager:'#pager4',
	altclass: 'blackandwhite',
	data: mydata,
	altRows: true,
	colNames:['Inv No','Client', 'Date', 'Amount','Tax','Total','Notes'], 
	colModel:[ {name:'id',index:'id', width:60, sorttype:"int"}, 
		{name:'invdate',label:'Inv-Date',index:'invdate', width:90, edittype:'text', editable:true, sorttype:"date", 
			editoptions:{readonly:true},
			formoptions:{rowpos:1, colpos:1}}, 
		{name:'name',index:'Name', formatter:doItRed, editable:true, width:100,
			formoptions:{rowpos:1, colpos:2}}, 
		{name:'amount',index:'amount', editable:true, width:80, align:"right",sorttype:"float",
			formoptions:{rowpos:2, colpos:1}}, 
		{name:'tax',index:'tax', editable:true, width:80, align:"right",sorttype:"float",
			formoptions:{rowpos:3, colpos:1}}, 
		{name:'total',index:'total', editable:true, width:80,align:"right",sorttype:"float",
			formoptions:{rowpos:4, colpos:1}}, 
		{name:'note',index:'note', editable:true, width:150, sortable:false,
			formoptions:{rowpos:5, colpos:1}} 
	],
	//data: mydata,
	toolbar: [true,"top"],
	multiselect: true, 
	caption: "Manipulating Array Data",
	onSelectRow: function(id) {
		alert( "onSelectRow(id:"+id);
		jQuery("#list4").jqGrid( "editRow", id );
	}
});
jQuery("#list4").jqGrid('navGrid',"#pager4",{edit:true,add:false,del:false});
$("#t_list4").append("<input type='button' value='Click Me' style='height:20px;font-size:-3'/>"); 
$("input","#t_list4").click(function(){ 
	alert("Hi! I'm added button at this toolbar"); 
});
 

//for(var i=0;i<=mydata.length;i++) 
//	jQuery("#list4").jqGrid('addRowData',i+1,mydata[i]);

//$('#list4').setGridParam({data: mydata}).trigger("reloadGrid");
</script>
</body>
</html>

