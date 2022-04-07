/* 
 * author: Frank Barthold
 * DispoRoute
 */
(function($) {
    var dataKey = 'fbDispoRouteDefaults';
    
    var eventTriggerMap = {
            'create': 'createRouteDefaults',
               'add': 'addRouteDefaults',
            'remove': 'removeRouteDefaults',
              'drop': 'dropRouteDefaults',
              'move': 'moveRouteDefaults',
            'resize': 'resizeRouteDefaults',
            'select': 'selectRouteDefaults',
             'hover': 'hoverRouteDefaults',
             'leave': 'leaveRouteDefaults',
      'hoverDetails': 'hoverRouteDetails',
       'showDetails': 'showRouteDefaultsDetails',
       'hideDetails': 'hideRouteDefaultsDetails',
      'dropResource': 'dropResourceDefaults',
      'moveResource': 'moveResourceDefaults',
    'removeResource': 'removeResourceDefaults'
    };
    
    var defaults = {
                 'data': null,
              'dataUrl': '',
            'timeStart': 0,
         'timeDuration': 0,
                 'test': 0,
              '_parent': null,
    '_parentJqFunction': null,
    
          'isDroppable': true,
          
                'onAdd': null, //*null*/ function(){ alert(dataKey + '.onAdd Default-Dummie-Handler'); },
             'onRemove': null, //*null*/ function(){ alert(dataKey + '.onRemove Default-Dummie-Handler'); },
             'onSelect': null, //*null*/ function(){ alert( $(this).data(dataKey).data.ZeitVon + '.onSelect Default-Dummie-Handler'); },
               'onDrop': null, //*null*/ function(){ alert(dataKey + '.onDrop Default-Dummie-Handler'); },
               'onMove': null, //*null*/ function(){ alert(dataKey + '.onMove Default-Dummie-Handler'); },
             'onResize': null, //*null*/ function(){ alert(dataKey + '.onResize Default-Dummie-Handler'); },
              'onHover': null,
              'onLeave': null,
        'onShowDetails': null, //*null*/ function(){ alert(dataKey + '.onShowDetails Default-Dummie-Handler'); },
        'onhideDetails': null, //*null*/ function(){ alert(dataKey + '.onHideDetails Default-Dummie-Handler'); },

       'onDropResource': null, //*null*/ function(){ alert("#39 " +dataKey + '.onDropResource Default-Dummie-Handler'); },
       'onMoveResource': null, //*null*/ function(){ alert(dataKey + '.onMoveResource Default-Dummie-Handler'); },
        
     'draggableOptions': Fb.DragRouteInstanceSettings,
     'droppableOptions': Fb.DropRsrcOnRouteSettings
    };
    
    var methods = {
        '_init': function() {
            
            // Do Some Rendering after first initializing
            var self = this;
            var $self = $( this );
            var data = $self.data(dataKey);
            
            if (data.data === null) data.data = {};
            
            if (!$self.attr('id') && ('id' in data.data) ) {
                $self.attr('id', dataKey + '_'+data.data.id);                
            }
            
            if (!data._parent) {
                data._parent = $self.closest("div.fbDispoTimelineDropzone");
                data._parentJqFunction = 'fbDispoTimelineDropzone';
            }
            
            
            $self.addClass('DropZoneDefaults Drag-Route ' + dataKey + '')
            .css({
                position:'absolute',
                left:0,
                bottom:0,
                width:'100%',
                height:19,
                zIndex:3000,
//                opacity:0.5,
                display:'block', //'none'
                borderLeft:0,
                borderRight:0,
                borderBottom:0,
                backgroundImage:'none',
                backgroundColor:'#fff'
            })
            .addClass('ui-state-default')
//            .droppable(Fb.DropRsrcOnRouteSettings)
            .droppable($.extend({}, Fb.DropRsrcOnRouteSettings, {accept: Fb.DropRsrcOnRouteSettings.accept+',.is-default-resource'}) )
            .bind('RsrcDropped', function(e, ui) {
                alert('Resource dropped on Resource-Defaults!');
            })
            .append( $("<div class='resourcesStat' />").css({display:'inline'}) )
            .append( $( "<ul class='resources' />").css({display:'inline'}) );
           
//            alert(dataKey + ' accept: ' + $self.droppable('option','accept'));
            if ($self.data('dragdata')) {
               $.extend(data.data, $self.data('dragdata'));
            }
            
            if (data.isDroppable) {
                methods._mkRouteDroppableOnClick.apply(this);
                $self.bind('drop', function(e,ui){    });
            }
            

            methods.reInitData.apply( this );
            methods.initHandler.apply( this );

            methods._trigger.apply( this, [this, 'create']);
            
            if ($(this).data(dataKey).data.resources) {
                methods.addResources.apply(self, [$(this).data(dataKey).data.resources] );
                delete $(this).data(dataKey).data.resources;
            }
            
            $(this).show();
        },
        'initHandler': function() {
            var self  = this;
            var $self = $( this );
            
            $self.hover(
                function(){
                    $self.data('MouseIsOver', true);
               }, 
               function(){ 
                   $self.data('MouseIsOver', false);
                   methods._trigger.apply( self, [self, 'leave', self]);
               }
            )
            .bind('dblclick', function() {
                Fb.showRouteDetails( $self, {pages:{Resources: 'Ressourcen',Options:'Einstellungen'}} );
            });
            $self.bind('mouseleave', function() {
                methods._trigger.apply( self, [self, 'leave', self]);                
            });
            
            var iconOpts = {position:"absolute", display:"block", left:0,
               top:0, width:"15px", height:"15px", background:"#fff" 
            }
            // Detail-Info-Handler
            $self.css({paddingLeft:20}).append( $("<div/>").css(iconOpts)
            .addClass("ui-corner-all")
            .append( $("<span/>").addClass("ui-icon ui-icon-info") )
            .hover(function(){
                    $(this).addClass("ui-state-hover");
                    
                    $self.data('MouseIsOver', true);
                    
                    setTimeout( function() {
                        if ($self.data('MouseIsOver')) 
                            methods._trigger.apply( self, [self, 'hover', self]);
                    }, 1000);
                
                    }, 
                   function(){
                       $(this).removeClass("ui-state-hover");
               } )
            .click(function() {
                Fb.showRouteDetails( $self, {pages:{Resources: 'Ressourcen',Options:'Einstellungen'}} );
            })
            );
            
            $self.bind('showDetails', function(e, tabs) {
                methods._trigger.apply( this, [this, 'showDetails', tabs]);
            });
            $self.bind('hideDetails', function(e) {
                methods._trigger.apply( this, [this, 'hideDetails']);
            });
            
            // Bind Resource Dropping and Moving
            $( this ).bind('resource-dropped', methods.addDroppedResource);
            $( this ).bind('resource-moved',   methods.addDroppedResource);
        },
        'resfresh': function(dbdata) {
            var self = this;
            var $self = $( this );
            methods.setData.apply(this, [dbdata]);
            var data = $self.data(dataKey);
            
            methods.reInitData.apply( this );
            methods.initHandler.apply( this );
            
            $("ul.resources li").remove();
            
            if (data.data.resources) {
                methods.addResources.apply(self, [data.data.resources] );
                delete data.data.resources;
            }

            methods._trigger.apply( this, [this, 'reload']);
            
        },
        'sortResources': function() {
//            var items = $.makeArray( $('ul.resources li', $(this) ) );
//            
//            items.sort(function(a,b){
//              var keyA = $(a).find('div:first').attr("class").match(/\bRsrcType-([^ ]*)/);
//              var keyB = $(b).find('div:first').attr("class").match(/\bRsrcType-([^ ]*)/);
//              
//              if (!keyA || !keyB) return (keyA ? -1 : 1);
//
//              if (keyA[1] < keyB[1]) return -1;
//              if (keyA[1] > keyB[1]) return 1;
//              if ($(a).text() < $(b).text()) return -1;
//              if ($(a).text() > $(b).text()) return 1;
//              return 0;
//            });
//            var ul = $('ul.resources', $(this) );
//            $.each(items, function(i, li){
//              ul.append(li);
//            });
        },
        'getResourcesUrl': function() {
            var data  = $( this ).data(dataKey);
            if (data.rsrcUrl) return data.rsrcUrl;
            
            var urlTpl = $( this ).closest( "div.fbDispoCalendar").fbDispoCalendar( "getObjectData", "resourcesUrlTpl");
            return (urlTpl.match(/{#ROUTEID}/)) ? urlTpl.replace(/{#ROUTEID}/, data.data.id) : urlTpl+data.data.id;
        },
        'initResourceUrl': function() {
            var data  = $( this ).data(dataKey);
            if (!data.rsrcUrl) data.rsrcUrl = methods.getResourcesUrl.apply( this );
            return data.rsrcUrl;            
        },
        'getDataUrl': function() {
            var data  = $( this ).data(dataKey);
            if (data.rsrcUrl) return data.rsrcUrl;
            
            var urlTpl = $( this ).closest( "div.fbDispoCalendar").fbDispoCalendar( "getObjectData", "routeUrlTpl");
            return (urlTpl.match(/{#ROUTEID}/)) ? urlTpl.replace(/{#ROUTEID}/, data.data.id) : urlTpl+data.data.id;
        },
        'reloadResources': function() {
            var data  = $( this ).data(dataKey);
            if (!data.rsrcUrl) methods.initResourceUrl.apply( this );
            var rsrcData = null;
            
            if  ( data.rsrcUrl ) {
            // Prüfen, ob rsrcUrl gesetzt ist, wenn ja Daten von Url nachladen     
                 $.ajax({
                    type: 'GET', dataType: 'json', async: false, data: {id:data.data.id},
                    url: data.rsrcUrl,
                    'success':function(data) {rsrcData = data.data;},
                    'error':function(a,b,c) {
                        alert('#240 Reload-Resource-Error: a:'+a+'; b:'+b+'; c:'+c);
                    }
                 });                
            } else {
                alert('#206 '+dataKey+'.reloadResourcesFromUrl Error missing rsrcUrl!');
            }
            methods.reloadResourcesByData.apply( this, [rsrcData] );            
        },
        'reloadResourcesByData': function(rsrcData) {
            var self  = this;
            var $self = $( this );
            
            $("ul.resources li", $self).remove();
            if (rsrcData && typeof(rsrcData) == "object" && rsrcData.length) {
                methods.addResources.apply( self, [rsrcData] );
            }
            methods.refreshResourcesStat.apply( self );
        },
        'refreshResourcesStat': function() {
            
            $(".resourcesStat", $(this) ).html("");
            var rsrcStat = new Array();
            var rsrcList = new Array();
            $("ul.resources .fbDispoResource", $(this) ).each(function() {
                var c = ("string" === typeof $( this ).attr('class')) ? $( this ).attr('class') : "";
                var m = c.match(/\bRsrcType-([^ ]*)/);
                var t = (m ? m[1] : 'Rsrc');
                if (!(t in rsrcStat)) rsrcStat[t] = 1;
                else ++rsrcStat[t];
                
                rsrcList[rsrcList.length] = $(this).text();
                //alert($(this).text() + '\nm: ' + m + "\n" + rsrcStat);
            });
            
            for(var i in rsrcStat) {
                $(".resourcesStat", $(this) ).append(  $("<span rel='"+i+"' style='margin-right:5px;'>"+i+" "+rsrcStat[i]+"</span> &nbsp;")  );
            }
            
//            $(".resourcesStat", $(this) ).append( rsrcList.join(', &nbsp; '));
        },
        'addDroppedResource': function(e, objRsrc, ui) {
//            alert('#142 '+dataKey+'.addDroppedResource!\narguments.length:'+arguments.length+'\n'+flatObjToString($.makeArray(arguments)));
            var self = this;
            var resourceOpts = $(objRsrc).fbDispoResource('options');
            var resourceSetOpts = {
                _parent:$(this),
                _parentJqFunction:dataKey,
                _parentFrom:(resourceOpts && ('_parent' in resourceOpts) ? resourceOpts._parent : null),
                data: ui.helper.data('dragdata')
            }
            
            $(objRsrc).data('dragdata', resourceSetOpts.data);
            if (objRsrc == ui.draggable || objRsrc.is(".is-default-resource")) {
//                var m = '#296 ' + dataKey + '.addDroppedResource moved dragdata: ';
//                for(var i in objRsrc.data('dragdata') ) m+= i+'='+objRsrc.data('dragdata')[i]+"\n";
//                alert(m);                
//                var src = (ui && ui.draggable) ? ui.draggable : null;
//                var src_parent = (src && src.data(dataKey) && src.data(dataKey)._parent) ? src.data(dataKey)._parent : null;
                
                // Ein bereits hinzugefügtes wurde verschoben
                objRsrc.fbDispoResource('options', resourceSetOpts );
                objRsrc.fbDispoResource('_moved', e, ui);
                
            } else {
                // Neu hinzugefügt
                //alert('#294 Resource was dropped!');
                objRsrc.fbDispoResource( resourceSetOpts );
                objRsrc.fbDispoResource('_dropped', e, ui);
            }
            
            if (!this || !$(this).length) return false;
            
            methods.sortResources.apply( this );
            methods.refreshResourcesStat.apply( this );            
//          var m = 'dragdata: ';for(var i in resourceSetOpts.data) m+= i+', '; alert(m);
        },
        'addResources': function(resources) {
//            alert("#314 "+dataKey+".addResources [this.class:"+$(this).attr("class")+"\nresources: "+resources.length);
            for(var ti in resources) {
                methods.addResource.apply(this, [ resources[ti] ] );
            }
            methods._trigger.apply(this, [this, 'loadResources']);
            
            methods.sortResources.apply( this );
            methods.refreshResourcesStat.apply( this );
        },
        'addResource': function(addData) {
//            alert("#107 "+dataKey+".addResource [this.class:"+$(this).attr("class")+"\naddData: "+addData.length);
            
//            var xdata = addData;
//            var m='#163 '+dataKey+' addResource addData: '; for(var i in xdata) m+= i+':'+xdata[i]+'\n'; alert(m);
            var self = this;
            var $self = $(self);
            if (!addData) addData = {name: '??'};

            var t = $('<div />').text(addData.name);
            if (!t || !t.length) {
                alert('Unbekannter Systemfehler. Ressource konnte nicht initialisiert werden!');
                return false;
            }
            
            $("ul.resources:first", $self).append( $("<li/>").css({display:'inline',marginLeft:5}).append( t )  );
            t.addClass('is-default-resource');
            // Hier wird das Parent-Element an fbDispoResource uebergeben
            // _parent
            // _parentJqFunction
            $.fn.fbDispoResource.apply(t, [ {
                             data: addData,
                        '_parent': $(this),
              '_parentJqFunction': dataKey} ] 
            );
            t.data('dragdata',addData); //.removeClass('Drag-Rsrc');
            t.removeClass('Drag-Rsrc');
        },
        'getTimeline': function() {
            return $( this ).closest("div.fbDispoTimelineDropzone");
        },
        'getResources': function() {
            return $("ul.resources li .Drag-Rsrc", $(this));
        },
        'getRoutes': function() {
            var data = $(this).data(dataKey);            
            return (data && data._parent) ? $("div.fbDispoRoute", data._parent).add(this) : $();
        },
        'setTimeSlot': function(timeRange) {
            //alert( '#137 '+dataKey+'.setTimeSlot [this.class:'+$(this).attr('class')+']\n data(dataKey): ' + $(this).data(dataKey) + ', ' + $(this).data(dataKey).data +"; " + $(this).data(dataKey)._parent);
            var $self = $(this);
            var data = $self.data(dataKey);
            var routeData = $self.data("route");
            if (timeRange && timeRange.from) data.data.ZeitVon = timeRange.from;
            if (timeRange && timeRange.to)   data.data.ZeitBis = timeRange.to;
            
            var lineal = $(data._parent).fbDispoTimelineDropzone('getTimelineLineal');
            var ts = $(lineal).data("fbDispoTimelineLineal").settings;
            
            routeData.start = $.timeToMinutes( data.data.ZeitVon );
            routeData.end   = $.timeToMinutes( data.data.ZeitBis );
            routeData.duration = routeData.end - routeData.start;
            
            if (routeData.duration < ts._stepWidthM) routeData.duration = ts._stepWidthM;
            
            this.style.position   = 'absolute';
            this.style.top   = $(lineal).height();
            this.style.left  = ((routeData.start-ts._startM)*100/ts._totalM)+"%";
            this.style.width = (routeData.duration*100/ts._totalM)+"%";
            this.style.height= $self.parent().height()-$(lineal).height()-8;
//            alert('#189 '+dataKey+' '+this.style.left +"=("+(routeData.start-ts._startM)+"*"+100+"/"+ts._totalM+")+%");
        },
        // Will be call from procedures in script fb.dnd.settings.js
        '_moved': function() {
//            alert('#95 ' + dataKey + '._moved!');
            methods.reInitData.apply( this );
            methods._trigger.apply( this, [this, 'move']);
        },
        // Will be call from procedures in script fb.dnd.settings.js
        '_resized': function() {
//           alert('#100 ' + dataKey + '._resized!');
            var $self = $(this);
            var routeData = $self.data('route');
            var data = $self.data(dataKey);
            data.timeStart = routeData.start;
            data.timeEnd = routeData.end;
            data.timeDuration = routeData.duration;
            
            methods._trigger.apply( this, [this, 'resize']);
        },
        '_dropped': function() {
            methods.reInitData.apply( this );
            methods._trigger.apply( this, [this, 'drop']);
        },
        'reInitData': function() {
            // Mandant, Auftragsnummer, timeline_id, DatumVon, ZeitVon, DatumBis, ZeitBis
            var data = $(this).data(dataKey);
            if (data.data === null) data.data = {};
            if (data._parent && data._parentJqFunction) {
                var p = data._parent;
                var pf= data._parentJqFunction;
                
                data.data.timeline_id = p[pf].apply(p, ['getData','id']);
                data.date = p[pf].apply(p, ['option','date']);
                data.data.DatumVon = p[pf].apply(p, ['option','date']);
                data.data.DatumBis = p[pf].apply(p, ['option','date']);
                
                
                var lineal = $(data._parent).fbDispoTimelineDropzone('getTimelineLineal');
                var ts = $(lineal).data("fbDispoTimelineLineal").settings;
                data.data.ZeitVon = ts.start;
                data.data.ZeitBis = ts.end;
                
            }
        },
        'draggable': function() {
            
            $( this ).draggable.call( arguments );
        },
        'droppable': function() {
           $( this ).droppable.call( arguments );
        },
        'resizable': function() {
           $( this ).resizable.call( arguments );
        },
        'getDraggableOptions': function() {
           return $( this ).draggable.call( arguments );
        },
        'getDroppableOptions': function() {
           return $( this ).droppable.call( arguments );
        },
        'getResizableOptions': function() {
           return $( this ).resizable.call( arguments );
        },
        'destroy': function() {
           methods._trigger.apply( this, [this, 'remove']);
           $(this).removeData(dataKey);
           $(this).remove();
           // Do some Cleanup-Work like undo rendering, etc
        },
        '_mkDroppable': function() {
           if ( "string" !==typeof $( this ).attr('class') || !$( this ).attr('class').match(/\bDragRoute-Focus\b/) ) {
               $("div.Drag-Route").removeClass("DragRoute-Focus");
               $(this).addClass("DragRoute-Focus");
               $("div.Drag-Route:not(.DragRoute-Focus)").droppable( "option", "accept", '.is-default-resource' );
               //$("div.Drag-Route").droppable( "option", "accept", 'none' );
               $("div.Drag-Route.DragRoute-Focus").droppable( "option", "accept", '.Drag-Rsrc,.is-default-resource' );

               Fb.Dnd.droppable_route = $(this);
               methods._trigger.apply( this, [this, 'select'].concat($.makeArray(arguments)));
               //alert("Klick on Route!\nclass:"+$(this).attr('class')+"\nhtml: " + $(this).html() );
           }
        },
        '_mkRouteDroppableOnClick': function() {
            //alert('#301 '+dataKey+'._mkRouteDroppableOnClick ');
            $(this).bind("click", methods._mkDroppable);
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
//            var m= dataKey + '.getData(' + $.makeArray(arguments).join(',')+')\n';
            if ("object"!== typeof $(this).data(dataKey) ) return null;
            var data = $(this).data(dataKey);
            if (!("data" in data )) data.data = {};
            if (arguments.length) {
               return ("object"=== typeof data.data && arguments[0] in data.data) ? data.data[arguments[0]] : null;
            }
            return data.data;
        },
        'setData': function() {
            var data = $(this).data(dataKey);
            if (data.data === null || typeof(data.data)=='undefined') data.data = {};
            if (arguments.length == 1)
               data.data = arguments[0];
            else if(arguments.length >= 2)
               data.data[arguments[0]] = arguments[1];
        },
        'clear': function() {
            $( ".Drag-Route", this).remove();
        },
        'load': function(dbdata) {
            var data = $(this).data(dataKey);
            data.data = dbdata.data; 
            // First Calendar-Entries
            methods.clear.apply( this );

            if (dbdata.options) $.fn[dataKey].apply(this, ['options', dbdata.options]);
            if (!dbdata.resources) return;
            
            for(var i = 0; i < dbdata.resources.length; ++i) {
                methods.addResource.apply( this, [ 'load', dbdata.resources[i]] );
            }
        },
        '_trigger': function(obj, eventName) {
            var data = $(this).data(dataKey);
//            if (eventName == 'removeResource') {
//                var m='';
//                if (typeof(arguments[2])=='object') for(var i in arguments[2]) m+=i+':'+arguments[2][i]+'\n';
//                alert( '#335 '+dataKey+'._trigger '+eventName+'\ntypeof(arguments[2]):'+typeof(arguments[2])+'('+m+'); this.html:'+$(this).html() );
//            }
//            alert( '#516 ' + dataKey + ' : ' + eventName );
                        
            var triggerEventName = methods._getEventTriggerName(eventName);
//            alert( '#335 '+dataKey+'._trigger:\neventName:'+eventName+'; triggerEventName:'+triggerEventName );
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            var args = $.makeArray(arguments).slice(1);
//            alert( '#522 ' + dataKey + ' : ' + eventName + ' ownEventName: ' + ownEventName );
            if (methods['_'+ownEventName]) methods['_'+ownEventName].apply(this, arguments);
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                if (false === data[ownEventName].apply(obj, args.slice(1))) 
                    return false;
            }
            
//            alert( '#529 ' + dataKey + ' : ' + eventName + ' Fire jQueryEvent: ' + args[0] );
            // Fire jQuery-Event
            args[0] = triggerEventName;
//            if (obj == this) {
//                if (false === $( this ).trigger.apply( $(this), args))
//                    return false;
//            }
//            alert( '#536 ' + dataKey + ' : ' + eventName + ' trigger to parent: ' + data._parentJqFunction );
            if (data._parent && data._parentJqFunction) {
//                alert('#190 trigger event ' + eventName + '; triggerEventName:'+triggerEventName + ' an ' + $(data._parent).attr('class'));
                args.unshift(obj);
                args.unshift('_trigger');                
                if (false === data._parent[data._parentJqFunction].apply(data._parent, args) ) {
                    return false;
                }
            }
            //if (eventName.match(/select/)) alert('#295 ' + $(this).data(dataKey).data.ZeitVon);
//            alert( '#546 ' + dataKey + ' : ' + eventName );
            return true;
        },
        '_getEventTriggerName': function(eventName) {
            if (eventName in eventTriggerMap) eventName = eventTriggerMap[eventName];
            return eventName;
        },
        '_getOwnEventHandlerName': function(eventName) {
            var e = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
            return (e in defaults) ? e : '';
        },
        '_onDropResource': function(thisObj, triggeredEventName, obj, e, ui) {
            //alert( "#246 jquery.fbDispoRoute._onDropResource!\nargs.length: "+arguments.length + "\n" + objToString(arguments) );
            var data = $(this).data(dataKey);
            var self = this;
            var $self = $(self);
            
            var t = $( obj );            
            t.fbDispoResource({
                             data: {route_id:data.data.id},
                        '_parent': $self,
              '_parentJqFunction': dataKey
            });
        },
        '_onMoveResource': function(thisObj, triggeredEventName, e, ui, obj) {
            //alert( "#559 jquery.fbDispoRoute._onMoveResource!\nargs.length: "+arguments.length + "\n" + objToString(arguments) );
            var data = $(this).data(dataKey);
            var self = this;
            var $self = $(self);
            var t = $( obj );            
            t.fbDispoResource({
                             data: {route_id:data.data.id},
                        '_parent': $self,
              '_parentJqFunction': dataKey
            });
        }
    };
    
    $.fn[dataKey] = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
                
        // Default-Routine: Getting Options from allready initialized Object
        // Mit options kann getestet werden, ob ein Objekt initialisiert wurde
        // ohne dass es dabei angelegt wird. 
        if (typeof(options) == 'string' && this.length ) {
            switch(options) {
                case 'options':
                    if (argsLen == 1) return $( this[0] ).data(dataKey);
                    break;

                case 'option':
                    if (argsLen == 2) {
                        var data = $( this[0] ).data(dataKey);
                        return (typeof(data)=="object" && typeof(data[args[1]]) != "undefined") ? data[args[1]] : null;
                    }
                    break;

                default:
                    if (typeof(methods[options]) == 'function' && options.substr(0,3) == 'get') {
                        if (!$( this[0] ).data(dataKey)) return null;
                        return methods[options].apply( this[0], (argsLen>1?$.makeArray(args).slice(1):[]) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options            
            if (!$( self ).data(dataKey)) {
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.routeDefaults) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.routeDefaults);
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
                            methods[options].apply( self, (argsLen>1?$.makeArray(args).slice(1):null) );
                        }
                }
            }
            
            if (_callInit) methods['_init'].apply( self );
        });
    };
    
})(jQuery);

