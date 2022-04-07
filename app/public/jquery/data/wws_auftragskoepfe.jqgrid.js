//<![CDATA[

var createGridAuftragskoepfe = function(targetSelector, pagerSelector, dataUrl, opts) {
    
    jQuery(function() {
      jQuery(targetSelector).jqGrid(
      {
        "colNames":[
            "Mandant",
            "WWS",
            "Vorgangstitel",
            "L-Name",
            "Adr",
            "PLZ",
            "Ort",
            "Land",
            "KW",
            "Jahr",
            "Termin",
            "BestaetigtAm",
            "AW", 
            "Fix",
            "LieferterminHinweisText"
        ],
        "colModel":[
            {"name":"Mandant","index":"Mandant","editable":false,"hidden":true},
            {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false, "key":true,"width":50},
            {"name":"Vorgangstitel","index":"Vorgangstitel","editable":false},
            {"name":"LieferungName","index":"LieferungName","editable":false,"hidden":false},
            {"name":"LieferungStrassePostfach","index":"LieferungStrassePostfach","editable":false,"width":150},
            {"name":"LieferungPostleitzahl","index":"LieferungPostleitzahl","editable":false,"width":35},
            {"name":"LieferungOrt","index":"LieferungOrt","editable":false,"width":50},
            {"name":"LieferungLand","index":"LieferungLand","editable":false,"hidden":true},
            {"name":"Lieferwoche","index":"Lieferwoche","editable":false,"width":30},
            {"name":"Lieferjahr","index":"Lieferjahr","editable":false,"width":30},
            {"name":"Liefertermin","index":"Liefertermin","editable":false,"width":50,
                "formatter":function(value, options, rData){
                    return (typeof(value)=='string' ? value.replace(/00:00:00/, '') : ''); 
            }},
            {"name":"BestaetigtAm","index":"BestaetigtAm","editable":false,"hidden":true,
                "formatter":function(value, options, rData){
                    return (typeof(value)=='string' ? value.replace(/00:00:00/, '') : '');
            }},
            {"name":"Auftragswert","index":"Auftragswert","editable":false,"hidden":true},
            {"name":"LieferterminFix","index":"LieferterminFix","editable":false,"width":30},
            {"name":"LieferterminHinweisText","index":"LieferterminHinweisText",
                "editable":false,"hidden":true}
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
        "datatype":"local",
        "url":dataUrl,
        "shrinkToFit":true,
        "pager":pagerSelector,
        "loadError":function(xhr,status,error){alert(status+'\n'+error);},
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
                "colNames":["Mandant","Auftragsnummer","Bestellnummer","BestellName", "KW ErwarteterEingangWoche", "Jahr - ErwarteterEingangJahr", "Termin - ErwarteterEingang", "Fix - ErwarteterEingangterminFix"],
                "colModel":[
                        {"name":"Mandant","index":"Mandant","editable":false,"hidden":true},
                        {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false,"hidden":true},
                        {"name":"Bestellnummer","index":"Bestellnummer","editable":false, "key":true},
                        {"name":"BestellName","index":"BestellName","editable":false},
                        {"name":"ErwarteterEingangWoche","index":"ErwarteterEingangWoche","editable":false},
                        {"name":"ErwarteterEingangJahr","index":"ErwarteterEingangJahr","editable":false},
                        {"name":"ErwarteterEingang","index":"Lieferjahr","editable":false},
                        {"name":"ErwarteterEingangterminFix","index":"ErwarteterEingangterminFix","editable":false}
                ],
                "height":"auto",
                "jsonReader":{"repeatitems":false,"id":0},
                "autowidth":true,
                "autoload":false,
                "rowList":[10,20],
                "rowNum":10,
                "rownumbers":false,
                "resizable":true,
                "sortable":true,
                "datatype":"json",
                "shrinkToFit":true,
                "pager":pager_id,
                "loadError":function(xhr,status,error){alert(status+'\n'+error);},
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
                "subGridRowExpanded": function(subgrid_id, row_id) {
                    // we pass two parameters
                    // subgrid_id is a id of the div tag created whitin a table data
                    // the id of this elemenet is a combination of the "sg_" + id of the row
                    // the row_id is the id of the row
                    // If we wan to pass additinal parameters to the url we can use
                    // a method getRowData(row_id) - which returns associative array in type name-value
                    // here we can easy construct the flowing
                    var subgrid_table_id, pager_id;
                    subgrid_table_id = subgrid_id+"_t";
                    pager_id = "p_"+subgrid_table_id;

                    var row = $( this ).getRowData(row_id);
                    var mandant = row['Mandant'];
    //                alert('row_id:'+row_id+', mandant:'+row['Mandant']+', Auftragsnumer:'+row['Auftragsnummer']+', Bestellnummer:'+row['Bestellnummer']);

                    $("#"+subgrid_id).html(
                        "<table id='"+subgrid_table_id+"' class='scroll subgrid'></table>"
                       +"<div id='"+pager_id+"' class='scroll'></div>");

                    jQuery("#"+subgrid_table_id).jqGrid({ /*grid-Anweisungen*/ 
                        "url":APP_BASE_URL + "/bestellpositionen/gridresponsedata/parentid/"+row_id+"/mandant/"+mandant,
                        "colNames":[
                            "Mandant",
                            "Auftragsnummer","Bestellnummer","Positionsnummer",
                            "Artikelnummer","Bezeichnung","Liefermenge",
                            "Liefertermin - ErwarteterEingang",
                            "KW - ErwarteterEingangWoche",
                            "Jahr - ErwarteterEingangJahr",
                            "LieferterminFix - ErwarteterEingangterminFix"],
                        "colModel":[
                                {"name":"Mandant","index":"Mandant","editable":false, "key":true,"hidden":true},
                                {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false,"hidden":true},
                                {"name":"Bestellnummer","index":"Bestellnummer","editable":false, "key":true,"hidden":true},
                                {"name":"Positionsnummer","index":"Positionsnummer","editable":false, "key":true,"hidden":true},
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
                        "loadError":function(xhr,status,error){alert(status+'\n'+error);}
                    })
                },
                "subGridRowColapsed": function(subgrid_id, row_id) {
                        // this function is called before removing the data
                        //var subgrid_table_id;
                        //subgrid_table_id = subgrid_id+"_t";
                        //jQuery("#"+subgrid_table_id).remove();
                }

                ,"loadComplete": function() {

                    $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() { 
                        // alert( '#204 touren/data/dispovorgaenge.jqgrid.js: .ui-th-column each text: ' + $(this).text() );
                        $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
                    });
                }
            })

        },
        "loadComplete": function() {
            $('.ui-th-column', '#gview_' + $(this).attr('id') ).each(function() {
                $(this).attr('alt',$(this).text()).attr('title',$(this).text()); 
            });
            if (0) $("tr.jqgrow", this).click(function(e){});
        },
        'onSelectRow': opts.onSelectRow || null,
        "subGridRowColapsed": function(subgrid_id, row_id) {
                // this function is called before removing the data
                //var subgrid_table_id;
                //subgrid_table_id = subgrid_id+"_t";
                //jQuery("#"+subgrid_table_id).remove();
        }
    })
    .jqGrid("filterToolbar",{stringResult: true,searchOnEnter : false}) 
    })
}


//    .jqGrid(
//        "navGrid",
//        pagerSelector,
//        {
//            "edit":true,"addicon":"ui-icon-suitcase",
//            "search":{
//                multipleSearch:true,overlay:false,beforeShowSearch:function() {  
//                    $(targetSelector)[0].toggleToolbar(); 
//                },
//                onClose:function() {    	
//                    $(targetSelector)[0].toggleToolbar();   
//                }
//            }
//        },
//        {},
//        {},
//        {},
//        {
//            "sopt":["eq"],
//            "beforeShowSearch":function() {  
//                $( targetSelector )[0].toggleToolbar(); },
//            "onClose":function() { 
//                $( targetSelector )[0].toggleToolbar();   },
//            "multipleSearch":true
//        },
//        {}
//    )
//    .navButtonAdd(pagerSelector,{
//        "id":"btnTbarSearch","buttonicon":"ui-icon-pin-s",
//        "caption":"ColSearch","title":"Toggle Searching Toolbar",
//        "onClickButton":function () { 
//            $( targetSelector)[0].toggleToolbar(); 
//    }})
//    .navButtonAdd(pagerSelector,{
//        "id":"btnColCh","caption":"Spalten","title":"Spalten",
//        "onClickButton":function(){
//            jQuery( targetSelector ).jqGrid("columnChooser", {
//                "done":function (perm) {
//                    if (perm) {
//                            // "OK" button are clicked
//                            this.jqGrid("remapColumns", perm, true);
//                            // the grid width is probably changed co we can get new width
//                            // and adjust the width of other elements on the page
//                            var gwdth = this.jqGrid("getGridParam","width");
//                            this.jqGrid("setGridWidth",gwdth);
//                    } else {
//                            // we can do some action in case of "Cancel" button clicked
//                    }
//                }
//            })
//        }
//    })