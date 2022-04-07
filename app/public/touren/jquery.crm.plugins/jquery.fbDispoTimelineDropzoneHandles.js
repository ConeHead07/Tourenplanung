/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($) {
    var dataKey = 'fbDispoTimelineDropzoneHandles';
    
    
    var defaults= {
        sortable: true,
         movable: true,
       removable: true
    };
    
    var methods = {
        '_init': function() {
            var self  = this;
            var $self = $( this );
//            alert( "_init: " + $self.html() );
            var data = $self.data(dataKey);
            
            $("div.DropZone-DragHandle,div.DropZone-Remove,div.DropZone-Sort,div.DropZone-General", $(this) ).remove();
            
            var handleBox =                 
                $('<div class="Timeline-HandleBox ui-state-default ui-corner-left"'+
                  'style="position:absolute;top:-1px;width:15px;left:-17px;height:60px;" />'
                )
                .css('height', $(this).height() )
                .bind('mouseover', function() {$(this).addClass('ui-state-hover'); $('div', this).show(); })
                .bind('mouseout', function() {$(this).removeClass('ui-state-hover'); $('div', this).hide(); })
//                .append( 
//                    $("<div class='DropZone-DragHandle ui-state-disabled' title='Verschieben' style='display:none;'>"+
//                      "<span style='width:13px;background-position: -2 -82px;' class='ui-icon ui-icon-arrow-4'></span>"
//                    ).css({
//                        position:"absolute",
//                        top:2,
//                        left:1,
//                        width:"13px",
//                        height:"15px",
//                        border:0
//                    })
//                    .hover( function() {$(this).toggleClass('ui-state-default ui-corner-all').toggleClass('ui-state-disabled')} )
//                )
            ;
            
            if (data.removable)
            handleBox.append( 
                $("<div class='DropZone-Remove ui-icon-trash ui-state-disabled' title='Loeschen' "+
                  "style='display:none;'><span style='width:13px;background-position: -178px -98px;'"+
                  "class='ui-icon ui-icon-trash'></span></div>"
                ).css({
                    position:"absolute",
                    top:"19px",
                    left:1,
                    width:"13px",
                    height:"15px",
                    border:0
                }).click(function() {
                    self.fbDispoTimelineDropzone('destroy');
                })
                .hover( function() {
                    $(this).toggleClass('ui-state-default ui-corner-all').toggleClass('ui-state-disabled')
                })
            );
            
            if (data.sortable)
            handleBox.append( 
                $("<div class='DropZone-Sort ui-state-disabled' title='Sortieren' style='display:none;'>"+
                  "<span style='width:13px;background-position: -130px -1;' "+
                  "class='ui-icon ui-icon-carat-2-n-s'></span></div>"
                ).css({
                    position:"absolute",
                    top:"36px",
                    left:1,
                    width:"13px",
                    height:"15px",
                    border:0
                })
                .hover( function() {
                    $(this).toggleClass('ui-state-default ui-corner-all').toggleClass('ui-state-disabled')
                })
            );
            
            handleBox.append( 
                $("<div class='DropZone-General Drag-Route ui-state-disabled' title='Standard-Ressourcen' style='display:none;'>"+
                  "<span style='width:13px;background-position: -163px -130;' class='ui-icon ui-icon-clipboard'></span></div>"
                ).css({
                    position:"absolute",
                    top:"51px",
                    left:1,
                    width:"13px",
                    height:"15px",
                    border:0
                }).click(function() {
//                      return;
//                      alert("#46 Markiere Leiste für Resourcenzuweisung!");
                    var DropDefaultBox = $("div.DropZoneDefaults", self);
                    if ( !DropDefaultBox.length) {
//                            alert( '#75 '+dataKey+' typeof($.fn.fbDispoRouteDefaults) '+typeof($.fn.fbDispoRouteDefaults) );
                        DropDefaultBox = $( '<div/>');
                        self.append( DropDefaultBox );
                        DropDefaultBox.fbDispoRouteDefaults();
                    }
                    $("div.DragRoute-Focus").removeClass("DragRoute-Focus");

                    DropDefaultBox.toggleClass('DropZone-Focus');
                    DropDefaultBox.toggle( "string" === typeof DropDefaultBox.attr('class') && DropDefaultBox.attr('class').match(/\bDropZone-Focus\b/) );

                    if ("string" === typeof DropDefaultBox.attr('class') && DropDefaultBox.attr('class').match(/\bDropZone-Focus\b/)) {
                        DropDefaultBox.trigger('click');
                    }
                })
                .hover( function() {$(this).toggleClass('ui-state-default ui-corner-all').toggleClass('ui-state-disabled')} )
            );
            
            $(this).append( handleBox );
            
            if (data.movable)
            $(this).draggable( 
                $.extend({}, Fb.MoveTimelineSettings, {handle:'div.DropZone-DragHandle'}) 
            );
        }
    };
    
    $.fn[dataKey] = function(options) {
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

       return this.each(function() {
            var self = $(this);
            var _callInit = false, presets = {};
            
            // Default Routine: Setting options
            if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.timelineDropzoneHandles) {
                presets = $.extend({}, defaults, Fb.DispoCalendarSettings.timelineDropzoneHandles);
            } else {
                presets = defaults;
            }
            
            if (!$( self ).data(dataKey)) {
                if (!options) {
                    $( this ).data(dataKey, $.extend({}, presets));
                }
                else if (typeof(options) == "object") {
                    $( this ).data(dataKey, $.extend({}, presets, options) );
                }
                else
                    $( this ).data(dataKey, $.extend({}, presets));                
                _callInit = true;
            }
            
            if (_callInit) methods['_init'].apply( self );
        });
    };
})(jQuery);