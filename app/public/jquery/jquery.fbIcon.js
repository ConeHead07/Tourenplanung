
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

