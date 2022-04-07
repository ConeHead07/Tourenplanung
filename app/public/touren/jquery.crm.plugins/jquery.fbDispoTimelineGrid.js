/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function($) {
    var requireds = {
       timeline: null
    };
    
    var dataKey  = "fbDispoTimelineGrid";
    var defaults = {
       timeline: '',
      gridClass: '',
          width: '1px',
          color: '#d2d2d2',
          style: 'dashed',
           show: true,
            top: 0,
         zIndex: 5000,
refreshIfExists: false
    };

    $.fn.fbDispoTimelineGrid = function(options) {
        
        if (typeof(options)=="string") {
            switch(options) {
                case "destroy":
                    return this.each(function() {
                        $(".tl-grid-step", $(this)).remove();
                        $(this).removeData(dataKey);
                    });
                case "hide":
                    return this.each(function() {
                        $(".tl-grid-step", $(this)).css('borderLeftWidth',0).hide();
                    });
                case "show":
                    return this.each(function() {
                        $(".tl-grid-step", $(this)).css('borderLeftWidth','1px').show();
                    });
                case "toggle":
                    return this.each(function() {
                        $(".tl-grid-step", $(this)).toggle();
                    });
                case "getGridRulers":
                    if (!this.length) return [];
                    var data = this[0].data(dataKey);
                    if (data && data.gridRulers) return data.gridRulers;
                    return [];
                case "refresh":
                    // Kein Return, damit GridRulers neu aufgebaut werden können!
                    this.each(function() {
                        var data = $(this).data(dataKey);
                        if (data && 'gridRulers' in data) data.gridRulers = null;
                    });
            }
        }

        return this.each(function(index) {
            var $self = $(this);
            if (!$(this).data(dataKey)) $(this).data(dataKey, {});
            var data = $(this).data(dataKey);            
            
            // Default Routine: Setting options
            var presets = {};
            if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.timelineGrid) {
                presets = $.extend({}, defaults, Fb.DispoCalendarSettings.timelineGrid);
            } else {
                presets = defaults;
            }
            
            if (!data.settings) data.settings = $.extend({}, presets);
            data.settings = $.extend({}, data.settings, options);
            
            if (!data.gridRulers) data.gridRulers = null;
            if (data.gridRulers && data.gridRulers.length && !data.refreshIfExists) return;
            var s = data.settings;
            if (data.refreshIfExists != defaults.refreshIfExists) data.refreshIfExists = defaults.refreshIfExists;
            
            for(var i in requireds) {
                if (!s[i] || !$(s[i], $self).length) {
                    //var m=""; for (j in defaults) m+= j+":"+defaults[j]+"; "; alert(m);
                    if (!s[i]) alert("Missing option for Timeline: "+i);
                    else alert("Invalid option for Timeline: Can not Found Element: $("+s[i]+", $self); self.class:"+$self.attr("class"));
                    return;
                }
            }
            
            var h = $self.height();
            
            var timeline = $(s.timeline, $self);
            var tSteps = $(".tl-step", timeline );
            $self.find( 'div.tl-grid-step' ).remove();
            
            tSteps.each(function(index){
                //if (index == 2) alert(" ADD "+index+" Vertikal Ruler $(this)[0].style.left:"+$(this)[0].style.left+"+!");
                $self.append( $("<div/>").css({
                        position:'absolute',
                          height: h,
                             top: s.top,
                            left: $(this)[0].style.left,
                           width: 0,
                      borderLeft: (s.show ? '1px':'0px') + ' ' + s.style + ' ' + s.color,
                          zIndex: s.zIndex,
                        overflow: 'hidden'
                    }).addClass('tl-grid-step '+ s.gridClass).data("gridStep", {time: $(this).data("timeStep").time})
                 );
            });
            
            $self.css("height", $("div.tl-grid-step:first", $self).height());
            data.gridRulers = $("div.tl-grid-step", $self);
        });
        
    };
 })(jQuery);


