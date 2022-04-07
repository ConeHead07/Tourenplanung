if (typeof(Fb)=="undefined") var Fb = {};

(function($) {
    var defaults = {
        pages : { 
            Resources: 'Ressourcen',
            //           Positionen: 'Positionen',
            Gruppierungen: 'Gruppierungen',
            Details: 'Daten',
            Timetable: 'Zeitplan',
            Touren: 'Touren',
            Bemerkungen: 'Bemerkungen',
            Historie: 'Historie',
            Abschluss: 'Abschluss'
        },
        close: '<span class="ui-state-error" style="background:none;border:0;">Schliessen <span class="ui-icon ui-icon-closethick" style="height:13px;"></span></span>'
    };
   
    Fb.hideRouteDetails = function(obj) {
        var $obj = $(obj);
        $obj.slideUp('normal', function() {
//           alert( typeof(obj) );
           if ( $obj.length ) $obj.remove();
       });
       
    }
    
    Fb.showRouteDetails = function(obj, opts) {
       
       var $tour = $(obj);
       //var $portlet = $tour.closest("div.portlet-content");
       var $timeline = $tour.closest('div.DropZone.DropZone-Route');
       
       if (typeof(opts) == "undefined" || opts == null) opts = {};
       var o = $.extend({}, defaults, opts);
       
       var toggle = false;
       $( "div.TourTabbedSheet").each( function() {
          if ( $(this).data('BelongToTour') == $tour.get(0)) {
              toggle = true;
          }
          Fb.hideRouteDetails( this );
       });
       if (toggle) {
           $( obj ).fbDispoRoute('_trigger', obj, 'hideDetails');
//           $( obj ).trigger('hideDetails');
           return;
       }
       
       if (!o.pages.close) o.pages.close = o.close;
       
       
       
       var hd = $( "<div class='TourTabbedHead' style='padding-left:10px'>Tour-Basis-Daten</div>");
       var ul = $( "<ul>");
       var tabs = $( "<div class='TourTabbedSheet tabs-flat' style='display:none;min-height:400px;'/>" );
       tabs.append( ul );
       tabs.append( hd );
       tabs.data('BelongToTour', $tour.get(0));
       var pageName = '';
        
       for(var i in o.pages) {
           pageName = "tabs-"+i;
           ul.append( $( "<li><a href='#"+pageName+"' id='Anchor-"+pageName+"'>"+o.pages[i]+"</a></li>"));
           tabs.append( $( "<div id='"+pageName+"'><p></p></div>") );
       }
        
       $timeline.after( tabs.tabs({
           create: function() { // event, ui
               $(this).tabs('select', 0);
               $("li a:contains('Schliessen')", $(this) ).bind("click", function() {
                   Fb.showRouteDetails( obj );
               })
           },
           show: function() { // event, ui
               var selectedIndex = $(this).tabs( "option", "selected" );
               var selectedTitle = $.trim( $("li:eq("+selectedIndex+")", this).text() );
               switch( selectedTitle ) {
                   case "Schliessen":
                       break;
               } 
           }
       }) );
       tabs.slideDown('normal');
//       $( obj ).fbDispoRoute('_trigger', obj, 'showDetails', tabs);
       $( obj ).trigger('showDetails', [tabs]);
            
    };
})(jQuery);
