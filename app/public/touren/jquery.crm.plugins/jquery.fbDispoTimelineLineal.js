/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @description TimeConverter-Funktions
 */
(function($){
$.extend({
   /**
    * @description Convert TimeString to Minutes
    */
   timeToMinutes: function (time) {
        var t = (time+":0:0").split(":").slice(0,2);
        for(var i = 0; i < t.length; i++) {
            while(t[i].substring(0,1)=="0") t[i] = t[i].substring(1);
            t[i] = parseInt(t[i]) || 0;
        }
        return t[0] * 60 + t[1];
    },
   /**
    * @description Convert Minutes to TimeString
    */
    minutesToTime: function (minutes) {
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        if (h > 23 ) h-= 24;
        return (h<10?"0":"")+h+":"+(m<10?"0":"")+m;
    }
});
})(jQuery);

(function($) {
    var dataKey  = "fbDispoTimelineLineal";
    var defaults = {
              start: '06:00',
                end: '20:00',
          stepWidth: '00:30',
              width: '100%',
           minWidth: '',
           addClass: '',
          stepClass: '',
       stepClassAlt: '',
       
             _total: '',
            _startM: 0,
              _endM: 0,
            _totalM: 0,
        _stepWidthM: 0,
      _stepWidthPct: 0,
      
      'onCreate': null,
      'onRemove': null
       
           
    }
    
    var methods = {
        'timeToMinutes': function (time) {
            var t = (time+":0:0").split(":").slice(0,2);
            for(var i = 0; i < t.length; i++) {
                while(t[i].substring(0,1)=="0") t[i] = t[i].substring(1);
                t[i] = parseInt(t[i]) || 0;
            }
            return t[0] * 60 + t[1];
        },
        'minutesToTime': function(minutes) {
            var h = Math.floor(minutes / 60);
            var m = minutes % 60;
            if (h > 23 ) h-= 24;
            return (h<10?"0":"")+h+":"+(m<10?"0":"")+m;
        },
        'getDataKey': function() {
            return dataKey;
        },
        'getSettings': function() {
            //alert('getSettings');
            return $.extend({}, $(this).data(dataKey).settings);
        }
    }
    
    $.fn[dataKey] = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
//        alert('#86 ' + dataKey + ' ');
        
//        var d=options, i=null, m="#76 "+dataKey+" options\n";
//        for(i in d) m+=i+":"+d[i]+"\n"; alert(m);
        
        if (typeof(options) == "string" && this.length ) {
            switch(options) {
                case "destroy":
                    return this.each(function() {
                        this.html('');
                    });
                    break;
                case "data":
                    return this.first().data(dataKey);

                default:
                    if (typeof(methods[options]) == 'function' && options.substr(0,3) == 'get') {
                        return methods[options].apply( this[0], $.makeArray(args).slice(1));
                    }
            }
        }

        return this.each(function(index) {
//            alert('#109 ' + dataKey + ' this.each');
            if ( $(this).get(0).tagName.toUpperCase() !== "DIV") 
                throw "Als Container für Timeline sind nur Div-Tags erlaubt. Übergeben wurde "+$(this).get(0).tagName;
            
            // Default Routine: Setting options
            var presets = {};
            if (Fb.DispoCalendarSettings && Fb.DispoCalendarSettings.timelineLineal) {
                presets = $.extend({}, defaults, Fb.DispoCalendarSettings.timelineLineal);
            } else {
                presets = defaults;
            }
            
//            alert('#113 ' + dataKey + ' this.each');
            if (!$(this).data(dataKey)) {
                $(this).data(dataKey, { settings: $.extend({}, presets) });
                $(this).addClass(dataKey);
            }
//            alert('#118 ' + dataKey + ' this.each');
            
            var data = $(this).data(dataKey);
            data.settings = $.extend({}, data.settings, options);
            data.steps = new Array();
//            alert('#122 ' + dataKey + ' this.each');
            var s = data.settings;
            var timeSlotWidth = methods.timeToMinutes(s.stepWidth);
            var currentTime = methods.timeToMinutes(s.start);
            var endTime = methods.timeToMinutes(s.end);
            if (endTime < currentTime) endTime+= 24*60;
            
            if (!timeSlotWidth) {
                alert('Es wurde kein gültiges Interval (stepWidth) für die Zeitleiste definiert!');
                alert('#128 ' + dataKey + 
                    ' this.each endTime:'+
                    endTime+'; currentTime:'+
                    currentTime +' s.stepWidth:' + s.stepWidth + 
                    '; timeSlotWidth:' + timeSlotWidth );
                return;
            }
            
            do {
                data.steps.push( currentTime);
                currentTime+= timeSlotWidth;
            } while(currentTime < endTime);
            
//            alert('#134 ' + dataKey + ' this.each');
            s._startM = methods.timeToMinutes(s.start);
            s._endM = endTime;
            s._totalM = s._endM - s._startM;
            s._total = methods.minutesToTime( s._totalM );
            s._stepWidthM = methods.timeToMinutes(s.stepWidth);
            s._stepWidthPct = s._stepWidthM*100/s._totalM;
            
//            alert('#142 ' + dataKey + ' this.each');
            data.container = $("<table>").css({width:s.width,'min-width':s.minWidth }).append( $("<tr>") );
            if (s.addClass) data.container.addClass( s.addClass );
            $(this).html('').append( data.container );
            data.containerTr = $("tr:first", data.container);
            
            data.container = $("<div>").css({position:'relative',padding:0,border:0,overflow:'visible',height:'auto',width:s.width,'min-width':s.minWidth });
            if (s.addClass) data.container.addClass( s.addClass );
            $(this).html('').append( data.container );
            data.containerTr = data.container;
            
            
//            alert('#154 ' + dataKey + ' this.each');
            var stepWidth = Math.round( (data.container.width()/data.steps.length)*10)/10;
            var stepWidthPct = (stepWidth*100/data.container.width());
            //alert(stepWidth+"= ("+data.container.width()+"/"+data.steps.length+"*10)/10")
            var pctLeft = '';
            for(var i = 0; i < data.steps.length; ++i) {
                pctLeft = (i*stepWidthPct)+"%";
                data.containerTr.append( 
                    $("<div>")
                    .addClass('tl-step tl-step-'+(i%2?"odd":"even"))
                    .data("timeStep", {time:data.steps[i], left: pctLeft})
                    .css({
                        position:'absolute',
                        left: pctLeft,
                        width:stepWidthPct+"%",
                        overflow:'hidden',
                        display:'block',
                        margin:0,
                        padding:0,
                        border:0})
                    .text( methods.minutesToTime(data.steps[i])) );
            }
            
//            alert('#177 ' + dataKey + ' this.each');
            if (data.steps.length) data.containerTr.css({height: $("div.tl-step:first", data.containerTr).height()});
            if (s.stepClass) $( (s.stepClassAlt ? "div:even" :"div"), data.containerTr ).addClass(s.stepClass);
            if (s.stepClassAlt) $( "div:odd", data.containerTr ).addClass(s.stepClassAlt);
            
//            alert('#182 ' + dataKey + ' ');
        });
    };

})(jQuery);
