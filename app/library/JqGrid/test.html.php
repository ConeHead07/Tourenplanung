<h4><a name="how_is_the_form_constructed" id="how_is_the_form_constructed">How is the form constructed</a></h4>
<div class="level4">

<p>When the form is constructed we set the following rules:</p>
<ul>
<li class="level1"> Hidden fields are included in the form with the display:none property of the table row
<li class="level1"> The id of the editable element is constructed from the name of the colModel array - propery - name
<li class="level1"> The name of the editable element is constructed from the name of the colModel array - propery - name
<li class="level1"> For ease in manipulating the elements in an edit form, every table row in the form that holds the data for the edit has a id which is a combination of “tr_” + name (from colmodel). Example:
</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'tr_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span>edited element named, in colModel, as "myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 

<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a>&gt;</span></pre>
<p>

This allow us to easily show or hide some table rows depending on conditions using beforeShowForm event
</p>



<h4><a name="what_is_posted_to_the_server" id="what_is_posted_to_the_server">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in editing mode the data that is posted to the server is object {} that contain:
</p>

<ul>
<li class="level1"> the name:value pair  where the name is the name of the input element represented in the form (this is for all input elements)

<li class="level1"> additionally we add a pair id:rowid where the rowid is the id of the edited row

<li class="level1"> additionally we add a pair oper:edit to indicate the edit mode

<li class="level1"> if the editData object is not empty we extend this data with the posted data


<li class="level1"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object

</ul>



<h3><a name="adding_row" id="adding_row">Adding Row</a></h3>


<p>
The editGridRow method is also used to add data to the server, by passing “new” as the rowid. <br>
 
This method uses <a href="/jqgridwiki/doku.php?id=wiki:common_rules" class="wikilink1" title="wiki:common_rules"> Common Editing Properties </a> from colModel and editurl option from jqGrid.

</p>

<p>
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">editGridRow</span><span class="br0">(</span> <span class="st0">"new"</span><span class="sy0">,</span> properties <span class="br0">)</span>;</pre>

<p>

or with the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">jqGrid</span><span class="br0">(</span><span class="st0">'editGridRow'</span><span class="sy0">,</span> <span class="st0">"new"</span><span class="sy0">,</span> properties <span class="br0">)</span>;</pre>

<p>
Where
</p>
<ul>
<li class="level1"> grid_id: the id of the parent grid

<li class="level1"> properties: an array of name: value pairs, including any of the following properties or events.

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
These options can be overwritten when passed as options to the method. When passed to the method we should use by example bSubmit : “Submit” and not $.jgrid.edit.bSubmit : “Submit” <br>
 
</p>

<p>
The options are the same as those in editng row - See above.
</p>



<h4><a name="how_is_the_form_constructed1" id="how_is_the_form_constructed1">How is the form constructed</a></h4>

<div class="level4">

<p>
When the form is constructed we set the following rules: <br>

</p>
<ul>
<li class="level1"> Hidden fields are included in the form with the display:none property of the table row

<li class="level1"> The id of the editable element is constructed from the name of the colModel array - propery - name

<li class="level1"> The name of the editable element is constructed from the name of the colModel array - propery - name


<li class="level1"> For ease in manipulating the elements in an edit form, every table row in the form that holds the data for the edit has a id which is a combination of “tr_” + name (from colmodel). Example:

</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'tr_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span>edited element named, in colModel, as "myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 

<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a>&gt;</span></pre>
<p>

This allow us to easily show or hide some table rows depending on conditions using beforeShowForm event
</p>



<h4><a name="what_is_posted_to_the_server1" id="what_is_posted_to_the_server1">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in editing mode the data that is posted to the server is object {} that contain:
</p>

<ul>
<li class="level1"> the name:value pair  where the name is the name of the input element represented in the form (this is for all input elements)

<li class="level1"> additionally we add a pair id:_empty where the _empty indicates that new row is inserted

<li class="level1"> additionally we add a pair oper:add to indicate the add mode

<li class="level1"> if the editData object is not empty we extend this data with the posted data


<li class="level1"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object

</ul>



<h3><a name="viewgridrow" id="viewgridrow">viewGridRow</a></h3>


<p>

This method is similar to the editGridRow method except that the fields are not editable and we do not have any events attached to the form. <br>
 

</p>

<p>
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">viewGridRow</span><span class="br0">(</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>

<p>

or when we use the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">jqGrid</span><span class="br0">(</span><span class="st0">'viewGridRow'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> properties <span class="br0">)</span>;</pre>

<p>
Where
</p>
<ul>
<li class="level1"> grid_id: the id of the parent grid

<li class="level1"> rowid: the id of the row to edit

<li class="level1"> properties: an array of name: value pairs, including any of the following properties or events.

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


<h4><a name="properties1" id="properties1">Properties</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Property</th><th class="col1">Description</th><th class="col2">Default</th>
	</tr>

	<tr class="row1">
		<td class="col0">top<td class="col1">the initial top position of modal dialog. The default value of 0 mean the top position from the upper left corner of the grid. When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the top position from upper left corner of the window. Default 0
	</tr>
	<tr class="row2">
		<td class="col0">left<td class="col1">the initial left position of modal dialog. The default value of 0 mean the left position from the upper left corner of the grid. When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the left position from upper left corner of the window. Default 0
	</tr>

	<tr class="row3">
		<td class="col0">width<td class="col1">the width of confirmation dialogDefault 300
	</tr>
	<tr class="row4">
		<td class="col0">height<td class="col1">the entry height of confirmation dialogDefault auto
	</tr>

	<tr class="row5">
		<td class="col0">dataheight<td class="col1">the parameter control the scrolling content - i.e between the modal header and modal footer.Default auto
	</tr>
	<tr class="row6">
		<td class="col0">modal<td class="col1">determines if the dialog will be modal. Also works only if jqModal plugin is presentDefault false
	</tr>

	<tr class="row7">
		<td class="col0">drag<td class="col1">Determines if the dialog is dragabale. Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UIDefault  true
	</tr>
	<tr class="row8">
		<td class="col0">resize<td class="col1">determines if the dialog can be resized. Works only is jqDnR plugin is available or resizable widget is present from jQuery UIDefault true

	</tr>
	<tr class="row9">
		<td class="col0">jqModal<td class="col1">If set to true uses jqModal plugin (if present) to creat the dialogs. If set to true and the plugin is not present jqGrid uses its internal function to create dialogDefault true
	</tr>
	<tr class="row10">
		<td class="col0">topinfo<td class="col1">When set this information is placed just after the modal header as additional rowDefault empty string

	</tr>
	<tr class="row11">
		<td class="col0">bottominfo<td class="col1">When set this information is placed just after the buttons of the form as additional rowDefault empty string
	</tr>
	<tr class="row12">
		<td class="col0">closeicon<td class="col1"> Array. Determines the icon of the cancel button. The default values are [true,”left”,”ui-icon-close”]. For description of these see saveiconDefault  

	</tr>
	<tr class="row13">
		<td class="col0">navkeys<td class="col1"> array. This option works only in edit mode and  add keyboard navigation, which allow us to navigate through the records while in form editing pressing certain keys. The default state is disabled. The first item enables/disables the navigation. The second item corresponds to reccord up and by default is the  the key code for Up key. The third item corresponds to reccord down and by default is the  key code for Down keyDefault [false,38,40]
	</tr>
	<tr class="row14">
		<td class="col0">closeOnEscape<td class="col1">When set to true the modal window can be closed with ESC key from the user.Default false

	</tr>
	<tr class="row15">
		<td class="col0">labelswidth<td class="col1">Since we construct the view with table element it is difficult to calculate, in this case, how much width is needed for the labels. Depending on the needs this value can be increased or decreasedDefault 30%
	</tr>
	<tr class="row16">
		<td class="col0">viewPagerButtons<td class="col1"> This option enable or disable the appearing of the previous and next buttons (pager buttons) in the form Default true

	</tr>
	<tr class="row17">
		<td class="col0">zIndex<td class="col1">The starting z-index for the dialog. If you will see the dialog form under another elements or dialogs you should use the parameter with some value higher as default value 950. In the most cases it should be the value higher as 1000 - the default value of jQuery UI dialog.Default 950
	</tr>
</tbody></table>



<h4><a name="events1" id="events1">Events</a></h4>

<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Event</th><th class="col1">Description</th>
	</tr>
	<tr class="row1">
		<td class="col0">onClose<td class="col1">This event is called just before closing the form and when a close icon is clicked, a cancel button is clicked, ESC key is pressed or click on overlay (if jqModal is present). The event can return (optionally) true or false. If the return value is true the form is closed, if false the form does not close. If nothing is returned the form is closed
	</tr>

	<tr class="row2">
		<td class="col0">beforeShowForm<td class="col1">fires before showing the form with the new data; receives as Parameter the id of the constructed form. <br>
 beforeShowForm : function(formid) {…} 
	</tr>
</tbody></table>

<p>

When the form is constructed we set the following rules: <br>

</p>
<ul>
<li class="level1"> No input elements are created

<li class="level1"> Hidden fields are included in the form with the display:none property of the table row

<li class="level1"> The id of the  viewed element is constructed from “v_” + the name of the colModel array - propery - name 

<li class="level1"> For ease in manipulating the elements in an edit form, every table row in the form that holds the data for the edit has a id which is a combination of “trv_” + name (from colmodel). Example:


</ul>
<pre class="code html4strict"><span class="sc2">&lt;<a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a> ....&gt;</span> 
  <span class="sc2">&lt;<a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 
    <span class="sc2">&lt;<a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">'trv_myfield'</span>&gt;</span>  
      <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> Caption<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> <span class="sc2">&lt;<a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a> <span class="kw3">id</span><span class="sy0">=</span><span class="st0">"v_myfield"</span>&gt;&lt;<a href="http://december.com/html/4/element/span.html"><span class="kw2">span</span></a>&gt;</span>content of"myfield"<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/span.html"><span class="kw2">span</span></a>&gt;&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/td.html"><span class="kw2">td</span></a>&gt;</span> 
    <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/tr.html"><span class="kw2">tr</span></a>&gt;</span> ... 
  <span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/table.html"><span class="kw2">table</span></a>&gt;</span> 

<span class="sc2">&lt;<span class="sy0">/</span><a href="http://december.com/html/4/element/form.html"><span class="kw2">form</span></a>&gt;</span></pre>


<h3><a name="delgridrow" id="delgridrow">delGridRow</a></h3>


<p>
With this method we can perform a delete operation at server side. <br>
 
This method uses colModel and editurl parameters from jqGrid
</p>

<p>

Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">delGridRow</span><span class="br0">(</span> row_id_s<span class="sy0">,</span> options <span class="br0">)</span>;</pre>

<p>

or when we use the new <acronym title="Application Programming Interface">API</acronym>

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">jqGrid</span><span class="br0">(</span><span class="st0">'delGridRow'</span><span class="sy0">,</span> row_id_s<span class="sy0">,</span> options <span class="br0">)</span>;</pre>

<p>
Where:
</p>
<ul>
<li class="level1"> grid_id: the id of the parent grid

<li class="level1"> row_id_s: the id of the row(s) to delete; can be a single value or list of ids separated by comma

<li class="level1"> options: an array of name: value pairs, including any of the following properties or events.

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
These options can be overwritten when passed as options to the method. When passed to the method we should use by example bSubmit : “Delete” and not $.jgrid.del.bSubmit : “Delete” <br>
 
</p>



<h4><a name="properties2" id="properties2">Properties</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Property</th><th class="col1">Description</th><th class="col2">Default</th>
	</tr>
	<tr class="row1">

		<td class="col0">top<td class="col1">the initial top position of modal dialog. The default value of 0 mean the top position from the upper left corner of the grid. When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the top position from upper left corner of the window. Default 0
	</tr>
	<tr class="row2">
		<td class="col0">left<td class="col1">the initial left position of modal dialog. The default value of 0 mean the left position from the upper left corner of the grid. When jqModal option is true (see below) and jqModal plugin is present any value different from 0 mean the left position from upper left corner of the window. Default 0
	</tr>
	<tr class="row3">

		<td class="col0">width<td class="col1">the width of confirmation dialogDefault 300
	</tr>
	<tr class="row4">
		<td class="col0">height<td class="col1">the entry height of confirmation dialogDefault auto
	</tr>
	<tr class="row5">

		<td class="col0">dataheight<td class="col1">the parameter control the scrolling content - i.e between the modal header and modal footer.Default auto
	</tr>
	<tr class="row6">
		<td class="col0">modal<td class="col1">determines if the dialog will be modal. Also works only if jqModal plugin is presentDefault false
	</tr>
	<tr class="row7">

		<td class="col0">drag<td class="col1">Determines if the dialog is dragabale. Works only if jqDnR plugin is present or if the dragable widget is present from jQuery UIDefault  true
	</tr>
	<tr class="row8">
		<td class="col0">resize<td class="col1">determines if the dialog can be resized. Works only is jqDnR plugin is available or resizable widget is present from jQuery UIDefault true
	</tr>

	<tr class="row9">
		<td class="col0">url<td class="col1"> url where to post data. If set, replaces the editurlDefault null
	</tr>
	<tr class="row10">
		<td class="col0">mtype<td class="col1">Defines the type of request to make (“POST” or “GET”) when data is sent to the serverDefault POST

	</tr>
	<tr class="row11">
		<td class="col0">delData<td class="col1">an array used to add content to the data posted to the serverDefault empty
	</tr>
	<tr class="row12">
		<td class="col0">jqModal<td class="col1">If set to true uses jqModal plugin (if present) to creat the dialogs. If set to true and the plugin is not present jqGrid uses its internal function to create dialogDefault true

	</tr>
	<tr class="row13">
		<td class="col0">delicon<td class="col1"> array. Determines the icon of the submit button. The default value is [true,”left”,”ui-icon-delete”]. The first item enables/disables the icon. The second item tells where to put the icon to left or to right of the text. The third item corresponds to valid ui icon from theme roller Default  
	</tr>
	<tr class="row14">
		<td class="col0">cancelicon<td class="col1"> Array. Determines the icon of the cancel button. The default values are [true,”left”,”ui-icon-cancel”]. For description of these see deliconDefault  

	</tr>
	<tr class="row15">
		<td class="col0">reloadAfterSubmit<td class="col1">reload grid data after postingDefault true
	</tr>
	<tr class="row16">
		<td class="col0">closeOnEscape<td class="col1">When set to true the modal window can be closed with ESC key from the user.Default false

	</tr>
	<tr class="row17">
		<td class="col0">ajaxDelOptions<td class="col1">This option allow to set global ajax settings for the form editiing when we delete the data to the server. Note that with this option is possible to overwrite all current ajax setting in the save request including the complete event.Default empty object
	</tr>
	<tr class="row18">
		<td class="col0">zIndex<td class="col1">The starting z-index for the dialog. If you will see the dialog form under another elements or dialogs you should use the parameter with some value higher as default value 950. In the most cases it should be the value higher as 1000 - the default value of jQuery UI dialog.Default 950

	</tr>
</tbody></table>



<h4><a name="events2" id="events2">Events</a></h4>
<div class="level4">
<table class="inline">
	<tbody><tr class="row0">
		<th class="col0">Event</th><th class="col1">Description</th>
	</tr>

	<tr class="row1">
		<td class="col0">afterComplete<td class="col1"> This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid. <br>
 afterComplete : function (response, postdata, formid) {…} <br>
 where <br>
 <em>response</em> is the data returned from the server (if any) <br>

 <em>postdata</em> an array, is the data sent to the server <br>
 <em>formid</em> is the jQuery object of form id, you can use formid[0].id to get form id. 
	</tr>
	<tr class="row2">
		<td class="col0">afterShowForm<td class="col1">fires after showing the form; receives as Parameter the id of the constructed form. <br>

 afterShowForm : finction (formid) {…} 
	</tr>
	<tr class="row3">
		<td class="col0">afterSubmit<td class="col1"> fires after response has been received from server. Typically used to display status from server (e.g., the data is successfully deleted or the delete cancelled for server-side reasons). Receives as parameters the data returned from the request and an array of the posted values of type id=value1,value2. <br>
 When used this event should return array with the following items [success, message] <br>
 where <br>

 <em>success</em> is a boolean value if true the process continues, if false a error <em>message</em> appear and all other processing is stoped. <br>
 afterSubmit : function(response, postdata) <br>
 { <br>
 … <br>

 return [succes,message] <br>
 } 
	</tr>
	<tr class="row4">
		<td class="col0">beforeShowForm<td class="col1">fires before showing the form with the new data; receives as Parameter the id of the constructed form. <br>
 beforeShowForm : function(formid) {…} 

	</tr>
	<tr class="row5">
		<td class="col0">beforeSubmit<td class="col1">fires before the data is submitted to the server. Recieves as parameter the posted data array and the formid. <br>
 beforeSubmit : function(postdata, formid) { <br>
 … <br>
 return[success,message]; <br>

 } <br>
 When defined this event should return array with the following values <br>
 <em>success</em> boolean indicating if the proccess should continue (true) or a error (false) <em>message</em> should appear to the user
	</tr>
	<tr class="row6">

		<td class="col0">onclickSubmit<td class="col1">fires after the submit button is clicked and the postdata is constructed. Parameters passed to this event is a options array of the method. The event should return array of type {} which extends the postdata array. <br>
 onclickSubmit : function(params) { <br>
 … <br>
 return {add_data} <br>
  }
	</tr>

	<tr class="row7">
		<td class="col0">onClose<td class="col1">This event is called just before closing the form and when a close icon is clicked, a cancel button is clicked, ESC key is pressed or click on overlay (if jqModal is present). The event can return (optionally) true or false. If the return value is true the form is closed, if false the form does not close. If nothing is returned the form is closed
	</tr>
	<tr class="row8">
		<td class="col0">errorTextFormat<td class="col1">The event (can) fire when error occurs from the ajax call and can be used for better formatting of the error messages. To this event is passed response from the server. The event should return single message (not array), which then is displayed to the user.
	</tr>
	<tr class="row9">

		<td class="col0">serializeDelData<td class="col1">If set this event can serialize the data passed to the ajax request when we save a form data. The function should return the serialized data. This event can be used when a custom data should be passed to the server - e.g - JSON string, <acronym title="Extensible Markup Language">XML</acronym> string and etc. To this event is passed the data which will be posted to the server.
	</tr>
</tbody></table>



<h4><a name="what_is_posted_to_the_server2" id="what_is_posted_to_the_server2">What is posted to the server</a></h4>
<div class="level4">

<p>
When we are in delete mode the data that is posted to the server is object {} that contain:
</p>
<ul>
<li class="level1"> the pair id:rowids where the rowids can be a single value a value string separated with comma in case of multiple selection

<li class="level1"> additionally we add a pair oper:del to indicate the delete mode

<li class="level1"> if the delData object is not empty we extend this data with the posted data

<li class="level1"> if the returned object from onclickSubmit event is not empty we extend the posted data with this object


</ul>



<h3><a name="gridtoform" id="gridtoform">GridToForm</a></h3>


<p>
This method does not have interaction with the user <br>
 
Calling convention:

</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">GridToForm</span><span class="br0">(</span> rowid<span class="sy0">,</span> formid <span class="br0">)</span>;</pre>

<p>or</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">jqGrid</span><span class="br0">(</span><span class="st0">'GridToForm'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> formid <span class="br0">)</span>;</pre>

<p>Where</p>
<ul>
<li class="level1"> grid_id: the id of the parent grid
<li class="level1"> rowid: the id of the row 
<li class="level1"> formid: is the jQuery object of form id, you can use formid[0].id to get form id.
</ul>

<p>

When called this method read the grid data with the id = rowid and places the data from the grid to the given form. if the form element has a name which is equal of those from colModel the value is placed to the form.
</p>



<h3>FormToGrid</h3>
<p>This method does not have interaction with the user <br> 
Calling convention:</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">FormToGrid</span><span class="br0">(</span> rowid<span class="sy0">,</span> formid<span class="sy0">,</span> mode<span class="sy0">,</span> position <span class="br0">)</span>;</pre>

<p>or</p>
<pre class="code javascript">jQuery<span class="br0">(</span><span class="st0">"#grid_id"</span><span class="br0">)</span>.<span class="me1">jqGrid</span><span class="br0">(</span><span class="st0">'FormToGrid'</span><span class="sy0">,</span> rowid<span class="sy0">,</span> formid<span class="sy0">,</span> mode<span class="sy0">,</span> position <span class="br0">)</span>;</pre>

<p>Where</p>
<ul>
<li class="level1"> grid_id: the id of the parent grid
<li class="level1"> rowid: the id of the row 
<li class="level1"> formid: is the jQuery object of form id, you can use formid[0].id to get form id.
<li class="level1"> mode: determines the mode. Can be set or add. Default is set. If the mode is defined as set, the record in the grid is updated with the new values from the form. When the mode is defined as add the record is added to the grid.
<li class="level1"> position: this parameter have sense only when the mode parameter is set to add. Can be first - the record is inserted as first row in the grid or last - the record is inserted as last row in the grid.
</ul>

<p>

When called this method does the opposite of the GridToForm method - i.e. places the form data in the grid if the names matches.
</p>