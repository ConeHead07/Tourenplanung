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
                        
                        $self.fbDispoPortlet('setTitle', 'Tournr <span class="p-tagesnr">' + dbdata.tagesnr + '</span> <span class="p-holiday">' + dbdata.holiday + '</span> <span class="p-top25">' + top25 + '</span> <span class="p-title">' + title + '</span>');
                    }
                }
             );
        }
        var title = ( dbdata.title ) ? dbdata.title : '';
	var top25 = ( dbdata.topcustom ) ? dbdata.topcustom : '';
        $self.fbDispoPortlet('setTitle', 'Tournr <span class="p-tagesnr">' + dbdata.tagesnr + '</span> <span class="p-holiday">' + dbdata.holiday + '</span> <span class="p-top25">' + top25 + '</span> <span class="p-title">' + title + "</span>");
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
    // 'onPrintPortlet': defined in ....base.js
    'onCreateTimeline': function() {
        var $self = $(this);
        var data = $self.fbDispoTimelineDropzone('getData');
        var s = $self.fbDispoTimelineDropzone('getTimelineSettings');
        
        data.start = s.start;
        data.end = s.end;
        data.interval = s.stepWidth;
        
        if (data.portlet_id && !data.id) {

             return Fb.AjaxTourRequest({
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
        }
        return false;
    },
    // Keine Ahnung, wann und ob diese Funktion aufgerufen wird!!
    // Wenn Vorgaenge in die Timeline gezogen werden, wird onDropRoute aufgerufen!!!
    // onCreateRoute ist fuers Hinzufügen gedacht, dass nicht auf Dragn'Drop basiert
    'onCreateRoute': function() {
        alert('#81 onCreateRoute');
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
            return false;
        }
        if (data.id) {
            alert('Interner Fehler: Es wurde versucht eine Tour mit bereits bestehender ID '+data.id + ' anzulegen!');
            return false;
        }
        if (!data.id) { 
             
             return Fb.AjaxTourRequest({
                    url: Fb.AppBaseUrl + '/touren/ajax/droproute',
                    data: { data:data }
                }, {
                    defaultError: "Interner Fehler beim Anlegen der Tour!",
                    onsuccess: function(d){
                        $self.fbDispoRoute('setData', 'id', d.id).fbDispoRoute('setData', 'tour_id', d.id);
                    }
                }
             );
        }
        return false;
    }
});


