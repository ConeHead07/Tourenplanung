if (!Fb) throw("Missing Object Js-Objekt Fb!");
if (!Fb.DispoCalendarEvents) Fb.DispoCalendarEvents = {};

$.extend(Fb.DispoCalendarEvents, {    
    'onCreatePortlet': function() {
        var $self = $(this);
        var dbdata = $self.fbDispoPortlet('getData');
        dbdata.datum = dbdata.date;
        
//        alert( '#10 onCreatePortlet dbdata.date: ' + dbdata.date);
        if (!dbdata || !dbdata.id) {
            return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/addportlet',
                    data: {data:dbdata}
                }, {
                    defaultError: "Interner Fehler beim Entfernen der Resource!",
                    onsuccess: function(d) {
                        dbdata = d.data;
                        var title = ( dbdata.title ) ? dbdata.title : '';
						var top25 = ( dbdata.topcustom ) ? dbdata.topcustom : '';
                        $self.fbDispoPortlet('setData', 'id', d.id);
                        
                        $self.fbDispoPortlet(
                            'setTitle',
                            'Tournr <span class="p-tagesnr">' +
                                dbdata.tagesnr + '</span> <span class="p-holiday">' +
                                dbdata.holiday + '</span> <span class="p-top25">' +
                                top25 + '</span> <span class="p-title">' +
                                title + '</span>');
                    }
                }
             );
        }

        if (1) {
            var title = (dbdata.title) ? dbdata.title : '';
            var top25 = (dbdata.topcustom) ? dbdata.topcustom : '';
            $self.fbDispoPortlet(
                'setTitle',
                'Tournr <span class="p-tagesnr">' +
                dbdata.tagesnr + '</span> <span class="p-holiday">' +
                dbdata.holiday + '</span> <span class="p-top25">' +
                top25 + '</span> <span class="p-title">' +
                title + "</span>");
        }
        return true;
    },
   'onUpdatePortletTitle': function(title) {
        var $portlet = $(this); //ui.item.find( 'div.portlet-content' );        
        var id = $portlet.fbDispoPortlet('getData','id');
		
	if (id) {
			 
			 return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/updateportlettitle',
                    data: {id:id,title:title}
                }, {
                    defaultError: "Interner Fehler beim Speichern!"
                }
             );
		} else {
			alert("ID der Tourenleiste konnte nicht ermittelt werden!");
			return false;
		}
		
	},
    'onSortPortlet': function(event, ui) {
        // Da das Dispo-Portlet-Objekt gewrapt wurde beim Rendering
        // muss das Objekt aus ui.item gefiltert werden
        var $portlet = ui.item.find( 'div.portlet-content' );        
        var id = $portlet.fbDispoPortlet('getData','id');
        var pos = ui.item.index()+1; // index() is zero-based -> Db-Sortierung one-based
        
        if (id) {            
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/sortportlet',
                    data: {id:id,pos:pos}
                }, {
                    defaultError: "Interner Fehler bei Sortierung!"
                }
             );
        }
        return false;
    },
    'onRemovePortlet': function(){
        var data = $(this).fbDispoPortlet('getData');
        
        if (data.id) {                
             var id = data.id;
             if (!confirm('Moechten Sie den Tourenplan wirklick loeschen?')) return false;
             
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/removeportlet',
                    data: {id:id}
                }, {
                    defaultError: "Interner L�schen des Tourenplans!"
                }
             );
        }
        return false;
    },

    'onSetTourFarbklasse': function(fk) {
        var data = $(this).fbDispoRoute('getData');

        if (data.id) {
            var id = data.id;

            return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/set-Tour-Farbklasse',
                    data: {tour_id:id, fk:fk}
                }, {
                    defaultError: "Farbklasse konnte nicht gespeichert werden!"
                }
            );
        }
        return false;
    },

    // 'onPrintPortlet': defined in ....base.js
    'onCreateTimeline': function() {
        var $self = $(this);
        var data = $self.fbDispoTimelineDropzone('getData');
        var s = $self.fbDispoTimelineDropzone('getTimelineSettings');
        var re = true;
        
        data.start = s.start;
        data.end = s.end;
        data.interval = s.stepWidth;
        
        if (data.portlet_id && !data.id) {

             re = Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/addtimeline',
                    data: {data:data}
                }, {
                    defaultError: "Interner Fehler beim Anlegen der Zeitleiste!",
                    onsuccess: function(d){
                        $self.fbDispoTimelineDropzone('setData', 'id', d.id);
                        $self.fbDispoTimelineDropzone('addRoute', {IsDefault:1});
                    }
                }
             );
            return re;
        }
        return re;
    },
    'onSortTimeline': function(event, ui) {
        if (!ui || !ui.item) {
            console.error('115 init_DispoCalendarEvents.dispo.js onSortTimeline: Missing Property ui.item');
            return false;
        }

        var $timeline = ui.item;
        var id = $timeline.fbDispoTimelineDropzone('getData','id');
        var pos = ui.item.index()+1; // index() is zero-based -> Db-Sortierung one-based
        
        if (id) {
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/sorttimeline',
                    data: {id:id,pos:pos}
                }, {
                    defaultError: "Interner Fehler beim Sortieren der Zeitleiste!"
                }
             );
        }
        return false;
    },
    'onMoveTimeline': function(event, ui) {        
        var targetPortletId = $( this ).fbDispoPortlet('getData','id');
        if (!targetPortletId) {
            console.error('137 init_DispoCalendarEvents.dispo.js onMoveTimeline: Cannot retrieve targetPortletId by $(this).fbDispoPortlet(\'getData\', \'id\')', 'this', this);
            return false;
        }
        if (!ui || !ui.item) {
            console.error('140 init_DispoCalendarEvents.dispo.js onMoveTimeline: Missing Property ui.item');
            return false;
        }
        var $timeline = ui.item;
        var id = $timeline.fbDispoTimelineDropzone('getData','id');
        var pos = ui.item.index()+1; // index() is zero-based -> Db-Sortierung one-based
        
        if (id) {
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/movetimeline',
                    data: {id:id,pos:pos,portlet_id:targetPortletId}
                }, {
                    defaultError: "Interner Fehler beim Ausf�hren der Aktion!"
                }
             );
        }
        return false;
    },
    'onRemoveTimeline': function() {
        var data = $(this).fbDispoTimelineDropzone('getData');
        
        if (data.id) {             
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/removetimeline',
                    data: {id:data.id}
                }, {
                    defaultError: "Interner Fehler beim L�schen der Zeitleiste!"
                }
             );
        } else {
            alert('#459 Fehlende id f�r Zeitleiste');
        }
        return false;
        
    },
    // Keine Ahnung, wann und ob diese Funktion aufgerufen wird!!
    // Wenn Vorgaenge in die Timeline gezogen werden, wird onDropRoute aufgerufen!!!
    // onCreateRoute ist fuers Hinzuf�gen gedacht, dass nicht auf Dragn'Drop basiert
    'onCreateRoute': function() {
        alert('#218 onCreateRoute');
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        
        data.datum = data.date;
        if (data.portlet_id && !data.id) {
            
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/droproute',
                    data: {data:data}
                }, {
                    defaultError: "Interner Fehler beim Anlegen der Tour!",
                    onsuccess: function(d){
                        $self.fbDispoTimelineDropzone('setData', 'id', d.id);
                    }
                }
             );
        }
        return false;
    },
    'onDropPoolRoute': function() {
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        var timeStart = $self.fbDispoRoute('option', 'timeStart');
        var timeDuration = $self.fbDispoRoute('option', 'timeDuration');
        data.ZeitVon = $.minutesToTime(timeStart);
        data.ZeitBis = $.minutesToTime(timeStart + timeDuration);
        
        if ( !$self.attr("id") || $self.attr("id") == 1 ) {
            $self.attr({id: "Drop-Pool-Route-Tmp-" + parseInt( Math.random() * 100000000)});
        }
        
        if ( !$("#PoolAddDialog").length ) {
            $("body").append( 
                $("<div/>").attr({
                    id:"PoolAddDialog",
                    title:"Poolvorgang"
                }).append( $("<div/>").attr({id:"PoolAddContent"}))
            );
            
            $("#PoolAddDialog").dialog({autoOpen:false,width:550,modal:true});
        }
        $("#PoolAddContent", "#PoolAddDialog").load(
            Fb.AppBaseUrl + '/vorgaenge/insertpool?mandant=110&tourNodeId='+$self.attr("id")+'&layout=0&'+decodeURIComponent($.param(data))
        );
        $("#PoolAddDialog").dialog('open');
        $("#PoolAddDialog").dialog('option', 'close', function(){
            //alert( "#191 init_DispoCalendarEvents.dispo.js onclose Dialog!");
            if ( $self.attr("id").match(/Drop-Pool-Route-Tmp/)) $self.remove();
        });
    },
    'onDropRoute': function() {
        Fb.DispoLoading(1);
        var self = this;
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        if ( $self.attr("class").match(/\bIs-Pool\b/) && !data.Auftragsnummer ) {
            return Fb.DispoCalendarEvents.onDropPoolRoute.apply(this);
        }
        var timeStart = $self.fbDispoRoute('option', 'timeStart');
        var timeDuration = $self.fbDispoRoute('option', 'timeDuration');
        data.ZeitVon = $.minutesToTime(timeStart);
        data.ZeitBis = $.minutesToTime(timeStart + timeDuration);


        
        if (!data.timeline_id) {
            alert('Interner Fehler: Fehlende Timeline-ID');
            $(self).remove();
            Fb.DispoLoading(0);
            return false;
        }
        if (data.id) {
            alert('Interner Fehler: Es wurde versucht eine Tour mit bereits bestehender ID '+data.id + ' anzulegen!');
            $(self).remove();
            Fb.DispoLoading(0);
            return false;
        }
        if (!data.id) { 
             
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/droproute',
                    data: { data:data }
                }, {
                    defaultError: "Interner Fehler beim Anlegen der Tour!",
                    onsuccess: function(d){
                        if ("data" in d && "ZeitVon" in d.data && "id" in d.data) {
                            $self.fbDispoRoute( 'setData', d.data);
                        } else {
                            $self.fbDispoRoute('setData', 'id', d.id).fbDispoRoute('setData', 'tour_id', d.id);
                        }
                    },
                    onerror: function(jqXHR, textStatus) {
                        console.error('#250 init_DispoCalendarEvents.dispo.js error-callback', 'xhr', jqXHR, 'clear element self:', self);
                        $(self).remove();
                    }
                }
             );
        }
        Fb.DispoLoading(0);
        return false;
    },
    'onMoveRoute': function() {
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        var tl = $self.fbDispoRoute('getTimeline');
        
        data.timeline_id = tl.fbDispoTimelineDropzone('getData').id;
        
        var timeStart = $self.fbDispoRoute('option', 'timeStart');
        var timeDuration = $self.fbDispoRoute('option', 'timeDuration');
        
        data.ZeitVon = $.minutesToTime(timeStart);
        data.ZeitBis = $.minutesToTime(timeStart + timeDuration);       
        
        if (data.timeline_id && data.id) {
             
              return Fb.AjaxTourRequest({
                    data: {data:data},
                    url: Fb.AppBaseUrl + '/touren/ajax/moveroute'
                }, {
                    defaultError: "Interner Fehler beim Verschieben der Tour!",
                    onsuccess:function(d, textStatus, jqXHR) {
                        $self.fbDispoRoute('setData', $.extend({}, data, d.data) );
                    },
                    onerror:function(jqXHR) {
                        var d = '';
                        if (jqXHR.responseText){
                            try {
                                d = $.parseJSON( jqXHR.responseText )
                            } catch(e) {
                                d = jqXHR.responseText;
                            }
                        }
                        if (typeof(d)=="object" && d.data) {
                            $self.fbDispoRoute('setData', $.extend({}, data, d.data) );
                        }
                    }
                }
             );
        }
        return false;
    },
    'onResizeRoute': function() {
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        var timeStart = $self.fbDispoRoute('option', 'timeStart');
        var timeDuration = $self.fbDispoRoute('option', 'timeDuration');
        
//        alert('#527 fbDispoCalendar timeStart:'+timeStart+', timeDuration:'+timeDuration);
        data.ZeitVon = $.minutesToTime(timeStart);
        data.ZeitBis = $.minutesToTime(timeStart + timeDuration);
        
        if (data.timeline_id && data.id) {
            
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/resizeroute',
                    data: {data:data}
                }, {
                    defaultError: "Interner Fehler beim Skalieren der Tour!",
                    onsuccess:function(d) {
                        $self.fbDispoRoute('setData', $.extend({}, data, d.data) );
                    },
                    onerror:function(jqXHR) {
                        var d = '';
                        if (jqXHR.responseText){
                            try {
                                d = $.parseJSON( jqXHR.responseText )
                            } catch(e) {
                                d = jqXHR.responseText;
                            }
                        }
                        if (typeof(d)=="object" && d.data) $self.fbDispoRoute('setData', $.extend({}, data, d.data) );
                    }
                }
             );
        }
        return false;
    },
    'onRemoveRoute': function() {
        var $self = $(this);
        var data = $self.fbDispoRoute('getData');
        
        if (data.id) {
             if (confirm('Eintrag loeschen?')) {
                return Fb.AjaxTourRequest({
                       data: {id:data.id},
                       url: Fb.AppBaseUrl + '/touren/ajax/removeroute'
                   }, {
                       defaultError: "Interner Fehler beim Entfernen der Tour!"
                   }
                );
             }
        }
        return false;
    },
    'onAddResource': function() {
        alert('onAddResource');
    },
    'onDropResource': function() {
        var $self = $(this);
        var data = $self.fbDispoResource('getData');
        var filter = {tour_id: data.tour_id};
        var CountConcurrency = Fb.DispoCheckConcurrency(filter);
        
        if (CountConcurrency) {
            //alert('#460 fbDispoCalenday.js onDropResource CountConcurrency: '+CountConcurrency);
            var lc = Fb.DispoListConcurrency(filter);
            var list = 'Die Tour wurde vor kurzem von anderen Usern bearbeitet:\n';
            for(var i in lc) {
                list+= '- ' + lc[i].zugriffszeit + lc[i].user_name + ' ' + lc[i].aktion + "\n";
            }
            alert(list);
        }
        
        if (data.route_id && !data.id) {
            
            return Fb.AjaxTourRequest({
                   data: {data:data},
                   url: Fb.AppBaseUrl + '/touren/ajax/dropresource'
               }, {
                   defaultError: "Interner Fehler beim Ablegen der Resource!",
                   onsuccess: function(d){
                       $self.fbDispoResource('setData', 'id', d.id);
                   }
               }
            );
        } else {
            return false;
        }
        $self.fbDispoResource('destroy');
        return false;
    },
    'onRemoveResource': function(data) {
//        var m='#196 onRemoveResource routeData: '; for(i in data) m+= i+':'+data[i]+'\n'; alert(m);
//        alert('#397 onRemoveResource typeof(data):' + typeof(data) );
        
        if (data.resourceType && data.id) {                
             
             return Fb.AjaxTourRequest({
                    data: {id:data.id, resourceType:data.resourceType},
                    url: Fb.AppBaseUrl + '/touren/ajax/removeresource'
                }, {
                    defaultError: "Interner Fehler beim Entfernen der Ressource!"
                }
             );
        } else {
           // Noch nicht gespeicherte Resource
           return true;
        }
    },
    'onCreateRouteDefaults': function() {
        var $self = $(this);
        var data = $self.fbDispoRouteDefaults('getData');
        data.datum = data.date;
        data.IsDefault = 1;
//        var m='';for(var i in data) m+= i+':'+data[i]+'\n';
//        alert('#398 fbDispoCalendar a onCreateRouteDefaults\narguments.length:'+arguments.length+
//              '\ntimeline_id:'+data.timeline_id+
//              '\nid ('+typeof(data.id)+':'+data.id+
//              '\n'+m);
    
        if (data.timeline_id && (('id' in data)==false || !parseInt(data.id))) {

             return Fb.AjaxTourRequest({
                    data: {data:data},
                    url: Fb.AppBaseUrl + '/touren/ajax/droproute'
                }, {
                    defaultError: "Interner Fehler beim Anlegen der Standard-Ressourcen-Leiste!",
                    onsuccess: function(d){
                        $self.fbDispoRouteDefaults('setData', 'id', d.id)
                             .fbDispoRouteDefaults('setData', 'tour_id', d.id)
                             .attr("id", "fbDispoRouteDefaults_" + d.id);
                    },
                    onerror: function(xhr){}
                }
             );
        }
        return false;
    },
    'onDropResourceDefaults': function() {
        //alert('#398 fbDispoCalendar onDropResourceDefaults\narguments.length:'+arguments.length);
        var $self = $(this);
        var data = $self.fbDispoResource('getData');
        var re = false;
        var eventCallbackFunction = 'onDropResourceDefaults';
        
        if (data.route_id && !data.id) {                
             var hasRoutes = $self.closest("div.fbDispoTimelineDropzone").find("div.fbDispoRoute").length;
             var cnfRouteDef = window.Fb.DispoCalendarSettings.routeDefaults;

            if (!cnfRouteDef.askForApplyDefaults && "applyDefaults" in cnfRouteDef) {
                var applyDefaults = cnfRouteDef.applyDefaults;
            } else {

                var applyDefaults = hasRoutes && confirm(
                    'Soll die Ressource fuer ' + hasRoutes + ' bereits gebuchte Vorgaenge in der Zeitleiste uebernommen werden?\n' +
                    'OK => Ja\nAbbrechen => Nein'
                ) ? 1 : 0;
            }
             
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/dropresource',
                    data: {data:data, applyDefaults:applyDefaults}
                }, {
                    defaultError: "Interner Fehler beim Speichern der Resource!",
                    onsuccess: function(d) {
                        $self.remove();
                        if ("data" in d && "inserts" in d.data) {
                            var inserts = d.data.inserts;
                            for(var i = 0; i < inserts.length; i++) {
                                var tour = inserts[i]['tour'];
                                var rsrc = inserts[i]['rsrc'];
                                var isDef = parseInt(tour['IsDefault']);

                                if (isDef === 1) {
                                    $("#fbDispoRouteDefaults_" + tour['tour_id']).fbDispoRouteDefaults( 'addResources', [rsrc]);
                                } else {
                                    $("#fbDispoRoute_" + tour['tour_id']).fbDispoRoute('addResources', [rsrc]);
                                }
                            }
                        } else {
                            console.error(eventCallbackFunction, "No Insert-Data Found to add new ressources!");
                        }
                        // $self.fbDispoResource('destroy');

                        // Fb.ReloadTimelineResources( $self.closest("div.fbDispoRouteDefaults").fbDispoRouteDefaults('getTimeline')  );
                    },
                    onerror: function() {
                        $self.remove();
                        // Fb.ReloadTimelineResources( $self.closest("div.fbDispoRouteDefaults").fbDispoRouteDefaults('getTimeline')  );
                    }
                }
             );

        }
        return false;
    },
    'onMoveResourceDefaults': function(srcParent, e, ui) {
          
        //alert('#398 fbDispoCalendar onDropResourceDefaults\narguments.length:'+arguments.length);
        var $self = $(this);
        var data = $self.fbDispoResource('getData');
        var filterData = {};
        for(var i in data) if (typeof(i) !="object" && typeof(data[i])!="object") filterData[i] = data[i];
        var re = false;

        if (data && data.route_id && data.id) {                
             var hasRoutes = $self.closest("div.fbDispoTimelineDropzone").find("div.fbDispoRoute").length;
             var applyDefaults = hasRoutes && confirm(
                'Soll die Resource fuer ' + hasRoutes + ' bereits gebuchte Vorgaenge in der Zeitleiste uebernommen werden?\n' +
                'OK => Ja\nAbbrechen => Nein'
             ) ? 1 : 0;
            
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/moveresource',
                    data: {data:filterData, applyDefaults:applyDefaults}
                }, {
                    defaultError: "Interner Fehler beim Verschieben der Resource!",
                    onsuccess: function(d) {
                        $self.fbDispoResource('setData', 'id', d.id);
                        var dstParent = $self.closest("div.fbDispoRouteDefaults");
                        Fb.ReloadTimelineResources( $( dstParent).fbDispoRouteDefaults('getTimeline')  );
                        Fb.ReloadTimelineResources( $( srcParent).fbDispoRouteDefaults('getTimeline')  );
                    },
                    onerror: function() {
                        Fb.ReloadTimelineResources( $self.closest("div.fbDispoRouteDefaults").fbDispoRouteDefaults('getTimeline')  );
                        Fb.ReloadTimelineResources( $( srcParent).fbDispoRouteDefaults('getTimeline')  );
                    }
                }
             );
            
        }
        return false;
    },
    // ACHTUNG !!
    // Bis jetzt unerkl�rlicher Namenskonflikt, wenn data in die Parameterliste geschrieben wird
    // sind zwar noch alle Felder von data vorhanden, aber ohne Werte (undefined) ??
    // Daher statt function(data) ==> function() { var data = arguments[0]; ...
    'onRemoveResourceDefaults': function() {
//        var m='#196 onRemoveResource routeData: '; for(i in data) m+= i+':'+data[i]+'\n'; alert(m);
        var self = this;
        var data = arguments[0];

        if (("resourceType" in data)
            && ("id" in data)
            && data.resourceType
            && data.id) {

            var removeResourcesByRows = function(rows) {
                if (rows.length) {
                    for(var i = 0; i < rows.length; i++) {
                        var itm = rows[i];
                        var tourId = itm['tour_id'];
                        var rsrcType = itm['type'];
                        var rsrcId = itm['id'];

                        var rsrcObj = $("#fbDispoResource_"+rsrcType+rsrcId);
                        if (!rsrcObj.length) {
                            continue;
                        }
                        var elmData = rsrcObj.data("fbDispoResource");
                        var parentObj = ("_parent" in elmData) ? elmData._parent : null;
                        var parentObjFnc = ("_parentJqFunction") ? elmData._parentJqFunction : null;

                        if (parentObj && parentObjFnc) {
                            rsrcObj.remove();
                            $(parentObj)[parentObjFnc]('addResources', []);
                        } else {
                            console.error("ParentObj not Found of Item: ", itm);
                        }

                    }
                }
            };

             var callbackResult = Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/removeresourcedefault',
                    data: {id:data.id, resourceType:data.resourceType}
                }, {
                    defaultError: "Interner Fehler beim Entfernen der Resource!",
                    onsuccess: function(data, textStatus, jqXHR) {
                        var rows = ("data" in data && "rows" in data.data && data.data.rows[0])
                                    ? data.data.rows
                                    : [];

                        removeResourcesByRows( rows );
                        return true;
                    },
                    onerror: function(jqXHR, textStatus) {
                        console.error({
                            'line':601,
                            'file':'init_DispoCalendarEvents.dispo.js',
                            'callback':'onerror',
                            textStatus, jqXHR
                        });
                        var data = JSON.parse(jqXHR.responseText);

                        var rows = ("data" in data && "rows" in data.data && data.data.rows[0])
                            ? data.data.rows
                            : [];

                        removeResourcesByRows( rows );
                        return false;
                    }
                }
             );

        }
        else {
            console.error({
                'line':624,'file':'init_DispoCalendarEvents.dispo.js',
                'CANNOT sendRequest data': data
            });
        }

        return false;
    }
});


