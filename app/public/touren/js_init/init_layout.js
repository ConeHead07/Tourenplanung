/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

	function customSaveState (Instance, state, options, name) {
		console.log('#7 customSaveState!');
		// get the state and save it in a database...
		var state_JSON = Instance.readState();
		// use Layout utilities to stringify the JSON - IF NECESSARY
		var state_String = Instance.encodeJSON( state_JSON );
		// now can save the data in the database string field
	};

	function customLoadState (Instance, state, options, name) {
		console.log('#16 customLoadState!');
                /* if state loaded as a 'string', convert back to a hash
		var savedState_String = "{ west: { size: 200 } }";
		var savedState_JSON = Instance.decodeJSON( state_JSON );
		Instance.loadState( savedState_JSON, true ); // true = animate open/close/resize
		*/

		// create a custom 'load state' - this could come from the server
		var savedState_JSON = {
			west: {
				initClosed:	false
			,	size:		350
			}
		,	north: {
				initClosed:	false
                               ,size:           'auto'
			}
		}

		// load the custom state
		Instance.loadState( savedState_JSON, false ); // false = DO NOT animate open/close/resize (default)
	};

	function toggleAll () {
		$.each(["north","west","east","south"], function(i, pane){
			pageLayout.toggle( pane );
		});
	};
        
var pageLayout;
var state;

(function($){
    $(document).ready(function(){
        $(".ui-layout-west:first").tabs({
        });
        // create page layout
        pageLayout = $('body').layout({
            name: 'tourenLayout'
//            ,useStateCookies: true
//            ,cookie__autoSave: true
//            ,stateManagement__enabled:	true // enable stateManagement - automatic cookie load & save enabled by default
////	    ,stateManagement__stateKeys:	"west.size,east.size,west.isClosed,east.isClosed"
//	    ,stateManagement__stateKeys:	"west.size,north.size,west.isClosed,east.isClosed"		// state-keys in sub-key format
//	    ,onload:	customLoadState // run custom state-code when Layout loads
//	    ,onunload:	customSaveState // ditto when page unloads OR Layout is 'destroyed'
            ,scrollToBookmarkOnLoad:     false // handled by custom code so can 'unhide' section first
            ,defaults: {
            }
            
            ,north: {
                size:			"auto"
                //,	spacing_open:		0
                //,	closable:		false
                //,	resizable:		false
            }
            ,west: {
                size:			parseInt($.cookie('layoutWestWidth')) ? parseInt($.cookie('layoutWestWidth')) : 350
                ,isClosed:              parseInt($.cookie('layoutWestIsClosed')) ? parseInt($.cookie('IsClosed')) : 0
                ,spacing_closed:        22
                ,togglerLength_closed:	140
                ,togglerAlign_closed:	"top"
                ,togglerContent_closed:	"V<BR>o<BR>r<BR>g<BR>&auml;<BR>n<BR>g<BR>e"
                ,togglerTip_closed:	"Open & Pin Contents"
                ,sliderTip:		"Slide Open Contents"
                ,slideTrigger_open:	"mouseover"
                ,onhide_end: function() {
                    // Doesn't work right now!!!
                    $.cookie('layoutWestIsClosed', 1); 
                }
                ,onshow_end: function() {
                    // Doesn't work right now!!!
                    $.cookie('layoutWestIsClosed', 0); 
                }
                ,onresize_end: function() {
                    
                    var newWidth = parseInt( $( "div.sFormBox:visible" ).outerWidth() );
                    $.cookie('layoutWestWidth', newWidth); 
                    
                    // alert ( 'newWidth: ' + newWidth );
//                    alert('onresize_end ! newWidth:' + newWidth);
                    $( '#gridDispoVorgaengeLst'  ).jqGrid( 'setGridWidth', newWidth );
                    $( '#gridDispoResourceFPLst' ).jqGrid( 'setGridWidth', newWidth );
                    $( '#gridDispoResourceMALst' ).jqGrid( 'setGridWidth', newWidth );
                    $( '#gridDispoResourceWZLst' ).jqGrid( 'setGridWidth', newWidth );
                    
                    $( 'table.subgrid', '#gridDispoVorgaengeLst' ).each(function() {
                        var gridId = $(this).attr('id');
                        var gridParentWidth = parseInt( $('#gbox_' + gridId).parent().width() );
                        if (gridParentWidth > 5) gridParentWidth-= 2;
                        $('#' + gridId).setGridWidth(gridParentWidth);
                    })
                }
            }
        });
    });
    


})(jQuery);
