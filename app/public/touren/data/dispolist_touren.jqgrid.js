//<![CDATA[
jQuery(function() { 
    jQuery("#gridDispoResourceFPLst").jqGrid({
//    "fid", "hersteller", "modell", "fahrzeugart", "FKL",
//    "Erstzulassung", "Anschaffung", "NaechsteInspektion",
//    "Kmst", "laderaum_laenge", "laderaum_breite", "laderaum_hoehe",
//    "ladevolumen", "nutzlast", "kw", "sitze"

        "colNames":[
            "fid", "hersteller", "modell", "fahrzeugart", "FKL",
            "Erstzulassung", "Anschaffung", "NaechsteInspektion",
            "Kmst", "laderaum_laenge", "laderaum_breite", "laderaum_hoehe",
            "ladevolumen", "nutzlast", "kw", "sitze"
        ],
        "colModel":[
            {"name":"fid","index":"fid","editable":false,"hidden":true, "key":true},
            {"name":"hersteller","index":"user_id","editable":false,"hidden":true },
            {"name":"modell","index":"modell","editable":false, 
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' colspan=14'},
                "formatter":function(value, options, rData){
                    return '<div id="'+rData['fid']+'" class="Drag-Rsrc" rel="FP">' +
                      ': '+ rData['hersteller'] + ': <span class="modell">' + value + '</span>' +
                      rData['fahrzeugart'] + ' ' + rData['FKL'] +
                      ',<br>B'+ rData['laderaum_breite'] + ' L' + rData['laderaum_laenge'] + ' H' + rData['laderaum_hoehe'] +
                      '</div>'; 
            }},
            {"name":"fahrzeugart","index":"fahrzeugart","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"FKL","index":"FKL","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"Erstzulassung","index":"Erstzulassung","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"Anschaffung","index":"Anschaffung","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"NaechsteInspektion","index":"NaechsteInspektion","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"Kmst","index":"Kmst","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},        
            {"name":"laderaum_laenge","index":"laderaum_laenge","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"laderaum_breite","index":"laderaum_breite","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"laderaum_hoehe","index":"laderaum_hoehe","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"ladevolumen","index":"ladevolumen","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"nutzlast","index":"nutzlast","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"kw","index":"kw","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}},
            {"name":"sitze","index":"sitze","editable":false,
                "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'}}
         ],
        "height":"auto",
        "jsonReader":{"repeatitems":false,"id":0},
        "autowidth":true,
        "rowList":[10,20,30,40,50,100],
        "rowNum":10,
        "rownumbers":false,
        "altRows":true,
        "altclass":"ui-jqgrid-altrow",
        "resizable":true,
        "sortable":true,
        "datatype":"json",
        "url":APP_BASE_URL + "/fuhrpark/gridresponsedata",
        "shrinkToFit":true,
        "pager":"#gridDispoResourceFPLst_pager",
        "loadError":function(xhr,status,error){
            if (Fb && typeof(Fb.logAjaxError)=='function') { 
                Fb.logAjaxError('#70 data/dispolist_touren.jqgrid.js', xhr, status, error);
            }
        },
        "loadComplete": function() {
            var $grid = $( this );
            $("div.Drag-Rsrc", this).addClass(" Drag-Rsrc Is-Template Rsrc-FP" ).draggable( Fb.DragRsrcTemplateSettings );
            $("div.Drag-Rsrc", this).bind( "dragstart", function(e, ui) {
                var id = $(this).attr('id');
                var rd = $grid.jqGrid('getRowData', id );
                var modell = $("span.modell", this).text();
                ui.helper.data('dragdata', { resourceType:'FP', fid:id, name:modell+' '+rd.fahrzeugart, ondrop:function() {
//                    alert( "#77 disporesources_fp.jqgrid.js dropped: delete Row or Refresh List");
                }});
            });
        },
        nav: {
            refresh:true
        },
        "onSelectRow":function(id, status) { 
            var lastSel = $( this ).data("lastSel");
            var rd = $(this).jqGrid('getRowData', id );
            
            var divId = 'tourJumpBox';
            if ( !$('#'+divId).length ) var ttip = $('#'+divId);
            else {
                var ttip =  $( "<div id='#"+divId+"></div>").css({
                    position: "absolute"
                });
            }
        }
    })
});
