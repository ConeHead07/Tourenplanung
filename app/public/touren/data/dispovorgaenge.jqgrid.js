//<![CDATA[
jQuery(function() {jQuery("#gridDispoVorgaengeLst").jqGrid(
{
    "colNames":[
        "Mandant","WWS",
        "Adr","PLZ","Ort","Land","L-Name",
        "KW","Jahr","Termin", "BestaetigtAm",
        "AW", 
        "Fix","LieferterminHinweisText",        
        "Touren",
        "Stat",
        "ANR"
    ],
    "colModel":[
        {"name":"Mandant","index":"Mandant","editable":false,"hidden":true, "key":false },
        {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false, "key":false, 
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' colspan=11'},
            "formatter":function(value, options, rData) {                
                //alert( rData['LieferungStrassePostfach'] );
                var aw = "";

                var formatNr = function(x) {
                    return x.toString().replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                };

                if (rData['Auftragswert'] > 0.00) {
                    aw = 'AW: ' + formatNr(rData['Auftragswert']);
                } else if (rData['AuftragswertListe'] > 0.00) {
                    aw = 'AWL: ' + formatNr(rData['AuftragswertListe']);
                }

                var terminTitle = rData['Fix'] ? "Fixtermin" : "Liefertermin";
                
                var termin = (rData['Liefertermin'] && !rData['Liefertermin'].match(/0000-/))
                       ?    (rData['Liefertermin'].match(/00:00:00/)
                            ? rData['Liefertermin'].replace(/00:00:00/, '').replace(/(\d+)-(\d+)-(\d+) (\d\d:){0,2}(.*)/, "$3.$2.$1 $4")
                            : rData['Liefertermin'].replace(/:00$/, '').replace(/(\d+)-(\d+)-(\d+) (\d\d:){0,2}(.*)/, "$3.$2.$1 $4")
                       )
                       : '';
                var JKW = (rData['Lieferwoche']>0 ? "KW " + rData['Lieferwoche'] : "") + (rData['Lieferjahr']>0 ? '-20' + (rData['Lieferjahr']>9 ? rData['Lieferjahr'] : "0"+rData['Lieferjahr']) : '' );
                var terminTxt = (termin || JKW ? terminTitle + ": " + termin + " " + JKW + "<br/>" : "");
                var re = '<div data-Auftragsnummer="'+value+'" style="height:auto;" class="Drag-Route Is-Template"><span class="title">' +
                    terminTxt + 
//                    ( rData['Fix'] ? "Fixtermin" : "Liefertermin") +
//                    ( (rData['Liefertermin'] && !rData['Liefertermin'].match(/0000-/))
//                       ? '<br>L-Termin: ' + (rData['Liefertermin'].match(/00:00:00/)
//                            ? rData['Liefertermin'].replace(/00:00:00/, '').replace(/(\d+)-(\d+)-(\d+) (\d\d:){0,2}(.*)/, "$3.$2.$1 $4")
//                            : rData['Liefertermin'].replace(/:00$/, '').replace(/(\d+)-(\d+)-(\d+) (\d\d:){0,2}(.*)/, "$3.$2.$1 $4")
//                       )
//                       : ''
//                     ) + ( (rData['Lieferjahr']>0 || rData['Lieferwoche']>0) ? '<br>KW:' + rData['Lieferwoche'] + '-20' + rData['Lieferjahr'] : '' ) +
                    
                    "<b>["+rData['Mandant'] + "] " + 
                    value + ', ' + rData['LieferungName'] + 
                    '</b><br />'+ rData['Vorgangstitel'] +
                    '<br />'+ ''+ rData['LieferungStrassePostfach'] +
                    ' / ' + rData['LieferungPostleitzahl'] + 
                    ' ' + rData['LieferungOrt'] + 
                    '/' + rData['LieferungLand'] +
                    '<br/>' + aw + " &euro;\n" +
                    ''+ 
                    //' ' + (rData['BestaetigtAm'] ? '<br>Bestaetigt '+rData['BestaetigtAm'].replace(/(\d+)-(\d+)-(\d+) (\d\d:){0,2}/, "$3.$2.$1 $4").replace(/00:00/,'') : '') + ','+
                    '</span></div>'
                  ; 
                  return re;
            },
            "unformat": function(cellvalue, options, rowData) {
                return $(cellvalue).data("Auftragsnummer");
            }
        },        
        
        {"name":"LieferungStrassePostfach","index":"LieferungStrassePostfach","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"LieferungPostleitzahl","index":"LieferungPostleitzahl","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"LieferungOrt","index":"LieferungOrt","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"LieferungLand","index":"LieferungLand","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"LieferungName","index":"LieferungName","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},        
        {"name":"Lieferwoche","index":"Lieferwoche","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"Lieferjahr","index":"Lieferjahr","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        {"name":"Liefertermin","index":"Liefertermin","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; },
            "formatter":function(value, options, rData){ return (typeof(value)=='string' ? value.replace(/00:00:00/, '') : ''); }},
        {"name":"BestaetigtAm","index":"BestaetigtAm","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; },
            "formatter":function(value, options, rData){ return (typeof(value)=='string' ? value.replace(/00:00:00/, '') : ''); }},
        
        {"name":"Auftragswert","index":"Auftragswert","editable":false,
            "cellattr":function( rowId, value, rowObject, colModel, arrData){return ' style="display:none"'; }},
        
        {"name":"LieferterminFix","index":"LieferterminFix","editable":false},        
        {"name":"LieferterminHinweisText","index":"LieferterminHinweisText","editable":false,"hidden":true},
        
        {"name":"tour_count","index":"tour_count","editable":false,
            "formatter":function(value, options, rData) {
                var re = '<div style="height:auto;" title="' + 
                rData['tour_date_first'] + ' bis ' + 
                rData['tour_date_last'] +'" onclick="alert(\'' + 
                rData['Auftragsnummer'] +' '+ 
                rData['tour_date_first'] + ' bis ' + 
                rData['tour_date_last'] +'\')">' +
                    value + 
                    '</div>'
                ; 
                return re;
            }},
        {"name":"dispoStatus","index":"dispoStatus","editable":false,"classes":"colDispoStatus",
            "formatter":function(value, options, rData) {
                var ti = (value == 'beauftragt') ? 'terminiert' : value;
                
                return '<div class="VorgangStatusBox"><span onclick="Fb.showReminderDialog(this,'+
                       rData.Mandant+','+     
                       rData.Auftragsnummer+','+
                       '\''+rData.auftrag_wiedervorlage_am+'\')" class="v-stat-icon v-stat-'+value+'" title="'+ti+'"></span></div>';
            }},
        {
            "name": "ANR", "index": "Auftragsnummer", "editable": false, "hidden": true,
            "formatter": function(value, options, rData) { return !isNaN(rData.Auftragsnummer) ? +rData.Auftragsnummer : null; }
        }

     ],
    "height":"auto",
    "jsonReader":{"repeatitems":false,"id":null },
    "autowidth":true,
    "rowList":[10,20,30,40,50,100],
    "rowNum":100,
    "rownumbers":false,
    "altRows":true,
    "altclass":"ui-jqgrid-altrow",
    "resizable":true,
    "sortable":true,
    "datatype":"local",
//    "url":APP_BASE_URL + "/vorgaenge/gridresponsedata/view/touren",
    "shrinkToFit":true,
    "pager":"#gridDispoVorgaengeLst_pager",
    "loadError":function(xhr,status,error){
        if (Fb && typeof(Fb.logAjaxError)=='function') {
            Fb.logAjaxError('#98 data/dispovorgaenge.jqgrid.js', xhr, status, error);
        }
    },
    "subGrid": true,
    "subGridRowExpanded": function(subgrid_id, row_id) {
        // we pass two parameters
        // subgrid_id is a id of the div tag created whitin a table data
        // the id of this elemenet is a combination of the "sg_" + id of the row
        // the row_id is the id of the row
        // If we want to pass additinal parameters to the url we can use
        // a method getRowData(row_id) - which returns associative array in type name-value
        // here we can easy construct the flowing
        var subgrid_table_id, pager_id;
        subgrid_table_id = subgrid_id+"_t";
        pager_id = "p_"+subgrid_table_id;
        var row = $( this ).getRowData(row_id);
        var mandant = row['Mandant'];
//        alert('row_id:'+row_id+', mandant:'+row['Mandant']+', Auftragsnumer:'+row['Auftragsnummer']);
        
        $("#"+subgrid_id).html(
            "<table id='"+subgrid_table_id+"' class='scroll subgrid'></table>"
           +"<div id='"+pager_id+"' class='scroll'></div>");

        jQuery("#"+subgrid_table_id).jqGrid({ /*grid-Anweisungen*/ 
            "url":APP_BASE_URL + "/bestellkoepfe/gridresponsedata/parentid/"+row_id+"/mandant/"+mandant,
            "editurl":APP_BASE_URL + "/bestellkoepfemeta/grideditdata/parentid/"+row_id+"/mandant/"+mandant,
            "colNames":[
                "Mandant","Auftragsnummer","Bestellnummer", "Stellplatz", 
                "BestellName", "KW ErwarteterEingangWoche", 
                "Jahr - ErwarteterEingangJahr", 
                "Termin - ErwarteterEingang", "Fix - ErwarteterEingangterminFix"],
            "colModel":[
                    {"name":"Mandant","index":"Mandant","editable":false,"hidden":true},
                    {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false,"hidden":true},
                    {"name":"Bestellnummer","index":"Bestellnummer","editable":false, "key":true,
                    "formatter":function(value, options, rData) {
                        return (value ? value : 'Ohne Bestellnr');
                        var re = '<div style="height:auto;" title="'+ rData['tour_date_first']+ ' bis ' +rData['tour_date_last']+'" onclick="alert(\''+rData['Auftragsnummer']+' '+ rData['tour_date_first']+ ' bis ' +rData['tour_date_last']+'\')">' +
                            value + 
                            '</div>'
                        ; 
                        return re;
                    }},
                    {"name":"Stellplatz","index":"Stellplatz","editable":true},
                    {"name":"BestellName","index":"BestellName","editable":false},
                    {"name":"ErwarteterEingangWoche","index":"ErwarteterEingangWoche","editable":false},
                    {"name":"ErwarteterEingangJahr","index":"ErwarteterEingangJahr","editable":false},
                    {"name":"ErwarteterEingang","index":"Lieferjahr","editable":false},
                    {"name":"ErwarteterEingangterminFix","index":"ErwarteterEingangterminFix","editable":false}
            ],
            "height":"auto",
            "jsonReader":{"repeatitems":false,"id":0},
            "autowidth":true,
            "rowList":[10,20],
            "rowNum":10,
            "rownumbers":false,
            "resizable":true,
            "sortable":true,
            "datatype":"json",
            "shrinkToFit":true,
            "pager":pager_id,
            "loadError":function(xhr,status,error){
                alert('#148 data/dispovorgaenge.jqgrid.js subgrid'+
                    '\n\nxhr.repsonseText:'+xhr.responseText+
                    '\n\nstatus: '+status+
                    '\n\nerror:'  +error);
            },
            "subGrid": true,
            "loadComplete": function() {
                if ( $( this ).jqGrid( 'getGridParam', 'lastpage' ) < 2) {
                    $('#'+pager_id).toggle();
                }
        
                $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() { 
                    // alert( '#204 touren/data/dispovorgaenge.jqgrid.js: .ui-th-column each text: ' + $(this).text() );
                    $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
                });
            },
            "resizeStop": function() {
                alert( 'resizeStop' );
            },
            "onSelectRow": function(id, status) { 
                        var lastSel = $( this ).data("lastSel");
                        if (isNaN(id)) {
                            if (lastSel) $( this ).jqGrid("restoreRow", lastSel, {"afterrestorefunc":null});
                            jQuery( this ).jqGrid("resetSelection" );
                            return false; //alert("#202 id("+typeof(id)+")"+id);
                        }
			
			if (lastSel && lastSel != id) {
				$( this ).jqGrid("saveRow", lastSel, {"successfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);},"url":null,"extraparam":null,"aftersavefunc":null,"errorfunc":null,"afterrestorefunc":null,"succesfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);}});
//				$( this ).jqGrid("saveRow", lastSel, {"successfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);},"url":null,"extraparam":null,"aftersavefunc":null,"errorfunc":null,"afterrestorefunc":null,"succesfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);});
				$( this ).jqGrid("restoreRow", lastSel, {"afterrestorefunc":null});
			}
		
			if (!lastSel || lastSel != id) {
				$( this ).jqGrid("editRow", id, {
                                    "keys":true,
                                    "oneditfunc":function() {},
                                    "successfunc":function(re)  {
                                        var r = (re.responseText) ? $.parseJSON( re.responseText ) : re; 
                                        if (r.err) alert(r.err); 
                                        var b = (typeof(re)=="object" && r.type !== 'error' && r.success !== false);
                                        //alert( "#210 id: " + id + " b: " + typeof(b) + " " + b + " => " + typeof(re) + " && " + r.type + " && " + r.success);
                                        return b;
                                    },
                                    "aftersavefunc":function(a,b,c){ 
                                        $("#bk_" + id + "_t").trigger("reloadGrid");
                                    },
                                    "errorfunc": function(a,b,c){ 
                                        alert("#215 errorfunc " + a + "\n" + b + "\n" + c);
                                    },
                                    "afterrestorefunc":null,
                                    "succesfunc":function(re)  { 
                                        var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; 
                                        if (re.err) alert(re.err); 
                                        return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);
                                    }
                                });
				$( this ).data("lastSel", id);
			} else {
				$( this ).jqGrid("restoreRow", id, {"afterrestorefunc":null});
				jQuery( this ).jqGrid("resetSelection" );
				$( this ).data("lastSel", 0);
			}
			//alert(id+", "+status+", lastSel:" + lastSel );
            },
            "subGridRowExpanded": function(subgrid_id, row_id) {
                // we pass two parameters
                // subgrid_id is a id of the div tag created whitin a table data
                // the id of this elemenet is a combination of the "sg_" + id of the row
                // the row_id is the id of the row
                // If we wan to pass additinal parameters to the url we can use
                // a method getRowData(row_id) - which returns associative array in type name-value
                // here we can easy construct the flowing
                var subgrid_table_id, pager_id;
                subgrid_table_id = "bk_" + row_id + "_t";
                pager_id = "p_"+subgrid_table_id;
                
                var row = $( this ).getRowData(row_id);
                var mandant = row['Mandant'];
                var auftrag = row['Auftragsnummer'];
//                alert('row_id:'+row_id+', mandant:'+row['Mandant']+', Auftragsnumer:'+row['Auftragsnummer']+', Bestellnummer:'+row['Bestellnummer']);

                $("#"+subgrid_id).html(
                    "<table id='"+subgrid_table_id+"' class='scroll subgrid'></table>"
                   +"<div id='"+pager_id+"' class='scroll'></div>");

                jQuery("#"+subgrid_table_id).jqGrid({ /*grid-Anweisungen*/ 
                    "url":APP_BASE_URL + "/bestellpositionen/gridresponsedata/parentid/"+row_id+"/mandant/"+mandant+"/auftrag/"+auftrag,
                    "editurl":APP_BASE_URL + "/bestellpositionenmeta/grideditdata/parentid/"+row_id+"/mandant/"+mandant+"/auftrag/"+auftrag,
                    "colNames":[
                        "Mandant",
                        "Auftragsnummer","Bestellnummer","PosNr","LK - Lagerkennung","SP - Stellplatz",
                        "Artikelnummer","Bezeichnung","Liefermenge",
                        "Liefertermin - ErwarteterEingang",
                        "KW - ErwarteterEingangWoche",
                        "Jahr - ErwarteterEingangJahr",
                        "LieferterminFix - ErwarteterEingangterminFix"],
                    "colModel":[
                            {"name":"Mandant","index":"Mandant","editable":true, "key":false,"hidden":true},
                            {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false,"hidden":true},
                            {"name":"Bestellnummer","index":"Bestellnummer","editable":true, "key":false,"hidden":true},
                            {"name":"Positionsnummer","index":"Positionsnummer","editable":false, "key":true,"hidden":false},
                            {"name":"WB_Lagerkennung","index":"WB_Lagerkennung","editable":false, "hidden":false},
                            {"name":"Stellplatz","index":"Stellplatz","editable":(!isNaN(row_id)?true:false), "hidden":false,
                                "cellattr":function( rowId, value, rowData, colModel, blnAnything){                                    if (!rowData.StellplatzHistorie) return '';
                                    var h = rowData.StellplatzHistorie
                                           .replace(/;(\d{2})(\d{2})-(\d{1,2})-(\d{1,2}) (\d{2}):(\d{2}):(\d{2})/gm, ";$3.$2.$1 $4:$5")
                                           .replace(/;/gm," ");
                                    return ' title="' + h + '"';
                                }                            
                            },
                            {"name":"Artikelnummer","index":"Artikelnummer","editable":false,"hidden":true},
                            {"name":"Bezeichnung","index":"Bezeichnung","editable":false},
                            {"name":"Liefermenge","index":"Liefermenge","editable":false},
                            {"name":"ErwarteterEingang","index":"ErwarteterEingang","editable":false},
                            {"name":"ErwarteterEingangWoche","index":"ErwarteterEingangWoche","editable":false},
                            {"name":"ErwarteterEingangJahr","index":"ErwarteterEingangJahr","editable":false},                            
                            {"name":"ErwarteterEingangterminFix","index":"ErwarteterEingangterminFix","editable":false}
                    ],
                    "height":"auto",
                    "jsonReader":{"repeatitems":false,"id":0},
                    "autowidth":true,
                    "rowList":[10,20],
                    "rowNum":10,
                    "rownumbers":false,
                    "resizable":true,
                    "sortable":true,
                    "datatype":"json",
                    "shrinkToFit":true,
                    "pager":pager_id,
                    "loadComplete": function() {
                        if ( $( this ).jqGrid( 'getGridParam', 'lastpage' ) < 2) {
                            $('#'+pager_id).toggle();
                        }
                        
                        $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() { 
                            // alert( '#204 touren/data/dispovorgaenge.jqgrid.js: .ui-th-column each text: ' + $(this).text() );
                            $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
                        });
                    },
                    "loadError":function(xhr,status,error){
                        alert('#232 data/dispovorgaenge.jqgrid.js subsubgrid'+
                            '\n\nxhr.repsonseText:'+xhr.responseText+
                            '\n\nstatus: '+status+
                            '\n\nerror:'  +error);
                    },
                    "onSelectRow": function(id, status) { 
                                var lastSel = $( this ).data("lastSel");
                                if (isNaN(row_id)) {
                                    if (lastSel) $( this ).jqGrid("restoreRow", lastSel, {"afterrestorefunc":null});
                                    jQuery( this ).jqGrid("resetSelection" );
                                    return false; //alert("#202 id("+typeof(id)+")"+id);
                                }
                                

                                if (lastSel && lastSel != id) {
                                        $( this ).jqGrid("saveRow", lastSel, {"successfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);},"url":null,"extraparam":null,"aftersavefunc":null,"errorfunc":null,"afterrestorefunc":null,"succesfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);}});
        //				$( this ).jqGrid("saveRow", lastSel, {"successfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);},"url":null,"extraparam":null,"aftersavefunc":null,"errorfunc":null,"afterrestorefunc":null,"succesfunc":function(re)  { var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; if (re.err) alert(re.err); return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);});
                                        $( this ).jqGrid("restoreRow", lastSel, {"afterrestorefunc":null});
                                }

                                if (!lastSel || lastSel != id) {
                                        $( this ).jqGrid("editRow", id, {
                                            "keys":true,
                                            "oneditfunc":function() {},
                                            "successfunc":function(re)  { 
                                                var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; 
                                                if (re.err) alert(re.err); 
                                                return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);
                                            },
                                            "aftersavefunc":null,
                                            "errorfunc":null,"afterrestorefunc":null,
                                            "succesfunc":function(re)  { 
                                                var re = (re.responseText) ? $.parseJSON( re.responseText ) : re; 
                                                if (re.err) alert(re.err); 
                                                return (typeof(re)=="object" && re.type !== 'error' && re.success !== false);
                                            }
                                        });
                                        $( this ).data("lastSel", id);
                                } else {
                                        $( this ).jqGrid("restoreRow", id, {"afterrestorefunc":null});
                                        jQuery( this ).jqGrid("resetSelection" );
                                        $( this ).data("lastSel", 0);
                                }
                                //alert(id+", "+status+", lastSel:" + lastSel );
                    }
                })
            },
            "subGridRowColapsed": function(subgrid_id, row_id) {
                    // this function is called before removing the data
                    //var subgrid_table_id;
                    //subgrid_table_id = subgrid_id+"_t";
                    //jQuery("#"+subgrid_table_id).remove();
            }
            
//            ,"loadComplete": function() {
//
//                $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() { 
//                    // alert( '#204 touren/data/dispovorgaenge.jqgrid.js: .ui-th-column each text: ' + $(this).text() );
//                    $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
//                });
//            }
        })
        
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
                "url":APP_BASE_URL + "/vorgaenge/gridresponsedata/view/touren"
            });
        }
        var $grid = $( this );
        $("tr", this).bind( "dragstart", function(e, ui) {
            var rData = $grid.jqGrid('getRowData', $(this).attr('id'));
            rData.Auftragsnummer = rData.ANR;
            if (isNaN(rData.ANR)) {
                console.log("dispovorgaenge.jqgrid.js #412 jqGrid getRowData ", { rData });
            }
            ui.helper.data('dragdata', rData);
        });
        
        $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() { 
            // alert( '#204 touren/data/dispovorgaenge.jqgrid.js: .ui-th-column each text: ' + $(this).text() );
            $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
        });
        
        Fb.initDndRoutes();
//      alert( $( this ).jqGrid( 'getGridParam', 'lastpage' ) + '; ' +  $( this ).jqGrid( 'getGridParam', 'records' ) );
//        $( ".Drag-Route.Is-Template", this ).draggable( Fb.DragRouteTemplateSettings );
        
            var rowid; //ID of row with mouseover
            var my_tooltip = $("#jqTooltip"); // Div created for tooltip
            if (!my_tooltip.length) {
                my_tooltip = $("<div id='jqTooltip' style='display:none;'></div>").css({zIndex:50,position:'absolute',display:'block'}).hide().appendTo( "body" );
            }
            
                        
            my_tooltip
            .data('hasFocus', false)
            .css({padding:8})
            .addClass('ui-widget-content ui-corner-all')
            .mouseleave(function(e){
                var $rt = (e && e.relatedTarget) ? $(e.relatedTarget) : null;
                //if ($rt && $rt.closest( this ).length ) return;
                if (
                    $rt == null
                    || !$rt.closest('tr.jqgrow').length 
                    || $rt.closest('tr.jqgrow')[0] != my_tooltip.data('tooltipTarget')
                ) {
                    $(this).hide();
                }
            });

            if (1) $("tr.jqgrow td[aria-describedby=gridDispoVorgaengeLst_tour_count]", this).mouseenter(
                function(e) { 
                    var self = this;
                    var row = $(this).closest("tr.jqgrow");
                    $(this).data( 'tooltipTimer', setTimeout(
                    
                        function(){
                            var rData = $grid.jqGrid('getRowData', row.attr('id'));
                            var url = APP_BASE_URL + "/touren/ajax/tourlinks";

                            if (my_tooltip.data('tooltipTarget') == row[0]) {
                                //alert('tooltipTarget = this-row return');
                                if (my_tooltip.is(':visible')) return;
                            } else {
                                my_tooltip.data('tooltipTarget', row[0]);
                            }

                            // Check-Cache
                            if (!row.data('tooltipData')) {
                                $.get(url, {mandant:rData['Mandant'], auftrag:rData.ANR}, function(data) {
                                    row.data('tooltipData', data);
                                });
                                $.ajax({
                                    type: 'GET', dataType: 'html', async: false, data: {mandant:rData.Mandant, auftrag:rData.ANR},
                                    url: url,
                                    success:function(data) { 
                                        row.data('tooltipData', data);
                                    },
                                    error:function(a,b,c) {
                                        alert('#312 Tourdaten konnten nicht geladen werden: a:'+a+'; b:'+b+'; c:'+c);
                                    }
                                });
                            }
                            
                            // Da das Laden der Tourdaten etwas dauert,
                            // kann es sein, dass der Tooltip bereits ein anderes Target hat
                            // Daher erst pr�fen, sonst werden im Tooltip falsche Daten angezeigt !!
                            if (my_tooltip.data('tooltipTarget') == row[0]) {
                                my_tooltip.html( row.data('tooltipData') );

                                my_tooltip.show().css({
                                    left: $(self).offset().left+$(self).width(),
                                    top:  row.offset().top
                                });
                            }
                        }

                        , 1000) 
                    )
                }).mouseleave( function(e){
//                    alert( e.relatedTarget );
//                    alert( $(e.relatedTarget).closest("#"+my_tooltip[0].id).length );
                    clearTimeout( $(this).data( 'tooltipTimer' ) );
                    if (e.relatedTarget && !$(e.relatedTarget).closest("#"+my_tooltip[0].id).length  ) {
                        my_tooltip.hide();
                    }
                });
                
        
    },
    "subGridRowColapsed": function(subgrid_id, row_id) {
            // this function is called before removing the data
            //var subgrid_table_id;
            //subgrid_table_id = subgrid_id+"_t";
            //jQuery("#"+subgrid_table_id).remove();
    }
})
});    //]]>

