/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/* 
 * author: Frank Barthold
 * requires: fb.dnd.settings.js
 * 
 */


(function($) {
    
    var dataKey  = 'fbDispoTimelineDropzone';
    var debug = false;
    
    var eventTriggerMap = {
            'create': 'createTimeline',
            'remove': 'removeTimeline',
              'drop': 'dropRoute',
              'move': 'moveTimeline'
    };
    var defaults = {
              'data': null,
           '_parent': null,
 '_parentJqFunction': null,
 
        'isSortable': true,
         'isMovable': true,
       'isRemovable': true,
       'isDroppable': true,
       
          // Event-Handler, can be defined by overwriting in options
          'onCreate': null, //*null*/ function(){ alert(dataKey + '.onCreate Default-Dummie-Handler'); },
          'onRemove': null, //*null*/ function(){ alert(dataKey + '.onRemove Default-Dummie-Handler'); },
          // Wenn Daten vom Server geladen wurden
       'onLoadRoute': null, //*null*/ function(){ alert(dataKey + '.onLoadRoute Default-Dummie-Handler'); },
          // Wenn Route per Drag and Drop auf Time gezogen wurden
            'onDrop': null, //*null*/ function(){alert(dataKey + '.onDrop Default-Dummie-Handler');},
            'onMove': null, //*null*/ function(){alert(dataKey + '.onMove #35 Default-Dummie-Handler');},
            'onSort': null //*null*/ function(){alert(dataKey + '.onSort Default-Dummie-Handler');}
     };
    
    /**
     * Registered Methods are called with apply, so it work as they
     * would be a method of the object.
     * Note:
     * - Function beginning with underscore like '_init' should not called external
     *   but the access isn't protected by now
     * - Functions beginning with 'get' should return an value.
     *   That's why only the method of the first object in the jQuery-Selection-Range
     *   will called
     */
    var methods = {
        '_init': function() {
            // Do Some Rendering after first initializing
            var self  = this;
            var $self = $( this );
            var data  = $self.data(dataKey);
            
//           var d=$self.data(dataKey).data, i=null; m="#54"+dataKey+"._init [data.data]:\n";
//           for(i in d) m+=i+":"+d[i]+"\n"; alert(m);
           
            //$self.wrap('<div class="DropZoneWrapper" style="padding-left:15px;border:1px solid #f00;"/>')
           
            // In Fb.DropRouteSetting wird in der Methode create
            // fbDispoTimelineGrid aufgerufen. Dort wird auch style.height im Element gesetzt.
            // Eigentlich wird die Hoehe in der Klasse .DropZone-Route definiert      

            $self.addClass('DropZone DropZone-Route Drop-Timeline '+dataKey)
                .append( $('<div class="Timeline"/>') )
                .find( "div.Timeline" )
                    .fbDispoTimelineLineal($self.data(dataKey).data)
                .end()
                .fbDispoTimelineDropzoneHandles({
                    'sortable': data.isSortable,
                     'movable': data.isMovable,
                   'removable': data.isRemovable
                })
                .bind('drop', function(e, ui) { 
               //methods._trigger.apply(self, [self, 'drop'].concat($.makeArray(arguments)));
            });
           
            if (data.isDroppable) {
                $self.droppable( Fb.DropRouteSettings );
            }
           
            if (false === methods._trigger.apply( self, [self, 'create'])) {
                console.log('#94 ' + dataKey + ' _init self.remove ');
                $self.remove();
                return false;
            }
           
            var d=$(this).data(dataKey).data;
            
//            alert('#74 fbDispoTimelineDropzone data.touren.length: '+ $(this).data(dataKey).data.touren.length);
            if ($(this).data(dataKey).data.touren) {
                // console.log('#103 ' + dataKey + ' _init addRoutes');
                methods.addRoutes.apply(self, [$(this).data(dataKey).data.touren] );
                delete $(this).data(dataKey).data.touren;
            } else {
                // console.log('#107 ' + dataKey + ' _init no routes addded!');
            }
            $("div.fbDispoRouteDefaults", this).show();
        },
        'getTimelineLineal': function() {
            var obj = $("div.Timeline:first", this);
            return obj;
        },
        'getTimelineSettings': function() {
            return methods.getTimelineLineal.apply(this).fbDispoTimelineLineal('getSettings');
        },
        'getRoutes': function() {
            return $("div.fbDispoRoute,div.fbDispoRouteDefaults", this);
        },
        // used for loading Data, not for drop-event
        'addRoutes': function(touren) {
//            alert('#90 '+dataKey + '.addRoutes touren.length:'+touren.length);
            for(var ti in touren) {
                methods.addRoute.apply(this, [ touren[ti] ] );
            }
        },
        'addRoute': function(addData) {
            var self = this;
            if (!addData) addData = {};
            var t = $('<div />');
            t.append( $('<span/>').addClass('title').text(addData.name) );
            $(self).append( t );
            
            var routeOpts = {
                             data: addData,
                        '_parent': $(this),
              '_parentJqFunction': dataKey};
            
//            var d=addData;
//            var m="#108 "+dataKey+".addRoute \nthis.class:"+$(this).attr("class")+" name:"+addData.name+"\naddData:\n";for(var i in d ) m+=i+":"+d[i]+"\n";alert(m);
            if (!('IsDefault' in addData) || parseInt(addData['IsDefault'])!=1) {
                t.fbDispoRoute.apply(t, [ routeOpts ] );
            } else {
                t.fbDispoRouteDefaults.apply(t, [ routeOpts ] );                
            }

            // Call EventHandler
            methods._trigger.apply( self, [t, 'loadRoute']);
        },
        // Will be called from procedures in script fb.dnd.settings.js
        '_moved': function() {
//            if (debug) 
                alert('#127 fbDispoTimelineDropzone._moved!');
            methods._trigger.apply( this, [this, 'move']);
        },
        // Will be called from procedures in script fb.dnd.settings.js
        '_dropped': function() {
           if (debug) alert('fbDispoTimelineDropzone._dropped!');
           methods._trigger.apply( this, [this, 'drop']);
        },
        'destroy': function() {
           if (debug) alert('fbDispoTimelineDropzone.destroy!');
           if (methods._trigger.apply( this, [this, 'remove']) !== false) {
               $(this).remove();
           }
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
            var data = $(this).data(dataKey);
            if ("object"!==typeof data || !("data" in data) || "object"!==typeof data.data) return null;
            if (arguments.length) {
               return (arguments[0] in data.data) ? data.data[arguments[0]] : null;
            }
            return data.data;
        },
        'setData': function() {
//            var m= dataKey + '.setData(' + $.makeArray(arguments).join(',')+')\n';
            if (arguments.length == 1)
               $(this).data(dataKey).data = arguments[0];
            else if(arguments.length >= 2)
               $(this).data(dataKey).data[arguments[0]] = arguments[1];
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
            if (!data.routes) return;

            for(var i = 0; i < dbdata.routes.length; ++i) {
                methods.addRoute.apply( this, [ 'load', dbdata.routes[i]] );
            }
        },
        '_trigger': function(obj, eventName) {
//            alert( '#174 ' + dataKey + ' : ' + eventName );
            var data = $(this).data(dataKey);
//            alert('#171 ' + dataKey + "._trigger("+$(obj).attr('class')+", "+eventName+")");
            var triggerEventName = methods._getEventTriggerName(eventName);
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            var args = $.makeArray(arguments).slice(1);
            var re = null;
            
            // Member-Listener
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                if (false === data[ownEventName].apply(obj, args.slice(1))) 
                    return false;
            }
            
            // Fire jQuery-Event
            args[0] = triggerEventName;
            if (obj == this) {
                if (false === $( this ).trigger.apply( $(this), args))
                    return false;
            }
            
            // Bubble Event to Parent-Element
            if (data._parent && data._parentJqFunction) {
                args.unshift(obj);
                args.unshift('_trigger');
                if (false === data._parent[data._parentJqFunction].apply(data._parent, args) ) {
                    return false;
                }
            }
//            if (eventName.match(/remove/)) alert( '#203 ' + dataKey + '._trigger '+eventName+' return true!');
            return true;
        },
        '_getEventTriggerName': function(eventName) {
            if (eventName in eventTriggerMap) eventName = eventTriggerMap[eventName];
            return eventName;
        },
        '_getOwnEventHandlerName': function(eventName) {
            var e = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
            return (e in defaults) ? e : '';
        }
    };
    
    
    $.fn.fbDispoTimelineDropzone = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
        
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
                        return methods[options].apply( this[0], $.makeArray(args).slice(1));
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options
            
            if (!$( self ).data(dataKey)) {
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.timelineDropzone) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.timelineDropzone);
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
            var d = data.data;

            if (d.settings) {
                for(var _si in d.settings) {
                    data[_si] = d.settings[ _si ];
                }
                delete d.settings;
            }
            
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
                            methods[options].apply( self, (argsLen>1?$.makeArray(args).slice(1) : null) );
                        }
                }
            }
            
            if (_callInit) {
                methods['_init'].apply( self );
            }
        });
    };

})(jQuery);
