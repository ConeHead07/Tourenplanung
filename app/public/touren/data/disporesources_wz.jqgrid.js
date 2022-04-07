//<![CDATA[
jQuery(function() { jQuery("#gridDispoResourceWZLst").jqGrid(
{
//    "wid", 	"bezeichnung",	"erforderliche_qualifikation"

    "colNames":[
        "wid", 	"Bezeichnung",	"Quali"
    ],
    "colModel":[
        {"name":"wid","index":"fid","editable":false,"hidden":true, "key":true},
        {"name":"bezeichnung","index":"bezeichnung","editable":false, 
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' colspan=10'},
            "formatter":function(value, options, rData){
                return '<div id="'+rData['wid']+'" class="Drag-Rsrc" rel="WZ">' +
                  rData['erforderliche_qualifikation'] + ': ' + '<span class="bezeichnung">'+value + '</span>'
                  '</div>'; 
        }},
        {"name":"erforderliche_qualifikation","index":"erforderliche_qualifikation","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}}
     ],
    "height":"auto",
    "jsonReader":{"repeatitems":false,"id":0},
    "autowidth":true,
    "rowList":[10,20,30,40,50,100],
    "rowNum":100,
    "rownumbers":false,
    "altRows":true,
    "altclass":"ui-jqgrid-altrow",
    "resizable":true,
    "sortable":true,
    "datatype":"local",
//    "url":APP_BASE_URL + "/werkzeug/gridresponsedata?extFilter=int",
    "shrinkToFit":true,
    "pager":"#gridDispoResourceWZLst_pager",
    "loadError":function(xhr,status,error){
        if (Fb && typeof(Fb.logAjaxError)=='function') { 
            Fb.logAjaxError('#37 data/disporesources_wz.jqgrid.js', xhr, status, error);
        }
    },
    "loadComplete": function() {
        if ( $(this).jqGrid('getGridParam', "datatype") == "local" ){
            // onInit um unnoetige teuere Datenbankabfrage zu vermeiden
            // wird das Grid ohne Autoloading der Daten initialisiert
            // onInit gridParam.datatype:  local
            // onInit gridParam.search: false
            // onInit gridParam.url:  empty String
            var defaultSearchUrl = "/werkzeug/gridresponsedata?extFilter=int";
            var extraSearchUrl = $(this).data('searchUrl');
            var searchUrl = (extraSearchUrl) ? extraSearchUrl : defaultSearchUrl;
            $(this).jqGrid('setGridParam', {
                "datatype":"json",
                "search": true,
                "url":APP_BASE_URL + searchUrl
            });
        }
        var $grid = $( this );
        $("div.Drag-Rsrc", this).addClass(" Drag-Rsrc Is-Template Rsrc-WZ" ).draggable( Fb.DragRsrcTemplateSettings );
        $("div.Drag-Rsrc", this).bind( "dragstart", function(e, ui) {
            var id = $(this).attr('id');
            var rd = $grid.jqGrid('getRowData', id );
            var name = $("span.bezeichnung", this).text();
            ui.helper.data('dragdata', { resourceType:'WZ', wid:id, name:name+' ', ondrop:function() {
//                alert( "#44 disporesources_wz.jqgrid.js dropped delete Row or Refresh List");
            }});
        });
    }
})
});
