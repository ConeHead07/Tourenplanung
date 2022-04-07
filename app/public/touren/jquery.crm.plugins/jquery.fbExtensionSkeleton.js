/* 
 * @author: Frank Barthold
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
    
    var dataKey  = 'fbExtensionSkeleten';
    
    var eventTriggerMap = {
          'create': 'createAnything',
        'complete': 'completeAnything'
    };
    
    var defaults = {
                 'data': {},
              '_parent': null,
    '_parentJqFunction': null,
    
             'onCreate': null,
             'onRemove': null
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
           methods['doAnything'].apply(this);
           
           var data = $(this).data(dataKey);
           if (data && typeof(data['onCreate']) == 'function') {
               data['onCreate'].apply(this);
           }
       },
       'destroy': function() {
           var data = $(this).data(dataKey);
           if (data && typeof(data['onCreate']) == 'function') {
               data['onRemove'].apply(this);
           }
           $(this).removeData(dataKey);
           // Do some Cleanup-Work like undo rendering, etc
       },
       'doAnything': function() {
           var data = $(this).data(dataKey);
       },
       'getData': function() {
           var data = $(this).data(dataKey);
           if (arguments.length) {
               var fld = arguments[1];
               return (fld in data['data']) ? data['data'][fld] : null;
           }
           return data['data'];
       },
       'setData': function() {
           var data = $(this).data(dataKey);
           if (arguments.length == 1)
               data['data'] = arguments[0];
           else if(arguments.length >= 2)
               data['data'][arguments[0]] = arguments[1];
       },
        '_trigger': function(obj, eventName) {
            var data = $(this).data(dataKey);
            
            var triggerEventName = methods._getEventTriggerName(eventName);
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            var args = $.makeArray(arguments).slice(1);
            
            // Call Listener-Member of Object
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                data[ownEventName].apply(obj, args.slice(1));
            }
            
            // Fire jQuery-Trigger-Event
            args[0] = triggerEventName;
            if (obj == this) $( this ).trigger.apply( $(this), args);
            
            // Bubble Event to Parent-Element
            if (data._parent && data._parentJqFunction) {
//                alert('#190 trigger event ' + eventName + '; triggerEventName:'+triggerEventName + ' an ' + $(data._parent).attr('class'));
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
    
    
    $.fn.fbExtensionSkeleton = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
        
        // Default-Routine: Getting Options from allready initialized Object
        // Noch nicht initialisierte Routinen geben null zurück, wenn:
        // als Argument nur 'options' übergeben wird, um gesetzte options abzufragen
        // als Arg1 'option' und ein zweites Argument übergeben wird
        // als Arg1 eine Getter-Funktion aufgerufen wird
        // In diesem Fall bleiben uninitialisierte Objekte unberührt bzw. uninitialisiert
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
                        if (! $( this[0] ).data(dataKey) ) return null;
                        return methods[options].apply( this[0], (argsLen>1?$.makeArray(args).slice(1):null) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options
            if (ExtensionSettings && ExtensionSettings.Skeleton) {
                presets = $.extend({}, defaults, ExtensionSettings.Skeleton);
            } else {
                presets = defaults;
            }
            
            if (!$( self ).data(dataKey)) {
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