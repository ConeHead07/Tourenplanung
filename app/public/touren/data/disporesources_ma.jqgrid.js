//<![CDATA[
jQuery(function() { jQuery("#gridDispoResourceMALst").jqGrid(
{

//    "mid", "user_id", "standort", "anrede", "titel",
//    "name", "vorname", "abteilung", "eingestellt_als",
//    "fuehrerscheinklassen", "urlaubsanspruch"

    "colNames":[
        "MID", "UID", "Position", "Name", "Vorname",
        "Abteilung", "FK", "Urlaubsanspruch", "Standort", "Anrede", "Titel"
    ],
    "colModel":[
        {"name":"mid","index":"mid","editable":false,"hidden":true, "key":true},
        {"name":"user_id","index":"user_id","editable":false,"hidden":true },
        {"name":"eingestellt_als","index":"eingestellt_als","editable":false, 
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' colspan=10'},
            "formatter":function(value, options, rData){
                return '<div id="'+rData['mid']+'" onclick="" class="Drag-Rsrc" rel="MA">' +
                  '<span class="positition">'+value + '</span>: '+ rData['name'] + ' ' +
                  rData['vorname'] + ' ' + rData['abteilung'] +
                  ',<br>'+ rData['fuehrerscheinklassen'] +
                  '</div>'; 
        }},
        {"name":"name","index":"name","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"vorname","index":"vorname","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"abteilung","index":"abteilung","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"fuehrerscheinklassen","index":"fuehrerscheinklassen","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"urlaubanspruch","index":"urlaubsanspruch","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"standort","index":"standort","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},        
        {"name":"anrede","index":"anrede","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
        {"name":"titel","index":"titel","editable":false,
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
//    "url":APP_BASE_URL + "/mitarbeiter/gridresponsedata?extFilter=int",
    "shrinkToFit":true,
    "pager":"#gridDispoResourceMALst_pager",
    "loadError":function(xhr,status,error){
        if (Fb && typeof(Fb.logAjaxError)=='function') {
            Fb.logAjaxError('#58 data/disporesources_ma.jqgrid.js', xhr, status, error);
        }
    },
    "loadComplete": function() {
        if ( $(this).jqGrid('getGridParam', "datatype") == "local" ){
            // onInit um unnoetige teuere Datenbankabfrage zu vermeiden
            // wird das Grid ohne Autoloading der Daten initialisiert
            // onInit gridParam.datatype:  local
            // onInit gridParam.search: false
            // onInit gridParam.url:  empty String
            $(this).jqGrid('setGridParam', {
                "datatype":"json",
                "search": true,
                "url":APP_BASE_URL + "/mitarbeiter/gridresponsedata?extFilter=int"
//                "url":APP_BASE_URL + "/mitarbeiter/listavaiables?extFilter=int"
            });
        }
        var $grid = $( this );
        $("div.Drag-Rsrc", this).addClass(" Drag-Rsrc Is-Template Rsrc-MA" ).draggable( Fb.DragRsrcTemplateSettings );
        $("div.Drag-Rsrc", this).bind( "dragstart", function(e, ui) {
            var id = $(this).attr('id');
            var rd = $grid.jqGrid('getRowData', id );
            var name = $("span.position", this).text()+': '+rd.name+','+rd.vorname;
            ui.helper.data('dragdata', { resourceType:'MA', mid:id, name:name, ondrop:function() {
                //alert( "#65 disporesources_ma.jqgrid.js dropped delete Row or Refresh List");
            }});
        });
        
        
    }
})
});
