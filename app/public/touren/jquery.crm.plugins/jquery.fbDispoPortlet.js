/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($) {
    
    var counter = 0;
    var dataKey  = 'fbDispoPortlet';
    var debug = false;
    
    var eventTriggerMap = {
            'create': 'createPortlet',
            'remove': 'removePortlet',
              'fold': 'foldPortlet',
            'unfold': 'unfoldPortlet',
             'print': 'printPortlet',
       'updateTitle': 'updatePortletTitle'
    };
    var defaults = {
                 'data': {},
                 'date': null,
              '_parent': null,
    '_parentJqFunction': null,
           'isEditable': true,
           'isSortable': true,
          'isRemovable': true,
          'isPrintable': true,
            'isAddable': true,
          'isDroppable': true,
             // EVENTS
             'onCreate': null, //*null*/ function() { alert(dataKey + '.onCreate Default-Dummie-Handler!')},
             'onRemove': null, //*null*/ function() { alert(dataKey + '.onRemove Default-Dummie-Handler!')},
              'onPrint': null, //*null*/ function() { alert(dataKey + '.onPrint Default-Dummie-Handler!')},
     'onCreateTimeline': null, //*null*/ function() {alert(dataKey + '.onCreateTimeline Default-Dummie-Handler!')},
     'onRemoveTimeline': null, //*null*/ function() {alert(dataKey + '.onRemoveTimeline Default-Dummie-Handler!')},
       'onSortTimeline': null, //*null*/ function() {alert(dataKey + '.onSortTimeline Default-Dummie-Handler!')},
       // Registriert im Portlet, wenn Timeline auf ein Portlet gezogen wird
       'onDropTimeline': null, //*null*/ function() {alert(dataKey + '.onDropTimeline Default-Dummie-Handler!')},
               'onFold': null, //*null*/ function() { alert(dataKey + '.onFold Default-Dummie-Handler!')},
             'onUnfold': null //*null*/ function() { alert(dataKey + '.onUnfold Default-Dummie-Handler!')}
    };
    
    var methods = {
        '_init': function() {
            var self = this;
            var $self = $(self);
            var data = $self.data(dataKey);            
            if (data.data === null) data.data = {};
            
            counter++;
            $self.addClass(dataKey + ' portlet-content num-'+counter)
            .wrap( "<div class=\"portlet ui-widget-header ui-corner-all\" />" )
            .before( "<div class=\"portlet-header ui-corner-all\"><span class=\"ui-icon-set\"/><span class=\"portlet-header-title\">Routen-Schienen</span></div>" );
            
            $self.closest( "div.portlet" )
            //.resizable({ handles:"s" })
            .find( "div.portlet-header" )
                .addClass( "ui-corner-all" )
            .end();
            
            var editable = data.isEditable;
            
            var iconSet = $( "div.portlet-header span.ui-icon-set", $self.closest( "div.portlet" ) );
            iconSet.append( "<span class='ui-icon collapse ui-icon-triangle-1-s'></span>");
            
            if (editable && data.isSortable)
                iconSet.append( "<span class='ui-icon sortable ui-icon-arrow-4 handle'></span>");
            if (editable && data.isAddable)
                iconSet.append( "<span class='ui-icon addTrack ui-icon-plus'></span>");
            if (editable && data.isRemovable) 
                iconSet.append( "<span class='ui-icon rmPortlet ui-icon-trash'></span>");
            if (data.isPrintable)
                iconSet.append( "<span class='ui-icon prPortlet ui-icon-print'></span>");
            
            var portletContent = $( "div.portlet-content", $self.closest( "div.portlet" ) );
            portletContent.css('paddingLeft','15px');
            if (editable) {
                if (data.isSortable)
                    portletContent.sortable({
                       'cursor': "move",
                       'handle':'.DropZone-Sort',
                       'connectWith':'.portlet-content',
                        stop: function(e, ui) {
                            var dstPortlet = ui.item.parent().get(0);
                            if ( self == ui.item.parent().get(0)) {
    //                            alert('#66 old(self) and new portlet is the same');
                                methods._trigger.apply(self, [self, "sortTimeline", e, ui]);
                            } else {
    //                            alert('#69 old(self) and new portlet is not the same');
                                methods._trigger.apply(dstPortlet, [dstPortlet, "moveTimeline", e, ui, ui.item.parent()]);
                            }
                        }
                    // To-Do: Add EventHandler
                    });
                
                if (data.isDroppable)
                    portletContent.droppable({
                        accept:'.DropZone-Route',
                        drop: function(e, ui) {
                            ui.draggable.appendTo( $(this) ).css({
                                position:"relative",
                                top:0,
                                left:0,
                                width:"100%"
                            });
                            Fb.DndHelpers.droppable.drop.apply(this, arguments);
                            methods._trigger.apply(self, [self, "dropTimeline", e, ui]);

                        }                
                    });
            
                if (data.isAddable)
                    iconSet
                    .find( "span.addTrack")
                    .click(function() {
                        methods.addTimeline.apply( self );
                    });

                if (data.isRemovable)
                    iconSet
                    .find( "span.rmPortlet")
                    .click(function() {
                        //alert( ' [self=Portlet].class: ' + $(self).attr('class') );
                        methods.destroy.apply( self );
                    });

                if (data.isPrintable) 
                    iconSet
                    .find( "span.prPortlet")
                    .click(function() {
                        //alert( ' [self=Portlet].class: ' + $(self).attr('class') );
                        methods._trigger.apply( self, [self, "print" ]);
                    });
            }
            
            iconSet
            .find( "span.collapse")
            .click(function() {
                var c = ("string"===typeof $( this ).attr("class")) ? $( this ).attr("class") : "";
                $( this ).toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s");
                $( this ).parents( "div.portlet:first" ).find( "div.portlet-content:first" ).slideToggle();
                
                // To-Do: Add Eventhandler
                methods._trigger.apply(self, [self, (c.match(/\bui-icon-triangle-1-e\b/) ? 'fold' : 'unfold')]);
            });
            methods._trigger.apply( self, [self, 'create']);
            
            // alert( '#93 fbDispoPortlet data.id: ' + $(this).data(dataKey).data.id);
            var d=$(this).data(dataKey).data;
            // var m="Portlet Data:\n";for(var i in d ) m+=i+":"+d[i]+"\n";alert(m);
            
            if ('timelines' in $(this).data(dataKey).data) {
                // console.log('#155 ' + dataKey + ' timelines.length: ' + $(this).data(dataKey).data.timelines.length);
                methods.addTimelines.apply(self, [$(this).data(dataKey).data.timelines] );
                delete $(this).data(dataKey).data.timelines;
            } else {
                methods.addTimeline.apply( self );
            }
        },
        'destroy': function() {
           // alert('#114 '+dataKey+'.destroy!');
           
           if (methods._trigger.apply( this, [this, 'remove']) === false )
               return false;
           
           if ( $(this).parent().is(".portlet")) $(this).parent().remove();
           else $(this).remove();
        },
        'addTimelines': function(timelines) {
            for(var ti in timelines) {
                methods.addTimeline.apply(this, [ timelines[ti] ] );
            }
        },
        'addTimeline': function(b) {
            var data = $(this).data(dataKey);
            var self = this;
            var $self = $(self);
            if (typeof(b)=="undefined" || b===null) b = {};
            b.portlet_id = data.data.id;
            b.date = data.date;
            
            var t = $('<div />');
            $self.append(t);

            t.fbDispoTimelineDropzone({
                             date: data.date,
//                             data: $.extend({},{date: data.date, portlet_id:data.data.id },b),
                             data: b,
                        '_parent': $self,
              '_parentJqFunction': dataKey
            });

            if (!t.parent().length) {
                // console.log("#196 " + dataKey + " remove added Timeline, since it returned with false");
            } else {
                // console.log("#198 " + dataKey + " added Timeline");
            }
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getData': function() {
            var d = $(this).data(dataKey);
            if ("undefined" === typeof d ) return null;
            if ("object" !== typeof d || !("data" in d) ) return null;
            if (arguments.length) {
               return (arguments[0] in d.data) ? d.data[arguments[0]] : null;
            }
            return d.data;
        },
        'getTitle': function() {
            return $("span.portlet-header-title", $(this).closest("div.portlet") ).html();
        },
        'setData': function() {
//            var m= dataKey + '.setData(' + $.makeArray(arguments).join(',')+')\n';
            if (arguments.length == 1)
               $(this).data(dataKey).data = arguments[0];
            else if(arguments.length >= 2)
               $(this).data(dataKey).data[arguments[0]] = arguments[1];
        },
        'setTitle': function(title) {
            var portlet = this;
            $("span.portlet-header-title", $(this).closest("div.portlet") )
            .html(title)			
            .unbind("click").bind("click", function(){
                    var $pTitle = $(this).find("span.p-title");
                    var title = $pTitle.text();
                    var newtitle = prompt("Titel bearbeiten", title );
                    if (typeof(newtitle) == "string" && newtitle !== title) {
                            if (methods._trigger.apply(portlet, [portlet, 'updateTitle', newtitle] ) !== false ) {
                                    $pTitle.text( newtitle );
                            } else {
                                    alert( 'trying update title returns false!');
                            }
                    }
            });
        },
        'clear': function() {
            $( ".DropZone.DropZone-Route", this).remove();
            methods._trigger.apply(this, array(this, 'clearDispoPortlet'));
        },
        'load': function(dbdata) {
            var data = $(this).data(dataKey);
            data.data = dbdata.data; 
            // First Calendar-Entries
            methods.clear.apply( this );
            
            if (dbdata.options) $.fn[dataKey].apply(this, ['options', dbdata.options]);
            
            if (!dbdata.timelines) return;
              
            for(var i = 0; i < dbdata.timelines.length; ++i) {
                methods.addTimeline.apply( this, [ 'load', dbdata.timelines[i]] );
            }
            methods._trigger.apply(this, array(this, 'loadDispoPortlet'));
        },
        '_trigger': function(obj, eventName) {
            var data = $(this).data(dataKey);
//            alert('#190 ' + dataKey + "._trigger("+$(obj).attr('class')+", "+eventName+")");
            var triggerEventName = methods._getEventTriggerName(eventName);
            var ownEventName = methods._getOwnEventHandlerName(eventName);
            
            var args = $.makeArray(arguments).slice(1);
            var re = null;
            
            if (ownEventName && ownEventName in data && typeof(data[ownEventName])=='function') {
                if (false === data[ownEventName].apply(obj, args.slice(1) ))
                    return false;
            }
            
            args[0] = triggerEventName;
            if (obj == this) {
                if (false === $( this ).trigger.apply( $(this), args))
                    return false
            }
            
            if (data._parent && data._parentJqFunction) {
                args.unshift(obj);
                args.unshift('_trigger');
//              alert('#174 ' + dataKey + "._trigger  => data["+ownEventName+"].apply("+$(obj).attr('class')+", ["+args+"])");
                re = data._parent[data._parentJqFunction].apply(data._parent, args);
                if (false === re) {
                    return false;
                }
            }
//            if (eventName.match(/remove/)) alert('#220 '+dataKey+'._trigger '+eventName+' return true');
            return true;
        },
        '_getEventTriggerName': function(eventName) {
            if (eventName in eventTriggerMap) {
                eventName = eventTriggerMap[eventName];
            }
            return eventName;
        },
        '_getOwnEventHandlerName': function(eventName) {
            var e = 'on' + eventName.charAt(0).toUpperCase() + eventName.substr(1);
            return (e in defaults) ? e : '';
        }
    };
    
    $.fn.fbDispoPortlet = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
//        alert("#194 this:"+$(this).attr('class')+" $.fn."+dataKey + "([" + $.makeArray(args)+"])\n" + $.fn.fbDispoPortlet.caller);
        
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
                        return methods[options].apply( this[0], $.makeArray(args).slice(1) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options            
            if (!$( self ).data(dataKey)) {
                if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.portlet) {
                    presets = $.extend({}, defaults, Fb.DispoCalendarSettings.portlet);
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
            var d=data.data;

            if (d && typeof d === "object" && "settings" in d && d.settings) {
                for(var _si in d.settings) {
                    data[_si] = d.settings[ _si ];
                }
                delete d.settings;
            }
            // var m="#245 fbDispoPortlet data.data.data:\n";for(var i in d ) m+=i+":"+d[i]+"\n";alert(m);
            
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
                            methods[options].apply( self, $.makeArray(args).slice(1) );
                        }
                }
            }
            if (_callInit) methods['_init'].apply( self );
        });
    };

})(jQuery);
