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
    if (!path) path = "";
    var loopPath='', re = "";
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
//    alert("MyForms.setSelectOptions");
    var defaults = {
        dataMode: 'val'
    };
    var o = $.extend( {}, defaults, opt),
            val, txt;

    if (!opt) opt = {};
    if (!selectBox.length) {
        alert("Ungültige selectBox.length");
        return;
    }
    
    for(var i in data) {
        val = (o.dataMode == 'key' || o.dataMode == 'both' ) ? i : data[i];
        txt = (o.dataMode == 'val' || o.dataMode == 'both' ) ? data[i] : i;
        selectBox.append( $("<option/>").val(val).text(txt) );
    }
    if (selectBox.attr("default")) selectBox.val( selectBox.attr("default") );
};

(function($){
    var dataKey  = 'fbMultiSearchBox';

    var defaults = {
        searchTbl: null,
        searchOpers: {"bw":"bw"},
        defaultOpers: {},
        showOpers: false,
        searchFields: [],        
        jqGridID: null,
        jqGridSearchUrl: '',
        liveSearchUrl: '',
        categoryTreeFields: null,
        searchFieldsFormat: {},
        initialized: false,
        onerror:  null,
        onsend:   null,
        onselect: null,
        opers_i18n: {
            'gt' : 'groesser',
            'ge' : 'groesser gleich',
            'lt' : 'kleiner',
            'le' : 'kleiner gleich',
            'eq' : 'gleich',
            'ne' : 'ungleich',
            'bw' : 'beginnt mit',
            'bn' : 'beginnt nicht mit',
            'ew' : 'endet mit',
            'en' : 'endet nicht mit',
            'cn' : 'enthält',
            'nc' : 'enthält nicht',
            'nu' : 'is null',
            'nn' : 'is not null',
            'in' : 'ist in',
            'ni' : 'ist nicht in'
        }
    };
    var mustOpts = [ 'searchFields', 'jqGridID' ];
    
    var methods = {
        '_init': function() {
            var data  = $( this ).data(dataKey),
                self  = this,
                $this = $( this );
//            alert( "#93 "+dataKey+" id: #"+$this.attr("id")+"; class:"+$this.attr("class"));
            if (data && data.initialized) return;
            
            if (!data) {
                $( this ).data(dataKey, $.extend({}, defaults ));
                data  = $( this ).data(dataKey);
            }
            $("td.columns select", $this).each(function() {
                setSelectOptions( $(this), data.searchFields, {dataMode:'both'});
            });
            
            $("td.operators select", $this).each(function() {
                setSelectOptions( $(this), data.searchOpers, {dataMode:'both', i18n: defaults.opers_i18n});
            });
            
            
            if (!data.showOpers) $("td.operators", $this).css("display", "none");
            methods._initCombobox.apply(self, [$("td.columns select", $this)] );
            methods._bindSearchOnEnter.apply(self, [$("td.data input[type=text][name=data]", $this)] );
            
            $("td.data input[type=text]").bind('enter', function(){
//                alert( $this.attr("class"));
                $(this).closest("form").find("button[name=sendQuery]").trigger("click");
            });
            
            $("button.delete-rule.ui-del", $this).button({
                icons: {primary: "ui-icon-minus"},text: false
            })
            .removeClass("ui-corner-all")
            .addClass("ui-corner-right")
            .css({width:"18px",marginLeft:0})
            .click(function() {$(this).closest("tr").remove();});

            $("button[name=resetQuery]", $this).button({
                icons: {secondary: "ui-icon-cancel"}
            })
            .unbind("click")
            .bind("click", function() {
                var frmBox = $( this ).closest("div.sForm");
                frmBox.find("input[type=text],input[name=date],input[name=dateTo]").val("");
                frmBox.find('select option[value=""]').attr('selected','selected');
                frmBox.find("li.search-choice").remove();
                //alert("Suchangaben zuruecksetzen ist aktuell in Entwicklung!");
            });

            $("button[name=sendQuery]", $this).button({
                icons: {secondary: "ui-icon-search"}
            })
            .unbind("click")
            .bind( "click", function() {
                var frmBox = $( this ).closest("div.sForm");
                var frmData = {_search:true};
                var filters = "{\"groupOp\":\""+$("select[name=groupOp]", frmBox).val()+"\",\"rules\":[";
                var numRules = 0;
                var data = frmBox.data(dataKey);
                if (!data.jqGridSearchUrl) {
                    data.jqGridSearchUrl = $(data.jqGridID).jqGrid("getGridParam", "url");
                }
                var searchUrl = data.jqGridSearchUrl;
                
                if ( $(self).attr('id') == 'sFormVorgaenge') {
                    var CheckedDispoStatus = $( '#FilterDispoStatus input:radio[name=DispoStatus]:checked');
                    if ( CheckedDispoStatus.length ) {
                        frmData['dispoStatus'] = CheckedDispoStatus.val();
                    }
                    var CheckedDispoStatusWV = $( '#FilterDispoStatusWV input:radio[name=DispoStatusWV]:checked');
                    if ( CheckedDispoStatusWV.length ) {
                        frmData['dispoStatusWV'] = CheckedDispoStatusWV.val();
                    }
                }
                
                $("tr", frmBox).each(function(index) {
                    if ("string" === typeof $(this).attr("class") && $(this).attr("class").match(/simpleInput/)) {
                        $("input, select", this).each(function() {
                            frmData[$(this).attr('name')] = $(this).val();
                        });
                        return;
                    }
                    var dataFld = $("td.data input:first,td.data select:first,td.data textarea:first", this);
                    if (!dataFld.length || !dataFld.val()) return;

                    var rule = {field:null, op:null, data:null};
                    rule.data = dataFld.val();

                    if (dataFld.attr("name") != "data") {
                        rule.field = $.trim(dataFld.attr("name"));
                    } else {
                        rule.field = $.trim($("td.columns select[name=field]:first", this).val());
                    }
                    var opTd  = $("td.operators:first", this);
                    var opFld = $("select:first", opTd);
                    rule.op = ($.trim(opFld.length)) ? $.trim(opFld.val()) : null;
                    
                    filters+= (numRules++?",":"");
                    filters+= "{\"field\":\""+rule.field+"\",\"op\":\""+rule.op+"\",\"data\":\""+rule.data+"\"}";
                });
                filters+= "]}";
                frmData.filters = filters;
                searchUrl+= (searchUrl.indexOf("?")==-1?"?":"") +encodeURI(jsonToQuery(frmData));
                if ( data.onsend ) {
                    if ( !data.onsend.apply( searchUrl) ) return;
                }
                
                try {
                    $(data.jqGridID).jqGrid('getGridParam', "postData" ).filters = filters;
                    $(data.jqGridID).jqGrid('setGridParam',{search:true, url:searchUrl} );
                    $(data.jqGridID).trigger("reloadGrid");
                } catch(e) {
                    alert(e);
                }
            });
            
            $("input.switch-op", $this).unbind("click").bind("click", function() {
                $("tr td:eq(2):not(td.data), tr td.operators", $this).toggle();
            });
            
            $("input.add-rule", $this).unbind('click').click(function() 
            {
                methods.addRule.apply( $this[0] );
            });
            data.initialized = false;
        },
        '_initCombobox': function(selectbox) {
            // Zentrale Methode, in der die combobox eingerichtet wird
            // und das Event-Handling für On-Select gebunden wird
            // zudem wird bei Initiierung der Combobox das select-Event gefeuert
            var data = $(this).data(dataKey);
            var self = this;
            
            $(selectbox).each(function() {
                var cell  = $(this).closest('tr').find('td.data');
                var input = $('input[name=data]', cell);
                input.data('lastSearchField', '');
                $( this ).combobox().unbind('comboboxselected').bind('comboboxselected', function(e,option) {
                    
                    input = $('*[name=data]', cell);
                
                    var searchField = (option) ? option.item.value : $(this).val();
                    var lastSearchField = input.data('lastSearchField');
                    
                    var lastFieldIsCategory   = (lastSearchField in data.categoryTreeFields);
                    var lastFieldIsFormat     = (lastSearchField in data.searchFieldsFormat);
                    
                    if (lastFieldIsCategory) {
                        methods._rmTreeSelection.apply(self, [input]);
                        $(this).closest('tr').find('td.operators select[name=op]').val('bw');
                    }
                    if (lastFieldIsFormat) {
                        methods._rmSearchFormat.apply(self, [input]);
                        $(this).closest('tr').find('td.operators select[name=op]').val('bw');
                        input = $('*[name=data]', cell);
                    }
                    
                    if (searchField in data.categoryTreeFields) {
                        methods._initTreeSelection.apply(self, [input, searchField]);
                        $(this).closest('tr').find('td.operators select[name=op]').val('eq');                        
                    }
                    
                    if (searchField in data.searchFieldsFormat) {
                        methods._initSearchFormat.apply(self, [input, searchField]);
                        $(this).closest('tr').find('td.operators select[name=op]').val('eq');
                        input = $('*[name=data]', cell);
                    }
                    
                    if (data.defaultOpers && data.defaultOpers[searchField]) {
                        $(this).closest('tr').find('td.operators select[name=op]').val('eq');
                    }
                    
                    if (data && typeof(data.onselect) == 'function')  {
                        data.onselect.apply(self, [this, input, option.item.value]);
                    }
                    input.data('lastSearchField', searchField);
                    return true;
                });
                if ($(this).attr("readonly")) {
                    $(this).parent()
                    .find("input").attr("readonly", "readonly")
                    .end()
                    .find("input.ui-autocomplete-input").attr("readonly","readonly")
                    .end()
                    .find("button").button("option","disabled",true);
                }
                $( this ).trigger('comboboxselected');
            });
        },
        '_rmTreeSelection': function(input) {
            $( input ).fbChooser( 'destroy' );
        },
        '_bindSearchOnEnter': function(fields) {
            var $this = $(this);
            fields.bind('keypress', function(e){
                if (e.keyCode == 13) {
                    $this.find("button[name=sendQuery]").trigger("click");
                }
            })
        },
        'addRule': function(defaultField, opts) 
        {
            var data = $(this).data(dataKey);
            var self = this;
//            alert( "#264 this.id: " + $(this).attr("id") + "; data: " + data);
            
            var tdF = $("<td class='columns'><select name='field'></select></td>");
            var tdO = $("<td class='operators'><select name='op'></select></td>");
            var tdM = $("<td><button class='delete-rule ui-del' type='button' title='Delete rule'>-</button></td>");
            var tdD = $("<td class='data'><input  name='data' type='text' class='sQuery' /></td>");
            if (!data.showOpers) tdO.css("display", "none");

            setSelectOptions( $("select", tdF), data.searchFields, {dataMode:'val'});
            if (data.showOpers) setSelectOptions( $("select", tdO), data.searchOpers, {dataMode:'both', i18n: defaults.opers_i18n});

            methods._initCombobox.apply(self, [$("select", tdF)] );

            $("tr:last", data.searchtbl)
            .before(
                $("<tr/>").append( tdF).append(tdO).append(tdD).append(tdM)
            );
            $("button", tdM)
            .button({ icons: {primary: "ui-icon-minus"},text: false })
            .removeClass("ui-corner-all")
            .addClass("ui-corner-right")
            .css({width:"18px",marginLeft:0})
            .click(function(){
                $(this).closest("tr").remove(); 
            });
            
            methods._bindSearchOnEnter.apply(self, [tdD.find("input[type=text]")] );
        },
        '_initSearchFormat':  function( inputField, searchFieldName ) {
            inputField.val('');
            var data = $(this).data(dataKey);
            var formatOpts = data.searchFieldsFormat[searchFieldName];
                        
            var sel = $("<select/>").attr({
                'name' : inputField.attr("name"),
                'class': inputField.attr("class"),
                'rel' : 'specialSearchFormat'
            });
            for(var i in formatOpts["options"]) {
                sel.append( new Option(formatOpts["options"][i], formatOpts["options"][i]));
            }
            inputField.after( sel );
            inputField.remove();
        },
        '_rmSearchFormat': function(input) {
            var iText = $("<input/>").attr({
                'type' : input.attr("text"),
                'name' : input.attr("name"),
                'class': input.attr("class"),
                'rel'  : 'specialSearchFormat'
            });
            input.before( iText );
            input.remove();
        },
        '_initTreeSelection': function( inputField, searchFieldName ) {
            inputField.val('');
            var data = $(this).data(dataKey);
            var treeSelectUrl = data.categoryTreeFields[searchFieldName];
            
//            if (1) 
            $( inputField ).fbChooser( {
                "useUserBox": true,
                "showSearchInput": false,
                "showOnload": false,
                "source": treeSelectUrl, 
                "sourceLoadedCallback": 
                    function() {
                        var self = this;
                        //alert( '#41 self: '+$(self).get(0).tagName+' '+$(self).attr('class'));
                        $("#treeSelectDialog").bind("selectTreeNode", function(event, data) {
//                            alert('#266 sourceLoadedCallback() '+data.id+' = ' + data.name);
                            $( self ).fbChooser('setData', [{value:data.id, label:data.name}] );
                        });
                    }
            });
        }
    };
    
    var setSelectOptions = function (selectBox, data, opt) {
        var defaultOpts = {
                dataMode: 'val',
                i18n: null
            },
            o = $.extend( {}, defaultOpts, opt),
            i='', val = '', txt = '';

        if (!opt) opt = {};
        if (!selectBox.length) {
            alert("Ungültige selectBox.length");
            return;
        }
        var readOnly = selectBox.attr("readonly");
        var optDefault= selectBox.attr("default");
        
        for(i in data) {
            val = (o.dataMode == 'key' || o.dataMode == 'both' ) ? i : data[i];
            txt = (o.dataMode == 'val' || o.dataMode == 'both' ) ? data[i] : i;
            if (readOnly && optDefault && val != optDefault) continue;
//            if (i == 'Auftragsnummer') alert(i+' : ' + data[i] + ' ('+o.dataMode+','+opt.dataMode+') => ' + val + ' : ' + txt);
            selectBox.append( $("<option/>").val(val).text(txt).attr('title', (o.i18n && val in o.i18n) ? o.i18n[val] : '' ) );
        }
        
        var selector = "option[value='"+selectBox.attr("default")+"']";
        //if (selectBox.attr('name') == 'field') alert(selector + " length " + $("option[value='"+selectBox.attr("default")+"']", selectBox).length);
        if (selectBox.attr("default")) $( selector , selectBox).attr("selected", true); 
        //selectBox.val( selectBox.attr("default") );
    };
    
    if ( !$.fn.combobox ) {
        jQuery("head").append('<link href="' + APP_BASE_URL + '/jquery/combobox/jquery.ui.combobox.css" type="text/css" rel="Stylesheet" />');
        jQuery.getScript(APP_BASE_URL + "/jquery/combobox/jquery.ui.combobox.js", function(data, textStatus){
            //jQuery.getScript(APP_BASE_URL + "/touren/jquery.crm.plugins/searchvorgaenge.js");
        });
    }

    $.fn[dataKey] = function(options) {
        // Abfragen bestehender Data-Werte beantworten
        if (typeof(options)=='string') {
            var data = $( this[0] ).data(dataKey);
            return (data && (options in data)) ? data[options] : null;
        }
            
        return this.each(function(){
            if (!$( this ).data(dataKey)) {
                $( this ).data(dataKey, $.extend({}, defaults, options) );
            } else if (typeof(options)=='object') 
                $( this ).data(dataKey, $.extend({}, $( this ).data(dataKey), options) );
            
            var data  = $( this ).data(dataKey),
                $this = $( this );
            
            // alert( "#372 data.jqGridSearchUrl: " + data.jqGridSearchUrl );
            // Es kann jederzeit an das Objekt fbMultiSearchBox
            // eine neue Url für das jqGrid uebergeben
            // Diese wird als Basis-Url für die Searchbox
            // und zugleich im jqGrid gesetzt
            if ('jqGridSearchUrl' in options && data.jqGridID) {
                // Set New jqGrid Data-Src-Url as Base-Url
                //alert( 'New Url for ' + data.jqGridID + ': ' + data.jqGridSearchUrl );
                $(data.jqGridID).jqGrid('setGridParam',{search:true, url:data.jqGridSearchUrl} ).trigger("reloadGrid");
            } else {
                //alert( "#382 " + dataKey );
            }

            // Falls Objekt bereits initialisiert wurde
            // Hier abbrechen
            if (data.initialized) return;

            // Check PreConditions
            var missing = [];
            for(var mi in mustOpts) if (data[mi] === null) missing.push(mi);

            if (missing.length) {
                alert( "Missing options for $.fn." + dataKey + ":\n " + missing.join("\n") );
                return;
            }
            
            if (!$this.length) return;

            data.searchtbl = $("table.group", $this);
            
            //alert ( 'data.searchtbl.length: ' + data.searchtbl.length );
            if (!data.searchtbl.length) return;
            
            
            if (!data.jqGridSearchUrl) {
                data.jqGridSearchUrl = $(data.jqGridID).jqGrid("getGridParam", "url");
            }
            
            // Call Rendering
            if (!data.initialized) methods._init.apply( this );
            return;
        });
    };
})(jQuery);


/*
 * http://localhost/mertens_rm/public/vorgaenge/findvorgaenge/view/touren/Mandant/110?&_search=true&date=&=&dateTo=&filters=%7B%22groupOp%22:%22AND%22,%22rules%22:%5B%7B%22field%22:%22Auftragsnummer%22,%22op%22:%22bw%22,%22data%22:%22444%22%7D%5D%7D&_search=true&nd=1411919209736&rows=10&page=1&sidx=&sord=asc&filters=%7B%22groupOp%22%3A%22AND%22%2C%22rules%22%3A%5B%5D%7D
 */