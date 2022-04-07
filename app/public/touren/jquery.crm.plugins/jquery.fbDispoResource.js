/* 
 * author: Frank Barthold
 * Full Skeleten with:
 * - default Options
 * - Options Setters and Getters
 * - Own registered valid Object-Methods, which can be external called
 * 
 * For Making a new Extension just 
 * save a copy of this file and replace 'fbExtensionSkeletion'
 * by your ExtensionName and extend with your own code.
 * 
 */


(function($) {
    
    var dataKey  = 'fbDispoResource';
    
    var eventTriggerMap = {
            'create': 'createResource',
              'drop': 'dropResource',
              'move': 'moveResource',
            'remove': 'removeResource'
    };
    
    var defaults = {
                 'data': {},
         'resourceType': 'Rsrc',
         
          'isDraggable': true,
          'isRemovable': true,
          
             'onCreate': null,
             'onRemove': null,
              '_parent': null,
    '_parentJqFunction': null,
     'draggableOptions': null
    };
    
    /**
     * Register Methods are called with apply, so it work as they
     * would be method of the object.
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
//            alert( "_init: " + $self.html() );
            var data = $self.data(dataKey);
            
            if (typeof(data.data.ondrop)=="function") {
                data.data.ondrop();
                delete data.data.ondrop;
            }

            var resourceType = ('resourceType' in data.data ? data.data.resourceType : (data.resourceType || ''));
            var resourceId = ('id' in data.data ? data.data.id : (data.id || ''));
            var resourceTypeClass = "RsrcType-" + resourceType;
            var elmId = dataKey + "_" + resourceType + "" + resourceId;

            $self.addClass(dataKey + " Drag-Rsrc " + resourceTypeClass );
            $self.attr({"id": elmId, "data-type": resourceType, "data-id": resourceId});
            
//            data.draggableOptions = $.extend({}, ($self.is('.Is-Template') ? Fb.DragRsrcTemplateSettings :  Fb.DragRsrcInstanceSettings)); 
            if (data.isDraggable) {
                data.draggableOptions = $.extend({}, Fb.DragRsrcTemplateSettings);
                if ($self.is('.is-default-resource') ) data.draggableOptions = {
                    helper:function(){ 
                        var obj = $(this).clone().appendTo('body').css('zIndex',3000);
                        obj.data(dataKey, $(this).data(dataKey) );
                        obj.data('dragdata', $(this).data(dataKey).data );
                        obj.data('dragdata').sourceElement = this;
                        return obj;
                    },
                    appendTo:'body',
                    scroll:true,
                    scrollSensitivity: 50,
                    start:function(){
    //                    alert( $(this).draggable('option', 'appendTo') + ', ' + $(this).draggable('option', 'helper') ); 
                    } 
                };
                $self.draggable( $.extend({}, data.draggableOptions, {appendTo:'body'} ) );
            }
            
                
//            alert('#66 ' + dataKey + ' draggable appendTo: ' + $self.draggable('option','appendTo'));
//            alert( $(this).draggable('option', 'appendTo') + ', ' + $(this).draggable('option', 'helper') );
            if (data && typeof(data.onCreate) == 'function') {
               data.onCreate.apply( self );
            }
        },
        'destroy': function() {
            var data = $(this).data(dataKey).data;
            if (methods._trigger.apply( this, [this, 'remove', data]) !== false) {
                var p = $( this ).parent("li");
                (p.length) ? p.remove() : $(this).remove();
                // Do some Cleanup-Work like undo rendering, etc
            }
        },
        'doAnything': function() {
            var data = $(this).data(dataKey);
        },
        'getDroppableOptions': function() {
            return $( this ).droppable.call( arguments );
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
//            var m= dataKey + '.getData(' + $.makeArray(arguments).join(',')+')\n';
            var data = $(this).data(dataKey);
            if (arguments.length) {
               return (arguments[0] in data.data) ? data.data[arguments[0]] : null;
            }
            return (data && data.data ? data.data : null);
        },
        'setData': function() {
//            var m= dataKey + '.setData(' + $.makeArray(arguments).join(',')+')\n';
            if (arguments.length == 1)
               $(this).data(dataKey).data = arguments[0];
            else if(arguments.length >= 2)
               $(this).data(dataKey).data[arguments[0]] = arguments[1];
        },
        'reInitData': function() {
            // Mandant, Auftragsnummer, timeline_id, DatumVon, ZeitVon, DatumBis, ZeitBis
            var data = $(this).data(dataKey);
            
            // var m="resource data:\n"; for(var i in data) m+= i+":"+data[i]+"\n";alert(m);
            if (data._parent && data._parentJqFunction) {
                var p = data._parent;
                var pf= data._parentJqFunction;
                
                data.data.route_id = p[pf].apply(p, ['getData','id']);
            }
        },
        'load': function(dbdata) {
            var data = $(this).data(dataKey);
            data.data = dbdata.data; 
            // First Calendar-Entries

            if (dbdata.options) $.fn[dataKey].apply(this, ['options', dbdata.options]);
        },
        // Will be call from procedures in script fb.dnd.settings.js
        '_moved': function(e, ui) {
//            alert('#145 ' + dataKey + '._moved!');
            var src = (ui && ui.draggable) ? ui.draggable : null;
            var src_parent = (src && src.data(dataKey) && src.data(dataKey)._parent) ? src.data(dataKey)._parent : null;
                
            methods.reInitData.apply( this );
            if (methods._trigger.apply( this, [this, 'move', src_parent, e, ui]) === false) {
                alert('#151 ' + dataKey + ' triggered event move returns false');
                return false;
            }
            
            if (ui && ui.helper && $(ui.helper).length) {
                $(ui.helper).remove();
                return true;
            }
            
//            alert('#154 '+dataKey + ' after trigger returned. typeof this:'+typeof(this)+' class='+$(this).attr('class'));
            // Mandant, Auftragsnummer, timeline_id, DatumVon, ZeitVon, DatumBis, ZeitBis
            var data = $(this).data(dataKey);
//            alert('#157 '+dataKey + ' after trigger returned. typeof this:'+typeof(this)+'; data:'+data);
            
            // var m="resource data:\n"; for(var i in data) m+= i+":"+data[i]+"\n";alert(m);
            if (data && data._parent && data._parentJqFunction) {
                var p = data._parent;
                var pf= data._parentJqFunction;
                
                data.data.route_id = p[pf].apply(p, ['getData','id']);
//                alert("#161 " + dataKey + " parent.route_id:" + data.data.route_id);
                
                var src = (ui && ui.draggable) ? ui.draggable : null;
                if (src && src.data(dataKey) && src.data(dataKey)._parent) { 
                    var id = ui.draggable.data(dataKey)._parent[pf]( 'getData', 'id' );
//                    if (src.data(dataKey)._parent.attr( 'class' ).match(/fbDispoRouteDefaults/) ) {
//                        alert( "#167 Moved from DispoRouteDefaults with id " + id + "!");
//                    } else if (src.data(dataKey)._parent.attr( 'class' ).match(/fbDispoRoute/) ) {
//                        alert( "#169 Moved from DispoRoute with id " + id + "!");
//                    }
                }
            }
        },
        '_dropped': function() {
            methods.reInitData.apply( this );
            if (methods._trigger.apply( this, [this, 'drop']) === false) {
                $(this).closest("li").remove();
            }
        },
        '_trigger': function(obj, eventName) {
//            alert( '#158 ' + dataKey + ' : ' + eventName );
            var data = $(this).data(dataKey);
            
            var triggerEventName = methods._getEventTriggerName(eventName);
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            var args = $.makeArray(arguments).slice(1);
            
            // Member-Listener
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                data[ownEventName].apply(obj, args.slice(1) );
            }
            
            // Fire jQuery-Event
            args[0] = triggerEventName;
            if (obj == this) $( this ).trigger.apply( $(this), args);
            
            // Bubble Event to Parent-Element
            if (data._parent && data._parentJqFunction) {
                args.unshift(obj);
                args.unshift('_trigger');
                data._parent[data._parentJqFunction].apply(data._parent, args);
            }
            return;
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
    
    
    $.fn.fbDispoResource = function(options) {
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
                    if (typeof(methods[options]) == 'function' && options.substr(0,3) == 'get') {
                        return methods[options].apply( this[0], (argsLen>1?$.makeArray(args).slice(1):null) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options            
            if (!$( self ).data(dataKey)) {
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.resource) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.resource);
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
                            methods[options].apply( self, (argsLen>1?$.makeArray(args).slice(1):null));
                        }
                }
            }
            
            if (_callInit) methods['_init'].apply( self );
        });
    };

})(jQuery);