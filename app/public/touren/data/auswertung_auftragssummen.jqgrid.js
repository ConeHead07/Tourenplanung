
//<![CDATA[
jQuery(function() { jQuery("#gridAuswertungSummenLst").jqGrid(
{
   "colNames":[
        "Mandant",
        "WWS",
        "Auftragspos.",
        "AP-Menge",
        "Disponierte Pos.",
        "DP-menge",
        "DP-PCT",
        "DP-Mengen-Pct",
        "Status &gt; 80%",
        "DispoDatum",
        "KNr",
        "Kunde", 
        "Auftragswert"
    ],
//[{
//    "Mandant"
//    "Auftragsnummer"
//    "ap_cnt"
//    "ap_sum"
//    "dp_cnt"
//    "dp_sum_menge"
//    "pos_pct"
//    "sum_pct"
//    "stat"
//    "auftrag_disponiert_am"
//    "auftrag_abgeschlossenn_am"
//    "Kundennummer"
//    "LieferungName"
//    "AngebotName"
//    "Auftragswert"
//}
    "colModel":[
        {"name":"Mandant","index":"Mandant","editable":false,"hidden":true, "key":true},
        {"name":"Auftragsnummer","index":"Auftragsnummer","editable":false, "key":true},
        {"name":"ap_cnt","index":"ap_cnt","editable":false}, 
        {"name":"ap_sum","index":"ap_sum","editable":false,"hidden":true}, 
        {"name":"dp_cnt","index":"dp_cnt","editable":false}, 
        {"name":"dp_sum_menge","index":"dp_sum_menge","editable":false,"hidden":true},  
        {"name":"pos_pct","index":"pos_pct","editable":false,"hidden":true},  
        {"name":"sum_pct","index":"sum_pct","editable":false,"hidden":true},   
        {"name":"stat","index":"stat","editable":false,
         "formatter":function(value, options, rData) { return (value >=1? 'Ja':'Nein');}},  
        {"name":"DispoDatum","index":"DispoDatum","editable":false},      
        {"name":"Kundennummer","index":"Kundennummer","editable":false},        
        {"name":"Kunde","index":"Kunde","editable":false},
        {"name":"Auftragswert","index":"Auftragswert","editable":false}
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
    "datatype":"json",
    "url":APP_BASE_URL + "/auswertungen/auftragssummendata?" +
            "monat=" + jQuery("#gridAuswertungSummenLst").attr("data-month") +
            "&sidx" + jQuery("#gridAuswertungSummenLst").attr("data-sidx") +
            "&sord" + jQuery("#gridAuswertungSummenLst").attr("data-sord"),
    "shrinkToFit":true,
    "pager":"#gridAuswertungSummenLst_pager",
    "loadError":function(xhr,status,error){
        if (Fb && typeof(Fb.logAjaxError)=='function') {
            Fb.logAjaxError('#98 data/auswertung_auftragssummen.js', xhr, status, error);
        }
    },
    "subGrid": false
    })
});    //]]>




