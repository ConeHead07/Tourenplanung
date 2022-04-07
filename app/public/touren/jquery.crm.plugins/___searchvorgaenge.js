//alert( '#1 searchvorgaenge.js' );

function objToString(obj, depth) {
    if (!depth) depth = "";
    if (typeof(obj) !== "object") return obj;
    var m = "";
    for(i in obj) {
        m+= depth+i+"("+typeof(obj[i])+") : ";
        if ( typeof( obj[i] ).toString().match( /string|number|boolean/) )
            m+= obj[i] + "\n";
        else if (typeof(obj[i]) == 'object')
            m+= objToString(obj[i], depth+"-");
    }
    return m;
}

function jsonToQuery(json, path) {
    if (!path) path = "", loopPath='', re = "", t='';
    for(var i in json) {
        loopPath = (path) ? path+"["+i+"]" : i;
        if ( typeof(json[i]).toString().match( /string|number|boolean/) )
            re+= "&" + loopPath+"="+json[i];
        else if ( typeof(json[i]) == 'object')
            re+= jsonToQuery(json[i], loopPath);
    }
    return re;
}

if (typeof(MyForms)=="undefined") MyForms = {};
MyForms.setSelectOptions = function (selectBox, data, opt) {
    var defaults = {
        dataMode: 'val'
    };
    var o = $.extend( {}, defaults, opt);

    if (!opt) opt = {};
    if (!selectBox.length) {
        alert("Ungültige selectBox.length");
        return;
    }
    var dM = o.dataMode;
    for(i in data) {
        val = (o.dataMode == 'key' || o.dataMode == 'both' ) ? i : data[i];
        txt = (o.dataMode == 'val' || o.dataMode == 'both' ) ? data[i] : i;
        selectBox.append( $("<option/>").val(val).text(txt) );
    }
    if (selectBox.attr("default")) selectBox.val( selectBox.attr("default") );
};

(function($){
    $(function() {
        TVG.searchbox = $("div.touren-vorgaenge-widget.searchFilter");
        if (!TVG.searchbox.length) return;

        TVG.searchtbl = $("table.group", TVG.searchbox);
        if (!TVG.searchtbl.length) return;

        $("td.columns select").each(function() {
            MyForms.setSelectOptions( $(this), TVG.searchFields, {dataMode:'val'});
        });
        if ( $("td.columns select").length ) $("td.columns select").combobox();

        $("td.operators select").each(function() {
            MyForms.setSelectOptions( $(this), TVG.searchOpers, {dataMode:'key'});
        });
        if (!TVG.showOpers) $("td.operators").css("display", "none");
        
        $("button.delete-rule.ui-del").button({
            icons: {primary: "ui-icon-minus"},text: false
        })
        .removeClass("ui-corner-all")
        .addClass("ui-corner-right")
        .css({width:"18px",marginLeft:0})
        .click(function() { $(this).closest("tr").remove(); });
        
        $("button[name=sendQuery]").button({
            icons: {secondary: "ui-icon-search"}
        })
        .click(function() {
            var frmBox = $( this ).closest("div.sForm");
            var frmData = { _search:true };
            var filters = "{\"groupOp\":\""+$("select[name=groupOp]", frmBox).val()+"\",\"rules\":[";
            var numRules = 0;
            $("tr", frmBox).each(function(index) {
                var dataFld = $("td.data input:first", this);
                if (!dataFld.length || !dataFld.val()) return;
                
                var rule = { field:null, op:null, data:null };
                rule.data = dataFld.val();
                
                if (dataFld.attr("name") != "data") {
                    rule.field = $.trim(dataFld.attr("name"));
                } else {
                    rule.field = $.trim($("td.columns select[name=field]:first", this).val());
//                    alert( $("td.columns select[name=field]:first", this).val() );
                }
                var opTd = $("td.operators:first", this);
                var opFld = $("select:first", opTd);
                rule.op = ($.trim(opFld.length)) ? null : $.trim(opFld.val());
//                frmData.filters.rules.push(rule);

                filters+= (numRules++?",":"");
                filters+= "{\"field\":\""+rule.field+"\",\"op\":\""+rule.op+"\",\"data\":\""+rule.data+"\"}";
            });
            filters+= "]}";
            frmData.filters = filters;
            //window.open(url_query, "WinSearch");
            var jqGridID = "#gridDispoVorgaengeLst";
            var searchUrl = APP_BASE_URL + '/vorgaenge/gridresponsedata?';
            searchUrl+= encodeURI(jsonToQuery(frmData));
            //alert( '#113 searchvorgaenge.js searchUrl: ' + searchUrl);
            $(jqGridID).jqGrid('setGridParam',{search:true, url:searchUrl } ).trigger("reloadGrid");
//          $(jqGridID).jqGrid('setGridParam',{search:true, postData:frmData } ).trigger("reloadGrid");
        });
        
        $("input.add-rule").click(function() 
        {
            var tdF = $("<td class='columns'><select name='field'></select></td>");
            var tdO = $("<td class='operators'><select name='op'></select></td>");
            var tdM = $("<td><button class='delete-rule ui-del' type='button' title='Delete rule'>-</button></td>");
            var tdD = $("<td class='data'><input  name='data' type='text' class='sQuery' /></td>");
            if (!TVG.showOpers) tdO.css("display", "none");

            MyForms.setSelectOptions( $("select", tdF), TVG.searchFields, {dataMode:'val'});
            if (TVG.showOpers) MyForms.setSelectOptions( $("select", tdO), TVG.searchOpers, {dataMode:'key'});
            $("select", tdF).combobox();

            $("tr:last", TVG.searchtbl)
            .before(
                $("<tr/>").append( tdF).append(tdD).append(tdM)
            );
            $("button", tdM).button({
                icons: {primary: "ui-icon-minus"},text: false
            })
            .removeClass("ui-corner-all")
            .addClass("ui-corner-right")
            .css({width:"18px",marginLeft:0})
            .click(function(){ 
//                $( "select[name=field]", tdF ).toggle();
                $(this).closest("tr").remove(); 
            });

        });
    });
})(jQuery);

/*
APP_BASE_URL + "/vorgaenge/gridresponsedata?&_search=true&filters=%7B%22
groupOp%22:%22AND%22,%22rules%22:%5B%7B%22field%22:%22Auftragsnummer%22,%22
op%22:%22null%22,%22data%22:%22184%22%7D%5D%7D&_search=true&nd=1327458449090&rows=10&page=1&sidx=&sord=asc

APP_BASE_URL + /vorgaenge/gridresponsedata?_search=true&nd=1327458291496&
rows=10&page=1&sidx=&sord=asc&filters=%7B%22groupOp%22%3A%22AND%22%2C%22rules
%22%3A%5B%7B%22field%22%3A%22Auftragsnummer%22%2C%22op%22%3A%22bw%22%2C%22data%22%3A%22184%22%7D%5D%7D
*/