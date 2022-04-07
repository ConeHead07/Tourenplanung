
$( function() {
    if ('Fb' in window && 'DispoLoading' in window.Fb) {
        $(document).ajaxStart(window.Fb.DispoLoading.bind(window.Fb, 1)).ajaxStop(window.Fb.DispoLoading.bind(window.Fb, 0));
    }

    $(document).ajaxComplete(function(event, jqXHR, ajaxOptions){});

    $(document).ajaxSuccess(function(event, jqXHR, ajaxOptions, d /* data */){
        if ((typeof ajaxOptions) === 'object' && ("async" in ajaxOptions) && ! ajaxOptions.async) {
            var url = ajaxOptions.url;
            var async = ajaxOptions.async;
            console.log("#11 Not Async!! jquery.fbDispoCalendar for url: ", {async:async, url:url});
        }

        if (typeof d !== "object" || (!("type" in d) && !("success" in d) && !("msg" in d) && !("error" in d)) ) {
            // console.log('#13 abort. Nothing to show for Toast', 'typeof d:', (typeof d)); //, d, 'jqXHR', jqXHR);
            return
        }

        if (!("type" in d) || typeof d.type !== "string") {
            if (typeof d.type === 'boolean' ) d.type = d.type ? 'success' : 'error';
            else if ("success" in d) d.type = d.success ? 'success' : 'error';
        }

        d.success = d.success || (d.type === 'success');
        d.msg = d.msg || '';
        d.error = d.error || '';
        d.data = d.data || '';
        var msg = (d.error || d.msg || '');
        if (msg.length > 505) msg = msg.substr(0,500) + "...";

        var toastData = {
            "heading": d.success ? "OK" : "Fehler",
            "text":
            "<div class='nachricht'>" + msg.split("\n").join("<br>") + "</div>",
            'hideAfter' : Math.min(2.5, Math.max(10, Math.ceil(msg.length/30))) * 1000,
            "position": {
                "top": 10,
                "left":10
            },
            "icon": d.success ? "info" : "error"
        };

        // console.log("#54 jquery.fbDispoCalendar.js opened Toast", {toastData});
        $.toast(toastData);
    });


});

function flatObjToString(obj, depth) {
    if (typeof(obj) !== "object") return obj;
    if (!depth) depth = '';
    var m = "";
    for(var i in obj) {
        if (typeof(obj.hasOwnProperty)!="undefined" && !obj.hasOwnProperty(i)) continue;
        m+= depth + i+"("+typeof(obj[i])+") : ";
        try {
            if ( typeof( obj[i] ).toString().match( /string|number|boolean/) )
                m+= obj[i] + "\n";
            else if (typeof(obj[i]) == 'object') {
                m+= depth + obj[i].constructor + (typeof(obj[i].toString)=='function'?'toString: ' + obj[i].toString():'')+ "\n";
                m+= flatObjToString(obj[i], depth+'...') + "\n";
            }
        } catch(e) {
            m+= e+'\n';
        }
    }
    return m;
}

function btos(boolVal) {
    return (boolVal ? 'true' : 'false');
}

if (typeof(Fb)=="undefined") var Fb = {};
if (!Fb.DispoCalendarSettings) Fb.DispoCalendarSettings = {};

/*
 * Default TourenAktionen Request im Synchron-Modus
 * 
 * abstract
 * im requestOpts-Objekt muss mind. eine url angeben, und im Regelfall sollten noch data uebergeben werden.
 * Requests werden synchron abgeschickt, d.h. alle anderen Aktionen ruhen bis
 * die Antwort des Servers empfangen und ausgewertet wurde, kann ueberschrieben werden (async:true).
 * Unterstuetzt Best�tigungsantworten des Servers:
 * Hierf�r werden im json-Response-Objekt die Properties 
 * - confirm (string)
 * - confirmUrl  (string) Wohin soll bestaetigung geschickt werden
 * - confirmData (string) Daten die mit bestaetigung hinzugefuegt werden (default: {confirm:1})
 * 
 * param requestOpts  object
 * - url 
 * - data 
 * 
 * param reponseOpts  object
 * - defaultError string (Wenn im Fehlerfall keine Fehlermeldung gefunden wird!)
 * - onsuccess function 
 * - onerror   function 
 * return bool success 
 */
Fb.DispoLoading = function(on) {
    var debug = 0;
    var loader = $("#fbDispo_loading");
    if (!$("#fbDispo_loading").length) {
        loader = $("<div/>")
            .attr({id:"fbDispo_loading","class":"fbDispoLoading ui-state-default ui-state-active"})
            .html("l&auml;dt ...")
            .data('tasknr', 0)
            .click(function(){
                if ($(this).css("top") !== 5 && $(this).css("top") !== "5px")
                    $(this).css({top:"5px",right:"25px"});
                else {
                    console.log('#68 Hide Loading', $("#fbDispo_loading").length);
                    $(this).hide();
                }
            })
            .appendTo("body");
    }

    if (debug) {
        if ('DebugDispoLoading' in window) {
            window.DebugDispoLoading(on, 'beforeExecution');
        }

        var d = new Date(), t = d.toLocaleTimeString() + '.' + d.getMilliseconds() + ' ', action = (on ? 'SHOW' : 'HIDE');
        console.log('#76 ' + t + action + ' Loading-Bar exists:', loader.length, ' is visible:', loader.is(':visible'), loader.offset());
    }

    if (arguments.length && !on) {
        var taskNrBefore = loader.show().data().tasknr;
        setTimeout(function() {
            if (taskNrBefore === loader.show().data().tasknr) {
                $("#fbDispo_loading").hide();
            }
        }, 950);

        if (debug) {
            console.log('hide loading delayed by 950ms');
        }

    } else {
        loader.show().data().tasknr++;
    }

    if (debug) {
        console.log('#82 ' + t + action + ' Loading-Bar exists:', loader.length, ' is visible:', loader.is(':visible'), loader.offset());

        if ('DebugDispoLoading' in window) {
            window.DebugDispoLoading(on, 'afterExecution');
        }
    }



};

Fb.AjaxTourRequest = function(requestOpts, responseOpts) {
    Fb.DispoLoading(1);
    var interact  = false;
    var returnVal = false;
    
    if (!responseOpts) responseOpts = {};
    if (!requestOpts)  requestOpts  = {};
    
    responseOpts = $.extend({},{
        "defaultError":"",
        "onsuccess":null,   // handler function
        "onerror":null     // handler function
    }, responseOpts);
    
    requestOpts = $.extend({}, {
        url: "./",
        data: {},
        type: 'GET',
        dataType:'json',
        async: false,
        success: function(data, textStatus, jqXHR) {
            interact = false;
            if ('confirm' in data && data.confirm) {
                interact = confirm(data.confirm);
                if ( interact ) {
                    if ("confirmUrl" in data && data.confirmUrl) 
                        requestOpts.url = data.confirmUrl;

                    if ("confirmData" in data && data.confirmData) 
                        requestOpts.data = $.extend({}, requestOpts.data, data.confirmData);
                    else {
                        requestOpts.data = $.extend({}, requestOpts.data, {confirm:1});
                    }
                    return;
                }
            }
            
            if (Fb.AjaxSuccessCheck(data, textStatus, jqXHR, {"defaultError":responseOpts.defaultError})) {
                returnVal = true;
                if (responseOpts.onsuccess && typeof responseOpts.onsuccess === "function") {
                    console.log("#213 responseOpts.onsuccess", responseOpts.onsuccess.toString() );
                    try {
                        responseOpts.onsuccess.apply(null, [data, textStatus, jqXHR]);
                    } catch(e) {
                        console.log(e);
                    }
                }
            } else {
                if (responseOpts.onerror && typeof responseOpts.onerror === "function") {
                    console.log("#213 responseOpts.onsuccess", responseOpts.onerror.toString() );
                    try {
                        responseOpts.onerror.apply(null, [jqXHR, textStatus]);
                    } catch(e) {
                        console.log(e);
                    }
                }
            }
            // (event, jqXHR, ajaxOptions, d /* data */){
            // $(document).trigger("ajaxSuccess", [jqXHR, requestOpts, data]);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('#231 AjaxTourRequest jquery.fbDispoCalendar error.js return', returnVal, textStatus, errorThrown);
            Fb.AjaxErrorShow(jqXHR, textStatus, errorThrown, responseOpts.defaultError);
            if (responseOpts.onerror) {
                try {
                    responseOpts.onerror.apply(null, [jqXHR, textStatus]);
                } catch(e) {
                    console.log(e);
                }
            }
        },
        complete: function(jqXHR, textStatus) {
            if (responseOpts.oncomplete) {
                try {
                    responseOpts.oncomplete.apply(null, [jqXHR, textStatus]);
                } catch(e) {
                    console.log(e);
                }
            }
        }
    }, requestOpts);
    
    do {
        console.log('#243 jquery.fbDispoCalendar.js return ', returnVal);
        Fb.DispoLoading(1);
        $.ajax(requestOpts);
    } while(interact);

    Fb.DispoLoading(0);
    return returnVal;
};

Fb.AppBaseUrl = (typeof(APP_BASE_URL) == 'string' ? APP_BASE_URL : '/');
Fb.AjaxSuccessCheck = function(data, textStatus, jqXHR, opts) { 
    if ( 
        (typeof(data) == "object")
        &&
        (
            ("error" in data && data.error) 
            || ("err" in data && data.err) 
            || ('success' in data && !data.success)
            || ('type' in data && (data.type === "error" || data.type===false) )
        )
    ) {
        if ("error" in data && data.error) alert(data.error);
        else if ("err" in data && data.err)alert(data.err);
        else if (typeof(opts) == "object" && "defaultError" in opts) alert(opts.defaultError);
        else alert("Ups, sorry, interner Fehler!");
        return false
    }                   
    return true;
};
Fb.AjaxErrorShow = function(jqXHR, textStatus, errorThrown, defaultError) {
    var re, e;
    try {
        re = (jqXHR.responseText) ? $.parseJSON( jqXHR.responseText ) : jqXHR.responseText;
    } catch(e) {
        re = jqXHR.responseText;
    }
    if (typeof(re)=="object" ) {
        if ("error" in re) alert( re.error);
    } else {
        alert(defaultError + "\n" + textStatus + " " + errorThrown + "\n" + (typeof(re) == "string" ? re : '') );
    }
};

Fb.RouteToolTip = function(elm, src, pos, srcType) {
    var TipId = "FbToolTip";
    var TipContentId = "FbToolTipContent";
    var TipCloseId = "aToolTipCloseBtn";
    var TipSrcType = (srcType ? srcType : 'url'); // url, html, selector
    if (!pos) pos = 'above'; // bottom
    
    try{
        var TT = $("#"+TipId);
        var target = $(elm);
        
        if (TT.length == 0) {
            TT = $('<div/>').attr({'id':TipId, 'class':'defaultTheme'}).css( 
                {   position:'absolute', zIndex:99 }
            ).append( 
                $('<p/>').attr('id',TipContentId).addClass('aToolTipContent')
            ).appendTo('body');
        }
        
        TT.hide().css({height:'auto', left:target.offset().left});
        TT.css('top', (pos=='above' 
                        ? target.offset().top- TT.outerHeight() - 5 
                        : target.offset().top + target.outerHeight() ) );
        
        if (TT.data('MouseTarget') == elm) {
            TT.show();
            return;
        }
//        alert( '#32 MouseTarget id: ' + $(elm).attr('id') + (TT.data('MouseTarget') ? ' ==? ' + TT.data('MouseTarget').attr('id') : '') );
        TT
        .data('MouseTarget', elm)
        .bind('mouseover', function() {
            target.data('MouseIsOver', true);
        })
        .show()
        .draggable();
        
        if (TipSrcType == 'url')        
        TT.find('#'+TipContentId)
        .text('loading ...')
        .load(src, 
            function() {
                $(this).append( 
                    $("<a id='"+TipCloseId+"' href='#' alt='close'>close</a>").click(
                        function(e){
                            e.preventDefault();
                            TT.data('MouseTarget', null);
                            TT.hide();
                            return false;
                        }
                    )
                );
                TT.css({'height':TT.height(),
                    'top': (pos == 'above' 
                            ? target.offset().top- TT.outerHeight() - 5
                            : target.offset().top + target.outerHeight()
                           )} );
            }
        );
        else if(TipSrcType == 'html')
            TT.find('#'+TipContentId).html( src );
        else if (TipSrcType == 'selector') {
            TT.find('#'+TipContentId).html("").append( $(src).clone() );
            
        }
        
        TT.bind('mouseleave', function(){ 
            Fb.RouteToolTipHide( elm );
        });
        $(elm).bind('mouseleave', function(){ 
            Fb.RouteToolTipHide( elm );
        });
        
    }
    catch(e) {
        alert('#96 Error Showing ToolTip: ' + e);
    }
    
};
Fb.RouteToolTipHide = function( elm ) {
    var target = $(elm);
    var TipId = "FbToolTip";
    
    if (!$("#"+TipId).length) return;
    if (!target.length) {
        $("#"+TipId).hide();
        return;
    }
    
    target.data('MouseIsOver', false);
//    alert('#93');
    setTimeout( function() {
//        alert('#95 ' + $(elm).data('MouseIsOver') + '; ' + $("#"+TipId).length);
        if (!$("#"+TipId).length) return;
        if (!$(elm).data('MouseIsOver')) {
            $("#"+TipId).hide();
//            alert('#99 ' + $(elm).data('MouseIsOver') + '; ' + $("#"+TipId).length);
        }
    }, 500);
}

/**
 * @param filter array|object multiple options: tour_id, timeline_id, portlet_id, lager_id, Auftragsnummer
 * @param maxAgeMinutes int Gibt an welcher Zeitraum abgefragt werden soll. Default die letzten 5 Minuten
 * @return int Count concurrent Activities other users
 */
Fb.DispoCheckConcurrency = function(filter, maxAgeMinutes) {
    var re = null;
    var url = Fb.AppBaseUrl + '/touren/concurrency/check';
    if (!filter) filter = {};
    if (!maxAgeMinutes) maxAgeMinutes = 5;
    filter.maxage = maxAgeMinutes;
    
    $.ajax({
        type: 'GET', dataType: 'json', async: false, data: filter,
        url: url,
        success:function(data) { 
//            alert('#41 Fb.DispoCheckConcurrency callback data:' + data);
            re = data;
        }
    });
    return re;
};

/**
 * @param filter array|object multiple options: tour_id, timeline_id, portlet_id, lager_id, Auftragsnummer
 * @param maxAgeMinutes int Gibt an welcher Zeitraum abgefragt werden soll. Default die letzten 5 Minuten
 * @return JSON List with concurrent Activities other users
 */
Fb.DispoListConcurrency = function(filter, maxAgeMinutes) {
    var re = null;
    var url = Fb.AppBaseUrl + '/touren/concurrency/list';
    if (!filter) filter = {};
    if (!maxAgeMinutes) maxAgeMinutes = 5;
    filter.maxage = maxAgeMinutes;
    
    $.ajax({
        type: 'GET', dataType: 'json', async: false, data: filter,
        url: url,
        success:function(data) { 
//            alert('#65 Fb.DispoListConcurrency callback data:' + data);
            re = data;
        }
    });
    return re;
};

Fb.ReloadTimelineResources = function(timeline) {
    $(timeline).fbDispoTimelineDropzone('getRoutes').each(function() {
        if ( $( this ).is( ".fbDispoRoute" ) )
            $(this).fbDispoRoute("reloadResources");
        else if( $( this ).is( ".fbDispoRouteDefaults" ) )
            $(this).fbDispoRouteDefaults("reloadResources");
     } );  
};
Fb.ReloadTimelineResourcesByRoute = function(route) {
    Fb.ReloadTimelineResources( $( route ).fbDispoRoute("getTimeline") );
};
Fb.ReloadTimelineResourcesByRouteDefaults = function(route) {
    Fb.ReloadTimelineResources( $( route ).fbDispoRouteDefaults("getTimeline") );
};

Fb.RenderResourceDelAction = function(contextSelector, resourceSelector, tour_id) {
    $(contextSelector).find(resourceSelector).fbIcon({icon:'trash',css:{background:'none',border:0}}).css({marginRight:'3px'}).click(function(){
        var self = this;
        var data = {
            id: $(this).attr('rsrcid'),
            resourceType: $(this).attr('rsrctype'),
            name: $(this).parent().find('span.rsrc-title').text()
        };
        
        var fbDispoRoute = $("div#fbDispoRoute_" + tour_id);
        if (!fbDispoRoute.length) return;
        
        if (fbDispoRoute.fbDispoRoute('_trigger', fbDispoRoute[0], 'removeResource', data) !== false) {
            if ($(self).closest("li").length) $(self).closest("li").remove();
            else $(self).remove();
            fbDispoRoute.fbDispoRoute("reloadResources");
        } else {
            alert("#192 Fb.RenderResourceDelAction; Error: CanNot Remove Resource");
        }
    });
};


Fb.DispoCalendarEvents = {};

(function($) {
    
    var dataKey = 'fbDispoCalendar';
    
    var eventTriggerMap = {
                   'create': 'createDispoCalendar',
                 'complete': 'completeDispoCalendar',
               'changeDate': 'changeDateDispoCalendar',
 'clickVorgangsEmpehlungen': 'clickVorgangsEmpehlungenDispoCalendar'
    };
    
    var _instanceNo = 1;
    var rgxDateFormat = /^\d{4,4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-2])$/;
    var defaults = {
                      'data': null,
                      'date': null,
                       'url': '',
       'resourcesBaseUrlTpl': '',
           'routeBaseUrlTpl': '',
                 'searchUrl': null,
                 
               'isBrowsable': true,
                 'isAddable': true,
                 'isHidable': true,
             'isRefreshable': true,
               'isPrintable': true,
                'isSortable': true,
              'isSearchable': true,
       'isContextSearchable': true,
       
                     
          // Legt fest, ob Child-Events zus�tzlich an jQuery-Binded-Handler weitergereicht werden
          // Dies sollten die Child selbst �bernehmen, da sonst Events mehrfach getriggert werden
        'triggerChildEvents': false, 
                       
          // Event-Listeners
          // Registriert am Kalender-Element
                   'onCreate': null, //*null*/ function(){alert(dataKey + '.onCreate Default-Dummie-Handler!')},
                 'onComplete': null, //*null*/ function(){alert(dataKey + '.onComplete Default-Dummie-Handler!')},
               'onChangeDate': null, //*null*/ function(date){alert(dataKey + '.onChangeDate '+date+' Default-Dummie-Handler!')},
'onClickVorgangsEmpfehlungen': null, //*null*/ function(date){alert(dataKey + '.onClickVorgangsEmpehlungen '+date+' Default-Dummie-Handler!')},
                
             // Registriert am Portlet-Element
            'onCreatePortlet': null, //*null*/ function(){alert(dataKey + '.onCreatePortlet Default-Dummie-Handler!')},
            'onUpdatePortletTitle': null, //*null*/ function(){alert(dataKey + '.onCreatePortlet Default-Dummie-Handler!')},
            'onRemovePortlet': null, //*null*/ function(){alert(dataKey + '.onRemovePortlet Default-Dummie-Handler');},
             'onPrintPortlet': null, //*null*/ function(){alert(dataKey + '.onPrintPortlet Default-Dummie-Handler');},
              'onSortPortlet': null, //*null*/ function(e,ui){alert(dataKey + '.onSortPortlet Default-Dummie-Handler');},
              'onFoldPortlet': null, //*null*/ function(){alert(dataKey + '.onFoldPortlet Default-Dummie-Handler'+'\nportlet.class:'+$(this).attr('class'));},          
            'onUnfoldPortlet': null, //*null*/ function(){alert(dataKey + '.onUnfoldPortlet Default-Dummie-Handler');},
           
           // Registriert im Portlet, wenn Timeline auf ein Portlet gezogen wird
           // Also sowohl beim Timeline-Sortieren als auch Portlet�bergreifendes Verschieben
           // Droppable ui - overview
           // ui.draggable - current draggable element, a jQuery object.
           // ui.helper - current draggable helper, a jQuery object
           // ui.position - current position of the draggable helper { top: , left: }
           // ui.offset - current absolute position of the draggable helper { top: , left: }
             'onDropTimeline': null, //*null*/ function(e, ui){alert(dataKey + '.onDropTimline Default-Dummie-Handler');},
       
           //    Sortable ui - Overview
           //    ui.helper - the current helper element (most often jqXHR clone of the item)
           //    ui.position - current position of the helper
           //    ui.offset - current absolute position of the helper
           //    ui.item - the current dragged element
           //    ui.placeholder - the placeholder (if you defined one)
           //    ui.sender - the sortable where the item comes from (only exists if you move from one connected list to another)
             'onMoveTimeline': null, //*null*/ function(e,ui){alert(dataKey + '.onMoveTimline Default-Dummie-Handler');},
             'onSortTimeline': null, //*null*/ function(e,ui){alert(dataKey + '.onSortTimline Default-Dummie-Handler');},
           'onCreateTimeline': null, //*null*/ function(){alert(dataKey + '.onCreateTimline Default-Dummie-Handler');}, // function(timelineObj)
           'onRemoveTimeline': null, //*null*/ function(){alert(dataKey + '.onRemoveTimline Default-Dummie-Handler');},
         
             // Registriert in der Timeline
             // Draggable ui - overview
             // ui.helper - the jQuery object representing the helper that's being dragged
             // ui.position - current position of the helper as { top, left } object, relative to the offset element
             // ui.offset - current absolute position of the helper as { top, left } object, relative to page
              'onMoveTimeline': null, //*null*/ function(){alert(dataKey + '.onMoveTimline Default-Dummie-Handler');},
         
                 'onDropRoute': null, //*null*/ function(){alert(dataKey + '.onDropRoute Default-Dummie-Handler');},
               'onSelectRoute': null, //*null*/ function(){alert(dataKey + '.onSelectRoute Default-Dummie-Handler');},
                 'onMoveRoute': null, //*null*/ function(){alert(dataKey + '.onMoveRoute Default-Dummie-Handler');},
               'onResizeRoute': null, //*null*/ function(){alert(dataKey + '.onResizeRoute Default-Dummie-Handler');},
               'onRemoveRoute': null, //*null*/ function(){alert(dataKey + '.onRemoveRoute Default-Dummie-Handler');},
           'onChangeRouteData': null, //*null*/ function(){alert(dataKey + '.onChangeRouteData Default-Dummie-Handler');},
          
        'onHoverRouteDefaults': null,
        'onLeaveRouteDefaults': null,
       'onCreateRouteDefaults': null,
       'onSelectRouteDefaults': null,
      'onDropResourceDefaults': null,
      'onMoveResourceDefaults': null,
    'onRemoveResourceDefaults': null,
   
         'onHoverRouteDetails': null, //*null*/ function(){alert(dataKey + '.onHoverRouteDetails Default-Dummie-Handler');},
         'onLeaveRouteDetails': null, //*null*/ function(){alert(dataKey + '.onLeaveRouteDetails Default-Dummie-Handler');},
                 'onLoadRoute': /*null*/ function(){alert(dataKey + '.onLoadRoute Default-Dummie-Handler');},
                'onHoverRoute': null, //*null*/ function(){alert(dataKey + '.onHoverRouteDetails Default-Dummie-Handler');},
                'onLeaveRoute': null, //*null*/ function(){alert(dataKey + '.onLeaveRouteDetails Default-Dummie-Handler');},
                'onClickRoute': null, //*null*/ function(){alert(dataKey + '.onHoverRouteDetails Default-Dummie-Handler');},
          'onShowRouteDetails': null, //*null*/ function(){alert(dataKey + '.onShowRouteDetials Default-Dummie-Handler');},
          'onHideRouteDetails': null, //*null*/ function(){alert(dataKey + '.onHideRouteDetails Default-Dummie-Handler');},
  'onShowRouteDefaultsDetails': null,
  'onHideRouteDefaultsDetails': null,
         
               'onAddResource': null, //*null*/ function(){},
              'onDropResource': null, //*null*/ function(e, ui){},
              'onMoveResource': null, //*null*/ function(){alert(dataKey + '.onMoveResource Default-Dummie-Handler');},
            'onRemoveResource': null, //*null*/ function(data){alert(dataKey + '.onRemoveResource arguments.length:'+arguments.length);}
         'onSetTourFarbklasse': null
    };
   
    var methods = {
        'clear': function() {
            $("div.DispoCalendarPortlets", this).html('');
        },
        'addPortlets': function(portlets) {
//            alert('#384 ' + dataKey + ' addPortlets portlets: ' + typeof(portlets) );
            for(var ti = 0; ti < portlets.length; ++ti) {
//                methods.addPortlet.apply(this, [ $.extend({}, portlets[ti]) ] );
                methods.addPortlet.apply(this, [ portlets[ti] ] );
            }
//            $("div.fbDispoRouteDefaults", this).show();
        },
        'addPortlet': function(addData) {
            if (!addData) addData = {};
            addData.date = methods._getDateString( methods.getDate.apply( this ) );
            addData.lager_id = methods.getLager.apply( this );
            
            var portlet = $('<div />').prependTo( $("div.DispoCalendarPortlets", this) );
            portlet.fbDispoPortlet({
                             date: addData.date,
                             lager_id: addData.lager_id,
//                             data: $.extend({},{}, addData),
                             data: addData,
                        '_parent': $(this),
              '_parentJqFunction': dataKey
            });
            $( ".selector" ).sortable( "refreshPositions" );
            $( "div.DispoCalendarPortlets", this).sortable('refresh');
            return portlet;
        },
        'load': function(portlets) {
            //alert( '#648 ' + dataKey + '.load arguments.length:' + arguments.length );
            if (typeof(portlets) == 'object') {
                for(var i = 0; i < portlets.length; ++i) {
                    methods.addPortlet.apply( this, [ 'load', portlets[i]] );
                }
                return;
            }
            
            // Auto Load By Date Calendar-Entries
//            if (portlets) alert('#406 ' + dataKey + ' load portlets: ' + typeof(portlets) );
            var self = this;
            var d = $(this).data(dataKey);
            var log = '#493 start request calendarDayData ' + (new Date()).toLocaleString() + "\n";
            try {
                Fb.DispoLoading(1);
                $.getJSON(d.url, {date: d.date, lager_id:d.data.lager_id}, function (data) { // data, textStatus
                    log+= '#495 start render calendarDayData ' + (new Date()).toLocaleString() + "\n";
                    methods.clear.apply( self );                
                    methods.addPortlets.apply(self, [data.data] );
                    methods._trigger.apply(self, [self, 'changeDate', d.date] );
                    log+= '#499 finished building calendarDayData ' + (new Date()).toLocaleString() + "\n";
    //                alert( log );
                    Fb.DispoLoading(0);
                });
            }
            catch(e) {
                Fb.DispoLoading(0);
            }
            
        },
        'loadDate': function(date) {
            if (methods.setDate.apply(this, [date] )) {
                methods.load.apply( this );
            }
        },
        'loadPrevDay': function() {
            var date = methods['getDate'].apply( this );
            date.setDate(date.getDate()-1)
            methods.setDate.apply(this, [date] );
            methods.load.apply( this );
        },
        'loadNextDay': function() {
            var date = methods['getDate'].apply( this );
            date.setDate(date.getDate()+1)
            methods.setDate.apply(this, [date] );
            methods.load.apply( this );
        },
        'getDate': function() {
            var dateString = $( this ).data(dataKey).date;
            if (dateString) {
                var d = dateString.split('-');
                if (d[1].charAt(0)=='0') d[1] = d[1].substr(1);
                if (d[2].charAt(0)=='0') d[2] = d[2].substr(1);
                return new Date(d[0], (d[1]-1), d[2]);
            }
            return new Date();
        },
        'setDate': function(date) {
            // PreCondition Datumsformat
            var _ID = $(this).data(dataKey).ID;
            if (typeof(date)=="object" && String(date.constructor).match(/\bDate\b/)) {
                $(this).data(dataKey).date = methods._getDateString(date);
            }
            else if (typeof(date)=="string" && date.match( rgxDateFormat ) ) {
                $(this).data(dataKey).date = date;
            } else {
                alert( "Unzulaessiges Datum: " + typeof(date) + " " + date + ". Erwartet Datumsobjekt oder String JJJJ-MM-TT");
                return false;
            }
            
            var newDate = methods.getDate.apply( this );
            var newDateText = $.datepicker._defaults.dayNamesShort[ newDate.getDay()];
            newDateText+= ' ' + methods._getLocalString( $(this).data(dataKey).date );
            newDateText+= ' / ' + $.datepicker.iso8601Week( newDate ) + '. KW';
            //alert( 'KW ' + $.datepicker.iso8601Week( methods.getDate.apply( this ))+', '+$.datepicker._defaults.dayNamesShort[ newDate.getDay()] );
            $( "button#DispoDate"+_ID+" label", this).html( newDateText );
            $( "input#DispoDateTxt"+_ID+"", this).val( $(this).data(dataKey).date );
            return true;
        },
        'setLager': function(lager_id) {
            methods.setData.apply(this, ['lager_id', lager_id] );
            if ( $('#DispoLager').val() != lager_id ) $('#DispoLager').val(lager_id);
        },
        'getLager': function() {
            return methods.getData.apply(this, ['lager_id'] );
        },
        '_getDateString': function(date) {
            var m = date.getMonth()+1;
            var d = date.getDate();
            if (m<10) m = '0' + m.toString();
            if (d<10) d = '0' + d.toString();
            return date.getFullYear()+'-'+m+'-'+d;
        },
        '_getLocalString': function(dateString) {
            return (typeof(dateString)=="string") ? dateString.split('-').reverse().join('.') : "undefined";
        },
        '_trigger': function(obj, eventName) {
//            alert( '#1061 ' + dataKey + ' : ' + eventName );
//            alert( '#841 eventName:'+eventName);
            var data = $(this).data(dataKey);
            var args = $.makeArray(arguments).slice(1);
            var re = null;
            
            // Call own Event-Handler
            var ownEventName = methods._getOwnEventHandlerName(eventName);
//            alert( '#1024 fbDispoCalendar._trigger \neventName:'+eventName+'\nownEventName:'+ownEventName );
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
//                alert( '#1026 fbDispoCalendar._trigger \neventName:'+eventName+'\nownEventName:'+ownEventName );
                if (false === data[ownEventName].apply( obj, $.makeArray(arguments).slice(2) ) ) {
                    return false;
                }
            }
            
            // Fire Event via jQuery-Trigger
            args[0] = methods._getEventTriggerName(eventName);
            if (obj == this || data['triggerChildEvents']) {
                if (false === $( this ).trigger.apply( $(this), args) ) {
                    return false;
                }
            }
            return true;
        },
        '_getEventTriggerName': function(eventName) {
            if (eventName in eventTriggerMap) eventName = eventTriggerMap[eventName];
            return eventName;
        },
        '_getOwnEventHandlerName': function(eventName) {
            var e = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
//            alert( 'typeof defaults[' + e + '] ' + typeof(defaults[e]) );
            return (e in defaults) ? e : '';
        },
        '_init': function() {
            var self = this;
            var $self = $( this );
            var _ID = ++_instanceNo;
            var data = $(this).data(dataKey);
            data.ID = _ID;
                        
            $self.addClass('DispoCalendar ' + dataKey);
            
            $self.append( $("<span class='DispoDateDisplay' />") );
            
            var pager = $("<div class='DispoCalendarPager' />").attr('id', 'fbDispoCalendarPager'+_ID);
            $self.append( pager );
            
            var isBr=data.isBrowsable,
                isRf=data.isRefreshable,
                isAd=data.isAddable,
                isCs=data.isContextSearchable,
                isSe=data.isSearchable,
                isSo=data.isSortable,
                isPr=data.isPrintable,
                isHd=data.isHidable;
                
            pager.append(
                $(
                "<div style='float:left;margin-left:0px'>" +
                "    <span id='calendar-btn-browse"+_ID+"'>" + 
                "    <button class='collapse'>&nbsp;</button>" + 
                (isBr?"<button class='browse-prev'>&nbsp;</button>":"") +
                "<input id='DispoDateTxt"+_ID+"' type='text' style='width:1px;display:none;'>" +
                "<button id='DispoDate"+_ID+"' ><label>Date</label></button>" +
                (isBr?"<button class='browse-next'>&nbsp;</button>":"") +
                (isAd?"<button id='addPortlet"+_ID+"'>Neue Tour anlegen</button>":"") +
                (isRf?"<button class='browse-refresh' title='Ansicht aktualisieren'>&nbsp;</button>":"") +
                (isPr?"<button class='DispoPrint' title='Tagesdruckansicht'>&nbsp;</button>":"") +
                (isCs?"<button class='DispoEmpfehlungen' title='Passende Vorgaenge in Auswahlliste anzeigen'>&nbsp;</button>":"") +
                (isSe?"<button class='DispoSearch' title='Disponierte Vorgaenge suchen'>&nbsp;</button>":"") +
                (isHd?"<button class='DispoCalRemove' title='Diesen Tag ausblenden'>&nbsp;</button>":"") +
                "   </span>" +
                "</div>"
                )
            )
            .append( "<br style='clear:both;' />");
            
            
            $self.append( $("<div class='DispoCalendarPortlets' />") );
            
            $( "button, input:submit, a", pager ).button();
            $( "button#addPortlet"+_ID, pager).button({icons:{primary:"ui-icon-circle-plus"}, text: true});
            $( "span#calendar-btn-view"+_ID+"", pager ).buttonset();
            $( "span#calendar-btn-browse"+_ID, pager ).buttonset();
            $( "button.collapse", pager).button({icons: {primary: "ui-icon-minus"}, text: true});
            if (isRf) $( "button.browse-refresh", pager).button({icons: {primary: "ui-icon-refresh"}, text: true});
            if (isBr) $( "button.browse-prev", pager).button({icons: {primary: "ui-icon-circle-triangle-w"}, text: true});
            if (isBr) $( "button.browse-next", pager).button({icons: {primary: "ui-icon-circle-triangle-e"}, text: true});
            if (isCs) $( "button.DispoEmpfehlungen", pager).button({icons: {primary: "ui-icon-note"}, text: true});
            if (isSe) $( "button.DispoSearch", pager).button({icons: {primary: "ui-icon-search"}, text: true});
            if (isPr) $( "button.DispoPrint", pager).button({icons: {primary: "ui-icon-print"}, text: true});
            if (isHd) $( "button.DispoCalRemove", pager).button({icons: {primary: "ui-icon-close"}, text: true});
            $( "button#DispoDate"+_ID, pager).button('option', "secondary", "ui-icon-triangle-1-s");
            
            $( "button.collapse", this).click(function(){
                var p = $( this ).closest( "div.DispoCalendar" ).find( "div.DispoCalendarPortlets" ).slideToggle();
                $( this ).find(".ui-icon:first").toggleClass( "ui-icon-minus" ).toggleClass( "ui-icon-plus") ;
                
                // To-Do: Add Eventhandler
                methods._trigger.apply(self, [self, (p.is( ":visible") ? 'fold' : 'unfold')]); 
            });
            
            if (isSo) $( "div.DispoCalendarPortlets", this).sortable({
                   //'create': function(e, ui) { alert('#1216 '+dataKey+' create sortable portlets!')},
                   //'over': function(e, ui) { alert('#1217 '+dataKey+' over sortable portlet-handle!')},
                   'cursor': "move",
                   'handle':'.sortable.handle',
                    stop: function(event, ui) {
//                      alert('sort stop!' + ui.item.html() );
                        methods._trigger.apply(self, [self, 'sortPortlet'].concat( $.makeArray(arguments) ));
                  }
            });
                       
            if (isBr) $( "input#DispoDateTxt"+_ID, pager).datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                showWeek: true,
                firstDay: 1, // Startet die Woche mit Mo
                onSelect:function(date) {
//                    alert( "#963 " + $(self).attr('id'));
                    methods.setDate.apply( self, [ date ] );
                    methods.load.apply( self );
                }
            });
            
            $( "select#DispoLager").bind('change', function() {
                methods.setData.apply( self, ['lager_id', $(this).val()] );
                methods.load.apply( self );
            });
            
            if (isAd) $( "button#addPortlet"+_ID, self).click( function() {
                methods.addPortlet.apply( self );
            });
            if (isBr) $( "button.browse-prev", self).click( function() {
                methods.loadPrevDay.apply( self );
            });
            if (isBr) $( "button.browse-next", self).click( function() {
                methods.loadNextDay.apply( self );
            });
            if (isRf) $( "button.browse-refresh", self).click( function() {
                methods.load.apply( self );
            });
            if (isHd) $( "button.DispoCalRemove", self).click( function() {
                $( self ).remove();
            });
            if (isBr) $( "button#DispoDate"+_ID, self).click(function() {
                $("input#DispoDateTxt"+_ID, self).datepicker( "show" );
            });
            if (isCs) $( "button.DispoEmpfehlungen", pager).click(function() {
                var d = $(self).data(dataKey);
                methods._trigger.apply(self, [self, 'clickVorgangsEmpfehlungen', d.date] );
            });
            
            if (isPr && data.searchUrl) $( "button.DispoPrint", pager).click(function(){
                var d = methods._getDateString( methods.getDate.apply( self ) );
                var l = methods.getLager.apply( self );
                if (d && l) {
                    var url = Fb.AppBaseUrl + '/touren/page/printportletsday/date/' + d + '/lager_id/' + l;
                    if (typeof(winPrint) !== "undefined" && null !== winPrint && winPrint.close && !winPrint.closed) winPrint.close();
                    winPrint = window.open(url, "winPrint", "width=800px,height=800,menubar=yes,resizable=yes,scrollbars=yes");
                    winPrint.focus();
                }
            });
            
            if (isSe && data.searchUrl) $( "button.DispoSearch", pager).click(function(){
                if (!$('#DispoSearchBox').length) $('<div id="DispoSearchBox"/>').appendTo('body');
                
                var date = methods.getDate.apply(self),
                    //dateTxt = methods._getDateString( date ),
                    kw = $.datepicker.iso8601Week( date ),
                    lager= methods.getLager.apply(self),
                    loadUrl = data.searchUrl + (data.searchUrl.indexOf('?')==-1?'?':'') +
                                "&kw="+date.getFullYear()+"-"+ kw+"&lager_id="+lager+"&ajax=1";

                $('#DispoSearchBox').dialog({
                    width: $('body').width()*0.8
                }).load( loadUrl );

            });
            else $( "button.DispoSearch", pager).hide();
            
            methods.setLager.apply( this, [$( "#DispoLager").val()] );
            methods.setDate.apply( this, [ $(this).data(dataKey).date]);
            methods.load.apply( this );
            methods._trigger.apply(self, [self, 'create'] );
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
//            var m= dataKey + '.getData(' + $.makeArray(arguments).join(',')+')\n';
            var data = $(this).data(dataKey);
            if (!data || !data.data) return null;
            if (arguments.length) {
               return (arguments[0] in data.data) ? data.data[arguments[0]] : null;
            }
            return data.data;
        },
        'getObjectData': function() {
            var data = $(this).data(dataKey);
            if (!data || !data.data) return null;
            if (!arguments.length) return $(this).data(dataKey);
            else return (arguments[0] in data) ? data[arguments[0]] : null;
        },
        'setData': function() {
            if (!$(this).data(dataKey)) $(this).data(dataKey, {});
            if (!$(this).data(dataKey).data) $(this).data(dataKey).data = {};
            
//            var m= dataKey + '.setData(' + $.makeArray(arguments).join(',')+')\n';
            if (arguments.length == 1)
               $(this).data(dataKey).data = arguments[0];
            else if(arguments.length >= 2)
               $(this).data(dataKey).data[arguments[0]] = arguments[1];
        }
    };
    
    $.fn.fbDispoCalendar = function(options) {
        
        var args = arguments;
        var argsLen = arguments.length;
        
        var now = new Date();
        var m = now.getMonth()+1;if (m<10) m= '0'+m;
        var d = now.getDate();if (d<10) d= '0'+d;
        defaults.date = now.getFullYear()+'-'+m+'-'+d;
//        alert(defaults.date);
        
        // Default-Routine: Getting Options from allready initialized Object
        if (typeof(options) == 'string' && this.length ) {
            switch(options) {
                case 'options':
                    if (argsLen == 1) return $( this[0] ).data(dataKey);
                    break;

                case 'option':
                    if (argsLen == 2) {
                        var data = $( this[0] ).data(dataKey);
                        return (typeof(data[args[1]]) != "undefined") ? data[args[1]] : null;
                    }
                    break;

                default:
                    if (typeof(methods[options]) == 'function' && (options == '_trigger' || options.substr(0,3) == 'get') ) {
                        return methods[options].apply( this[0], (argsLen>1?$.makeArray(args).slice(1):null) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options            
            if (!$( self ).data(dataKey)) {
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.calendar) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.calendar);
                } else {
                    presets = defaults;
                }
                
                if (!options) {
                    $( self ).data(dataKey, $.extend({}, presets));
                }
                else if (typeof(options) == "object") {
                    $( self ).data(dataKey, $.extend({}, presets, options) );
                }
                else
                    $( self ).data(dataKey, $.extend({}, presets));
                
                _callInit = true;
            }
            var data = $( self ).data(dataKey);
//            alert( "in each-Schleife: fbDispoCalendar({date:"+options.date+"}");

            // Default-Routine: Analyse Options-Settings and 
            // execute called Functions
            if (typeof(options) == 'string') {
                switch(options) {
                    case 'options':
                        if (argsLen > 1) {
                            data = $.extend({}, data, args[1]);
                            $( self ).data(dataKey, data);
                        }
                        break;
                        
                    case 'option':
                        if (argsLen > 2) {
                            data[args[1]] = args[2];
                            $( self ).data(dataKey, data);
                        }
                        break;
                        
                    default:
                        if (typeof(methods[options]) == 'function') {
                            methods[options].apply( self, (argsLen>1?$.makeArray(args).slice(1):null));
                        }
                }
            }
            
            if (_callInit) methods['_init'].apply( self );
            
//            alert('#528 '+dataKey+' data[date]: '+typeof(data['date'])+'->'+data['date']);
//            $( "span.DispoDateDisplay", self).text( methods['_getLocalString'](data['date']));
            return this;
        });
    };
})(jQuery); 