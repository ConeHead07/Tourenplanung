/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var pageLayout;

function customSaveState (Instance, state, options, name) {
        // get the state and save it in a database...
        var state_JSON = Instance.readState();
        // use Layout utilities to stringify the JSON - IF NECESSARY
        var state_String = Instance.encodeJSON( state_JSON );
        // now can save the data in the database string field
}

function customLoadState (Instance, state, options, name) {
        /* if state loaded as a 'string', convert back to a hash
        var savedState_String = "{ west: { size: 200 } }";
        var savedState_JSON = Instance.decodeJSON( state_JSON );
        Instance.loadState( savedState_JSON, true ); // true = animate open/close/resize
        */

        // create a custom 'load state' - this could come from the server
        var savedState_JSON = {
                east: {
                        initClosed:	false
                ,	size:		350
                }
        ,	south: {
                        initClosed:	false
                }
        }

        // load the custom state
        Instance.loadState( savedState_JSON, false ); // false = DO NOT animate open/close/resize (default)
}

function toggleAll () {
        $.each(["north","west","east","south"], function(i, pane){
                pageLayout.toggle( pane );
        });
}

function setLayoutPaneWidth( pane, width) {
    if (!pageLayout) return;
    
}


(function($){
    $(document).ready(function(){
        // create page layout
        pageLayout = $('body').layout({
             stateManagement__enabled:	true   // enable stateManagement - automatic cookie load & save enabled by default
	    ,stateManagement__stateKeys:	"west.size,east.size,west.isClosed,east.isClosed"
//	    ,stateManagement__stateKeys:	"west.size,north.size"		// state-keys in sub-key format
	    ,stateManagement__stateKeys:	"west__size,north__size"	// state-keys in flat-format
	    ,onload:	customLoadState        // run custom state-code when Layout loads
	    ,onunload:	customSaveState        // dito when page unloads OR Layout is 'destroyed'
            ,scrollToBookmarkOnLoad:     false // handled by custom code so can 'unhide' section first
            ,defaults: {
            }
            ,north: {
                size:			"auto"
                //,	spacing_open:		0
                ,	closable:		true
                ,	resizable:		false
            }
            ,west: {
                size:			parseInt($.cookie('layout2WestWidth')) ? parseInt($.cookie('layout2WestWidth')) : 350
               ,resizable:		true
               ,initClosed:             true
//                ,spacing_closed:        22
//                ,togglerLength_closed:	140
//                ,togglerAlign_closed:	"top"
//                ,togglerContent_closed:	"C<BR>o<BR>n<BR>t<BR>e<BR>n<BR>t<BR>s"
//                ,togglerTip_closed:	"Open & Pin Contents"
//                ,sliderTip:		"Slide Open Contents"
//                ,slideTrigger_open:	"mouseover"
                ,onresize_end: function() {
                    try {
                        var args = arguments;
//                        alert( "#83 args.length: " + args.length );
                        $("body").trigger( "west_resize_end", args[0], args[1], args[2], args[3], args[4]  );
                    } catch(e) {
                        alert( e );
                    }
                    $.cookie('layout2WestWidth', pageLayout.state.west.size);
                }
                ,onhide_end: function() {
                    // Doesn't work right now!!!
                    alert('#92 init_layout.js onhide_end');
                    $.cookie('layout2WestIsClosed', 1); 
                }
                ,onshow_end: function() {
                    // Doesn't work right now!!!
                    alert('#92 init_layout.js onshow_end');
                    $.cookie('layout2WestIsClosed', 0); 
                }
            }
        });
    });
})(jQuery);
