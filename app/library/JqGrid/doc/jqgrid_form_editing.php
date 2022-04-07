<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en"><head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="generator" content="DokuWiki Release 2009-02-14b">
<meta name="robots" content="index,follow">
<meta name="date" content="2011-03-28T19:54:55+0300">
<meta name="keywords" content="wiki,form_editing">
<link rel="search" type="application/opensearchdescription+xml" href="http://www.trirand.com/jqgridwiki/lib/exe/opensearch.php" title="jqGrid Wiki">
<link rel="start" href="http://www.trirand.com/jqgridwiki/">
<link rel="contents" href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;do=index" title="Index">
<link rel="alternate" type="application/rss+xml" title="Recent Changes" href="http://www.trirand.com/jqgridwiki/feed.php">
<link rel="alternate" type="application/rss+xml" title="Current Namespace" href="http://www.trirand.com/jqgridwiki/feed.php?mode=list&amp;ns=wiki">
<link rel="alternate" type="text/html" title="Plain HTML" href="http://www.trirand.com/jqgridwiki/doku.php?do=export_xhtml&amp;id=wiki:form_editing">
<link rel="alternate" type="text/plain" title="Wiki Markup" href="http://www.trirand.com/jqgridwiki/doku.php?do=export_raw&amp;id=wiki:form_editing">
<link rel="stylesheet" media="all" type="text/css" href="jqgrid_form_editing-Dateien/css.css">
<link rel="stylesheet" media="screen" type="text/css" href="jqgrid_form_editing-Dateien/css_003.css">
<link rel="stylesheet" media="print" type="text/css" href="jqgrid_form_editing-Dateien/css_002.css">
<script type="text/javascript" charset="utf-8" src="jqgrid_form_editing-Dateien/edit.js"></script>
<script type="text/javascript" charset="utf-8" src="jqgrid_form_editing-Dateien/js.php"></script>
<script type="text/javascript" charset="utf-8"><!--//--><![CDATA[//><!--
NS = "wiki";var toolbar = [{"type":"format","title":"Bold Text","icon":"bold.png","key":"b","open":"**","close":"**"},{"type":"format","title":"Italic Text","icon":"italic.png","key":"i","open":"\/\/","close":"\/\/"},{"type":"format","title":"Underlined Text","icon":"underline.png","key":"u","open":"__","close":"__"},{"type":"format","title":"Code Text","icon":"mono.png","key":"c","open":"''","close":"''"},{"type":"format","title":"Strike-through Text","icon":"strike.png","key":"d","open":"<del>","close":"<\/del>"},{"type":"format","title":"Internal Link","icon":"link.png","key":"l","open":"[[","close":"]]"},{"type":"format","title":"External Link","icon":"linkextern.png","open":"[[","close":"]]","sample":"http:\/\/example.com|External Link"},{"type":"format","title":"Ordered List Item","icon":"ol.png","open":"  - ","close":"\\n"},{"type":"format","title":"Unordered List Item","icon":"ul.png","open":"  * ","close":"\\n"},{"type":"insert","title":"Horizontal Rule","icon":"hr.png","insert":"\\n----\\n"},{"type":"picker","title":"Smileys","icon":"smiley.png","list":{"8-)":"icon_cool.gif","8-O":"icon_eek.gif","8-o":"icon_eek.gif",":-(":"icon_sad.gif",":-)":"icon_smile.gif","=)":"icon_smile2.gif",":-\/":"icon_doubt.gif",":-\\":"icon_doubt2.gif",":-?":"icon_confused.gif",":-D":"icon_biggrin.gif",":-P":"icon_razz.gif",":-o":"icon_surprised.gif",":-O":"icon_surprised.gif",":-x":"icon_silenced.gif",":-X":"icon_silenced.gif",":-|":"icon_neutral.gif",";-)":"icon_wink.gif","^_^":"icon_fun.gif",":?:":"icon_question.gif",":!:":"icon_exclaim.gif","LOL":"icon_lol.gif","FIXME":"fixme.gif","DELETEME":"delete.gif"},"icobase":"smileys"},{"type":"picker","title":"Special Chars","icon":"chars.png","list":["\u00c0","\u00e0","\u00c1","\u00e1","\u00c2","\u00e2","\u00c3","\u00e3","\u00c4","\u00e4","\u01cd","\u01ce","\u0102","\u0103","\u00c5","\u00e5","\u0100","\u0101","\u0104","\u0105","\u00c6","\u00e6","\u0106","\u0107","\u00c7","\u00e7","\u010c","\u010d","\u0108","\u0109","\u010a","\u010b","\u00d0","\u0111","\u00f0","\u010e","\u010f","\u00c8","\u00e8","\u00c9","\u00e9","\u00ca","\u00ea","\u00cb","\u00eb","\u011a","\u011b","\u0112","\u0113","\u0116","\u0117","\u0118","\u0119","\u0122","\u0123","\u011c","\u011d","\u011e","\u011f","\u0120","\u0121","\u0124","\u0125","\u00cc","\u00ec","\u00cd","\u00ed","\u00ce","\u00ee","\u00cf","\u00ef","\u01cf","\u01d0","\u012a","\u012b","\u0130","\u0131","\u012e","\u012f","\u0134","\u0135","\u0136","\u0137","\u0139","\u013a","\u013b","\u013c","\u013d","\u013e","\u0141","\u0142","\u013f","\u0140","\u0143","\u0144","\u00d1","\u00f1","\u0145","\u0146","\u0147","\u0148","\u00d2","\u00f2","\u00d3","\u00f3","\u00d4","\u00f4","\u00d5","\u00f5","\u00d6","\u00f6","\u01d1","\u01d2","\u014c","\u014d","\u0150","\u0151","\u0152","\u0153","\u00d8","\u00f8","\u0154","\u0155","\u0156","\u0157","\u0158","\u0159","\u015a","\u015b","\u015e","\u015f","\u0160","\u0161","\u015c","\u015d","\u0162","\u0163","\u0164","\u0165","\u00d9","\u00f9","\u00da","\u00fa","\u00db","\u00fb","\u00dc","\u00fc","\u01d3","\u01d4","\u016c","\u016d","\u016a","\u016b","\u016e","\u016f","\u01d6","\u01d8","\u01da","\u01dc","\u0172","\u0173","\u0170","\u0171","\u0174","\u0175","\u00dd","\u00fd","\u0178","\u00ff","\u0176","\u0177","\u0179","\u017a","\u017d","\u017e","\u017b","\u017c","\u00de","\u00fe","\u00df","\u0126","\u0127","\u00bf","\u00a1","\u00a2","\u00a3","\u00a4","\u00a5","\u20ac","\u00a6","\u00a7","\u00aa","\u00ac","\u00af","\u00b0","\u00b1","\u00f7","\u2030","\u00bc","\u00bd","\u00be","\u00b9","\u00b2","\u00b3","\u00b5","\u00b6","\u2020","\u2021","\u00b7","\u2022","\u00ba","\u2200","\u2202","\u2203","\u018f","\u0259","\u2205","\u2207","\u2208","\u2209","\u220b","\u220f","\u2211","\u203e","\u2212","\u2217","\u221a","\u221d","\u221e","\u2220","\u2227","\u2228","\u2229","\u222a","\u222b","\u2234","\u223c","\u2245","\u2248","\u2260","\u2261","\u2264","\u2265","\u2282","\u2283","\u2284","\u2286","\u2287","\u2295","\u2297","\u22a5","\u22c5","\u25ca","\u2118","\u2111","\u211c","\u2135","\u2660","\u2663","\u2665","\u2666","\u03b1","\u03b2","\u0393","\u03b3","\u0394","\u03b4","\u03b5","\u03b6","\u03b7","\u0398","\u03b8","\u03b9","\u03ba","\u039b","\u03bb","\u03bc","\u039e","\u03be","\u03a0","\u03c0","\u03c1","\u03a3","\u03c3","\u03a4","\u03c4","\u03c5","\u03a6","\u03c6","\u03c7","\u03a8","\u03c8","\u03a9","\u03c9","\u2605","\u2606","\u260e","\u261a","\u261b","\u261c","\u261d","\u261e","\u261f","\u2639","\u263a","\u2714","\u2718","\u00d7","\u201e","\u201c","\u201d","\u201a","\u2018","\u2019","\u00ab","\u00bb","\u2039","\u203a","\u2014","\u2013","\u2026","\u2190","\u2191","\u2192","\u2193","\u2194","\u21d0","\u21d1","\u21d2","\u21d3","\u21d4","\u00a9","\u2122","\u00ae","\u2032","\u2033","[","]","{","}","~","(",")","%","\u00a7","$","#","|","@"]}];

//--><!]]></script>
		<title>wiki:form_editing - jqGrid Wiki</title>
					<link rel="shortcut icon" href="http://www.trirand.com/jqgridwiki/lib/tpl/monobook/user/favicon.ico">
				<style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "/jqgridwiki/lib/tpl/monobook/monobook/main.css"; /*]]>*/</style>

		<link rel="stylesheet" type="text/css" media="print" href="jqgrid_form_editing-Dateien/commonPrint.css">
		<link rel="stylesheet" type="text/css" media="print" href="jqgrid_form_editing-Dateien/print.htm">

		<script type="text/javascript" src="jqgrid_form_editing-Dateien/wikibits.js"></script>
		<style type="text/css" media="screen,projection">/*<![CDATA[*/
		@import "/jqgridwiki/lib/tpl/monobook/wikipedia/en/Common.css";
		@import "/jqgridwiki/lib/tpl/monobook/wikipedia/en/Monobook.css";
		@import "/jqgridwiki/lib/tpl/monobook/dokuwiki/doku.css";
		@import "/jqgridwiki/lib/tpl/monobook/lang/en/lang.css";
		@import "/jqgridwiki/lib/tpl/monobook/user/Common.css";
		@import "/jqgridwiki/lib/tpl/monobook/user/Monobook.css";
		/*]]>*/</style>
    <!--[if lt IE 5.5000]><style type="text/css">@import "/jqgridwiki/lib/tpl/monobook/monobook/IE50Fixes.css";</style><![endif]-->
    <!--[if IE 5.5000]><style type="text/css">@import "/jqgridwiki/lib/tpl/monobook/monobook/IE55Fixes.css";</style><![endif]-->
    <!--[if IE 6]><style type="text/css">@import "/jqgridwiki/lib/tpl/monobook/monobook/IE60Fixes.css";</style><![endif]-->
    <!--[if IE 7]><style type="text/css">@import "/jqgridwiki/lib/tpl/monobook/monobook/IE70Fixes.css";</style><![endif]-->
    <!--[if lt IE 7]><script type="text/javascript" src="/jqgridwiki/lib/tpl/monobook/common/IEFixes.js"></script>

    <meta http-equiv="imagetoolbar" content="no" /><![endif]-->
	</head>
	<body class="mediawiki ns-1 ltr">
		<div id="globalWrapper">
			<div id="column-content">
				<div id="content">
					<a name="top" id="top"></a>
					<div id="siteNotice"></div>					<div id="bodyContent">
						<div class="dokuwiki">
							<!-- start content -->
							
														<div id="qsearch__out" class="ajax_qsearch JSpopup"></div>
<!-- TOC START -->
<div class="toc">
<div style="cursor: pointer;" class="tocheader toctoggle" id="toc__header"><span style="cursor: pointer;" class="toc_close" id="toc__toggle"><span>−</span></span>Table of Contents</div>
<div id="toc__inside">

<ul class="toc">
<li class="level1"><div class="li"><span class="li"><a href="#form_editing" class="toc"><span class="search_hit">Form</span> <span class="search_hit">Editing</span></a></span></div>
<ul class="toc">
<li class="level2"><div class="li"><span class="li"><a href="#software_requirements_installation" class="toc">Software Requirements &amp; Installation</a></span></div></li>
<li class="level2"><div class="li"><span class="li"><a href="#methods" class="toc">Methods</a></span></div>
<ul class="toc">
<li class="level3"><div class="li"><span class="li"><a href="#searchgrid" class="toc">searchGrid</a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#editgridrow" class="toc">editGridRow</a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#adding_row" class="toc">Adding Row</a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#viewgridrow" class="toc">viewGridRow</a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#delgridrow" class="toc">delGridRow</a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#gridtoform" class="toc">GridTo<span class="search_hit">Form</span></a></span></div></li>
<li class="level3"><div class="li"><span class="li"><a href="#formtogrid" class="toc"><span class="search_hit">Form</span>ToGrid</a></span></div></li>
</ul>
</li>
</ul>
</li>
<li class="level1"><div class="li"><span class="li"><a href="#discussion__section" class="toc">Discussion</a></span></div></li></ul>
</div>
</div>
<!-- TOC END -->

<p>
<a href="http://www.trirand.com/jqgridwiki/doku.php?do=export_odt&amp;id=wiki:form_editing" title="Export page to Open Document format"><img src="jqgrid_form_editing-Dateien/odt.png" alt="Export page to Open Document format" align="right" height="48" width="48"></a>

</p>

<h1><a name="form_editing" id="form_editing"><span class="search_hit">Form</span> <span class="search_hit">Editing</span></a></h1>
<div class="level1">

<p>

<span class="search_hit">jqGrid</span> supports creating a <span class="search_hit">form</span> “on the fly” to view, add, edit, delete, or search grid data.  A screenshot of an “Add Record” <span class="search_hit">form</span> is shown below:
</p>

<p>
<a href="http://www.trirand.com/jqgridwiki/lib/exe/detail.php?id=wiki%3Aform_editing&amp;media=wiki:formeditshot.jpg" class="media" title="wiki:formeditshot.jpg"><img src="jqgrid_form_editing-Dateien/fetch.jpg" class="media" alt=""></a>
</p>

<p>
</p><p></p><div class="notetip">It's possible to use a combination of Modal Boxes and Inline <span class="search_hit">Editing</span> in your application.  After all, Inline <span class="search_hit">editing</span>
 can only be used to edit an existing row and can't be used to search, 
delete, or add data.   However, it's not recommended to use both Inline <span class="search_hit">Editing</span> and Modal Box <span class="search_hit">Editing</span> because one will interfere with the other when both select the same row.  (In this scenario, when you select a row for <span class="search_hit">editing</span> then click the edit model box, you'll get the ID names of the cells in the box rather than the actual cell data contents.
</div><p></p>
<p></p>

<p>

The methods listed in the table below are included in the navGrid (navigator method).
</p>

</div>

<h2><a name="software_requirements_installation" id="software_requirements_installation">Software Requirements &amp; Installation</a></h2>
<div class="level2">

<p>

To use this module, you should mark the Common, <span class="search_hit">Form</span> <span class="search_hit">Editing</span> and optional jqModal and jqDnR plugins when you download the grid. For more in<span class="search_hit">form</span>ation refer to <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:download" class="wikilink1" title="wiki:download">Download</a>.<br>
 
</p>

<p>
Note For Developers - this is the grid.common.js, grid.<span class="search_hit">form</span>edit.js, jqModal.js and jqDnR.js in the src directory. 
</p>

</div>

<h2><a name="methods" id="methods">Methods</a></h2>
<div class="level2">

<p>
Methods used in <span class="search_hit">form</span> <span class="search_hit">editing</span> are a sub-set of those of the parent grid, and described below. <br>
 
All the properties and events of the methods are not a part of <span class="search_hit">jqGrid</span>, but a part of options passed as parameters to the method.
</p>

</div>

<h3><a name="searchgrid" id="searchgrid">searchGrid</a></h3>
<div class="level3">

<p>

For more in<span class="search_hit">form</span>ation about this method refer to <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:single_searching" class="wikilink2" title="wiki:single_searching" rel="nofollow">Single Searching</a> and <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:advanced_searching" class="wikilink1" title="wiki:advanced_searching">Advanced Searching</a> 
</p>

</div>

<h3><a name="editgridrow" id="editgridrow">editGridRow</a></h3>
<div class="level3">

<p>
This method creates a <span class="search_hit">form</span> via modal dialog for <span class="search_hit">editing</span> a particular row from the grid. <br>

This method uses <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules" class="wikilink1" title="wiki:common_rules"> Common <span class="search_hit">Editing</span> Properties </a> from colModel and editurl option from <span class="search_hit">jqGrid</span>.
</p>

<p>
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">editGridRow</span><span class="br0">(</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>

or when we use the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'editGridRow'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>

Where
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> rowid: the id of the row to edit</div>
</li>
<li class="level1"><div class="li"> properties: an array of name: value pairs, including any of the following properties or events.</div>
</li>
</ul>

<p>

The method uses the following properties from language file:

</p>
<pre class="code javascript">$.<span class="me1">jgrid</span> <span class="sy0">=</span> <span class="br0">{</span>
...
	<span class="me1">edit</span> <span class="sy0">:</span> <span class="br0">{</span>
		addCaption<span class="sy0">:</span> <span class="st0">"Add Record"</span><span class="sy0">,</span>
		editCaption<span class="sy0">:</span> <span class="st0">"Edit Record"</span><span class="sy0">,</span>
		bSubmit<span class="sy0">:</span> <span class="st0">"Submit"</span><span class="sy0">,</span>
		bCancel<span class="sy0">:</span> <span class="st0">"Cancel"</span><span class="sy0">,</span>
		bClose<span class="sy0">:</span> <span class="st0">"Close"</span><span class="sy0">,</span>
		saveData<span class="sy0">:</span> <span class="st0">"Data has been changed! Save changes?"</span><span class="sy0">,</span>
		bYes <span class="sy0">:</span> <span class="st0">"Yes"</span><span class="sy0">,</span>
		bNo <span class="sy0">:</span> <span class="st0">"No"</span><span class="sy0">,</span>
		bExit <span class="sy0">:</span> <span class="st0">"Cancel"</span><span class="sy0">,</span>
...
	<span class="br0">}</span><span class="sy0">,</span>
...
<span class="br0">}</span></pre>
<p>
These options can be overwritten when passed as options to the method. 
When passed to the method we should use by example bSubmit : “Submit” 
and not $.jgrid.edit.bSubmit : “Submit” <br>
 
</p>

<p>
When called the constructed <span class="search_hit">form</span> can look like this
<a href="http://www.trirand.com/jqgridwiki/lib/exe/detail.php?id=wiki%3Aform_editing&amp;media=wiki:editrow.png" class="media" title="wiki:editrow.png"><img src="jqgrid_form_editing-Dateien/fetch.png" class="mediacenter" title=" Edit Row" alt=" Edit Row"></a>
</p>

</div>

<h4><a name="properties" id="properties">Properties</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Property</th><th class="col1">Description</th><th class="col2">Default</th>
	</tr>
	<tr class="row1">
		<td class="col0">top</td><td class="col1">the initial top position of 
modal dialog. The default value of 0 mean the top position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the top 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row2">
		<td class="col0">left</td><td class="col1">the initial left position 
of modal dialog. The default value of 0 mean the left position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the left 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row3">
		<td class="col0">width</td><td class="col1">the width of confirmation dialog</td><td class="col2">300</td>
	</tr>
	<tr class="row4">
		<td class="col0">height</td><td class="col1">the entry height of confirmation dialog</td><td class="col2">auto</td>
	</tr>
	<tr class="row5">
		<td class="col0">dataheight</td><td class="col1">the parameter control the scrolling content - i.e between the modal header and modal footer.</td><td class="col2">auto</td>
	</tr>
	<tr class="row6">
		<td class="col0">modal</td><td class="col1">determines if the dialog will be modal. Also works only if jqModal plugin is present</td><td class="col2">false</td>
	</tr>
	<tr class="row7">
		<td class="col0">drag</td><td class="col1">Determines if the dialog is dragabale. Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UI</td><td class="col2"> true</td>
	</tr>
	<tr class="row8">
		<td class="col0">resize</td><td class="col1">determines if the dialog can be resized. Works only is jqDnR plugin is available or resizable widget is present from jQuery UI</td><td class="col2">true</td>
	</tr>
	<tr class="row9">
		<td class="col0">url</td><td class="col1"> url where to post data. If set, replaces the editurl</td><td class="col2">null</td>
	</tr>
	<tr class="row10">
		<td class="col0">mtype</td><td class="col1">Defines the type of request to make (“POST” or “GET”) when data is sent to the server</td><td class="col2">POST</td>
	</tr>
	<tr class="row11">
		<td class="col0">editData</td><td class="col1">an array used to add content to the data posted to the server</td><td class="col2">empty</td>
	</tr>
	<tr class="row12">
		<td class="col0">recreate<span class="search_hit">Form</span></td><td class="col1">when set to true the <span class="search_hit">form</span> is recreated every time the dialog is activeted with the new options from colModel (if they are changed)</td><td class="col2">false</td>
	</tr>
	<tr class="row13">
		<td class="col0">jqModal</td><td class="col1">If set to true uses jqModal plugin (if present) to creat the dialogs. If set to true and the plugin is not present <span class="search_hit">jqGrid</span> uses its internal function to create dialog</td><td class="col2">true</td>
	</tr>
	<tr class="row14">
		<td class="col0">addedrow</td><td class="col1">Controls where the row 
just added is placed: 'first' at the top of the gird, 'last' at the 
bottom. Where the new row is to appear in its natural sort order, set 
reloadAfterSubmit: true</td><td class="col2">first</td>
	</tr>
	<tr class="row15">
		<td class="col0">topinfo</td><td class="col1">When set this in<span class="search_hit">form</span>ation is placed just after the modal header as additional row</td><td class="col2">empty string</td>
	</tr>
	<tr class="row16">
		<td class="col0">bottominfo</td><td class="col1">When set this in<span class="search_hit">form</span>ation is placed just after the buttons of the <span class="search_hit">form</span> as additional row</td><td class="col2">empty string</td>
	</tr>
	<tr class="row17">
		<td class="col0">saveicon</td><td class="col1"> array. Determines the 
icon of the submit button. The default value is 
[true,”left”,”ui-icon-disk”]. The first item enables/disables the icon. 
The second item tells where to put the icon to left or to right of the 
text. The third item corresponds to valid ui icon from theme roller </td><td class="col2"> </td>
	</tr>
	<tr class="row18">
		<td class="col0">closeicon</td><td class="col1"> Array. Determines the
 icon of the cancel button. The default values are 
[true,”left”,”ui-icon-close”]. For description of these see saveicon</td><td class="col2"> </td>
	</tr>
	<tr class="row19">
		<td class="col0">savekey</td><td class="col1"> array. Determines the possibility to save the <span class="search_hit">form</span>
 with pressing a certain key.The first item enables/disables saving with
 pressing certain key. The second item corresponds to key code for 
saving. If enabled the default value for saving is [Enter]. <br>
 Note that this binding should be used for both adding and <span class="search_hit">editing</span> a row. Since the binding is for the <span class="search_hit">form</span>, there is no possibility to have one key in add and another in edit mode. </td><td class="col2">[false,13]</td>
	</tr>
	<tr class="row20">
		<td class="col0">navkeys</td><td class="col1"> array. This option 
works only in edit mode and  add keyboard navigation, which allow us to 
navigate through the records while in <span class="search_hit">form</span> <span class="search_hit">editing</span>
 pressing certain keys. The default state is disabled. The first item 
enables/disables the navigation. The second item corresponds to reccord 
up and by default is the  the key code for Up key. The third item 
corresponds to reccord down and by default is the  key code for Down key</td><td class="col2">[false,38,40]</td>
	</tr>
	<tr class="row21">
		<td class="col0">checkOnSubmit</td><td class="col1">This option work only in <span class="search_hit">editing</span> mode. If Set to true this option will work only when a submit button is clicked and  if any data is changed in the <span class="search_hit">form</span>.
 If the data is changed a dilog message appear where the user is asked 
to confirm the changes or cancel it. Pressing cancel button of the new 
dialog will return to the <span class="search_hit">form</span>, but does not set the values to its original state.</td><td class="col2">false</td>
	</tr>
	<tr class="row22">
		<td class="col0">checkOnUpdate</td><td class="col1">This option is applicable in add and edit mode. When this option is set to true the behaviour as follow: <br>
 when something is changed in the <span class="search_hit">form</span> and the user click on Cancel button, navigator buttons, close button (on upper right corner of the <span class="search_hit">form</span>),
 in overlay (if available) or press Esc key (if set) a message box 
appear asking the user to save the changes, not to save the changes or 
go back in the grid cancel all changes (this will close the modal <span class="search_hit">form</span>)</td><td class="col2">false</td>
	</tr>
	<tr class="row23">
		<td class="col0">closeAfterAdd</td><td class="col1">when add mode, close the dialog after add record</td><td class="col2">false</td>
	</tr>
	<tr class="row24">
		<td class="col0">clearAfterAdd</td><td class="col1">when add mode, clear the data after adding data</td><td class="col2">true</td>
	</tr>
	<tr class="row25">
		<td class="col0">closeAfterEdit</td><td class="col1">when in edit mode, close the dialog after <span class="search_hit">editing</span></td><td class="col2">false</td>
	</tr>
	<tr class="row26">
		<td class="col0">reloadAfterSubmit</td><td class="col1">reload grid data after posting</td><td class="col2">true</td>
	</tr>
	<tr class="row27">
		<td class="col0">closeOnEscape</td><td class="col1">When set to true the modal window can be closed with ESC key from the user.</td><td class="col2">false</td>
	</tr>
	<tr class="row28">
		<td class="col0">ajaxEditOptions</td><td class="col1">This option allow to set global ajax settings for the <span class="search_hit">form</span>
 editiing when we save the data to the server. Note that with this 
option is possible to overwrite all current ajax setting in the save 
request including the complete event.</td><td class="col2">empty object</td>
	</tr>
	<tr class="row29">
		<td class="col0">viewPagerButtons</td><td class="col1"> This option enable or disable the appearing of the previous and next buttons (pager buttons) in the <span class="search_hit">form</span> </td><td class="col2">true</td>
	</tr>
	<tr class="row30">
		<td class="col0">zIndex</td><td class="col1">The starting z-index for the dialog. If you will see the dialog <span class="search_hit">form</span>
 under another elements or dialogs you should use the parameter with 
some value higher as default value 950. In the most cases it should be 
the value higher as 1000 - the default value of jQuery UI dialog.</td><td class="col2">950</td>
	</tr>
</tbody></table>

</div>

<h4><a name="events" id="events">Events</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Event</th><th class="col1">Description</th>
	</tr>
	<tr class="row1">
		<td class="col0">afterclickPgButtons</td><td class="col1">This event 
can be used only when we are in edit mode and the navigator buttons are 
enabled; it fires after the data for the new row is loaded from the 
grid, allowing modification of the data or <span class="search_hit">form</span> before the <span class="search_hit">form</span> is redisplayed. <br>
 afterclickPgButtons : function(whichbutton, <span class="search_hit">form</span>id, rowid) {…} <br>
 where <br>
 <em>whichbutton</em> is either 'prev' or 'next' <br>
 <em><span class="search_hit">form</span>id</em> is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id. <br>
 <em>rowid</em> is the id of the current row </td>
	</tr>
	<tr class="row2">
		<td class="col0">afterComplete</td><td class="col1"> This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid. <br>
 afterComplete : function (response, postdata, <span class="search_hit">form</span>id) {…} <br>
 where <br>
 <em>response</em> is the data returned from the server (if any) <br>
 <em>postdata</em> an array, is the data sent to the server <br>
 <em><span class="search_hit">form</span>id</em> is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id. </td>
	</tr>
	<tr class="row3">
		<td class="col0">afterShow<span class="search_hit">Form</span></td><td class="col1">fires after showing the <span class="search_hit">form</span>; receives as Parameter the id of the constructed <span class="search_hit">form</span>. <br>
 afterShow<span class="search_hit">Form</span> : function (<span class="search_hit">form</span>id) {…} </td>
	</tr>
	<tr class="row4">
		<td class="col0">afterSubmit</td><td class="col1"> fires after 
response has been received from server. Typically used to display status
 from server (e.g., the data is successfully saved or the save cancelled
 for server-side <span class="search_hit">editing</span> reasons). 
Receives as parameters the data returned from the request and an array 
of the posted values of type id=value1,value2. <br>
 When used this event should return array with the following items [success, message, new_id] <br>
 where <br>
 <em>success</em> is a boolean value if true the process continues, if false a error <em>message</em> appear and all other processing is stopped. (message is ignored if success is true). <br>
 <em>new_id</em> can be used to set the new row id in the grid when we are in add mode. <br>
 afterSubmit : function(response, postdata) <br>
 { <br>
 … <br>
 return [success,message,new_id] <br>
 } </td>
	</tr>
	<tr class="row5">
		<td class="col0">beforeCheckValues</td><td class="col1">This event fires before checking the values (if checking is defined in colModel via editrules option). <br>
 To this event we pass the following parameters: <br>
 1. <em>posdata</em> - the array of values which should be chaced in name:value pair, where the name is the name from colModel. <br>
 2. <em><span class="search_hit">form</span>id</em> - the <span class="search_hit">form</span> id object. <br>
 3. <em>mode</em> - the current mode in which we are - can be add or edit. <br>
 In all cases the event should return object in name value pair which then will be posted to the server.</td>
	</tr>
	<tr class="row6">
		<td class="col0">beforeInitData</td><td class="col1">fires before initialize the new <span class="search_hit">form</span> data. Receives, as parameter, the id of the constructed <span class="search_hit">form</span>. <br>
 beforeInitData : function(<span class="search_hit">form</span>id) {…} </td>
	</tr>
	<tr class="row7">
		<td class="col0">beforeShow<span class="search_hit">Form</span></td><td class="col1">fires before showing the <span class="search_hit">form</span> with the new data; receives as Parameter the id of the constructed <span class="search_hit">form</span>. <br>
 beforeShow<span class="search_hit">Form</span> : function(<span class="search_hit">form</span>id) {…} </td>
	</tr>
	<tr class="row8">
		<td class="col0">beforeSubmit</td><td class="col1">fires before the data is submitted to the server. Recieves as parameter the posted data array and the <span class="search_hit">form</span>id. <br>
 beforeSubmit : function(postdata, <span class="search_hit">form</span>id) { <br>
 … <br>
 return[success,message]; <br>
 } <br>
 When defined this event should return array with the following values <br>
 <em>success</em> boolean indicating if the proccess should continue (true) or a error (false) <em>message</em> should appear to the user</td>
	</tr>
	<tr class="row9">
		<td class="col0">onclickPgButtons</td><td class="col1">This event can 
be used only when we are in edit mode; it fires immediately after the 
previous or next button is clicked, before leaving the current row, 
allowing working with (e.g., saving) the currently loaded values in the <span class="search_hit">form</span>. <br>
 onclickPgButtons : function (whichbutton, <span class="search_hit">form</span>id, rowid)  {…} <br>
 where <br>
 <em>whichbutton</em> is either 'prev' or 'next' <br>
 <em><span class="search_hit">form</span>id</em> is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id. <br>
 <em>rowid</em> is the id of the current row </td>
	</tr>
	<tr class="row10">
		<td class="col0">onclickSubmit</td><td class="col1">fires after the 
submit button is clicked and the postdata is constructed. Parameters 
passed to this event are: a options array of the method and the posted 
data array. The event should return array of type {} which extends the 
postdata array. <br>
 onclickSubmit : function(params, posdata) { <br>
 … <br>
 return {add_data} <br>
  }</td>
	</tr>
	<tr class="row11">
		<td class="col0">onInitialize<span class="search_hit">Form</span></td><td class="col1">fires only once when creating the data for <span class="search_hit">editing</span> and adding. Receives, as parameter, the id of the constructed <span class="search_hit">form</span>. <br>
 onInitialize<span class="search_hit">Form</span> : function(<span class="search_hit">form</span>id) {…}</td>
	</tr>
	<tr class="row12">
		<td class="col0">onClose</td><td class="col1">This event is called just before closing the <span class="search_hit">form</span>
 and when a close icon is clicked, a cancel button is clicked, ESC key 
is pressed or click on overlay (if jqModal is present). The event can 
return (optionally) true or false. If the return value is true the <span class="search_hit">form</span> is closed, if false the <span class="search_hit">form</span> does not close. If nothing is returned the <span class="search_hit">form</span> is closed</td>
	</tr>
	<tr class="row13">
		<td class="col0">errorText<span class="search_hit">Form</span>at</td><td class="col1">The event (can) fire when error occurs from the ajax call and can be used for better <span class="search_hit">form</span>atting
 of the error messages. To this event is passed response from the 
server. The event should return single message (not array), which then 
is displayed to the user.</td>
	</tr>
	<tr class="row14">
		<td class="col0">serializeEditData</td><td class="col1">If set this event can serialize the data passed to the ajax request when we save a <span class="search_hit">form</span>
 data. The function should return the serialized data. This event can be
 used when a custom data should be passed to the server - e.g - JSON 
string, <acronym title="Extensible Markup Language">XML</acronym> string and etc. To this event is passed the data which will be posted to the server</td>
	</tr>
</tbody></table>

</div>

<h4><a name="how_is_the_form_constructed" id="how_is_the_form_constructed">How is the <span class="search_hit">form</span> constructed</a></h4>
<div class="level4">

<p>
When the <span class="search_hit">form</span> is constructed we set the following rules: <br>

</p>
<ul>
<li class="level1"><div class="li"> Hidden fields are included in the <span class="search_hit">form</span> with the display:none property of the table row</div>
</li>
<li class="level1"><div class="li"> The id of the editable element is constructed from the name of the colModel array - propery - name</div>
</li>
<li class="level1"><div class="li"> The name of the editable element is constructed from the name of the colModel array - propery - name</div>
</li>
<li class="level1"><div class="li"> For ease in manipulating the elements in an edit <span class="search_hit">form</span>, every table row in the <span class="search_hit">form</span> that holds the data for the edit has a id which is a combination of “tr_” + name (from colmodel). Example:</div>
</li>
</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'tr_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span>edited element named, in colModel, as "myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a>&gt;</span></pre>
<p>

This allow us to easily show or hide some table rows depending on conditions using beforeShow<span class="search_hit">Form</span> event
</p>

</div>

<h4><a name="what_is_posted_to_the_server" id="what_is_posted_to_the_server">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in <span class="search_hit">editing</span> mode the data that is posted to the server is object {} that contain:
</p>
<ul>
<li class="level1"><div class="li"> the name:value pair  where the name is the name of the input element represented in the <span class="search_hit">form</span> (this is for all input elements)</div>
</li>
<li class="level1"><div class="li"> additionally we add a pair id:rowid where the rowid is the id of the edited row</div>
</li>
<li class="level1"><div class="li"> additionally we add a pair oper:edit to indicate the edit mode</div>
</li>
<li class="level1"><div class="li"> if the editData object is not empty we extend this data with the posted data</div>
</li>
<li class="level1"><div class="li"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object</div>
</li>
</ul>

</div>

<h3><a name="adding_row" id="adding_row">Adding Row</a></h3>
<div class="level3">

<p>
The editGridRow method is also used to add data to the server, by passing “new” as the rowid. <br>
 
This method uses <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules" class="wikilink1" title="wiki:common_rules"> Common <span class="search_hit">Editing</span> Properties </a> from colModel and editurl option from <span class="search_hit">jqGrid</span>.
</p>

<p>
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">editGridRow</span><span class="br0">(</span> <span class="st0">"new"</span><span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>

or with the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'editGridRow'</span><span class="sy0">,</span> <span class="st0">"new"</span><span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>
Where
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> properties: an array of name: value pairs, including any of the following properties or events.</div>
</li>
</ul>

<p>

The method uses the following properties from language file:

</p>
<pre class="code javascript">$.<span class="me1">jgrid</span> <span class="sy0">=</span> <span class="br0">{</span>
...
	<span class="me1">edit</span> <span class="sy0">:</span> <span class="br0">{</span>
		addCaption<span class="sy0">:</span> <span class="st0">"Add Record"</span><span class="sy0">,</span>
		editCaption<span class="sy0">:</span> <span class="st0">"Edit Record"</span><span class="sy0">,</span>
		bSubmit<span class="sy0">:</span> <span class="st0">"Submit"</span><span class="sy0">,</span>
		bCancel<span class="sy0">:</span> <span class="st0">"Cancel"</span><span class="sy0">,</span>
		bClose<span class="sy0">:</span> <span class="st0">"Close"</span><span class="sy0">,</span>
		saveData<span class="sy0">:</span> <span class="st0">"Data has been changed! Save changes?"</span><span class="sy0">,</span>
		bYes <span class="sy0">:</span> <span class="st0">"Yes"</span><span class="sy0">,</span>
		bNo <span class="sy0">:</span> <span class="st0">"No"</span><span class="sy0">,</span>
		bExit <span class="sy0">:</span> <span class="st0">"Cancel"</span><span class="sy0">,</span>
...
	<span class="br0">}</span><span class="sy0">,</span>
...
<span class="br0">}</span></pre>
<p>
These options can be overwritten when passed as options to the method. 
When passed to the method we should use by example bSubmit : “Submit” 
and not $.jgrid.edit.bSubmit : “Submit” <br>
 
</p>

<p>
The options are the same as those in editng row - See above.
</p>

</div>

<h4><a name="how_is_the_form_constructed1" id="how_is_the_form_constructed1">How is the <span class="search_hit">form</span> constructed</a></h4>
<div class="level4">

<p>
When the <span class="search_hit">form</span> is constructed we set the following rules: <br>

</p>
<ul>
<li class="level1"><div class="li"> Hidden fields are included in the <span class="search_hit">form</span> with the display:none property of the table row</div>
</li>
<li class="level1"><div class="li"> The id of the editable element is constructed from the name of the colModel array - propery - name</div>
</li>
<li class="level1"><div class="li"> The name of the editable element is constructed from the name of the colModel array - propery - name</div>
</li>
<li class="level1"><div class="li"> For ease in manipulating the elements in an edit <span class="search_hit">form</span>, every table row in the <span class="search_hit">form</span> that holds the data for the edit has a id which is a combination of “tr_” + name (from colmodel). Example:</div>
</li>
</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'tr_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span>edited element named, in colModel, as "myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a>&gt;</span></pre>
<p>

This allow us to easily show or hide some table rows depending on conditions using beforeShow<span class="search_hit">Form</span> event
</p>

</div>

<h4><a name="what_is_posted_to_the_server1" id="what_is_posted_to_the_server1">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in <span class="search_hit">editing</span> mode the data that is posted to the server is object {} that contain:
</p>
<ul>
<li class="level1"><div class="li"> the name:value pair  where the name is the name of the input element represented in the <span class="search_hit">form</span> (this is for all input elements)</div>
</li>
<li class="level1"><div class="li"> additionally we add a pair id:_empty where the _empty indicates that new row is inserted</div>
</li>
<li class="level1"><div class="li"> additionally we add a pair oper:add to indicate the add mode</div>
</li>
<li class="level1"><div class="li"> if the editData object is not empty we extend this data with the posted data</div>
</li>
<li class="level1"><div class="li"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object</div>
</li>
</ul>

</div>

<h3><a name="viewgridrow" id="viewgridrow">viewGridRow</a></h3>
<div class="level3">

<p>

This method is similar to the editGridRow method except that the fields 
are not editable and we do not have any events attached to the <span class="search_hit">form</span>. <br>
 
</p>

<p>
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">viewGridRow</span><span class="br0">(</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>

or when we use the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'viewGridRow'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>
<p>
Where
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> rowid: the id of the row to edit</div>
</li>
<li class="level1"><div class="li"> properties: an array of name: value pairs, including any of the following properties or events.</div>
</li>
</ul>

<p>

The method uses the following properties from language file:

</p>
<pre class="code javascript">$.<span class="me1">jgrid</span> <span class="sy0">=</span> <span class="br0">{</span>
...
	<span class="me1">view</span> <span class="sy0">:</span> <span class="br0">{</span>
		caption<span class="sy0">:</span> <span class="st0">"View Record"</span><span class="sy0">,</span>
		bClose<span class="sy0">:</span> <span class="st0">"Close"</span>
	<span class="br0">}</span><span class="sy0">,</span>
...</pre>
</div>

<h4><a name="properties1" id="properties1">Properties</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Property</th><th class="col1">Description</th><th class="col2">Default</th>
	</tr>
	<tr class="row1">
		<td class="col0">top</td><td class="col1">the initial top position of 
modal dialog. The default value of 0 mean the top position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the top 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row2">
		<td class="col0">left</td><td class="col1">the initial left position 
of modal dialog. The default value of 0 mean the left position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the left 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row3">
		<td class="col0">width</td><td class="col1">the width of confirmation dialog</td><td class="col2">300</td>
	</tr>
	<tr class="row4">
		<td class="col0">height</td><td class="col1">the entry height of confirmation dialog</td><td class="col2">auto</td>
	</tr>
	<tr class="row5">
		<td class="col0">dataheight</td><td class="col1">the parameter control the scrolling content - i.e between the modal header and modal footer.</td><td class="col2">auto</td>
	</tr>
	<tr class="row6">
		<td class="col0">modal</td><td class="col1">determines if the dialog will be modal. Also works only if jqModal plugin is present</td><td class="col2">false</td>
	</tr>
	<tr class="row7">
		<td class="col0">drag</td><td class="col1">Determines if the dialog is dragabale. Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UI</td><td class="col2"> true</td>
	</tr>
	<tr class="row8">
		<td class="col0">resize</td><td class="col1">determines if the dialog can be resized. Works only is jqDnR plugin is available or resizable widget is present from jQuery UI</td><td class="col2">true</td>
	</tr>
	<tr class="row9">
		<td class="col0">jqModal</td><td class="col1">If set to true uses jqModal plugin (if present) to creat the dialogs. If set to true and the plugin is not present <span class="search_hit">jqGrid</span> uses its internal function to create dialog</td><td class="col2">true</td>
	</tr>
	<tr class="row10">
		<td class="col0">topinfo</td><td class="col1">When set this in<span class="search_hit">form</span>ation is placed just after the modal header as additional row</td><td class="col2">empty string</td>
	</tr>
	<tr class="row11">
		<td class="col0">bottominfo</td><td class="col1">When set this in<span class="search_hit">form</span>ation is placed just after the buttons of the <span class="search_hit">form</span> as additional row</td><td class="col2">empty string</td>
	</tr>
	<tr class="row12">
		<td class="col0">closeicon</td><td class="col1"> Array. Determines the
 icon of the cancel button. The default values are 
[true,”left”,”ui-icon-close”]. For description of these see saveicon</td><td class="col2"> </td>
	</tr>
	<tr class="row13">
		<td class="col0">navkeys</td><td class="col1"> array. This option 
works only in edit mode and  add keyboard navigation, which allow us to 
navigate through the records while in <span class="search_hit">form</span> <span class="search_hit">editing</span>
 pressing certain keys. The default state is disabled. The first item 
enables/disables the navigation. The second item corresponds to reccord 
up and by default is the  the key code for Up key. The third item 
corresponds to reccord down and by default is the  key code for Down key</td><td class="col2">[false,38,40]</td>
	</tr>
	<tr class="row14">
		<td class="col0">closeOnEscape</td><td class="col1">When set to true the modal window can be closed with ESC key from the user.</td><td class="col2">false</td>
	</tr>
	<tr class="row15">
		<td class="col0">labelswidth</td><td class="col1">Since we construct 
the view with table element it is difficult to calculate, in this case, 
how much width is needed for the labels. Depending on the needs this 
value can be increased or decreased</td><td class="col2">30%</td>
	</tr>
	<tr class="row16">
		<td class="col0">viewPagerButtons</td><td class="col1"> This option enable or disable the appearing of the previous and next buttons (pager buttons) in the <span class="search_hit">form</span> </td><td class="col2">true</td>
	</tr>
	<tr class="row17">
		<td class="col0">zIndex</td><td class="col1">The starting z-index for the dialog. If you will see the dialog <span class="search_hit">form</span>
 under another elements or dialogs you should use the parameter with 
some value higher as default value 950. In the most cases it should be 
the value higher as 1000 - the default value of jQuery UI dialog.</td><td class="col2">950</td>
	</tr>
</tbody></table>

</div>

<h4><a name="events1" id="events1">Events</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Event</th><th class="col1">Description</th>
	</tr>
	<tr class="row1">
		<td class="col0">onClose</td><td class="col1">This event is called just before closing the <span class="search_hit">form</span>
 and when a close icon is clicked, a cancel button is clicked, ESC key 
is pressed or click on overlay (if jqModal is present). The event can 
return (optionally) true or false. If the return value is true the <span class="search_hit">form</span> is closed, if false the <span class="search_hit">form</span> does not close. If nothing is returned the <span class="search_hit">form</span> is closed</td>
	</tr>
	<tr class="row2">
		<td class="col0">beforeShow<span class="search_hit">Form</span></td><td class="col1">fires before showing the <span class="search_hit">form</span> with the new data; receives as Parameter the id of the constructed <span class="search_hit">form</span>. <br>
 beforeShow<span class="search_hit">Form</span> : function(<span class="search_hit">form</span>id) {…} </td>
	</tr>
</tbody></table>

<p>

When the <span class="search_hit">form</span> is constructed we set the following rules: <br>

</p>
<ul>
<li class="level1"><div class="li"> No input elements are created</div>
</li>
<li class="level1"><div class="li"> Hidden fields are included in the <span class="search_hit">form</span> with the display:none property of the table row</div>
</li>
<li class="level1"><div class="li"> The id of the  viewed element is constructed from “v_” + the name of the colModel array - propery - name </div>
</li>
<li class="level1"><div class="li"> For ease in manipulating the elements in an edit <span class="search_hit">form</span>, every table row in the <span class="search_hit">form</span> that holds the data for the edit has a id which is a combination of “trv_” + name (from colmodel). Example:</div>
</li>
</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'trv_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">"v_myfield"</span>&gt;&lt;<a href="http://december.com/html/4/element/span.html"><span class="kw2">span</span></a>&gt;</span>content of"myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/span.html"><span class="kw2">span</span></a>&gt;&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2"><span class="search_hit">form</span></span></a>&gt;</span></pre>
</div>

<h3><a name="delgridrow" id="delgridrow">delGridRow</a></h3>
<div class="level3">

<p>
With this method we can per<span class="search_hit">form</span> a delete operation at server side. <br>
 
This method uses colModel and editurl parameters from <span class="search_hit">jqGrid</span>
</p>

<p>

Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">delGridRow</span><span class="br0">(</span> row_id_s<span class="sy0">,</span> options <span class="br0">)</span>;</pre>
<p>

or when we use the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'delGridRow'</span><span class="sy0">,</span> row_id_s<span class="sy0">,</span> options <span class="br0">)</span>;</pre>
<p>
Where:
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> row_id_s: the id of the row(s) to delete; can be a single value or list of ids separated by comma</div>
</li>
<li class="level1"><div class="li"> options: an array of name: value pairs, including any of the following properties or events.</div>
</li>
</ul>

<p>

The method uses the following properties from language file: 

</p>
<pre class="code javascript">$.<span class="me1">jgrid</span> <span class="sy0">=</span> <span class="br0">{</span>
...
	<span class="me1">del</span> <span class="sy0">:</span> <span class="br0">{</span>
		caption<span class="sy0">:</span> <span class="st0">"Delete"</span><span class="sy0">,</span>
		msg<span class="sy0">:</span> <span class="st0">"Delete selected record(s)?"</span><span class="sy0">,</span>
		bSubmit<span class="sy0">:</span> <span class="st0">"Delete"</span><span class="sy0">,</span>
		bCancel<span class="sy0">:</span> <span class="st0">"Cancel"</span>
	<span class="br0">}</span><span class="sy0">,</span>
...</pre>
<p>
These options can be overwritten when passed as options to the method. 
When passed to the method we should use by example bSubmit : “Delete” 
and not $.jgrid.del.bSubmit : “Delete” <br>
 
</p>

</div>

<h4><a name="properties2" id="properties2">Properties</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Property</th><th class="col1">Description</th><th class="col2">Default</th>
	</tr>
	<tr class="row1">
		<td class="col0">top</td><td class="col1">the initial top position of 
modal dialog. The default value of 0 mean the top position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the top 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row2">
		<td class="col0">left</td><td class="col1">the initial left position 
of modal dialog. The default value of 0 mean the left position from the 
upper left corner of the grid. When jqModal option is true (see below) 
and jqModal plugin is present any value different from 0 mean the left 
position from upper left corner of the window. </td><td class="col2">0</td>
	</tr>
	<tr class="row3">
		<td class="col0">width</td><td class="col1">the width of confirmation dialog</td><td class="col2">300</td>
	</tr>
	<tr class="row4">
		<td class="col0">height</td><td class="col1">the entry height of confirmation dialog</td><td class="col2">auto</td>
	</tr>
	<tr class="row5">
		<td class="col0">dataheight</td><td class="col1">the parameter control the scrolling content - i.e between the modal header and modal footer.</td><td class="col2">auto</td>
	</tr>
	<tr class="row6">
		<td class="col0">modal</td><td class="col1">determines if the dialog will be modal. Also works only if jqModal plugin is present</td><td class="col2">false</td>
	</tr>
	<tr class="row7">
		<td class="col0">drag</td><td class="col1">Determines if the dialog is dragabale. Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UI</td><td class="col2"> true</td>
	</tr>
	<tr class="row8">
		<td class="col0">resize</td><td class="col1">determines if the dialog can be resized. Works only is jqDnR plugin is available or resizable widget is present from jQuery UI</td><td class="col2">true</td>
	</tr>
	<tr class="row9">
		<td class="col0">url</td><td class="col1"> url where to post data. If set, replaces the editurl</td><td class="col2">null</td>
	</tr>
	<tr class="row10">
		<td class="col0">mtype</td><td class="col1">Defines the type of request to make (“POST” or “GET”) when data is sent to the server</td><td class="col2">POST</td>
	</tr>
	<tr class="row11">
		<td class="col0">delData</td><td class="col1">an array used to add content to the data posted to the server</td><td class="col2">empty</td>
	</tr>
	<tr class="row12">
		<td class="col0">jqModal</td><td class="col1">If set to true uses jqModal plugin (if present) to creat the dialogs. If set to true and the plugin is not present <span class="search_hit">jqGrid</span> uses its internal function to create dialog</td><td class="col2">true</td>
	</tr>
	<tr class="row13">
		<td class="col0">delicon</td><td class="col1"> array. Determines the 
icon of the submit button. The default value is 
[true,”left”,”ui-icon-delete”]. The first item enables/disables the 
icon. The second item tells where to put the icon to left or to right of
 the text. The third item corresponds to valid ui icon from theme roller
 </td><td class="col2"> </td>
	</tr>
	<tr class="row14">
		<td class="col0">cancelicon</td><td class="col1"> Array. Determines 
the icon of the cancel button. The default values are 
[true,”left”,”ui-icon-cancel”]. For description of these see delicon</td><td class="col2"> </td>
	</tr>
	<tr class="row15">
		<td class="col0">reloadAfterSubmit</td><td class="col1">reload grid data after posting</td><td class="col2">true</td>
	</tr>
	<tr class="row16">
		<td class="col0">closeOnEscape</td><td class="col1">When set to true the modal window can be closed with ESC key from the user.</td><td class="col2">false</td>
	</tr>
	<tr class="row17">
		<td class="col0">ajaxDelOptions</td><td class="col1">This option allow to set global ajax settings for the <span class="search_hit">form</span>
 editiing when we delete the data to the server. Note that with this 
option is possible to overwrite all current ajax setting in the save 
request including the complete event.</td><td class="col2">empty object</td>
	</tr>
	<tr class="row18">
		<td class="col0">zIndex</td><td class="col1">The starting z-index for the dialog. If you will see the dialog <span class="search_hit">form</span>
 under another elements or dialogs you should use the parameter with 
some value higher as default value 950. In the most cases it should be 
the value higher as 1000 - the default value of jQuery UI dialog.</td><td class="col2">950</td>
	</tr>
</tbody></table>

</div>

<h4><a name="events2" id="events2">Events</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Event</th><th class="col1">Description</th>
	</tr>
	<tr class="row1">
		<td class="col0">afterComplete</td><td class="col1"> This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid. <br>
 afterComplete : function (response, postdata, <span class="search_hit">form</span>id) {…} <br>
 where <br>
 <em>response</em> is the data returned from the server (if any) <br>
 <em>postdata</em> an array, is the data sent to the server <br>
 <em><span class="search_hit">form</span>id</em> is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id. </td>
	</tr>
	<tr class="row2">
		<td class="col0">afterShow<span class="search_hit">Form</span></td><td class="col1">fires after showing the <span class="search_hit">form</span>; receives as Parameter the id of the constructed <span class="search_hit">form</span>. <br>
 afterShow<span class="search_hit">Form</span> : finction (<span class="search_hit">form</span>id) {…} </td>
	</tr>
	<tr class="row3">
		<td class="col0">afterSubmit</td><td class="col1"> fires after 
response has been received from server. Typically used to display status
 from server (e.g., the data is successfully deleted or the delete 
cancelled for server-side reasons). Receives as parameters the data 
returned from the request and an array of the posted values of type 
id=value1,value2. <br>
 When used this event should return array with the following items [success, message] <br>
 where <br>
 <em>success</em> is a boolean value if true the process continues, if false a error <em>message</em> appear and all other processing is stoped. <br>
 afterSubmit : function(response, postdata) <br>
 { <br>
 … <br>
 return [succes,message] <br>
 } </td>
	</tr>
	<tr class="row4">
		<td class="col0">beforeShow<span class="search_hit">Form</span></td><td class="col1">fires before showing the <span class="search_hit">form</span> with the new data; receives as Parameter the id of the constructed <span class="search_hit">form</span>. <br>
 beforeShow<span class="search_hit">Form</span> : function(<span class="search_hit">form</span>id) {…} </td>
	</tr>
	<tr class="row5">
		<td class="col0">beforeSubmit</td><td class="col1">fires before the data is submitted to the server. Recieves as parameter the posted data array and the <span class="search_hit">form</span>id. <br>
 beforeSubmit : function(postdata, <span class="search_hit">form</span>id) { <br>
 … <br>
 return[success,message]; <br>
 } <br>
 When defined this event should return array with the following values <br>
 <em>success</em> boolean indicating if the proccess should continue (true) or a error (false) <em>message</em> should appear to the user</td>
	</tr>
	<tr class="row6">
		<td class="col0">onclickSubmit</td><td class="col1">fires after the 
submit button is clicked and the postdata is constructed. Parameters 
passed to this event is a options array of the method. The event should 
return array of type {} which extends the postdata array. <br>
 onclickSubmit : function(params) { <br>
 … <br>
 return {add_data} <br>
  }</td>
	</tr>
	<tr class="row7">
		<td class="col0">onClose</td><td class="col1">This event is called just before closing the <span class="search_hit">form</span>
 and when a close icon is clicked, a cancel button is clicked, ESC key 
is pressed or click on overlay (if jqModal is present). The event can 
return (optionally) true or false. If the return value is true the <span class="search_hit">form</span> is closed, if false the <span class="search_hit">form</span> does not close. If nothing is returned the <span class="search_hit">form</span> is closed</td>
	</tr>
	<tr class="row8">
		<td class="col0">errorText<span class="search_hit">Form</span>at</td><td class="col1">The event (can) fire when error occurs from the ajax call and can be used for better <span class="search_hit">form</span>atting
 of the error messages. To this event is passed response from the 
server. The event should return single message (not array), which then 
is displayed to the user.</td>
	</tr>
	<tr class="row9">
		<td class="col0">serializeDelData</td><td class="col1">If set this event can serialize the data passed to the ajax request when we save a <span class="search_hit">form</span>
 data. The function should return the serialized data. This event can be
 used when a custom data should be passed to the server - e.g - JSON 
string, <acronym title="Extensible Markup Language">XML</acronym> string and etc. To this event is passed the data which will be posted to the server.</td>
	</tr>
</tbody></table>

</div>

<h4><a name="what_is_posted_to_the_server2" id="what_is_posted_to_the_server2">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in delete mode the data that is posted to the server is object {} that contain:
</p>
<ul>
<li class="level1"><div class="li"> the pair id:rowids where the rowids can be a single value a value string separated with comma in case of multiple selection</div>
</li>
<li class="level1"><div class="li"> additionally we add a pair oper:del to indicate the delete mode</div>
</li>
<li class="level1"><div class="li"> if the delData object is not empty we extend this data with the posted data</div>
</li>
<li class="level1"><div class="li"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object</div>
</li>
</ul>

</div>

<h3><a name="gridtoform" id="gridtoform">GridTo<span class="search_hit">Form</span></a></h3>
<div class="level3">

<p>
This method does not have interaction with the user <br>
 
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">GridTo<span class="search_hit">Form</span></span><span class="br0">(</span> rowid<span class="sy0">,</span> <span class="search_hit">form</span>id <span class="br0">)</span>;</pre>
<p>

or

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'GridTo<span class="search_hit">Form</span>'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> <span class="search_hit">form</span>id <span class="br0">)</span>;</pre>
<p>
Where
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> rowid: the id of the row </div>
</li>
<li class="level1"><div class="li"> <span class="search_hit">form</span>id: is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id.</div>
</li>
</ul>

<p>

When called this method read the grid data with the id = rowid and places the data from the grid to the given <span class="search_hit">form</span>. if the <span class="search_hit">form</span> element has a name which is equal of those from colModel the value is placed to the <span class="search_hit">form</span>.
</p>

</div>

<h3><a name="formtogrid" id="formtogrid"><span class="search_hit">Form</span>ToGrid</a></h3>
<div class="level3">

<p>
This method does not have interaction with the user <br>
 
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">Form</span>ToGrid</span><span class="br0">(</span> rowid<span class="sy0">,</span> <span class="search_hit">form</span>id<span class="sy0">,</span> mode<span class="sy0">,</span> position <span class="br0">)</span>;</pre>
<p>

or

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1"><span class="search_hit">jqGrid</span></span><span class="br0">(</span><span class="st0">'<span class="search_hit">Form</span>ToGrid'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> <span class="search_hit">form</span>id<span class="sy0">,</span> mode<span class="sy0">,</span> position <span class="br0">)</span>;</pre>
<p>
Where
</p>
<ul>
<li class="level1"><div class="li"> grid_id: the id of the parent grid</div>
</li>
<li class="level1"><div class="li"> rowid: the id of the row </div>
</li>
<li class="level1"><div class="li"> <span class="search_hit">form</span>id: is the jQuery object of <span class="search_hit">form</span> id, you can use <span class="search_hit">form</span>id[0].id to get <span class="search_hit">form</span> id.</div>
</li>
<li class="level1"><div class="li"> mode: determines the mode. Can be 
set or add. Default is set. If the mode is defined as set, the record in
 the grid is updated with the new values from the <span class="search_hit">form</span>. When the mode is defined as add the record is added to the grid.</div>
</li>
<li class="level1"><div class="li"> position: this parameter have sense 
only when the mode parameter is set to add. Can be first - the record is
 inserted as first row in the grid or last - the record is inserted as 
last row in the grid.</div>
</li>
</ul>

<p>

When called this method does the opposite of the GridTo<span class="search_hit">Form</span> method - i.e. places the <span class="search_hit">form</span> data in the grid if the names matches.
</p>

</div>
<div class="comment_wrapper">
  <h2><a name="discussion__section" id="discussion__section">
    Discussion
  </a></h2>
  <div class="level2 hfeed">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_54930c232f8796741181b5d8141dbc70" id="comment_54930c232f8796741181b5d8141dbc70"></a>
        <span class="vcard author"><span class="fn">quest</span></span>, <abbr class="published" title="2009-08-12T07:14:05Z">2009/08/12 07:14</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Thanks for this comprehensive documentation. I am a newbie to jQuery and just started to use <span class="search_hit">jqGrid</span> for some project, its really wonderful.
</p>

<p>
I have a question, basically I want to pull out the select tag options 
upon an ajax request, I can do this on page load, but I am unable to do 
this in the <span class="search_hit">form</span> <span class="search_hit">editing</span> mode.. For example, if i have 2 columns in the grid with both set to editable and as drop downs.
Now when on the edit <span class="search_hit">form</span>, if I choose 
some value in the first select drop down, the second should filter out 
subset values of the first and not display all generic items.. How do I 
achieve this?
</p>

<p>
I tried a generic event handler for select tag like 
$('select').bind('change',function(){}); this works on all select drop 
downs that are on the page but not on the ones that appear on the <span class="search_hit">editing</span> <span class="search_hit">form</span>. please help. Thanks

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_df6d9aee010b9f1bbae8cbd2958cf6de" id="comment_df6d9aee010b9f1bbae8cbd2958cf6de"></a>
        <span class="vcard author"><span class="fn">Tony Tomov</span></span>, <abbr class="published" title="2009-08-13T16:21:16Z">2009/08/13 16:21</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
quest,
You have the answer in the forum. Also for others - you will need to dataEvents array in editoptions
Regards
Tony 

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_efba7f542b12a87b1b81a8136de47acc" id="comment_efba7f542b12a87b1b81a8136de47acc"></a>
        <span class="vcard author"><span class="fn">Mickael Perraud</span></span>, <abbr class="published" title="2009-08-13T10:28:13Z">2009/08/13 10:28</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I want to use my own <span class="search_hit">form</span>. I like your box but I want to load my own <span class="search_hit">form</span> (Zend_<span class="search_hit">Form</span> generation). Is it possible?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_9b3f7b5743e95af8718703fd2effd609" id="comment_9b3f7b5743e95af8718703fd2effd609"></a>
        <span class="vcard author"><span class="fn">Tony Tomov</span></span>, <abbr class="published" title="2009-08-13T16:24:13Z">2009/08/13 16:24</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Mickael,
I suggest you to include the the jQuery UI with a dialog support. The <span class="search_hit">jqGrid</span> mimics exactly the behavior of the UI Dialog.
Also you can use <span class="search_hit">jqGrid</span> dialog, but currently it is a little difficult to configure this to work from the first time.
Regards

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_76d060f6577e7b2388cfbef823d50e98" id="comment_76d060f6577e7b2388cfbef823d50e98"></a>
        <span class="vcard author"><span class="fn">Mickael Perraud</span></span>, <abbr class="published" title="2009-08-13T16:46:47Z">2009/08/13 16:46</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Thanks. It's exactly this what I do for the moment <img src="jqgrid_form_editing-Dateien/icon_wink.gif" class="middle" alt=";-)"> Do you think it's possible to use the event initialize<span class="search_hit">Form</span> or beforeShow<span class="search_hit">Form</span> to load my <span class="search_hit">form</span> and in this case shunt the loading of the standard <span class="search_hit">form</span>?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_af5b21e4f27b7eaba42eac766050e44e" id="comment_af5b21e4f27b7eaba42eac766050e44e"></a>
        <span class="vcard author"><span class="fn">Tony Tomov</span></span>, <abbr class="published" title="2009-08-13T17:18:54Z">2009/08/13 17:18</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
If you use your own <span class="search_hit">form</span> you can do what you want, since it is open for you - also befor to load the data in your <span class="search_hit">form</span> you can do something and etc.
Regards

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    </div>
    </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_38e9df4cc67686b75a6cb00bf597518f" id="comment_38e9df4cc67686b75a6cb00bf597518f"></a>
        <span class="vcard author"><span class="fn">loume</span></span>, <abbr class="published" title="2009-08-25T05:37:09Z">2009/08/25 05:37</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I need use <span class="search_hit">form</span> edit.
How can I get response when I submit <span class="search_hit">form</span> edit from server site.
There had afterComplate and afterSubmit event, but I don't know how to use this event.
Is there had some example for me?
And is it possible when edit field type is select and set default select option?
Thanks

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_0dd1070ef07266f384a2f7b1c63a92f6" id="comment_0dd1070ef07266f384a2f7b1c63a92f6"></a>
        <span class="vcard author"><span class="fn">Oleg</span></span>, <abbr class="published" title="2010-01-19T14:56:25Z">2010/01/19 14:56</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Example for <span class="search_hit">form</span> edit:
</p>
<pre class="code">//define handler for 'editSubmit' event
var fn_editSubmit=function(response,postdata){
 var json=response.responseText; //in my case response text <span class="search_hit">form</span> server is "{sc:true,msg:''}"
 var result=eval("("+json+")"); //create js object from server reponse
 return [result.sc,result.msg,null]; 
}

//define edit options for navgrid
var editOptions={
 top: 50, left: "100", width: 500  
 ,closeOnEscape: true, afterSubmit: fn_editSubmit
}</pre>
<pre class="code">//create navgrid with defined options
//table is $("#table").<span class="search_hit">jqGrid</span>({...,editurl:'server.php'})
table.<span class="search_hit">jqGrid</span>('navGrid',"#navgrid",
     {add:false, view:false, del:false,edit:true}
     ,editOptions /* Our edit options with handler */
     ,{} /*add options*/
     ,{} /*delete options*/
     ,{multipleSearch:false}
     ,{closeOnEscape:true} 
);

</pre>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_0b1d3f59a2f2fb9fcf6c8ede95adea67" id="comment_0b1d3f59a2f2fb9fcf6c8ede95adea67"></a>
        <span class="vcard author"><span class="fn">Jack Zeng</span></span>, <abbr class="published" title="2010-02-07T06:21:16Z">2010/02/07 06:21</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Is it possible to do client-side <span class="search_hit">editing</span> w/o posting to the server? I'd like to allow users to manipulate data at will and then post all changes to the server at once.
</p>

<p>
Thanks

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_dc63f958dcb06a566c1333df0d9d291e" id="comment_dc63f958dcb06a566c1333df0d9d291e"></a>
        <span class="vcard author"><span class="fn">Khoi Pham</span></span>, <abbr class="published" title="2011-01-22T06:08:43Z">2011/01/22 06:08</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
this is a little bit too late an answer but better late than never. If 
you set editurl of your grid as “clientArray”, it wont connect to any 
server and leave your data alone :D. I only found this after looking at 
the code, a search for it in the wiki return nothing. Seriously, THIS IS
 AN IMPORTANT FEATURE!

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_f44a5c76933e9dc2b94b4eec33838a6d" id="comment_f44a5c76933e9dc2b94b4eec33838a6d"></a>
        <span class="vcard author"><span class="fn">Jeff Koch</span></span>, <abbr class="published" title="2010-03-09T06:39:22Z">2010/03/09 06:39</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I'm trying to use the afterSubmit event but I'm not sure how the editurl.php script is supposed to <span class="search_hit">form</span>at the data back to the event. Does anyone have an example? Is it supposed to be an <acronym title="Extensible Markup Language">XML</acronym>
 response, JSON response? Can someone just send a snippet of php code 
showing the result of editurl.php sending the response back to 
afterSubmit. Thanks

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_33e72f3c7cf6b98fb066ee5b5a565b03" id="comment_33e72f3c7cf6b98fb066ee5b5a565b03"></a>
        <span class="vcard author"><span class="fn">Jeff Koch</span></span>, <abbr class="published" title="2010-03-09T07:23:15Z">2010/03/09 07:23</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I figured it out. For those people struggling here's the php snippet for a deletion:

</p>
<pre class="code">              $deleted_rows = mysql_affected_rows();
              if ($deleted_rows &gt;= 1) {
                      $status = "success";
                      $message = "deletion succeeded";
              } else {
                      $status = "fail";
                      $message = "deletion failed";
              }
              $s = $status.';'.$message.';'.'';
              print $s;</pre>

<p>

and here's the javascript:
</p>

<p>
<code>{closeOnEscape:true,
        afterSubmit:function( data_from_server, array_data) {
                var result =data_from_server.responseText.split(”;”);
                if (result[0] == “fail”) {
                        return [false,result[1]]
                } else {
                        return [true,</code>,result[2]];
</p>
<pre class="code">              }
      }''</pre>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_e2fae30218f6abc970ec0ef71a6d087b" id="comment_e2fae30218f6abc970ec0ef71a6d087b"></a>
        <span class="vcard author"><span class="fn">Greg Johnson</span></span>, <abbr class="published" title="2010-04-14T16:30:57Z">2010/04/14 16:30</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I am using the navigation buttons to do add/edit/delete.  But am  not 
sure how to set the width of the dialog boxes that popup.  I also did a 
double click edit, and it sets the width, and if I open add after double
 clicking it retains the width, but if I click the nav add button I get a
 box too small in width.  Ideas?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_809671dc40a4658327304591324742c5" id="comment_809671dc40a4658327304591324742c5"></a>
        <span class="vcard author"><span class="fn">Peter</span></span>, <abbr class="published" title="2010-06-08T16:19:28Z">2010/06/08 16:19</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Re: jQuery(”#grid_id”).<span class="search_hit">jqGrid</span>('GridTo<span class="search_hit">Form</span>', rowid, <span class="search_hit">form</span>id );
</p>

<p>
For completeness' sake: instead of the <span class="search_hit">form</span> ID you have to put in the <span class="search_hit">form</span> *selector*. Eg, '#<span class="search_hit">form</span>_id' instead of '<span class="search_hit">form</span>_id'
</p>

<p>
Hope this helps,
-Peter
</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_c32b8258aa88f6fb3c3e76ce90057ef7" id="comment_c32b8258aa88f6fb3c3e76ce90057ef7"></a>
        <span class="vcard author"><span class="fn">Ye Chenhui</span></span>, <abbr class="published" title="2010-08-06T10:16:39Z">2010/08/06 10:16</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I wonder if I can load ajax select options that depend on other column value ?
</p>

<p>
For example:
when the value of column A is “Asia”, I hope that the select options of column B will be “China”,”Japan”,”Korea”;
when the value of column A is “Europe”, I hope that the select options of column B will be “U.K., France, Italy”.
</p>

<p>
I read the document of <span class="search_hit">form</span> <span class="search_hit">editing</span>, and I found that I cannot load ajax options from server more than once.

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_ae5cf30ed946cc1f8b3c00f12151e42a" id="comment_ae5cf30ed946cc1f8b3c00f12151e42a"></a>
        <span class="vcard author"><span class="fn">Milan Rukavina</span></span>, <abbr class="published" title="2010-08-27T15:03:21Z">2010/08/27 15:03</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Hi All,
</p>

<p>
When I do <em><span class="search_hit">Form</span>ToGrid</em> how can I trigger submit to that server - the same as if it was done by built in <span class="search_hit">form</span>?
</p>

<p>
Thanks!

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_2929a0576598f55dfdd0cf7af7604686" id="comment_2929a0576598f55dfdd0cf7af7604686"></a>
        <span class="vcard author"><span class="fn">shenshi</span></span>, <abbr class="published" title="2010-12-14T11:28:43Z">2010/12/14 11:28</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
i wonder if i can diy my add dialog, for example, i add fileupload button in add dialog?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_a3ec68f2b2973e73c8b382e33cfeca5d" id="comment_a3ec68f2b2973e73c8b382e33cfeca5d"></a>
        <span class="vcard author"><span class="fn">Justin Rimel</span></span>, <abbr class="published" title="2010-12-28T19:30:02Z">2010/12/28 19:30</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Hello,
Very new to the grid so please correct me if I haven't found the right 
solution.  These grids are great and appreciate all of the hard work 
done so far.  
</p>

<p>
In IE7, I have recreate<span class="search_hit">Form</span>:true but it's not firing off to my dataurl after an initial loading of the <span class="search_hit">form</span>(Chrome,
 FF all are working).  I've tried adding something to the end of my 
dataurl like + Math.random() etc but still having no luck.  Is there a 
better way to use recreate<span class="search_hit">Form</span>:true and not cache the dataurl call?  
</p>

<p>
What I've messed w/ so far was adding:
cache: false in my editoptions for the colModel
</p>

<p>
then in grid.common.js in (createEl() case:'select' inside the jquery ajax call:
cache: options.cache
</p>

<p>
Again I'm new so any advice would be great.
</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_dcb2de69a0ca860216e3d1f01b7c4548" id="comment_dcb2de69a0ca860216e3d1f01b7c4548"></a>
        <span class="vcard author"><span class="fn">sunil</span></span>, <abbr class="published" title="2011-01-28T18:02:31Z">2011/01/28 18:02</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Hi
</p>

<p>
I am trying to get afterSubmit event to fire without much success…
</p>

<p>
My grid definition is as follows

</p>
<pre class="code">   jQuery("#optbl").<span class="search_hit">jqGrid</span>({
                         url:'getusersxml.php',
                         datatype: "xml",
                         colNames:cols,
                         colModel:cm,
                         rowNum:25,
                         rowList:[10,20,30,40,50],
                         pager: jQuery('#opnav'),</pre>
<pre class="code">                         height:'300px',
                         sortname: 'id',
                         recordpos: 'left',
                         viewrecords: true,
                         sortorder: "desc",
                         editurl:"edituser.php",
                         afterSubmit:function(response,postdata)
                         {
                                    alert("Event Captured !!!!");
                         }
                         .... 
          });</pre>

<p>
Shouldnt I atleast get the alert box after submitting the <span class="search_hit">form</span> ?? What am I doing wrong ?
Please help.

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_b062bc3fac24d9b5d2f409a7960b6e62" id="comment_b062bc3fac24d9b5d2f409a7960b6e62"></a>
        <span class="vcard author"><span class="fn">Richard Munroe</span></span>, <abbr class="published" title="2011-02-03T13:20:30Z">2011/02/03 13:20</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
The onclickSubmit event documentation is wrong for this event during 
edit processing.  There are two parameters, the first is a equivalent to
 “this”, the second is the postdata submitted to the event.

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_6fff27991ea5a83956d4e878a3dcb63b" id="comment_6fff27991ea5a83956d4e878a3dcb63b"></a>
        <span class="vcard author"><span class="fn">wayne</span></span>, <abbr class="published" title="2011-03-03T08:49:35Z">2011/03/03 08:49</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
I am a new jqgird user from china,now I meet a problem.let me just take 
the “my first grid” as an example.in the grid,we have 7 column
(Inv No ,Date,Amount,Tax,Total,Notes)，but in the Add Record dialogue,i 
only need 2 cloumns(Inv No,Date).what should i do?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_7bd9eda02d20b2510026aa2122e62235" id="comment_7bd9eda02d20b2510026aa2122e62235"></a>
        <span class="vcard author"><span class="fn">Gabriel Schillaci</span></span>, <abbr class="published" title="2011-07-18T19:31:23Z">2011/07/18 19:31</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Question: if I user the GridTo<span class="search_hit">Form</span> option, what happens when I submit the edit <span class="search_hit">form</span>?
 Is the server called automatically to trigger the record update, or I 
must run the server update for myself? If the answer is the second one, 
how do I proceed?
</p>

<p>
I am asking it bc so far I couldn't make my custom edit <span class="search_hit">form</span> to save edited data in my DB…
</p>

<p>
thanks
Gabriel

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    <div class="comment_replies" style="margin-left: 20px;">
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_bb9a84aed889e190344fe840268e19ab" id="comment_bb9a84aed889e190344fe840268e19ab"></a>
        <span class="vcard author"><span class="fn">Gabriel Schillaci</span></span>, <abbr class="published" title="2011-07-18T19:42:52Z">2011/07/18 19:42</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
More on this: I am not using a real table for <span class="search_hit">editing</span>, but something like the following:
</p>

<p>
&lt;<span class="search_hit">form</span> method=“post” name=“my<span class="search_hit">form</span>” id=“my<span class="search_hit">form</span>” action=”” style=“display:none;”&gt; 
</p>
<pre class="code">  &lt;fieldset&gt; 
      &lt;legend&gt;&lt;span&gt;Edit a user&lt;/span&gt;&lt;/legend&gt; 
      &lt;div id="div_id" class="<span class="search_hit">form</span>field"&gt;
          &lt;label for="id"&gt;ID&lt;/label&gt;
          &lt;input type="text" name="id" id="id" true value="" /&gt;
          &lt;span id="id_status" class="obs"&gt;&lt;/span&gt;
      &lt;/div&gt;
      &lt;div id="div_id" class="<span class="search_hit">form</span>field"&gt;
          &lt;label for="id"&gt;E-mail&lt;/label&gt;
          &lt;input type="text" name="email" id="email" false value="" /&gt;
          &lt;span id="id_status" class="obs"&gt;&lt;/span&gt;
      &lt;/div&gt;
      &lt;div class="clear"&gt;&lt;/div&gt;
      &lt;div class="<span class="search_hit">form</span>submit"&gt;
          &lt;input type="hidden" name="oper" id="oper" value="edit"&gt;
          &lt;input type="submit" class="bttn-submit-universal" name="savedata" id="savedata" value="Update"&gt; 
      &lt;/div&gt;
  &lt;/fieldset&gt; </pre>

<p>
&lt;/<span class="search_hit">form</span>&gt;                             
</p>

<p>
As you see, I do have input fields matching the expected name, but <acronym title="Cascading Style Sheets">CSS</acronym> ruled divs instead or a table structure.
Also, I am showing/hiding the edit <span class="search_hit">form</span> when needed.
</p>

<p>
So as it is now, the server is never called after I submit the <span class="search_hit">form</span> button.
thanks again
</p>

<p>
Gabriel

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
    </div>
    <div class="hentry">
      <div class="comment_head">
        <a name="comment_c3a7f0050dcb3f7e18e500298614dc3d" id="comment_c3a7f0050dcb3f7e18e500298614dc3d"></a>
        <span class="vcard author"><span class="fn">George</span></span>, <abbr class="published" title="2011-09-01T13:21:51Z">2011/09/01 13:21</abbr>
      </div>
      <div class="comment_body entry-content" style="margin-left: 20px;">

<p>
Question:
I made a big table, it works, but there are disadvantages, I vyzvvayu line <span class="search_hit">editing</span> two methods

</p>
<pre class="code">                                      ondblClickRow: function(id) 	{
				if (id &amp;&amp; id != lastSel) {var gr = jQuery("#le_table").<span class="search_hit">jqGrid</span>('getGridParam','selrow'); 
				var ttop = 0;
				var html = document.documentElement;
				ttop = html.scrollTop;
				if( gr != null ) 
				jQuery("#le_table").<span class="search_hit">jqGrid</span>('editGridRow',gr,{top:ttop,reloadAfterSubmit:true,closeAfterEdit:true}); 
				else alert("select the string!");
				lastSel = id;
										}
											}</pre>

<p>

and
</p>

<p>
jQuery(”#le_table”).<span class="search_hit">jqGrid</span>('navGrid','#le_tablePager', {edit:true,add:false,del:true,refresh:true}, <em>options 
			{jqModal:true,modal:true,top:?????,left:500,reloadAfterSubmit:true,closeAfterEdit:true}, </em> edit options 
</p>
<pre class="code">		{height:500,width:980,reloadAfterSubmit:true,closeAfterEdit:true}, // add options 
		{reloadAfterSubmit:true,jqModal:true}, // del options 
		{closeOnEscape:true, multipleSearch:true, closeAfterSearch:true} // search options </pre>

<p>

When I select a row, the window is positioned at the top of the screen, 
although the line for a few screens below. This is inconvenient.
How can I make that window tied to the left of my browser, the current 
position of the screen?

</p>

      </div>
      <div class="comment_buttons">
      </div>
    </div>
  </div>
</div>

<br>
																					<!-- end content -->
							<div class="visualClear"></div>
						</div>
					</div>
				</div>
			</div>

			<div id="column-one">
				<div class="portlet" id="p-logo">
					<a style="background-image: url(&quot;/jqgridwiki/lib/tpl/monobook/user/logo.png&quot;);" href="http://www.trirand.com/jqgridwiki/" accesskey="h" title="[ALT+H]">
					</a>
				</div>

				<div id="p-cactions" class="portlet"> <h5>Views</h5>   <ul>
<li id="ca-nstab-main" class="selected"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing" accesskey="v" title="[ALT+V]">Special Pages</a></li>
<li id="ca-edit"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;do=edit&amp;rev=0" accesskey="e" title="[ALT+E]">Show pagesource</a></li>
<li id="ca-history"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;do=revisions" accesskey="o" title="[ALT+O]">Old revisions</a></li>
   </ul>
</div><div id="p-x-navigation" class="portlet"> <h5>Navigation</h5><div class="pBody">
<ul>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=start" class="wikilink1" title="start">Introduction</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:features" class="wikilink1" title="wiki:features">Features</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:jqgriddocs" class="wikilink1" title="wiki:jqgriddocs">Documentation</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:jqgridexamples" class="wikilink1" title="wiki:jqgridexamples">Examples</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:faq" class="wikilink1" title="wiki:faq">FAQ</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:dokuwiki" class="wikilink1" title="wiki:dokuwiki">Help</a></div>
</li>
<li class="level1"><div class="li"> <a href="http://www.trirand.com/jqgridwiki/doku.php?id=links" class="wikilink1" title="links">links</a></div>
</li>
</ul>
</div></div>
				<div id="p-personal" class="portlet"> <h5>Personal Tools</h5>  <div class="pBody">   <ul>
<li id="pt-login"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;do=login">Login</a></li>
   </ul>
  </div></div>
<div id="p-search" class="portlet">
    <h5>
        <label>Search</label></h5>
    <div class="pBody">
        <form action="/jqgridwiki/doku.php" accept-charset="utf-8" id="dw__search" name="search">
            <input name="do" value="search" type="hidden">
            <input id="qsearch__in" name="id" accesskey="f" type="text">
            <input class="searchButton" id="searchGoButton" value="Go" onclick="document.location.href='/jqgridwiki/doku.php?id=' + document.search.qsearch__in.value;" type="button">&nbsp;
            <input name="fulltext" class="searchButton" value="Search" type="submit">
        </form>
    </div>
</div>

<iframe src="jqgrid_form_editing-Dateien/adds1.htm" style="margin-left: 10px; width: 110px; height: 190px;" marginwidth="0" marginheight="0" vspace="0" hspace="0" frameborder="0" scrolling="no"></iframe>;
<div id="p-tb" class="portlet"> <h5>Toolbox</h5>  <div class="pBody">   <ul>
<li id="tb-whatlinkshere"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;do=backlink" rel="nofollow">What links here</a></li>
<li id="tb-upload"><a href="http://www.trirand.com/jqgridwiki/lib/exe/mediamanager.php?ns=wiki" rel="nofollow">Upload file</a></li>
<li id="tb-special"><a href="http://www.trirand.com/jqgridwiki/doku.php?idx=wiki" rel="nofollow">Special Pages</a></li>
<li id="tb-print"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;rev=1301331295&amp;mbdo=print" rel="nofollow">Printable Version</a></li>
<li id="tb-permanent"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;rev=1301331295" rel="nofollow">Permanent link</a></li>
<li id="tb-cite"><a href="http://www.trirand.com/jqgridwiki/doku.php?id=wiki:form_editing&amp;rev=1301331295&amp;mbdo=cite" rel="nofollow">Cite this Article</a></li>
   </ul>
  </div></div>			</div>
			<!-- end of the left (by default at least) column -->
			<div class="visualClear"></div>
<iframe src="jqgrid_form_editing-Dateien/adds2.htm" style="margin-left: 192px; width: 750px;" marginwidth="0" marginheight="0" vspace="0" hspace="0" frameborder="0" scrolling="no"></iframe>;

<div id="footer">
    <div id="f-poweredbyico">
        <a target="_blank" href="http://wiki.splitbrain.org/wiki:dokuwiki" title="Driven by DokuWiki">
            <img src="jqgrid_form_editing-Dateien/button-dw.png" alt="Driven by DokuWiki" border="0" height="31" width="88"></a></div>
    <div id="f-copyrightico">
        <a target="_blank" href="http://tatewake.com/wiki/projects:monobook_for_dokuwiki" title="Monobook for DokuWiki">
            <img src="jqgrid_form_editing-Dateien/button-mb.png" alt="Monobook for DokuWiki" border="0" height="31" width="88"></a></div>
    <a target="_blank" href="http://www.trirand.com/jqgridwiki/feed.php" title="Recent changes RSS feed">
        <img src="jqgrid_form_editing-Dateien/button-rss.png" alt="Recent changes RSS feed" border="0" height="15" width="80"></a> <a target="_blank" href="http://www.php.net/" title="Powered by PHP">
                <img src="jqgrid_form_editing-Dateien/button-php.gif" alt="Powered by PHP" border="0" height="15" width="80"></a> <a target="_blank" href="http://validator.w3.org/check/referer" title="Valid XHTML 1.0">
                        <img src="jqgrid_form_editing-Dateien/button-xhtml.png" alt="Valid XHTML 1.0" border="0" height="15" width="80"></a> <a target="_blank" href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid CSS">
                                <img src="jqgrid_form_editing-Dateien/button-css.png" alt="Valid CSS" border="0" height="15" width="80"></a>
    <ul id="f-list">
        <li id="lastmod">wiki/form_editing.txt · Last modified: 2011/03/28 19:54 by dan.baughman_gmail.com<br>
        </li>
        <li id="copyright"></li>
        <li id="usermod"><br>
        </li>
    </ul>
</div>
		</div>
<a href="http://www.trirand.com/jqgridwiki/doku.php?do=recent" accesskey="r" style="visibility: hidden;" rel="nofollow">&nbsp;</a>
		<img src="jqgrid_form_editing-Dateien/indexer.gif" alt="" height="1" width="1">	
<script type="text/javascript"> var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E")); </script><script src="jqgrid_form_editing-Dateien/ga.js" type="text/javascript"></script> <script type="text/javascript"> try { var pageTracker = _gat._getTracker("UA-5463047-4"); pageTracker._trackPageview(); } catch(err) {}
</script>
</body></html>