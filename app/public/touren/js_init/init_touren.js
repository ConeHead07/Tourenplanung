if (!Fb) var Fb = {};

Fb.layoutPrintToggle = function(btn) {
    
    if ( $("#LayoutContainer").toggleClass("print-page").is(".print-page") ) {
        pageLayout.close("north");
        pageLayout.close("west");
        $("#LayoutContainer").css({"overflow":"visible"});
        $("#LayoutCenter")
                .data("layoutHeight", $("#LayoutCenter").height() )
                .height( $("#LayoutCenter").get(0).scrollHeight );
        $(".DispoCalendarPager.sticky").removeClass("sticky").addClass("unset-sticky");
        
        window.print();
        
        $(btn)
                .data("label-before", $(btn).button("option", "label") )
                .button("option", {"label":"Druck-Reset"});
        
    } else {
        $(".DispoCalendarPager.unset-sticky").removeClass("unset-sticky").addClass("sticky");
        $("#LayoutCenter").height( $("#LayoutCenter").data("layoutHeight") );
        $("#LayoutContainer").css({"overflow":"hidden"});
        pageLayout.open("north");
        pageLayout.open("west");
        
        $(btn).button("option", {"label": $(btn).data("label-before")} );
    }
};

Fb.initDndRoutes = function () {
    $( ".Drag-Route.Is-Template" ).draggable( Fb.DragRouteTemplateSettings );
};
Fb.showReminderDialog = function (target, MNr, ANr, date) {
    var dateWV = '';
    var dlgRowData = {};
    
    if ( !$("#reminderDialog").length ) {
        var abschlussProzente = new Array()
        $.ajax(
            APP_BASE_URL + "/touren/ajax/getabschlussprozente",
            {
                "async": false,
                "type": "GET",
                "dataType": "json",
                "success": function(data, textStatus, jqXHR) {
                    abschlussProzente = data;
                }
            }
        );

        var abOptions = '';
        for(var i in abschlussProzente) {
            abOptions+= '<option value="' +i + '">' + abschlussProzente[i] + '</option>';
        }

        $("body").append(
            '<div id="reminderDialog" title="Vorgang zur&uuml;ckstellen">'+
            ' <div>'+
            ' <div id="vorgangAbschliessen" class="ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" ' +
            '  style="padding:2px;margin-top:10px;">' +
            '  <div class="ui-widget-header ui-corner-all" style="height:auto;overflow:hidden;">' +
            '   <span class="ui-dialog-title" style="margin:4px 8px;">Zur&uuml;ckstellen bis (Aktuell: <span id="activeDateWV">--</span>)</span>' +
            '  <span style="float:right;" id="removeWV">WV aufheben</span>'+
            '  </br/ style="clear:both;">'+
            ' </div>'+ 
            ' <div id="reminderDate" />'+
            '  </div>' +
            ' </div>'+ 
            ' <div id="vorgangAbschliessen" class="ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" ' +
            '  style="padding:2px;margin-top:10px;">' + // class="ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">'+
            '  <div class="ui-widget-header ui-corner-all" style="height:auto;overflow:hidden;">' +
            '   <span class="ui-dialog-title" style="margin:4px 8px;">Vorgang abschliessen</span>' +
            '  </div>' + //  class="ui-widget-header ui-corner-all"
            '  <div style="padding:5px;">' + 
            '   <div style="margin:5px 0;">' +
            '    <label style="display:inline-block;width:120px;">Auftragswert</label>' +
            '    <span id="dlg_auftragswert" style="font-size:10px;width:100px;"></span>' +
            '   </div>' +
            '   <div style="margin:5px 0;">' +
            '    <label style="display:inline-block;width:120px;">Abschluss-Summe</label>' +
            '    <input type="text" id="dlg_abschluss_summe" name="abschluss_summe" style="font-size:10px;width:100px;">' +
            '   </div>' +
            '   <div>' +
            '    <label style="display:inline-block;width:120px">Abschluss in %</label>' +
            '   <select id="dlg_abschluss_prozent" name="abschluss_prozent" style="font-size:10px;width:100px;">' +
            '   ' + abOptions +
            '   </select>' + 
            '   </div>' +
            '   <button id="btnVgAbschliessen">senden</button>' + 
            '  </div>'+
            ' </div>'+
            '</div>');
    }
        
        $("#reminderDialog").dialog({
            autoOpen:true,
            height:"auto",
            title:"Vorgang " + ANr
        });
        
        $("#removeWV").button().unbind("click").click(function(){
            $("#reminderDialog").dialog("close");
            Fb.AjaxTourRequest({
                "url": APP_BASE_URL + "/touren/ajax/setwiedervorlage/",
                "data": {
                    Mandant:MNr,
                    Auftragsnummer:ANr,
                    date: ""
                }
            }, {
            });
        });
        
        $("#btnVgAbschliessen").button().unbind("click").click(function(){
            var box = $(this).closest("div#vorgangAbschliessen");
            var summe = $("input[name=abschluss_summe]", box).val();
            var prozent = $(":input[name=abschluss_prozent]", box).val();
            Fb.AjaxTourRequest({
                "url": APP_BASE_URL + "/touren/ajax/finishauftragsabschluss/",
                "data": {
                    Mandant:MNr,
                    Auftragsnummer:ANr,
                    abschluss_summe: summe,
                    abschluss_prozent: prozent
                }
            }, {
                onsuccess: function(data,textStatus, jqXHR) {
                    alert("Vorgang " + ANr + " wurde abgeschlossen und wird nicht mehr in Vorgangsliste angezeigt!");
                },
                onerror: function(jqXHR, textStatus) {
                    alert("Vorgang " + ANr + " konnte nicht abgeschlossen werden!\n" + textStatus);
                }
            });
        });

    Fb.AjaxTourRequest({
         "url": APP_BASE_URL + "/touren/ajax/getwiedervorlage/",
         "data": { Mandant:MNr, Auftragsnummer:ANr }
     }, {
         onsuccess:function(data,textStatus, jqXHR) {
             if (data.date) dateWV = data.date;
             if (data.data) {
                 dlgRowData = data.data;
                 $("#dlg_auftragswert").text(data.data.Auftragswert);
                 $("#dlg_abschluss_summe").val(data.data.auftrag_abschluss_summe);
                 $("#dlg_abschluss_prozent").val(data.data.auftrag_abschluss_prozent);
                 $("#activeDateWV").val(data.data.auftrag_wiedervorlage_am);
             }
//             alert(data.date);            
         }
    });
    
    $("#reminderDate").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth: true,
        showWeek: true,
        onSelect:function(date, obj) {
//                alert("MNr:"+MNr+"; ANr: "+ANr + "\nonSelect date: " + date + "!");
            $("#reminderDialog").dialog("close");
//                $(this).closest("div.ui-dialog").css({display:"none"});

            Fb.AjaxTourRequest({
                "url": APP_BASE_URL + "/touren/ajax/setwiedervorlage/",
                "data": {
                    Mandant:MNr,
                    Auftragsnummer:ANr,
                    date: date
                }
            }, {                    
            });
        }
    });
    
    if (dateWV) $("#reminderDate").datepicker("setDate", dateWV);
    $("#reminderDialog").find("#activeDateWV").attr("time",dateWV).text(
        Fb.convertDate(dateWV, "D dd.mm.yy", "yy-mm-dd")
        //dateWV.split('-').reverse().join('.')
    );
    
    if (dateWV) {
        $("#reminderDialog").find("#removeWV").show();
    } else {
        $("#reminderDialog").find("#removeWV").hide();
    }
    
    $("#reminderDialog").dialog("open");
};

Fb.CalendarSurveyLoad = function(surveyID, context, baseUrl, interval) {
    var _ID = surveyID, d, m;
    // alert([surveyID, context.html(), baseUrl, interval].join(", "));
    // mixed var interval
    // - Date Object
    // - String formatted as Date YYYY-MM-DD
    // - integer interval zeiteinheiten +/-
    if (typeof(interval) == "string" && interval.match(/^(\d\d\d\d)-0?(\d{1,2})-0?(\d{1,2})$/)) {
        m = interval.match(/^(\d\d\d\d)-0?(\d{1,2})-0?(\d{1,2})$/);
        d = new Date( m[1], m[2]-1, m[3]);
    } else {
        // Wenn typeof interval == object, sollte es ein Date-Objekt sein!!
        d = (typeof(interval) == "object")
            ? interval
            : (
               $("#DispoDateTxt"+_ID, context).val().match(/^(\d\d\d\d)-0?(\d{1,2})-0?(\d{1,2})$/)
               ? $.datepicker.parseDate('yy-mm-dd', $("#DispoDateTxt"+_ID, context).val() )
               : $("div.fbDispoCalendar:first").fbDispoCalendar('getDate')
              );
    }
    //alert( "#22 aktuell: " + d );
//    var l = $("div.fbDispoCalendar:first").fbDispoCalendar('getLager');
    var l = $("select#DispoLager"+_ID).val();
    var url = "";
    
    switch(_ID) {
        case "tabs-woche":
        if (typeof(interval) == 'number' && interval != 0 ) {
            d.setTime(d.getTime() + (interval*(7*24*60*60*1000)));
        }
        var kw = $.datepicker.iso8601Week( d );
        url = baseUrl + "/lager_id/" + l + "/kw/" + d.getFullYear() + "-" + kw;
        $("#DispoDate"+_ID+" label", context).html( d.getFullYear() + ' KW ' + kw );
        break;
        
        case "tabs-monat":
        if (typeof(interval) == 'number' && interval != 0 ) {
            d.setMonth(d.getMonth() + interval);
        }
        url = baseUrl + "/lager_id/" + l + "/monat/" + d.getFullYear() + "-" + (d.getMonth()+1);
        $("#DispoDate"+_ID+" label", context).html( $.datepicker.formatDate("yy-MM", d) ); 
        break;
        
        case "tabs-einsatz":
        if (typeof(interval) == 'number' && interval != 0 ) {
            d.setTime(d.getTime() + (interval*(24*60*60*1000)));
        }
        url = baseUrl + "/lager_id/" + l + "/date/" + $.datepicker.formatDate( 'yy-mm-dd', d );
        $("#DispoDate"+_ID+" label", context).html( 
            $.datepicker.formatDate("D dd.mm.yy", d) + " / " + 
            $.datepicker.iso8601Week( d ) + ' KW'); 
        break;

        case 'tabs-historie':
            url = baseUrl + "/lager_id/" + l + "/date/" + $.datepicker.formatDate( 'yy-mm-dd', d );
            break;

        default:
            alert("26 _ID unbekannt: " + _ID);
    }
    
    $("#DispoDateTxt"+_ID, context).val( $.datepicker.formatDate("yy-mm-dd", d) ); //datepicker("setDate", d  );
    $('#'+_ID +'-content').load( url );
};

(function($) {
    $(function(){
        
        $( "#fbDispoCalendarTabs" ).tabs({
            create: function() { //event, ui
                $("span.DispoLager", "#tabs-tag").button( { text:false } );
                $("span.btnAddCalendar", "#tabs-tag").button( { text:false } );                
            },
            select: function(e, ui) {
//                alert( ui.index + ' ' + $(ui.tab).attr('name') );
                var _ID = $(ui.tab).attr('name');
                var _BASEURL = $(ui.tab).attr('rel');
                switch(_ID) {
                    case 'tabs-historie':
                    case 'tabs-woche':
                    case 'tabs-monat':
                    case 'tabs-einsatz':
                        if (!$(ui.panel).data('allreadyLoaded')) {
                            var startDate = $("div.fbDispoCalendar:last").fbDispoCalendar('getDate');
                            var pager = $("<div class='DispoCalendarPager' />").attr('id', 'fbDispoCalendarPager'+_ID);
                            $(ui.panel).prepend(pager);
                            pager.append( $(
                                "<div style='float:left;margin-left:0px'>" +
                                "<span class='DispoLager'><select id='DispoLager"+_ID+"' style='font-weight:bold;padding:0;background:inherit;color:inherit;border:0;'>" +
                                Fb.LagerOptions + 
                                "</select></span>" +
                                "<span id='calendar-btn-browse"+_ID+"'>" +
                                " <button class='browse-prev'>&nbsp;</button>" +
                                " <input  id='DispoDateTxt"+_ID+"' type='text' style='width:1px;display:none;'>" +
                                " <button id='DispoDate"+_ID+"' ><label>Date</label></button>" +
                                " <button class='browse-refresh'>&nbsp;</button>" +
                                " <button class='browse-next'>&nbsp;</button>" +
                                "</span>" +
                                "</div>"
                                )
                            );
                            $( "button.browse-prev", pager).button({icons: {primary: "ui-icon-circle-triangle-w"}, text: true});
                            $( "button.browse-next", pager).button({icons: {primary: "ui-icon-circle-triangle-e"}, text: true});
                            $( "button.browse-refresh", pager).button({icons: {primary: "ui-icon-refresh"}, text: true});
                            $( "span.DispoLager", pager).button( { test:false } );
                            $( "span#calendar-btn-browse"+_ID, pager ).buttonset();
                            
                            $( "button.browse-prev", pager).click( function() {
                                Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, -1);
                            });
                            $( "button.browse-next", pager).click( function() {
                                Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, 1);
                            });
                            $( "button.browse-refresh", pager).click( function() {
                                Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, 0);
                            });
                            
                            $( "button#DispoDate"+_ID, pager).click(function() {
                                $("input#DispoDateTxt"+_ID, pager).datepicker( "show" );
                            });
                            $(ui.panel).data('allreadyLoaded', 1);
                            
                            $( "#DispoLager"+_ID).bind('change', function() {
                                Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, 0);
                            });
                            
                            if ( "tabs-woche" == _ID || "tabs-einsatz" == _ID) {
                                $( "input#DispoDateTxt"+_ID, pager).datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    showWeek: true,
                                    firstDay: 1, // Startet die Woche mit Mo
                                    onSelect:function(date) {
                                        Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, date);
                                    }
                                });
                            }
                            else {
                                $( "input#DispoDateTxt"+_ID, pager).datepicker( {
                                    changeMonth: true,
                                    changeYear: true,
                                    showButtonPanel: true,
                                    dateFormat: 'MM yy',
                                    beforeShow: function(input, inst) {
                                        //alert("#141 beforeShow ");
                                        $("head").append('<style>table.ui-datepicker-calendar{display:none;}</style>');
                                    },
                                    onClose: function(dateText, inst) { 
                                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                        var date = new Date(year, month, 1);
                                        $(this).datepicker('setDate', date);
                                        Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, date);
                                        $("head").append('<style>table.ui-datepicker-calendar{display:block;}</style>');
                                    }
                                });
                            }                            
                        }
//                        alert("#105 " + typeof($("div.fbDispoCalendar:first").fbDispoCalendar('getDate')) + " " + $("div.fbDispoCalendar:first").fbDispoCalendar('getDate') );
                        Fb.CalendarSurveyLoad(_ID, pager, _BASEURL, $("div.fbDispoCalendar:first").fbDispoCalendar('getDate'));
                    break;
                }
            }
        });
        
        $( "#TrashBox").droppable(Fb.DropTrashBoxSettings);
    });
    function dateToString(date) {
        var m = date.getMonth()+1;
        var d = date.getDate();
        if (m<10) m = '0' + m.toString();
        if (d<10) d = '0' + d.toString();
        return date.getFullYear()+'-'+m+'-'+d;
    }
    
    Fb.dateToString = function(date) { return dateToString(date); };
    
    Fb.addCalendar = function(date) {
        var lager_id = arguments.length > 1 ? arguments[1] : 0;
        var lagerSelect = $("select#DispoLager");

        if (lagerSelect.val() != lager_id ) {
            lagerSelect.val( lager_id ).trigger("change");
        }

        var lastCal = $("div.fbDispoCalendar:last");
        var newNum = 2;
        var newDt = new Date();
        if (lastCal.length) {
            var matches = lastCal.attr("id").match(/^[^0-9]*?([0-9]+)$/);
            if (matches !== null) newNum = 1 + parseInt(matches[1]);
            var dt = lastCal.fbDispoCalendar('getDate');
            if (dt) newDt.setDate(dt.getDate()+1);
        }
        var newDateString = (date != null) ? date : dateToString(newDt);
        var newCalId = "fbDispoCalendar" + newNum;
        var newCal = $("<div/>").attr("id", newCalId);
        $("#DispoCalendarCloser:first").before(newCal );
        newCal.fbDispoCalendar(
            $.extend({}, Fb.DispoCalendarEvents, {date:newDateString})
        );
    };
    
    Fb.vorgangsDispoDialog = function() {
        var dlg = $("div#vgDispoDlg");
        var lager = $("select#DispoLager").val();
        if ( !dlg.length ) {
            dlg = $("<div/>")
                  .attr({id:"vgDispoDlg",title:"Top25 Vorgangsdispo"})
                  .appendTo("body")
                  .append( $("<div/>").attr({id:"vgDispoDlgContent"}) )
                  .dialog({autoOpen:false,width:550,height:"auto",modal:true});
        }
        
        dlg.find("div#vgDispoDlgContent").load( 
            Fb.AppBaseUrl + '/touren/ajax/vorgangsdispodialog', 
            {lager:lager},
            function(){
//                alert( 'loaded!');
            })
            .end()
            .dialog( "open" );
    };
})(jQuery);

jQuery(function() {
    
    $( "button.remoteToggle" ).button({
        icons: { secondary: "ui-icon-triangle-1-s" }
    })
    .click(function() {
        if ( !$( "div#" + $(this).attr("rel")).length ) return;
        
        var options;
        var remoteItem = $( "div#" + $(this).attr("rel"));
        if ( remoteItem.css("display") == "none" ) {
            options = {
                label: $(this).text().replace("ein", "aus"),
                icons: { secondary: "ui-icon-triangle-1-n" }
            };
            $( "div#" + $(this).attr("rel")).slideDown( "normal", function(){} );
        } else {
            options = {
                label: $(this).text().replace("aus", "ein"),
                icons: { secondary: "ui-icon-triangle-1-s" }
            };
            $( "div#" + $(this).attr("rel")).slideUp( "normal", function(){} );
        }
        $( this ).button( "option", options );
    });
    
    
    $( "button.quickSelect" ).parent().css({position:"relative"});
    $( "button.quickSelect" ).button({
        icons: {primary: "ui-icon-gear"},
        text:false            
    })
    .css({position:"absolute","border":0,top:0,left:0,margin:0})
    .buttonset();
    
    $( "#btnFPSwitchExtern,#btnMASwitchExtern,#btnWZSwitchExtern")
    .find("input" ).button({
        icons: {primary: "ui-icon-cart"},
        text:false 
    })
    .bind("click", function(){
        var $this = $(this),
            checked = $this.attr("checked"),
            myForm = $this.attr("rel-form") ,
            myGrid = $( $this.attr("rel-grid") ),
            tab = $this.attr("rel-tab"),
            url = $(myForm).fbMultiSearchBox('jqGridSearchUrl');
            
        $this.parent().find("label").css({borderWidth:checked ?"1px":"0px"});
        
        if (checked) {
            url = (url.match(/\bextFilter=int/)) ? url.replace(/\bextFilter=int/, "extFilter=ext") : url+(url.indexOf("?")>0?"":"?")+"&extFilter=ext";
        } else {
            url = (url.match(/\bextFilter=ext/)) ? url.replace(/\bextFilter=ext/, "extFilter=int") : url+(url.indexOf("?")>0?"":"?")+"&extFilter=int";
        }
        $(myForm).fbMultiSearchBox({'jqGridSearchUrl':url});        
        $( 'button[name=sendQuery]', tab).trigger('click'); 
    })
    .end()
    .find("label").css({
        borderWidth:0
    }).end()
    .css({
        position:"absolute",
        top:0,
        left:"32px"
    });
    
    $( "button.quickSelect" ).click(function(e){
        var self = this;
        var dlgId = "quickSelectDialog",
            dlg = null,
            field = $(self).attr("rel-field"),
            tab = $(self).attr("rel-tab"),
            frm = $(self).attr("rel-form"),
            cUrl= $(frm).fbMultiSearchBox('categoryTreeFields'),
            url = (cUrl && field in cUrl) ? cUrl[field] : '';
            
        if (!$(  "#"+dlgId).length) 
            $( "<div/>")
            .attr({id:dlgId})
            .css({position:"absolute",zIndex:999})
            .appendTo("body")
            .bind('mousemove', function() {
                $(this).data('mouseenter', true);
            })
            .bind('mouseleave', function() {
                $(this).data('mouseenter', false);
                var tip = this;
                setTimeout(function(){
                    if (!$(tip).data('mouseenter')) $(tip).hide();
                }, 900);
            })
            .data("target", null);
            
        dlg = $("#"+dlgId);
            
        $(this).bind("mouseleave", function(){
            setTimeout(function(){
                if (!dlg.data('mouseenter')) dlg.hide();
            }, 900);
        });
        
        if (dlg.data("target")!==this)        
            dlg    
            .load(
                url, //public/fpcategories/selectdialog", 
                function() {
                    $("#treeSelectDialog").bind("selectTreeNode", function(event, data) {
                        $( 'select[default='+field+']', frm).parent()
                        .nextAll("td.data").find("input[name=data]")
                        .fbChooser('setData', [{value:data.id, label:data.name}] );
                        
                        $("#quickSelectDialog").hide();
                        $( 'button[name=sendQuery]', tab).trigger('click');                        
                    });
                }
            )
            .data("target", this);
        
        dlg
        .show()
        .css({
            left:$(self).offset()["left"],
            top:$(self).offset()["top"]+$(self).height()
        });      
        e.preventDefault();
    })
});
