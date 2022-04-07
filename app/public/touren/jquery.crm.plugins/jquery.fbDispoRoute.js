/* 
 * author: Frank Barthold
 * DispoRoute
 */

(function($) {
    
    var dataKey  = 'fbDispoRoute';
    
    var eventTriggerMap = {
            'create': 'createRoute',
               'add': 'addRoute',
            'remove': 'removeRoute',
              'drop': 'dropRoute',
              'move': 'moveRoute',
            'resize': 'resizeRoute',
            'select': 'selectRoute',
        'changeData': 'changeRouteData',
      'hoverDetails': 'hoverRouteDetails',
       'showDetails': 'showRouteDetails',
       'hideDetails': 'hideRouteDetails'
    };
    
    var defaults = {
                 'data': null,
              'dataUrl': '',
              'rsrcUrl': '',
           'isEditable': true,
          'isDroppable': true,
          'isDraggable': true,
          'isResizable': true,
          'isRemovable': true,
            'timeStart': 0,
         'timeDuration': 0,
                 'test': 0,
              '_parent': null,
    '_parentJqFunction': null,
                'onAdd': /*null*/ function(){alert(dataKey + '.onAdd Default-Dummie-Handler');},
             'onRemove': null, // function(){ alert(dataKey + '.onRemove Default-Dummie-Handler'); },
             'onSelect': null, //*null*/ function(){ alert( $(this).data(dataKey).data.ZeitVon + '.onSelect Default-Dummie-Handler'); },
               'onDrop': null, //*null*/ function(){ alert(dataKey + '.onDrop Default-Dummie-Handler'); },
               'onMove': null, //*null*/ function(){ alert(dataKey + '.onMove Default-Dummie-Handler'); },
             'onResize': null, //*null*/ function(){ alert(dataKey + '.onResize Default-Dummie-Handler'); },
        'onShowDetails': null, //*null*/ function(){ alert(dataKey + '.onShowDetails Default-Dummie-Handler'); },
        'onhideDetails': null, //*null*/ function(){ alert(dataKey + '.onHideDetails Default-Dummie-Handler'); },

       'onDropResource': null, //*null*/ function(){ alert("#40 " +dataKey + '.onDropResource Default-Dummie-Handler'); },
       'onMoveResource': /*null*/ function(){alert(dataKey + '.onMoveResource Default-Dummie-Handler');},
                'route': null,
     'draggableOptions': Fb.DragRouteInstanceSettings,
     'droppableOptions': Fb.DropRsrcOnRouteSettings,
     'resizableOptions': Fb.RouteResizableSettings
    };
    
    /**
     * Registered Methods are called with apply, so it work as they
     * would be a method of the object.
     * Note:
     * - Function beginning with underscore like '_init' should not called external
     *   but the access isn't protected by now
     * - Functions beginning with 'get' should return an value.
     *   That's why only the method of the first object in the jQuery-Selection-Range
     *   will be called
     */
    var methods = {
        '_init': function() {
            
            // Do Some Rendering after first initializing
            var self = this;
            var obj = $( this );
            var data = obj.data(dataKey);            
            if (data.data === null) data.data = {};
            
            if (!obj.attr('id') && ('id' in data.data) ) {
                obj.attr('id', dataKey + '_'+data.data.id);                
            }
            
            if (!obj.attr('class') || !obj.attr('class').match(/\bDrag-Route\b/)) obj.addClass('Drag-Route');
            obj.removeClass(dataKey).addClass(dataKey);
            
            if (data.isDraggable) obj.draggable( data.draggableOptions );
//              .fbDispoRouteDroppableOnClick()
            if (data.isDraggable) obj.droppable( data.droppableOptions );
            if (data.isDraggable) obj.resizable( data.resizableOptions );
            
            obj.addClass("ui-corner-tr ui-corner-bottom")
               .append( $("<div class='resourcesStat' />"))
               .append( $("<ul class=\"resources\" />"));
           
            if (obj.data('dragdata')) {
               $.extend(data.data, obj.data('dragdata'));
            }
            
            if (data.isDraggable) {
                methods._mkRouteDroppableOnClick.apply(this);
                obj.bind('drop', function(e,ui){    });
            }
            
            methods.reInitData.apply( this );
            methods.initHandler.apply( this );
            
            methods.setTimeSlot.apply( this );
           
            methods._trigger.apply( this, [this, 'create']);
            if (!$(this).data(dataKey)) {
                throw "#95 "+dataKey+"._init: Falsches Objekt! Erwartet: "+dataKey+"; Erhalten:"+$(this).attr("class");
            }
            
//          var d=$(this).data(dataKey).data;
//          var m="#93 "+dataKey+"._init [this.class:"+$(this).attr("class")+"\n data.data:\n";for(var i in d ) m+=i+":"+d[i]+"\n";alert(m);
            //alert( '#97 fbDispoRoute data.resources.length:'+$(this).data(dataKey).data.resources.length);
            if ($(this).data(dataKey).data.resources) {
                methods.addResources.apply(self, [$(this).data(dataKey).data.resources] );
                delete $(this).data(dataKey).data.resources;
            }
            methods._trigger.apply( this, [this, 'changeData']);
        },
        'initHandler': function() {
            var self  = this;
            var $self = $( this );
            var data = $self.data(dataKey);            
            if (data.data === null) data.data = {};
                        
            var iconOpts = {position:"absolute", display:"block", right:0,
               top:0, width:"15px", height:"15px", background:"#fff" 
            }
            var iconInfoClass = (!data.data.DirektLieferInfo && !data.data.LieferterminHinweisText) ? "ui-icon-info" : "ui-icon-alert";
            
            // Detail-Info-Handler
            $self.append( 
                $("<div/>").css(iconOpts)
                .addClass("ui-corner-all")
                .append( $("<span/>").addClass("ui-icon " + iconInfoClass) )
                .hover(
                    function(){
                        var selfIcon = this;
                        
                        $(this).addClass("ui-state-hover").data('MouseIsOver', true);

                        setTimeout( function() {
                            if ($(selfIcon).data('MouseIsOver')) 
                                methods._trigger.apply( self, [self, 'hoverDetails', selfIcon]);
                        }, 650);
                   }, 
                   function(){ 
                       $(this).removeClass("ui-state-hover").data('MouseIsOver', false);
                   }
                )
                .click(function() {
                    Fb.showRouteDetails( self );
                })
            );
            
            $self.hover(
                function(){
                    $self.addClass("ui-state-hover").data('MouseIsOver', true);
                    
                    setTimeout( function() {
                        if ($self.data('MouseIsOver')) 
                            methods._trigger.apply( self, [self, 'hoverRoute', self]);
                    }, 650);
               }, 
               function(){ 
                   $self.removeClass("ui-state-hover").data('MouseIsOver', false);
                   methods._trigger.apply( self, [self, 'leaveRoute', self]);
               }
            )
            .bind('click', function(){
                var args = $.makeArray(arguments);
                args.unshift(self, 'clickRoute', self);
                methods._trigger.apply( self, args);
            })
            .bind('dblclick', function() {
                Fb.showRouteDetails( self );
            });
            $self.bind('mouseleave', function() {
                methods._trigger.apply( self, [self, 'leaveRouteDetails', self]);                
            });
            
            // Remove-Handler
            if (data.isEditable && data.isRemovable)
                $self.append( $("<div/>").css(iconOpts).css({top:"17px"})
                .addClass("ui-corner-all")
                .append( $("<span/>").addClass("ui-icon ui-icon-trash") )
                .hover(function(){$(this).addClass("ui-state-hover")}, function(){$(this).removeClass("ui-state-hover")} )
                .click(function() {
                    methods.destroy.apply( self );
                })
                );
            
            // Avisiert-flag
            if (data.data.avisiert == 1)
                $self.append( 
                    $("<div/>")
                    .text("A").attr("title","Avisiert: "  + (data.data.avisiert==1?"Ja":"Nein") )
                    .css(iconOpts).css({top:"34px",textAlign:"center",fontSize:"10px"})
                    .addClass("ui-corner-all ui-state-hover")
                );
            
            
            
            $self.bind('showDetails', function(e, tabs) {
                methods._trigger.apply( this, [this, 'showDetails', tabs]);
            });
            
            $self.bind('hideDetails', function(e) {
                methods._trigger.apply( this, [this, 'hideDetails']);
            });
            
            // Bind Resource Dropping and Moving
            if (data.isEditable) {
                $( this ).bind('resource-dropped', methods.addDroppedResource);
                $( this ).bind('resource-moved',   methods.addDroppedResource);
            }
        },
        'addDroppedResource': function(e, objRsrc, ui) {
//            alert('#142 '+dataKey+'.addDroppedResource!\narguments.length:'+arguments.length+'\n'+flatObjToString($.makeArray(arguments)));
            var resourceOpts = $(objRsrc).fbDispoResource('options');
            var resourceSetOpts = {
                _parent:$(this),
                _parentJqFunction:dataKey,
                _parentFrom:(resourceOpts && ('_parent' in resourceOpts) ? resourceOpts._parent : null),
                data: ui.helper.data('dragdata')
            }

            if (objRsrc !== ui.draggable) {
                // Neu hinzugefügt
                // kann auch aus DefaultResource verschoben worden sein
                objRsrc.fbDispoResource( resourceSetOpts );
                objRsrc.fbDispoResource('_dropped', e, ui);
            } else {
                // Ein bereits hinzugefügtes wurde verschoben
                objRsrc.fbDispoResource('options', resourceSetOpts );
                objRsrc.fbDispoResource('_moved', e, ui);
            }
            
            methods.sortResources.apply( this );
            methods.refreshResourcesStat.apply( this );
        },
        'sortResources': function() {
            return 0;
            
//            var items = $('$("ul.resources li', $(this) ).get();
//            items.sort(function(a,b){ 
//              var keyA = $(a).attr("class").match(/\bRsrcType-([^ ]*)/);
//              var keyB = $(b).attr("class");
//              if (!keyA || !keyB) return (keyA ? -1 : 1);
//
//              if (keyA[1] < keyB[1]) return -1;
//              if (keyA[1] > keyB[1]) return 1;
//              return 0;
//            });
//            var ul = $('$("ul.resources', $(this) );
//            $.each(items, function(i, li){
//              ul.append(li);
//            });
        },
        'setResourcesUrl': function(rsrcUrl) {
            var data  = $( this ).data(dataKey);
            data.rsrcUrl = rsrcUrl;
            
        },
        'getResourcesUrl': function() {
            var data  = $( this ).data(dataKey);
            if (data.rsrcUrl) return data.rsrcUrl;
            
            var urlTpl = $( this ).closest( "div.fbDispoCalendar").fbDispoCalendar( "getObjectData", "resourcesUrlTpl");
            return (urlTpl.match(/{#ROUTEID}/)) ? urlTpl.replace(/{#ROUTEID}/, data.data.id) : urlTpl + data.data.id;
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
                alert('#247 '+dataKey+'.reloadResourcesFromUrl Error missing rsrcUrl!');
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
            $("ul.resources .fbDispoResource", $(this) ).each(function() {
                var c = ("string" === typeof $(this).attr("class")) ? $(this).attr("class") : "";
                var m = c.match(/\bRsrcType-([^ ]*)/);
                var t = (m ? m[1] : 'Rsrc');
                if (!(t in rsrcStat)) rsrcStat[t] = 1;
                else ++rsrcStat[t];
                //alert($(this).text() + '\nm: ' + m + "\n" + rsrcStat);
            });
            
            for(var i in rsrcStat) {
                $(".resourcesStat", $(this) ).append(  $("<span rel='"+i+"' style='margin-right:5px;'>"+i+" "+rsrcStat[i]+"</span> &nbsp;")  );
            }
        },
        'addResources': function(resources) {
            //alert("#101 "+dataKey+".addResources [this.class:"+$(this).attr("class")+"\nresources: "+resources.length);
            for(var ti in resources) {
                methods.addResource.apply(this, [ resources[ti] ] );
            }
            methods._trigger.apply(this, [this, 'loadResources']);
            methods.refreshResourcesStat.apply( this );
        },
        'addResource': function(addData) {
            var self = this;
            var $self = $(self);
            if (!addData) addData = {};

            // Hier wird das Parent-Element an fbDispoResource uebergeben
            // _parent
            // _parentJqFunction
            var t = $('<div />').text(addData.name);
            $("ul.resources:first", $self).append( $("<li/>").append( t )  );
            if (!t || !t.length) {
                alert('Unbekannter Systemfehler: Eine Ressource konnte nicht zur Tour hinzugefuegt werden!');
                return false;
            }
            $.fn.fbDispoResource.apply(t, [ {
                             data: addData,
                        '_parent': $(this),
              '_parentJqFunction': dataKey} ] 
            );
            
        },
        'getPortlet': function() {
            return $( this ).closest("div.fbDispoPortlet");
        },
        'getTimeline': function() {
            return $( this ).closest("div.fbDispoTimelineDropzone");
        },
        'getResources': function() {
            return $("ul.resources li .Drag-Rsrc", $(this));
        },
        'getRoutes': function() {
            var data = $(this).data(dataKey);
            
            return (data && data._parent) ? $("div.fbDispoRoute,div.fbDispoRouteDefaults", data._parent) : $();
        },
        'setTimeSlot': function(timeRange) {
//            alert( '#137 '+dataKey+'.setTimeSlot [this.class:'+$(this).attr('class')+']\n data(dataKey): ' + $(this).data(dataKey) + ', ' + $(this).data(dataKey).data +"; " + $(this).data(dataKey)._parent);
            var $self = $(this);
            var data = $self.data(dataKey);
            if (!$self.data("route")) $self.data("route", {});
            var routeData = $self.data("route");
            if (timeRange && timeRange.from) data.data.ZeitVon = timeRange.from;
            if (timeRange && timeRange.to)   data.data.ZeitBis = timeRange.to;
            
            var lineal = $(data._parent).fbDispoTimelineDropzone('getTimelineLineal');
            var ts = $(lineal).data("fbDispoTimelineLineal").settings;
            
            routeData.start = $.timeToMinutes( data.data.ZeitVon );
            routeData.end   = $.timeToMinutes( data.data.ZeitBis );
            routeData.duration = routeData.end - routeData.start;
//            alert('#355 reset to ' + routeData.start);
            if (routeData.duration < ts._stepWidthM) routeData.duration = ts._stepWidthM;
            
            this.style.position   = 'absolute';
            this.style.top   = $(lineal).height();
            this.style.left  = ((routeData.start-ts._startM)*100/ts._totalM)+"%";
            this.style.width = (routeData.duration*100/ts._totalM)+"%";
            this.style.height= ($self.parent().height() - $(lineal).height() - 22 )+'px';
            
//            alert('#189 '+dataKey+' '+this.style.left +"=("+(routeData.start-ts._startM)+"*"+100+"/"+ts._totalM+")+%");
        },
        
        // Will be call from procedures in script fb.dnd.settings.js
        '_moved': function() {
            if (methods._trigger.apply( this, [this, 'move']) === false) {
//                var m='#372 reset to data:\n'; for(var i in $(this).data(dataKey).data) m+= i+':'+$(this).data(dataKey).data[i]+'\n'; alert(m);
                try {
                    var d = $(this).data(dataKey);
                    var dg = $(this).data('draggable');

                    if (dg && dg._src && dg._src.parent && d && d._parent) {
                        if (dg._src.parent.get(0) != d._parent.get(0)) {
                            $(this).appendTo(dg._src.parent);
                            d._parent = dg._src.parent;
                        } else {
    //                        alert('Timline is same!');
                        }
                    }
                } catch(e) {
                    alert(e);
                }
                methods.setTimeSlot.apply( this );
                return false;
            } else {
                //alert('#377 no reset!');
            }
        },
        // Will be call from procedures in script fb.dnd.settings.js
        '_resized': function() {
            var $self = $(this);
            var routeData = $self.data('route');
            if (!routeData) return false;
            
            var data = $self.data(dataKey);
            data.timeStart = routeData.start;
            data.timeEnd = routeData.end;
            data.timeDuration = routeData.duration;
            
            if (methods._trigger.apply( this, [this, 'resize']) === false) {
                methods.setTimeSlot.apply( this );
                return false;
            }
        },
        '_dropped': function() {
            methods.reInitData.apply( this );
            if (methods._trigger.apply( this, [this, 'drop']) === false) {
                return false;
            }
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
           if (true === methods._trigger.apply( this, [this, 'remove'])) {
               $(this).removeData(dataKey);
               $(this).remove();
           }
           // Do some Cleanup-Work like undo rendering, etc
        },
        '_mkDroppable': function() {
           if ( "string" !== typeof $( this ).attr('class') || !$( this ).attr('class').match(/\bDragRoute-Focus\b/) ) {
               $("div.Drag-Route").removeClass("DragRoute-Focus");
               $(this).addClass("DragRoute-Focus");
               $("div.Drag-Route:not(.DragRoute-Focus)").droppable( "option", "accept", 'none' );
               //$("div.Drag-Route").droppable( "option", "accept", 'none' );
               $("div.Drag-Route.DragRoute-Focus").droppable( "option", "accept", '.Drag-Rsrc' );

               Fb.Dnd.droppable_route = $(this);
               methods._trigger.apply( this, [this, 'select'].concat($.makeArray(arguments)));
               //alert("Klick on Route!\nclass:"+$(this).attr('class')+"\nhtml: " + $(this).html() );
           }
        },
        '_mkRouteDroppableOnClick': function() {
            $(this).bind("click", methods['_mkDroppable']);
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
//            var m= dataKey + '.getData(' + $.makeArray(arguments).join(',')+')\n';
            if ( typeof($(this).data(dataKey))!='object') return null;
            var data = $(this).data(dataKey);
            if (data.data == null) data.data = {};
            if (arguments.length) {
               return (arguments[0] in data.data) ? data.data[arguments[0]] : null;
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
           
           methods._trigger.apply( this, [this, 'changeData']);
        },
        'setTitle': function(title) {
            $(this).find('span.title').html( title );
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
//            if (!eventName.match(/hoverDetails|leaveRouteDetails/)) alert('#495 ' + dataKey +' _trigger ' + eventName);
            var data = $(this).data(dataKey);
            
            var triggerEventName = methods._getEventTriggerName(eventName);
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            var args = $.makeArray(arguments).slice(1);
            var re = null;
            
            if (methods['_'+ownEventName]) {
                if (false === methods['_'+ownEventName].apply(this, arguments)) {
                    return false;
                }
            }
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                if (false === data[ownEventName].apply(obj, args.slice(1) )) {
                    return false;
                }
            }
            args[0] = triggerEventName;
            if (obj == this) {
                if (false === $( this ).trigger.apply( $(this), args)) {
                    return false;   
                }
            }
            
            if (data._parent && data._parentJqFunction) {
                args.unshift(obj);
                args.unshift('_trigger');
                if ( false === data._parent[data._parentJqFunction].apply(data._parent, args) ) {
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
            return (e in defaults) ? e : '';
        },
        '_onDropResource': function(thisObj, triggeredEventName, obj, e, ui) {
            //alert( "#246 jquery.fbDispoRoute._onDropResource!\nargs.length: "+arguments.length + "\n" + objToString(arguments) );
            var data = $(this).data(dataKey);
            var self = this;
            var $self = $(self);
            //alert( "250 route_id (data.data.id): " + data.data.id);
            
            var t = $( obj );            
            t.fbDispoResource({
                             data: {route_id:data.data.id},
                        '_parent': $self,
              '_parentJqFunction': dataKey
            });
        },
        '_onMoveResource': function(thisObj, triggeredEventName, e, ui, obj) {
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
                    if (typeof(methods[options]) == 'function' && (options == '_trigger' || options.substr(0,3) == 'get') ) {
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
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.route) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.route);
                } else {
                    presets = defaults;
                }
                if (!options) {
                    $( self ).data(dataKey, $.extend({}, presets));
                }
                else if (typeof(options) == "object") {
                    $( self ).data(dataKey, $.extend({}, presets, options) );
                }
                else {
                    $( self ).data(dataKey, $.extend({}, presets));
                }
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
    
    $.fn[dataKey].getMethods   = function() {
        return methods;
    };
    $.fn[dataKey].getDefaults  = function() {
        return defaults;
    };
    $.fn[dataKey].getRouteById = function(id) {
//        alert( 'Search for id ' + id + ' with key ' + '#' + dataKey + '_' + id);
        return $('#' + dataKey + '_' + id);
    };

})(jQuery);
