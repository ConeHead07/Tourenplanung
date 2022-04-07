
(function($) {
    var defaults = {
        icon:'',
        display:'inline-block',
        css: null,
        clss:'ui-state-default ui-corner-all',
        hoverClass:'ui-state-hover',
        pos: 'prepend',
        click: null,
        bind: null //{click: function(){}}        
    };
    
    $.fn.fbIcon = function(opts) {
        var o = {};
        if (typeof(opts) == 'object') {
            o = $.extend({}, defaults, opts );
            if (o.click)   o.bind = $.extend({}, o.bind, { click:o.click} );
        } else {
            var al = arguments.length;
            o = $.extend({}, defaults);
            if (typeof(opts) == 'string') o.icon = opts;
            if (al== 2 && typeof(arguments[1])=='object') o = $.extend({}, o, arguments[1]);
            if (al== 3 && typeof(arguments[2])=='object') o = $.extend({}, o, arguments[2]);
            if (al > 1 && typeof(arguments[1]) == 'function') o.bind = $.extend({}, o.bind, { click: arguments[1] });
        }
        if (o.display) o.css  = $.extend({}, o.css,  { display:o.display} );
        
        return this.each(function() {
            var $self = $(this);
            
            var icon = $("<span/>").css(o.css)
            .addClass(o.clss)
            .append( $("<span/>").addClass("ui-icon ui-icon-"+o.icon) )
            .hover(function(){$(this).addClass(o.hoverClass)}, function(){$(this).removeClass(o.hoverClass)} );
            
            for(var e in o.bind)
                $self.bind(e, o.bind[e]);
            
            if (o.pos == 'prepend') $self.prepend(icon);
            else                    $self.append(icon);
        });
    }
})(jQuery);

if (typeof(Fb)=="undefined") var Fb = {};
Fb.Dnd = {
      overDroppable: null,
      overDropRulers: [],
     droppableBefore: null,
           draggable: null,
      draggable_data: null,
     draggable_width: null,
     droppable_route: null
};
Fb.DndHelpers = {};

(function($) {
Fb.DndHelpers.draggable = {
    start : function(e,ui) {
        //alert('Fb.DndHelpers.start class:' + ui.helper.attr('class'));
        if ( !$(this).data("draggable") ) $(this).data("draggable", {});
        var data = $(this).data("draggable");
        //alert("typeof(Fb.Dnd)"+typeof(Fb.Dnd));
        Fb.Dnd.draggable = $(this);
        Fb.Dnd.draggable_data = data;

        if (ui.helper && ("string"!==typeof ui.helper.attr('class') || !ui.helper.attr('class').match(/\bIs-Template\b/))) {
             // alert("position.left:"+$(this).position().left+"\ncss.width:"+$(this).css('width') );
             var posLeft = (1 && $(this)[0].style.left) ? parseInt($(this).css('left')) : $(this).position().left;
             var posTop = (0 && $(this).css('top')) ? parseInt($(this).css('top')) : $(this).position().top;

             var placeholder = $('<'+ui.helper.get(0).tagName+'/>').css({
                 position: ui.helper.css('position'),
                 display:'inline',
                 zIndex:ui.helper.css('zIndex'),
                 left:ui.helper.css('left'),
                 top:ui.helper.css('top'),
                 padding:0,
                 margin:0,
                 border:'1px dotted #d2d2d2'
             }).append( $('<div/>').css({paddin:0,margin:0,position:'relative',display:'inline-block',width:ui.helper.width()-2,height:ui.helper.height()-2}));

             data._src = {
                 changedParent: true,
                 revert:false,
                 placeholder: placeholder,
                 nextElm:this.nextSibling,
                 position:$(this).css("position"),
                 zIndex: $(this).css("zIndex"),
                 parent: $(this).parent().first(),
                 left: posLeft,
                 top: posTop,
                 styleWidth: $(this)[0].style.width,
                 styleLeft: $(this)[0].style.left,
                 styleTop: $(this)[0].style.top,
                 pctWidth: ($(this)[0].style.width.match(/%/))?$(this)[0].style.width:null,
                 pctLeft: ($(this)[0].style.left.match(/%/))?$(this)[0].style.left:null,
                 pctTop: ($(this)[0].style.top.match(/%/))?$(this)[0].style.top:null
             };
             data._dst = {};

             if (data._src.pctWidth || !data._src.styleWidth) {
                 $(this).css("width", $(this).width()+"px" );
             }
             //alert( 'data._src.parent.get(0).tagName:'+data._src.parent.get(0).tagName );

             var os = ui.helper.offset();

             // Mit diesem Pfad können auf die intern verwendeten Draggable-Daten zugegriffen werden
             data.offset.parent.top = 0;
             data.offset.parent.left = 0;
             data.offset.relative.top = 0;
             data.offset.relative.left = 0;
             data.positionAbs.top = os.top;
             data.positionAbs.left = os.left;
             placeholder.insertBefore(ui.helper);
             ui.helper.appendTo('body').css({position:'absolute',left:os.left, top:os.top, zIndex:2000});

         } else {
             data._src = {
                 zIndex: $(this).css("zIndex")
             }
         }
         //alert("pause drag start!");
         return true;
    },
    revert : function(Dropped) {
        // @param Dropped gibt an, ob Dropping vom droppable Element akzeptiert wurde (true) oder nicht (false)
        //
        // Als originalPosition werden hier die Top und Left - Werte
        // gesetzt, die das Element ursprünglich als css-Wert definiert hatte
        // Sofern die Angabe für position:absolute|relative|etc nach dem erfolgreichen Droppen
        // nicht geändert wurde, muss die Position nicht neu berechnet werden.
        // Es reicht wenn die Ursprungsposition gespeichert wurde oder einen Standardwert hatte.
        // Bei relativ positionierten Elementen genügt i.d.R die Angabe 0,0
        // $(this).data("draggable").originalPosition = { op: 0, left: 0 };

        if (!Dropped) {
            $( this ).fadeTo(200, 1);
        }

        // @return true, wenn Draggable zurück auf seinen Ausgangsplatz soll
        // @return false, wenn Draggable an der gezogenen Position bleiben soll
        var doRevert = (Dropped) ? false : true;
        if (doRevert) {
            var data = $(this).data('draggable') || {};
            var data_src = data._src || {}
            if ( data_src && data_src.changedParent) {
                data_src.revert = true;
                data.originalPosition.left = data_src.parent.offset().left+data_src.left;
                data.originalPosition.top = data_src.parent.offset().top+data_src.top;
            }
        }
        return doRevert;
    },
    stop        :  function(e,ui) {
        Fb.Dnd.draggable = null;
        Fb.Dnd.draggable_data = null;
        var data = $(this).data('draggable') || {};
        var data_src = data._src || {}

        if ( data_src)
        {
            if (data_src.revert) {
                //alert( data_src.nextElm );
                //alert( '#551 data_src.nextElm.tagName:'+data_src.nextElm.tagName );
                if ( $(data_src.placeholder).length ) {
                    $(data_src.placeholder).replaceWith(ui.helper);
                }
                else if ($(data_src.nextElm).length) {
                    $( ui.helper ).insertBefore( data_src.nextElm );
                } else {
                    ui.helper.appendTo( data_src.parent )
                }
                
                ui.helper.css({position:data_src.position, left:data_src.left, top:data_src.top});
                if (data_src.pctLeft && data_src.pctLeft.match(/%/)) ui.helper.css("left", data_src.pctLeft);

                if (data_src.pctTop && data_src.pctTop.match(/%/)) ui.helper.css("top", data_src.pctTop);
                if (data_src.pctWidth && data_src.pctWidth.match(/%/)) ui.helper.css("width", data_src.pctWidth);
                else ui.helper.css("width", data_src.styleWidth);
                ui.helper.css("left", data_src.styleLeft);
                ui.helper.css("top", data_src.styleTop);
            }

            if ( data_src.zIndex)
                ui.helper.css({zIndex: data_src.zIndex});
        }
    }
};

Fb.DndHelpers.droppable = {
     drop: function(e,ui) {
        //alert('drop');
        var data = ui.helper.data("draggable") || ui.draggable.data("draggable") || {};
        var data_src = data._src || {};

        if (data_src.zIndex) ui.helper.css({zIndex: data_src});
        //alert( data_src.placeholder);
        if ( data_src.placeholder) $(data_src.placeholder).remove();
        if (ui.helper !== ui.draggable) {
            ui.helper.removeClass('Is-Template').attr('id', ui.draggable.attr('id')+Fb.DndHelpers._getNextID() );
        }
//      alert("#165 fb.dnd.settings.js event Fb.DndHelpers.droppable.drop");
        return false;
    }
};
Fb.DndHelpers._getNextID = (function() {
    var ID = 0;
    return function() {
        return ++ID;
    };
})();

Fb.DropTrashBoxSettings = {
    hoverClass: "ui-state-active"
    ,activeClass: "ShowDroppableZone"
    ,accept: ".Drag-Route,.Drag-Rsrc,.portlet"
    ,tolerance: 'pointer' // Options: intersect (50%)|touch|pointer|fit

    // All droppable-callbacks receive two arguments:
    // The original browser event and a prepared ui object,
    // view below for a documentation of this object (if you name your second argument 'ui'):
    // ui.draggable - current draggable element, a jQuery object.
    // ui.helper    - current draggable helper, a jQuery object
    // ui.position  - current position of the draggable helper { top: , left: }
    // ui.offset    - current absolute position of the draggable helper { top: , left: }
    ,over: function(e,ui) {}
    ,out:  function(e,ui) {}
    ,drop: function(e,ui) {
        var Item = (ui.helper == ui.draggable) ? ui.helper : ui.helper.clone().appendTo('body');
        var data = Item.data("draggable");
        if (data && data._src && data._src.placeholder && data._src.placeholder ) data._src.placeholder.remove();
        Item.draggable('disable').hide(350, function() { $(this).remove(); });        
        return true;
    }
};


Fb.DragRsrcTemplateSettings = {
        helper:'clone'
        ,welcome: 'Juhuu!'
        ,appendTo:'body'
        //,scope:'DndRsrc'
        ,zIndex:2500
};
Fb.DragRsrcInstanceSettings = $.extend({}, Fb.DragRsrcTemplateSettings, { helper:'none', appendTo:null }, Fb.DndHelpers.draggable);

Fb.RouteDataDefaults = {
       start: 0,
    duration: 120,
         end: 0
};

Fb.DragRouteTemplateSettings = {
    create: function() {
        if (!$(this).data("route")) $(this).data("route", $.extend({}, Fb.RouteDataDefaults));
        var RouteData = $(this).data("route");
        for(var i in Fb.RouteDataDefaults) if (typeof(RouteData[i])=="undefined") RouteData[i] = Fb.RouteDataDefaults[i];
    }
    ,helper:'clone'
    //,snap: 'div.tl-grid-step'
    //,snapMode:'inner'
    ,appendTo:'body'
    //,scope:'DndRoute'
    ,zIndex:2500
    ,drag: function(e,ui) {
        if ("undefined"===typeof e.pageX && "undefined"!==typeof window.event && "undefined"!==typeof window.event.pageX) e.pageX = window.event.pageX;
        if (Fb.Dnd.draggable == null || !Fb.Dnd.draggable_data) {
            Fb.Dnd.draggable = $(this);
            Fb.Dnd.draggable_data = $(this).data("draggable");
        }
        if (!Fb.Dnd.draggable_data._dst) Fb.Dnd.draggable_data._dst = {};
        
        try {           
            if (Fb.Dnd.draggable_data.overDroppable && 
                Fb.Dnd.draggable_data.overDroppable.data("fbDispoTimelineGrid")) {
                var overDroppable = Fb.Dnd.draggable_data.overDroppable;
                if (Fb.Dnd.droppableBefore !== overDroppable) {
                    var offsetLeft = overDroppable.offset().left;
                    //var offsetTop = overDroppable.offset().top + $("div.Timeline:first", overDroppable).height()+1;
                    Fb.Dnd.draggable_width = Fb.Dnd.draggable.width();

                    Fb.Dnd.droppableRulers = overDroppable.data("fbDispoTimelineGrid").gridRulers.map(function() {
                       return {
                               left: offsetLeft+$(this).position().left,
                         originLeft: $(this)[0].style.left,
                                top: $(this).offset().top,
                          gridRuler: $(this)
                       }
                    });
                    Fb.Dnd.droppableBefore = overDroppable;
                }

                //alert(Fb.Dnd.draggable_data.offset.click.left);
                var dragLeft = e.pageX  - Fb.Dnd.draggable_data.offset.click.left;
                var r = Fb.Dnd.droppableRulers;

                var snapTo = 0;
                while(snapTo+1 < r.length && r[snapTo+1].left < dragLeft) ++snapTo;
                if (snapTo+1 < r.length && (0.8*(dragLeft - r[snapTo].left)) > (r[snapTo+1].left-dragLeft) ) ++snapTo;

                ui.position.top = Fb.Dnd.droppableRulers[snapTo].top;
                ui.position.left = Fb.Dnd.droppableRulers[snapTo].left;

                // Zwischenspeichern der aktuellen Position
                Fb.Dnd.draggable_data._dst.left = Fb.Dnd.droppableRulers[snapTo].originLeft;
                Fb.Dnd.draggable_data._dst.gridRuler = Fb.Dnd.droppableRulers[snapTo].gridRuler;            
            }         
        } catch( e ) {
            return;
        }
    }
};
$.extend(Fb.DragRouteTemplateSettings, Fb.DndHelpers.draggable);
Fb.DragRouteInstanceSettings = $.extend({}, Fb.DragRouteTemplateSettings, { helper:'none', appendTo:'body' }); //, Fb.DndHelpers.draggable);

Fb.MoveTimelineSettings = $.extend({}, Fb.DndHelpers.draggable, {
    start: function(e, ui) {
        Fb.DndHelpers.draggable.start.apply(this, arguments);
        $(this).fbDispoTimelineDropzone.apply( $(this), ["_trigger", this, "startmove", e, ui] );
    },
    stop: function(e, ui) {
        Fb.DndHelpers.draggable.stop.apply(this, arguments);
        $(this).fbDispoTimelineDropzone.apply( $(this), ["_trigger", this, "move", e, ui] );
    }
});

Fb.DropRouteSettings = {
    create: function(e,ui) {
        $(this).fbDispoTimelineGrid({
            timeline:"div.Timeline", 
            show:false, 
            top:$("div.Timeline:first", $(this)).height()
        });
        $(this).droppable( 'option', 'accept', '.Drag-Route');
    }
    ,hoverClass: "ui-state-active"
    ,activeClass: "ShowDroppableZone"
    ,accept: ".noDrag-Route"
    ,tolerance: 'pointer' // Options: intersect (50%)|touch|pointer|fit
    ,over: function(e, ui) {
        $(this).fbDispoTimelineGrid("show");
        ui.draggable.data("draggable").overDroppable = $(this);
    }
    ,out: function(e, ui) {
        $(this).fbDispoTimelineGrid( "hide" );
        ui.draggable.data("draggable").overDroppable = null;
    }
    ,drop:function(e,ui) {
        var $self = $(this);
        var dropTop   = $("div.Timeline:first", $(this)).height();
        var dragData  = ui.helper.data("draggable") || ui.draggable.data("draggable") || {};
        var routeData = ui.helper.data("route") || ui.draggable.data("route") || {};
        var gridData  = $(this).data("fbDispoTimelineGrid");
        var timelineData = $("div.Timeline", $(this)).fbDispoTimelineLineal("data");

        var dstLeft = (dragData && dragData._dst && dragData._dst.left) ? dragData._dst.left : null;
        $(this).fbDispoTimelineGrid({timeline:"div.Timeline", show:true, refreshIfExists:true, top:$("div.Timeline:first", $(this)).height()});

        //alert("e:"+typeof(e)+"; ui:"+typeof(ui));
        Fb.DndHelpers.droppable.drop.apply(this, arguments);
        var obj = (ui.helper===ui.draggable)?ui.helper:ui.helper.clone();
        if (ui.helper.data('dragdata')) obj.data('dragdata', ui.helper.data('dragdata') );
        var recalcPos = {
            left: ui.helper.offset().left - $self.offset().left,
            top: ui.helper.offset().top - $self.offset().top
        };
        obj.appendTo($self);
        obj.css({
            position:'absolute',
            left: recalcPos.left,
            top: recalcPos.top
        }).animate({left: Math.max(5, recalcPos.left), top:dropTop}, function() {
            if (dstLeft !== null) $(this).css("left", dstLeft);
        });

        routeData.start = Fb.Dnd.draggable_data._dst.gridRuler.data("gridStep").time;
        routeData.end = routeData.start + routeData.duration;
        
        var routeOpts = obj.fbDispoRoute('options');
        var routeSetOpts = {
            _parent:$(this),
            _parentJqFunction:'fbDispoTimelineDropzone',
            _parentFrom:(routeOpts && ('_parent' in routeOpts) ? routeOpts._parent : null),
            timeStart: routeData.start,
            timeDuration: routeData.duration,
            timeEnd:routeData.end
        }
        
        if (!routeOpts) {
            // Neu hinzugefügt
            obj.fbDispoRoute( routeSetOpts );
            if (obj.fbDispoRoute('_dropped', e, ui) === false) {
                return false;
            }
        } else {
            // Ein bereits hinzugefügtes wurde verschoben
            obj.fbDispoRoute('options', routeSetOpts );
            if (obj.fbDispoRoute('_moved', e, ui) === false) {
                //alert('397 fb.dnd.settings.js return of trigger : false');
                return false;
            }
        }
        obj.trigger("click");

        var ts = timelineData.settings;
        obj[0].style.width = (routeData.duration*100/ts._totalM)+"%";
        ui.draggable.data("draggable").overDroppable = null;
    }
};

Fb.DropRsrcOnRouteSettings = $.extend({}, Fb.DropRouteSettings, { 
    create: null, 
    drop: function(e, ui) {
        var $self = $(this);
        Fb.DndHelpers.droppable.drop.apply(this, arguments);
        
        var obj = (ui.helper===ui.draggable)?ui.helper:ui.helper.clone();
        
        var recalcPos = {
            left: ui.helper.offset().left - $self.offset().left,
            top: ui.helper.offset().top - $self.offset().top
        };
        
        obj.appendTo( $("ul.resources:first", $self) );
        if (obj.get(0).tagName.toLowerCase() === "li") {
            obj.appendTo( $("ul.resources:first", $self) );
        } else {
            $("ul.resources:first", $self).append( $("<li/>").append(obj)  );
        }
        obj.css({
            position:'relative',
            left:8,
            top:0
        });
        
        var showOriginVersion = 0;
        if (showOriginVersion) {
            if (obj !== ui.draggable) {
                obj.draggable( Fb.DragRsrcInstanceSettings );
                $( this ).fbDispoRoute('_trigger', this, 'dropResource', obj, e, ui);
            } else {
                $( this ).fbDispoRoute('_trigger', this, 'moveResource', obj, e, ui);
            }
        } else {
            //alert( "#440 fb.dnd.settings.js resource dropped!");  
            var parentJqFunction = 'fbDispoRoute';
            if ($(this).is('.fbDispoRouteDefaults')) parentJqFunction = 'fbDispoRouteDefaults';
            
            var resourceOpts = obj.fbDispoResource('options');
            var resourceSetOpts = {
                _parent:$(this),
                _parentJqFunction:parentJqFunction,
                _parentFrom:(resourceOpts && ('_parent' in resourceOpts) ? resourceOpts._parent : null),
                data: ui.helper.data('dragdata')
            }

            if (obj !== ui.draggable) {
                // Neu hinzugefügt
                $( this ).trigger('resource-dropped', [obj, ui]);
//                obj.fbDispoResource( resourceSetOpts );
//                obj.fbDispoResource('_dropped', e, ui);
            } else {
                // Ein bereits hinzugefügtes wurde verschoben
                $( this ).trigger('resource-moved', [obj, ui]);
//                obj.fbDispoResource('options', resourceSetOpts );
//                obj.fbDispoResource('_moved', e, ui);
            }
        }
    }
});

Fb.RouteResizableSettings = {
    handles: "w,e", // sw,s,se,
    create: function(e,ui) {
    },
    start: function(e,ui) {
        //alert('#473 fb.dnd.settings.js enter callback start resizing');
        //return;
        var $self = $(this),
               rd = $self.data("resizable"), 
                o = rd.options,
                p = $self.parent("div.DropZone-Route:first"),
               gr = p.data("fbDispoTimelineGrid").gridRulers;
        
        o.grid = [gr.eq(1).position().left - gr.eq(0).position().left,1];
        $self.resizable( "option", "minWidth", o.grid );
        
        Fb.Dnd.droppableRulers = gr.map(function() {
           return {
                   left: $(this).position().left,
             originLeft: Math.floor($(this)[0].style.left),
                    top: $(this).offset().top,
                  ruler: $(this),
                   time: $(this).data("gridStep").time
           }
        });
        
        rd._src = { 
                  parent: p, 
                  rulers: Fb.Dnd.droppableRulers, 
            timelineData: $("div.Timeline", p).fbDispoTimelineLineal("data") 
        };
        //alert('#501 fb.dnd.settings.js leave callback start resizing');
    },
    resize: function(e,ui) {
        //alert('#540 resizing');
    },
    stop: function(e, ui) {
        var $self = $(this);
        var data = $self.data("resizable");
        var timelineData = $("div.Timeline", data._src.parent).fbDispoTimelineLineal("data");

        var m="";
        var r = Fb.Dnd.droppableRulers;
        if (!r || !r.length) return;
        var rid = 0;
        var resLeft = data.position.left;
        var resRight = resLeft + $self.width();
        var stepTolerance = Math.round((r[1].left - r[0].left)*0.5);

        while(rid+1 < r.length && Math.floor(r[rid+1].left) <= resLeft) ++rid;
        var start_rid = (rid+1<r.length && r[rid+1].left-resLeft<resLeft-r[rid].left) ? ++rid : rid;

        while(rid+1 < r.length && Math.floor(r[rid+1].left) <= resRight+stepTolerance) {
            //alert( rid + "; " + r[rid+1].left + " => " + Math.floor(r[rid+1].left) + " <=" + resRight )
            ++rid;
        }
        var end_rid = (rid+1<r.length && r[rid+1].left-resLeft<resLeft-r[rid].left) ? ++rid : rid;

        var ts = timelineData.settings;
        //m="#797 timelineData.settings "; for(var i in ts) m+= i+":"+ts[i]+"; "; alert(m);
        var routeData = $self.data("route");
        
        routeData.start = r[start_rid].time;
        routeData.end = r[end_rid].time;
        routeData.duration = routeData.end - routeData.start;
        if (routeData.duration < ts._stepWidthM) routeData.duration = ts._stepWidthM;
        
        $self[0].style.left = r[start_rid].originLeft;
        $self[0].style.width = (routeData.duration*100/ts._totalM)+"%";
        
        $self.fbDispoRoute('_resized');

        //$self[0].style.width = ($self.width()*100/$self.parent("div:first").width())+"%";
    }
};

})(jQuery);