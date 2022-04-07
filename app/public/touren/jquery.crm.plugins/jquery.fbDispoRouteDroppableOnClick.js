/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($) {
    var dataKey = "fbDispoMkRouteDroppableOnClick"; // RouteDroppableOnClick
    $.fn.fbDispoRouteDroppableOnClick = function() {   // fbMkRouteDroppableOnClick
       
       var mkDroppable = function() {
           $("div.Drag-Route").removeClass("DragRoute-Focus");
           $(this).addClass("DragRoute-Focus");
           $("div.Drag-Route:not(.DragRoute-Focus)").droppable( "option", "accept", 'none' );
           //$("div.Drag-Route").droppable( "option", "accept", 'none' );
           $("div.Drag-Route.DragRoute-Focus").droppable( "option", "accept", '.Drag-Rsrc' );
           
           Fb.Dnd.droppable_route = $(this);
           //alert("Klick on Route!\nclass:"+$(this).attr('class')+"\nhtml: " + $(this).html() );
       }
       
       return this.each(function() {
           if (!$(this).data(dataKey)) $(this).data(dataKey, { init:false });
           var data = $(this).data(dataKey);
           if (data.init) return;
           
           //alert("Init " + dataKey + "!");
           $(this).bind("click", mkDroppable);
           data.init = true;
       });
    }
})(jQuery);