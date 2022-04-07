(function($) {
    var dataKey  = 'fbChooser';
    var getNextID = (function() {
        var ID = 0;
        return function() {
            return ++ID;
        };
    })();
        
    var defaults = {
        'onCreate': null,
        'onRemove': null,
        'onselect': null,
        'originInputDisplay':'',
        'searchInput': null,
        'dataPairs': null,
        // Achtung object funktioniert nicht, wird offenbar nicht als Referenz gebunden !!!
        'dataPairsUserStorage': null, // function | {object}| attr-string | attr-json | data-json
        'cacheValues': null,
        'hasFocus': null,
        'timer': null,
        'inputObj': null,
        'multiple': true,
        'source': null,
        'sourceLoaded': null,
        'sourceLoadedCallback': null,
        'showSearchInput': false,
        'initialized': false,
        'showOnload': false,
        'useUserBox': false,
        'userBox': null,
        'userBoxId': 'fbChooserUserBox'
    };
    
    /**
     * Register Methods are called with apply, so it work as they
     * would be method of the object.
     * Note:
     * - Function beginning with underscore like '_init' should not called external
     *   but the access isn't protected by now
     * - Functions beginning with 'get' should return an value.
     *   That's why only the method of the first object in the jQuery-Selection-Range
     *   will called
     */
    var methods = {
       addCache: function(value, label) {
            var data = $(this).data(dataKey);
            if (data.cacheValues == null) data.cacheValues = new Array();
            if (typeof(data.cacheValues[value]) != "undefined") return;
            data.cacheValues[value] = label;
       },
       '_init': function() {           
           var data = $(this).data(dataKey);
           if (data.initialized) return;
           
           if (data && typeof(data.onCreate) == 'function') {
               data.onCreate.apply(this);
           }
           
           if (data.useUserBox) {
               //alert( '#57 '+dataKey+'._init data.userBoxId:'+data.userBoxId);
//               if (0) alert('#50 data.userBox:'+data.userBox+'; data.source:'+data.source);
               methods.registerUserBox.apply(this);
           } else {
               methods.register.apply(this, [data.source, data.sourceLoadedCallback]);
           }
           //alert('#55 '+dataKey+'._init ' + '\ntypeof(data.ul):'+typeof(data.ul));
           if (data.onCreate) data.onCreate.apply(this);
           data.initialized = true;
           
       },        
        split: function( val ) {
            return val.split( /,\s*/ );
        },
        extractLast: function ( term ) {
            return methods.split( term ).pop();	
        },
        destroy: function() {
            var data = $(this).data(dataKey);
            
            //jQuery(originInput).removeAttr("jqAutocompleteInit");
            data.div.remove();
            $( this ).css("display", data.originInputDisplay);
            if (data.useUserBox) methods.unregisterUserBox.apply(this);
            data.searchInput = null;
            data.hasFocus = false;
            data.timer = null;
            $( this ).removeData(dataKey);
            if (data.onRemove) data.onRemove.apply(this);
        },
        create: function() {
            var data = $(this).data(dataKey);
            
            data.cell = $(this).parent();
            data.div = $("<div/>").attr("class", "chzn-container chzn-container-multi"); //.css("border", "1px solid #00f");
            data.ul = $("<ul/>").attr("class", "chzn-choices");
            data.li = $("<li/>").attr("class", "search-field");
            data.searchInput = $("<input/>")
                               .attr({type:"text", autocomplete: "off"}).css("width", "25px;");
            
            data.originInputDisplay = $( this ).css("display");

            data.div.append( data.ul.append( data.li.append( data.searchInput ) ) );
            
            // Urspruenglich wurde der Chooser-Helper vor das jqGrid-Input-Feld in den DOM
            // eingefuegt, allerdings holt sich jqGrid dann beim Speichern den Wert aus dem
            // falschen Input, da er offenbar nicht an hand der Id das Input-Feld sucht, 
            // sondern sich einfach das erstplazierte Input-Element schnappt !!!
            $( this ).after( data.div );
            $( this ).css("display", "none");
            
            if (data.showSearchInput == false) data.searchInput.hide();
            methods.initChItems.apply( this );
            
            if (data.useUserBox) methods.addButton.apply( this );
            
            return data.searchInput;
        },
        initChItems: function() {
            var data = $(this).data(dataKey);
            var self = this;
            
            /** Liste leeren */
            data.ul.remove("li.chzn-choice");
            
            if (!data.dataPairsUserStorage) {
                // Auto-Detect dataPairsUserStorage
                if ($(this).attr('data-pairs')) {
                    if ( typeof($(this).attr('data-pairs')) == "object") {
                        data.dataPairsUserStorage = 'attr-json';
                    } else {
                        data.dataPairsUserStorage = 'attr-string';
                    }
                } else {
                    data.dataPairsUserStorage = 'data-json';
                }
            }
            
            if (typeof(data.dataPairsUserStorage) == "object") {
                data.dataPairs = data.dataPairsUserStorage;
            }
            if (typeof(data.dataPairsUserStorage) == "function") {
                data.dataPairs = data.dataPairsUserStorage.apply(this, []);
            }
            else if (data.dataPairsUserStorage == 'data-json') {
                data.dataPairs = $(this).data('data-pairs');
            }
            else if ($(this).attr('data-pairs')) {
                if ( data.dataPairsUserStorage == 'attr-json') {
                    data.dataPairs = $(this).attr('data-pairs');
                } else {
                    try {
                        data.dataPairs = jQuery.parseJSON($(this).attr('data-pairs'));
                    } catch(e) {
                        alert(e);
                    }
                }
            }        
            
            /** Liste mit aktuellen Werten füllen */
            if ( $(this).val())
            $.each($( this ).val().split(","), function(index, addValue) {
                if (!addValue) return;
                var addLabel = (data.dataPairs && addValue in data.dataPairs) ? data.dataPairs[addValue] : null;
                if (!addLabel && data.cacheValues && addValue in data.cacheValues) addLabel = data.cacheValues[addValue];
                methods.addItem.apply(self, [addValue, addLabel]);
            });
        },
        remValue: function(removeObj) {
            var data = $(this).data(dataKey);

            // chItem entfernen
            removeObj.remove();

            var chItems = $("li.search-choice",  data.ul );

            // Alle noch bestehenden chItems auslesen und Input-Feld aktualisieren
            var chDaten = "";
            var newDataPairs = {};
            for (var j = 0; j < chItems.length; ++j) {
                if (typeof(chItems.eq(j).data("choice"))=="string" && chItems.eq(j).data("choice").length) {
                    var c = chItems.eq(j).data("choice")
                    chDaten+= (chDaten?",":"")+c;
                    
                    newDataPairs[c] = (c in data.dataPairs) ? data.dataPairs[c] : c;
                }
            }
            data.dataPairs = $.extend({}, newDataPairs);
            methods.refreshDataPairsUserStorage.apply( this );

            // Input-Feld aktualisieren
            $( this ).val(chDaten);
            methods.refreshUserBox.apply( this );
        },
        /**
         * Create and add the ui-element
         * @param addValue string|number addValue
         * @param addLabel string addLabel
         */
        addItem: function(addValue, addLabel) {
            var data = $( this ).data(dataKey);
            
            var self = this;
            //alert('#137 addItem '+addValue+' = ' + addLabel + '\ntypeof(data.ul):'+typeof(data.ul));
            if ( !$( data.ul ).length ) return null;
            //if ( !data.multiple) methods.clearData.apply(self);
            
            var newChItem = $("<li/>")
                .attr("class", "search-choice")
                .data("choice", addValue)
                .append( $("<span/>").attr({"rel-value":addValue,"rel-label":addLabel}).text(addLabel || addValue) )
                .append( 
                    $("<a/>")
                    .attr({"class": "search-choice-close", href:"javascript:void(0)"})
                    .bind("click",   function(event) {
                        methods.remValue.apply(self, [$(this).parent("li:first")] );
                    })
                );
                    
            if ( $("li.search-field", data.ul).length) {
                $("li.search-field:first", data.ul).before( newChItem );
            } else {
                data.ul.append( newChItem );
            }
            if (data.showSearchInput == false) data.searchInput.hide();
            
            methods.refreshUserBox.apply( this );
            return newChItem;
        },
        /**
         * @param addData object|value
         * @param addLabel string label optional, if data is not an object, then th obj have to a label-Property
         * @param init bool
         * @return jQuery-Object addedItem
         */
        addValue: function(addData, addLabel, init) {        
            if (typeof(init) == "undefined" || init == null) init = 0;
            if (typeof(addLabel) == "undefined" || addLabel == null) addLabel = '';
            var data = $(this).data(dataKey), 
                addValue='';
                
            if (typeof(addData) =='object') {
                addValue = addData.value;
                addLabel = addData.label || addValue;
            } else {
                addValue = addData;
                if (!addLabel) addLabel = addData;
            }
            //alert('#177 addValue '+addValue+' = ' + addLabel + '\ntypeof(data.ul):'+typeof(data.ul));
            
            if (data && !data.multiple) methods.clearData.apply( this );
            
            // chItem hinzufügen
            var newChItem = methods.addItem.apply(this, [addValue, addLabel]);
            methods.addCache.apply(this, [addValue, addLabel]); // Protected Method
            
            methods.refreshUserBox.apply( this );

            // Neuen Wert dem Input-Feld hinzufügen
            if (!init) {
                var newVal = $(this).val() + ($(this).val() ? ',' : '') + addValue;
                $( this ).val( newVal );
                
                data.dataPairs[addValue] = addLabel;
                methods.refreshDataPairsUserStorage.apply( this );
            }

            // Search-Field leeren und Focus setzen
            $(data.searchInput).val("");
            $(data.searchInput).focus();
            return newChItem;
        },
        addButton: function(inputSearchObj) {
            var data = $( this ).data(dataKey);
            var self = this;
            //alert('#137 addItem '+addValue+' = ' + addLabel + '\ntypeof(data.ul):'+typeof(data.ul));
            if ( !$( data.ul ).length ) return null;
            
            if (!data.ul || !data.ul.length ) return;
            var newChItem = $("<li/>")
                .attr("class", "search-choice search-button")
                .css({padding:"1px 0"})
                .data("choice", "")
                .append( 
                    $("<a/>")
                    .attr({"class": "search-choice-open ui-icon ui-icon-plus", href:"javascript:void(0)"})
                    .bind("click",   function(event) { 
                        methods.showUserBox.apply(self );
                    })
                );
            
            if ( $("li.search-field:first", data.ul).length) {
                $("li.search-field:first", data.ul).after( newChItem );
            } else {
                data.ul.append( newChItem );
            }
            return newChItem;
        },
        getData: function() {
            var data = $( this ).data( dataKey);
            var re = [];
            
            $("li.search-choice>span",  data.ul ).each( function(index) {
                re.push( {value: $(this).attr("rel-value"), label: $(this).attr("rel-label")} );
            });
            return re;
        },
        getCacheData: function() {
            var data = $( this ).data( dataKey);
            return data.cacheValues;
        },
        clearData: function() {
            var data = $( this ).data( dataKey);
            $( data.ul ).find('li.search-choice:has(span)').remove();
            $( this ).val(''); 
            
            data.dataPairs = {};
            methods.refreshDataPairsUserStorage.apply( this );
        },
        setData: function(setData) {
            var data = $(this).data(dataKey);
            var self = this;
            //alert('#213 ' + setData[0].value + ' = ' + setData[0].label + '\ntypeof(data.ul):'+typeof(data.ul));
            
            methods.clearData.apply( this );
            if (setData) for(var i = 0; i < setData.length; ++i) {
                methods.addValue.apply(self, [setData[i]] );
            }
        },
        setFocus: function(stat) { 
            var data = $( this ).data( dataKey);
            var self = this;
            
            data.hasFocus = stat;
            if (!stat) setTimeout( function(){methods.checkHide.apply( self )}, 500);
        },
        checkHide: function() {
            var data = $( this ).data( dataKey);
            if (!data.hasFocus) methods.hideUserBox.apply( this );
        },
        refreshDataPairsUserStorage: function() {
            var data = $( this ).data( dataKey);
            if (typeof(data.dataPairsUserStorage) == "object")   {
                data.dataPairsUserStorage = data.dataPairs;
            }
            else if (typeof(data.dataPairsUserStorage) == "function")   {
                data.dataPairsUserStorage.apply(this, [data.dataPairs]);
            }
            else if (data.dataPairsUserStorage == 'data-json')   $(this).data('data-pairs', data.dataPairs);
            else if (data.dataPairsUserStorage == 'attr-json')   $(this).attr('data-pairs', data.dataPairs);
            else if (data.dataPairsUserStorage == 'attr-string') $(this).attr('data-pairs', JSON.stringify(data.dataPairs));
        },
        refreshUserBox: function() {
            var data = $( this ).data( dataKey);         
            
            // Pre-Condition: Does a UserBox exist?
            if (!data.userBox || !data.userBox.length) return false;

            var $target = ($( data.searchInput ).is(':visible') ) ? $( data.searchInput ) : $( data.searchInput ).closest("td");
            if (!$target.length) return false;
    //        if (0) alert( 'target.length: ' + $target.length + "; top: " + $target.offset()["top"] + "; tagName: " + $target.get(0).tagName );

            var t = $target.offset()["top"] + $target.outerHeight();
            var l = $target.offset()["left"];
            var w = $target.outerWidth();
            
            data.userBox.css({position:"absolute",top:t,left:l});
            
            var thisZIndex = data.userBox.css("zIndex"),
                maxZIndex = thisZIndex;
            data.userBox.parents().each(function() {
                maxZIndex = Math.max(maxZIndex, $(this).css("zIndex"));
            });
            if (thisZIndex != maxZIndex) data.userBox.css("zIndex", 1+maxZIndex);

            return true;
        },
        bindUserBox: function() {
            var data = $(this).data(dataKey);
            var self = this;
            
            data.userBox
                .bind( 'mouseleave', function(){methods.setFocus.apply(self, [false])} )
                .bind( 'mouseenter', function(){methods.setFocus.apply(self, [true])} )
                ;
                
            data.cell
                .bind( 'mouseleave', function(){methods.setFocus.apply(self, [false])} )
//                .bind( 'mouseenter', function(){methods.showUserBox.apply(self )} )
                ;
                
            data.searchInput
                .bind( 'blur', function(){methods.setFocus.apply(self, [false])} )
//                .bind( 'focus', function(){methods.showUserBox.apply(self)} )
                ;
        },
        unbindUserBox: function() {
            var data = $(this).data(dataKey);
            
            data.userBox
                .unbind( 'mouseleave' )
                .unbind( 'mouseenter' );
                
            data.cell
                .unbind( 'mouseleave' )
                .unbind( 'mouseenter' );
                
            data.searchInput
                .unbind( 'blur' )
                .unbind( 'focus' );
        },
        showUserBox: function(e) {
            var data = $(this).data(dataKey);
            if ( !data.cell.is(':visible') ) return;
            
            if (data.userBox.data('target') != this) {
                methods.loadUserBoxSource.apply(this, [methods.showUserBox]);
                return;
            }
            
            data.hasFocus = true;
            //alert('#305 '+dataKey+'.showUserBox \ndata.userBox.length:'+data.userBox.length+'\nuserBoxId:'+data.userBox.attr('id'));
            
            if (!data.userBox.length) return;
            
            methods.refreshUserBox.apply(this);
            data.userBox.show();
            data.userBox.data('lastTarget', this);
        },
        hideUserBox: function() {
            var data = $(this).data(dataKey);
            var self = this;
            data.userBox.hide();
//            data.cell.one( 'mouseenter', function() {methods.showUserBox.apply(self);} );
        },
        loadUserBoxSource: function(callback) {
            var data = $(this).data(dataKey);
            var self = this;
            data.sourceLoaded = false;
            
            data.userBox.find("div#"+data.userBoxId+"Inner").load(data.source, function() { 
                if (data.sourceLoadedCallback) {
                    //alert('#323 '+dataKey+'.loadUserBoxSource typeof(data.userBox) '+typeof(data.userBox));
                    data.sourceLoadedCallback.apply(self, [data.userBox]);
                }
                data.sourceLoaded = true;
                data.userBox.data('target', self);
                if (typeof(callback)=='function') callback.apply(self);
                methods.refreshUserBox.apply( self );
            } );  
        },
        registerUserBox: function() {
            var data = $(this).data(dataKey);
            var self = this;
            
            methods.create.apply(this);
            data.cacheValues = {};
            //alert( '#326 '+dataKey+'.registerUserBox typeof(data.ul):'+typeof(data.ul));
            
            data.userBox = $("div#"+data.userBoxId);
            
            if (!data.userBox.length) $("body").append(
                data.userBox = $("<div id='"+data.userBoxId+"'/>")
                .css({zIndex:999}) // height:"200px",
                .append( $("<span/>").fbIcon({icon:"close",click:function(){
                                $(this).closest("div#"+data.userBoxId).hide();
                            }})
                            .css({position:"absolute",right:2,top:2,zIndex:1001})
                            )
                            .append( $("<div id='"+data.userBoxId+"Inner'/>") )
            );
            if (!data.showOnload) data.userBox.hide();
                        
            //methods.loadUserBoxSource.apply( this );
            
            methods.bindUserBox.apply( this );
        },
        unregisterUserBox: function() {
            var data = $(this).data(dataKey);
            methods.hideUserBox.apply(this);
            methods.unbindUserBox.apply(this);
            data.userBox.remove();
        },
        getSearchInputByOriginInput: function() {
            return $( this ).data(dataKey).searchInput;
        },
        register: function () {
            var data = $(this).data(dataKey);
            var self = this;
            if ($(this).attr("jqAutocompleteInit")) return null;
            
            methods.create.apply(this);
            //jQuery( obj )
            data.searchInput
            // don't navigate away from the field on tab when selecting an item
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).data( "autocomplete" ).menu.active ) {
                        event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
    //                if (0) alert("source:" + source);
                    $.getJSON( data.source, {
                        term: methods.extractLast( request.term )
                    }, response );
                },
                minLength: 0,
                search: function() {
    //                if (0) alert( "search" );
                    // custom minLength
                    var term = methods.extractLast( this.value );
                    var minLength = $( this ).autocomplete('option', 'minLength');
                    return ( term.length >= minLength );
                },
                focus: function() {
    //                if (0) alert("focus");
                    // prevent value inserted on focus
                    return false;
                },
                select: function( event, ui ) {            
                    var data = $(self).data(dataKey);
                    var terms = methods.split( this.value );
                    
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
                    methods.addValue.apply(self, [ui.item] );
                    return false;

                    // add placeholder to get the comma-and-space at the end
//                    terms.push( "" );
//                    this.value = terms.join( ", " );
//                    if (data.onselect && typeof(data.onselect)=='function')
//                        data.onselect.apply( this, event, ui.item);
//                    return false;
                }
            });
            $(this).attr("jqAutocompleteInit", "1");
            return data.searchInput;
        }
    }
    
    $.fn[dataKey] = function(options) {
        var args = arguments;
        var argsLen = arguments.length;
        
        // Default-Routine: Getting Options from allready initialized Object
        // Noch nicht initialisierte Routinen geben null zurück, wenn:
        // als Argument nur 'options' übergeben wird, um gesetzte options abzufragen
        // als Arg1 'option' und ein zweites Argument übergeben wird
        // als Arg1 eine Getter-Funktion aufgerufen wird
        // In diesem Fall bleiben uninitialisierte Objekte unberührt bzw. uninitialisiert
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
                        if (! $( this[0] ).data(dataKey) ) return null;
                        return methods[options].apply( this[0], (argsLen>1?$.makeArray(args).slice(1):null) );
                    }
            }
        }
        
        return this.each(function(index) {
            var self = this;
            var _callInit = false;
            // Default Routine: Setting options
            if (!$( self ).data(dataKey)) {
                if (typeof(options) == "object") {
                    $( self ).data(dataKey, $.extend({}, defaults, options) );
                }
                else
                    $( self ).data(dataKey, $.extend({},defaults));
                _callInit = true;
            }
            var data = $( self ).data(dataKey);
            
            // Default-Routine: Analyse Options-Settings and 
            // execute called Functions
            if (typeof(options) == 'string') {
                switch(options) {
                    case 'options':
                        if (argsLen > 1) {
                            data = $.extend({}, data, args[1]);
                            $( self ).data(dataKey, data);
                        }
                        break;
                        
                    case 'option':
                        if (argsLen > 2) {
                            data[args[1]] = args[2];
                            $( self ).data(dataKey, data);
                        }
                        break;
                        
                    default:
                        if (typeof(methods[options]) == 'function') {
                            methods[options].apply( self, (argsLen>1?$.makeArray(args).slice(1):null));
                        }
                }
            }
            
            if (_callInit) methods._init.apply( this );
        });
    };
})(jQuery);