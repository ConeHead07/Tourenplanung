// jQuery Autocomplete fuer fixe multiple Auswahlwerte ohne Freitexteingabe

jqChooser = {};
(function() {
    var searchInput;
    var cacheValues = {};
    var hasFocus = true;
    var timer = false;
    var inputObj = null;
    var source = null;
    var sourceLoaded = false;
    var sourceLoadedCallback = null;
    
    var addCache = function(value, label) {
        if (typeof(cacheValues[value]) != "undefined") return;
        cacheValues[value] = label;
    }
    jqChooser = {
        
        split: function( val ) {
            return val.split( /,\s*/ );
        },
        extractLast: function ( term ) {
            return jqChooser.split( term ).pop();	
        },
        destroy: function(inputSearchObj) {
            // Weiche, um auf beide InputFelder reagieren zu können
            // - entweder auf Original-Input-Feld
            // - oder das jqChooser-Input-Feld
            if ($(inputSearchObj).data("jqChooserInput" )) {
                var originInput = inputSearchObj;
                inputSearchObj = $(inputSearchObj).data("jqChooserInput" );
            } else {
                var originInput = $(inputSearchObj).data("originInput");
            }
            var $div = $(inputSearchObj).data("div");
            var originInputDisplay = $(inputSearchObj).data("originInputDisplay");
            //jQuery(originInput).removeAttr("jqAutocompleteInit");
            $div.remove();
            $(originInput).css("display", originInputDisplay);
            jqChooser.hideUserBox();
            jqChooser.unbindUserBox();
            searchInput = null;
            hasFocus = false;
            timer = null;
        },
        create: function(originInput) {
            var chznContainer = $(originInput).parent().find("div.chzn-container.chzn-container-multi");
            if (chznContainer.length) {
                chznContainer.remove();
            }
            var $div = $("<div/>").attr("class", "chzn-container chzn-container-multi"); //.css("border", "1px solid #00f");
            var $ul = $("<ul/>").attr("class", "chzn-choices");
            var $li = $("<li/>").attr("class", "search-field");
            var $inputSearchObj = $("<input/>")
                                  .attr({type:"text", autocomplete: "off"}).css("width", "25px;")
                                  .data({"div": $div, "ul": $ul, "originInput": originInput, 
                                   "originInputDisplay":$(originInput).css("display")});
            
            $(originInput).data("jqChooserInput", $inputSearchObj);

            $div.append( $ul.append( $li.append(  $inputSearchObj ) ) );

            // Urspruenglich wurde der Chooser-Helper vor das jqGrid-Input-Feld in den DOM
            // eingefuegt, allerdings holt sich jqGrid dann beim Speichern den Wert aus dem
            // falschen Input, da er offenbar nicht an hand der Id das Input-Feld sucht, 
            // sondern sich einfach das erstplazierte Input-Element schnappt !!!            
            $(originInput).after( $div );
            $(originInput).css("display", "none");
            
            
            jqChooser.initChItems( $inputSearchObj );
            
            jqChooser.addButton( $inputSearchObj);
            
            searchInput = $inputSearchObj ;
            return $inputSearchObj;
        },
        initChItems: function(inputSearchObj) {
            var originInput = $(inputSearchObj).data("originInput");
            var $ul = $(inputSearchObj).data("ul");

            /** Liste leeren */
            $ul.remove("li.chzn-choice");
            
            /** Liste mit aktuellen Werten füllen */
            jQuery.each($( originInput ).val().split(","), function(index, addValue) {
                if (addValue) jqChooser.addItem(inputSearchObj, addValue);
            });
        },
        remValue: function(inputSearchObj, removeObj) {
            var originInput = $(inputSearchObj).data("originInput");
            var $ul = $(inputSearchObj).data("ul");

            // chItem entfernen
            removeObj.remove();

            var chItems = $("li.search-choice:not(.search-button)",  $ul );

            // Alle noch bestehenden chItems auslesen und Input-Feld aktualisieren
            var chDaten = "";
            for (var j = 0; j < chItems.length; ++j) {
                if (typeof(chItems.eq(j).data("choice"))=="string" && chItems.eq(j).data("choice").length) 
                    chDaten+= (chDaten?",":"")+chItems.eq(j).data("choice");
            }

            // Input-Feld aktualisieren
            $(originInput).val(chDaten);
            jqChooser.refreshUserBox(inputSearchObj);
        },
        /**
         * Create and add the ui-element
         * @param inputObject as jqSelect
         */
        addItem: function(inputSearchObj, addValue, addLabel) {
            var $ul = $(inputSearchObj).data("ul");
            if ( !$( $ul ).length ) return;

            var newChItem = $("<li/>")
                .attr("class", "search-choice")
                .data("choice", addValue)
                .append( $("<span/>").attr({"rel-value":addValue,"rel-label":addLabel}).text(addLabel || addValue) )
                .append( 
                    $("<a/>")
                    .attr({"class": "search-choice-close", href:"javascript:void(0)"})
                    .bind("click",   function(event) {jqChooser.remValue(inputSearchObj, $(this).parent("li:first"));})
                );

            if ( $("li.search-field", $ul).length) {
                $("li.search-field:first", $ul).before( newChItem );
            } else {
                $ul.append( newChItem );
            }
            return newChItem;
        },
        addButton: function(inputSearchObj) {
            var $ul = $(inputSearchObj).data("ul");
            if ( !$( $ul ).length ) return;
            var newChItem = $("<li/>")
                .attr("class", "search-choice search-button")
                .css({padding:"1px 0"})
                .data("choice", "")
                .append( 
                    $("<a/>")
                    .attr({"class": "search-choice-open ui-icon ui-icon-plus", href:"javascript:void(0)"})
                    .bind("click",   function(event) { jqChooser.showUserBox() ;})
                );
            
            if ( $("li.search-field:first", $ul).length) {
                $("li.search-field:first", $ul).after( newChItem );
            } else {
                $ul.append( newChItem );
            }
            return newChItem;
        },
        /**
         * @param object|value
         * @param string label optional, if data is not an object, then th obj have to a label-Property
         * @return jQuery-Object addedItem
         */
        addValue: function(inputSearchObj, data, addLabel, init) {
            
            var originInput = $(inputSearchObj).data("originInput");
            //if (0) alert( "addValue\noriginInput.id: " + $(originInput).attr("id") + "\ninputSearchObj.id: " + $( inputSearchObj ).attr("id"));

            if (typeof(data) =='object') {
                addValue = data.value;
                addLabel = data.label || addValue;
            } else {
                addValue = data;
                if (!addLabel) addLabel = data;
            }

            // chItem hinzufügen
            var newChItem = jqChooser.addItem(inputSearchObj, addValue, addLabel);
            addCache(addValue, addLabel); // Protected Method
            
            jqChooser.refreshUserBox(inputSearchObj);

            // Neuen Wert dem Input-Feld hinzufügen
            if (!init) $(originInput).val( ($(originInput).val() ? $(originInput).val()+",":"") + addValue );

            // Search-Field leeren und Focus setzen
            $(inputSearchObj).val("");
            $(inputSearchObj).focus();
            return newChItem;
        },
        getData: function(inputSearchObj) {
            var $ul = $(inputSearchObj).data("ul");
            var data = [];
            $("li.search-choice:not(.search-button)>span",  $ul ).each( function(index) {
                data.push( {value: $(this).attr("rel-value"), label: $(this).attr("rel-label")} );
            });
            return data;
        },
        getCacheData: function() {
            return cacheValues;
        },
        clearData: function(inputSearchObj) {
            $( $(inputSearchObj).data("ul") ).find('li.search-choice:not(.search-button):has(span)').remove();
            $( $(inputSearchObj).data("originInput") ).val('');
            
            var originInput = $(inputSearchObj).data("originInput");
            var $ul = $(inputSearchObj).data("ul");
        },
        setData: function(inputSearchObj, data) {
            jqChooser.clearData(inputSearchObj);
            if (data) for(var i = 0; i < data.length; ++i) {
                jqChooser.addValue( inputSearchObj, data[i] );
            }
        },
        setFocus: function(stat) { 
//            if (0) alert( "setFocus("+(stat?'true':'false'));
            hasFocus = stat;
            if (!stat) setTimeout( "jqChooser.checkHide()", 1000);
        },
        checkHide: function() {
            if (!hasFocus) jqChooser.hideUserBox();
            //try { clearTimeout(timer); } catch(e) { /* Nothing */ }
        },
        refreshUserBox: function(inputSearchObj) {
            var $UserBox = $("div#jqChooserUserBox");
            // Pre-Condition: Does a UserBox exist?
            if (!$UserBox.length) return false;

            var $target = ($( inputSearchObj ).is(':visible') ) ? $( inputSearchObj ) : $( inputSearchObj ).closest("td");
            $target.css({height: 'auto', display:'inherit'});
            var $ul = $("ul .ui-icon-plus", $target);
            if ( $ul.length ) {
                $target = $ul;
            }
            
            if (!$target.length) return;
            $target.css({border:'solid 1px #f00'});
            
            var t = $target.offset()["top"] + $target.outerHeight();
            var l = $target.offset()["left"];
            var w = $target.outerWidth();
            
            $UserBox.css({position:"absolute",top:t,left:l});
            
            var thisZIndex = $UserBox.css("zIndex"),
                maxZIndex = thisZIndex;
            $UserBox.parents().each(function() {
                maxZIndex = Math.max(maxZIndex, $(this).css("zIndex"));
            });
            if (thisZIndex != maxZIndex) $UserBox.css("zIndex", 1+maxZIndex);

            return true;
        },
        bindUserBox: function(inputSearchObj) {
            $cell = $(inputSearchObj).closest( ":visible" );
            $( "div#jqChooserUserBox" ).bind( 'mouseleave', function(){jqChooser.setFocus(false)} );
            $cell.bind( 'mouseleave', function(){jqChooser.setFocus(false)} );
            $(inputSearchObj).bind( 'blur', function(){jqChooser.setFocus(false)} );
            
            $( "div#jqChooserUserBox" ).bind( 'mouseenter', function(){jqChooser.setFocus(true)} );
            
            //$cell.bind( 'mouseenter', function(){jqChooser.setFocus(true)} );
            $(inputSearchObj).bind( 'focus', function(){jqChooser.setFocus(true)} );
        },
        unbindUserBox: function(inputSearchObj) {
            $cell = $(inputSearchObj).closest( "td" );
            $( "div#jqChooserUserBox" ).unbind( 'mouseleave' );
            $cell.unbind( 'mouseleave' );
            $(inputSearchObj).unbind( 'blur' );
            
            $( "div#jqChooserUserBox" ).unbind( 'mouseenter' );
            $cell.unbind( 'mouseenter', function(){jqChooser.setFocus(true)} );
            $(inputSearchObj).unbind( 'focus' );
        },
        showUserBox: function(e) {
            var $UserBox = $("div#jqChooserUserBox");
            hasFocus = true;
            if (!$UserBox.length) return;
            if (!sourceLoaded)

            jqChooser.refreshUserBox(searchInput);
            $UserBox.show();
        },
        hideUserBox: function() {
            $("div#jqChooserUserBox").hide();
            $cell = $(searchInput).closest( "td" );
            //$cell.one( 'mouseenter', function() {jqChooser.showUserBox();} );
        },
        loadUserBoxSource: function(source) {
            sourceLoaded = true;
            $("div#jqChooserUserBox").load(source, function() { 
                if (sourceLoadedCallback) callback(sourceLoadedCallback);
                jqChooser.refreshUserBox(searchInput);
            } );  
        },
        registerUserBox: function(obj, source, callback) {
            var chooserInput = jqChooser.create(obj);
            var $input = jQuery( chooserInput );
            cacheValues = {};            

            if (!$("div#jqChooserUserBox").length) $("body").append(
                $("<div id='jqChooserUserBox'/>").css({display:"none",zIndex:999}) // height:"200px",
            );

            $("div#jqChooserUserBox").load(source, function() { 
                if (callback) callback($input);
                jqChooser.refreshUserBox($input);
            } );
            
            searchInput = $input;
            inputObj = obj;
            sourceLoadedCallback = callback;
            sourceLoaded = false;
            jqChooser.bindUserBox(chooserInput);
        },
        unregisterUserBox: function(input) {
            $("div#jqChooserUserBox").remove();
            jqChooser.destroy(input);
        },
        getSearchInputByOriginInput: function(originInput) {
            return $(originInput).data("jqChooserInput");
        },
        register: function (obj, source, callback) {
    //        if (0) alert("#102 jquery.chooser.js jqChooser.register(): source:" + source);
            if (jQuery(obj).attr("jqAutocompleteInit")) return;

            var chooserObj = jqChooser.create(obj);
            //jQuery( obj )
            jQuery( chooserObj )
            // don't navigate away from the field on tab when selecting an item
            .bind( "keydown", function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( "autocomplete" ).menu.active ) {
                        event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
    //                if (0) alert("source:" + source);
                    jQuery.getJSON( source, {
                        term: jqChooser.extractLast( request.term )
                    }, response );
                },
                minLength: 0,
                search: function() {
    //                if (0) alert( "search" );
                    // custom minLength
                    var term = jqChooser.extractLast( this.value );
                    var minLength = $( this ).autocomplete('option', 'minLength');
                    return ( term.length >= minLength );
                },
                focus: function() {
    //                if (0) alert("focus");
                    // prevent value inserted on focus
                    return false;
                },
                select: function( event, ui ) {
    //                if (0) alert("select");
                    var terms = jqChooser.split( this.value );
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push( ui.item.value );
                    var button = $("<button></button>").text( ui.item.label || ui.item.value ).data("dstInput", this);
                    button.button({
                        icons: {
                            secondary: "ui-icon-locked"
                        }
                    });
                    jqChooser.addValue(this, ui.item );
                    return false;

                    // add placeholder to get the comma-and-space at the end
                    terms.push( "" );
                    this.value = terms.join( ", " );
                    if (callback && typeof(callback)=='function')
                        callback.apply( this, event, ui.item);
                    return false;
                }
            });
            jQuery(obj).attr("jqAutocompleteInit", "1");
            searchInput = $input;
            return jQuery( chooserObj );
            //if (0) alert("#86 register_jq_autocomplete fuer "+obj.attr("name")+"jQuery(obj).attr(name):"+jQuery(obj).attr("name"));
        }
    }

})();