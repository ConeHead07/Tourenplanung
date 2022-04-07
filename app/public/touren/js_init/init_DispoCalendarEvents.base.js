if (!Fb) Fb = {};
if (!Fb.DispoCalendarEvents) Fb.DispoCalendarEvents = {};

$.extend(Fb.DispoCalendarEvents, {
    'url': Fb.AppBaseUrl + '/touren/ajax/calendardaydata', // /date/2012-02-01',
    'routeUrlTpl': Fb.AppBaseUrl + '/touren/ajax/tourdata/id/{#ROUTEID}',
    'resourcesUrlTpl': Fb.AppBaseUrl + '/touren/ajax/resourcedata/id/{#ROUTEID}',
    'onCreate': function() {
        var $self = $(this);
        $self.waypoint({
            handler: function(direction) {
                $("div.DispoCalendar div.DispoCalendarPager.sticky").removeClass("sticky");
                if ( $("div.DispoCalendarPortlets div.portlet", this).length < 2) return;

                var p = $self.find("div.DispoCalendarPager").toggleClass('sticky', direction==='down');
                if (direction === 'down') {
                    // p.css({top: $("div.ui-layout-center:first").offset().top});
                }
            },
            context: 'div.ui-layout-center'
        });
    },
    'onCreatePortlet': function() {
        var $self = $(this);
        var dbdata = $self.fbDispoPortlet('getData');
        dbdata.datum = dbdata.date;
        
        var title = ( dbdata.title ) ? dbdata.title : '';
	var top25 = ( dbdata.topcustom ) ? dbdata.topcustom : '';
        $self.fbDispoPortlet('setTitle', 'Tournr <span class="p-tagesnr">' + dbdata.tagesnr + '</span> <span class="p-holiday">' + dbdata.holiday + '</span> <span class="p-top25">' + top25 + '</span> <span class="p-title">' + title + "</span>");
        return true;
    },    
    'onPrintPortlet': function(){
        var data = $(this).fbDispoPortlet('getData');
        if (data.id) {
             var url = Fb.AppBaseUrl + '/touren/page/printportlet/id/' + data.id;
             if (typeof(winPrint) !== "undefined" && null !== winPrint && winPrint.close && !winPrint.closed) winPrint.close();
             var winPrint = window.open(url, "winPrint", "width=800px,height=800,menubar=yes,resizable=yes,scrollbars=yes");
             winPrint.focus();
        }
    },
    'onCreateDetails': function(tabs) {
        var url = '?NewTabUrl',
            label = 'NewTabLbl',
            index = 'NewTabIdx';
            
        $(tabs).tabs('add', url, label, index );
    },
    'onHoverRouteDetails': function() {
        var tour_id = $( this ).fbDispoRoute('getData').id,
            url = Fb.AppBaseUrl + '/touren/ajax/vorgangsdaten/tour_id/'+tour_id+'/format/html-base/with-resources/1';
        Fb.RouteToolTip(this, url);
    },
    'onLeaveRouteDetails': function() {
        //Fb.RouteToolTipHide( this );
    },
    'onLoadRoute': function() {
        if (!$(this).attr("class") || !$(this).attr("class").match(/fbDispoRoute/)) return true;
        
        var d = $(this).fbDispoRoute('getData');
        
        if (d===null || "object"!==typeof d) {
            return;
        }

        $(this).addClass("dispo-stat-" + d.dispoStatus);
        
        if("string"===typeof d.tour_abgeschlossen_am && d.tour_abgeschlossen_am > '1') {
            // Status: gruen
            $(this).addClass("route-stat-finished");
        }
        else if ("string"===typeof d.tour_disponiert_am && d.tour_disponiert_am > '1') {
            // Status gelb: disponiert
            $(this).addClass("route-stat-dispo");
        } else  {
            if ("string" ===typeof d.created_role && d.created_role == "innendienst") {
                if (!d.modified_role || d.modified_role == "innendienst")
                    $(this).addClass("dispo-stat-innendienst");
            } else {
                // Status rot: noch nicht disponiert
                $(this).addClass("route-stat-nodispo");
            }
        }
//        alert("#668 fbDispoCalendar loadRoute; this.class:" + $(this).attr("class") + "; args.length:" + arguments.length);
        
    },
    'onChangeRouteData': function() {
        var d = $(this).fbDispoRoute('getData'); //var m = 'data:\n'; for(var i in d) m+= i+': ' + d[i] + '\n'; alert(m);
        
        var t = '';
        if (d.Auftragsnummer) t = "<span class=\"anr\">" + d.Auftragsnummer + "</span>";
        try {
            if (d.LieferungName)  t+= (t ? ', ' : '') + d.LieferungName;
            if (d.LieferungOrt)   t+= (t ? ', ' : '') + d.LieferungOrt;
        } catch(e) {
            alert(e);
        }
        $(this).fbDispoRoute('setTitle', t);
    },
    'onClickRoute': function() {
//        Fb.DispoCalendarEvents.onHoverRouteDetails.apply(this, arguments);
    },
    'onHoverRoute': null, //function(elm) {},
    'onLeaveRoute': null, //function() {},
    'onShowRouteDetails': function(tabs) {
//       alert( "#285 fbDispoCalendar onShowRouteDetails; tabs:"+tabs+" class:"+$(tabs).attr("class")+"; this route class: " + $( this ).attr('class') );
//       alert( "this route getData.tour_id: " + $( this ).fbDispoRoute('getData').tour_id);
       var tour_id = $( this ).fbDispoRoute('getData').id,
           mandant = $( this ).fbDispoRoute('getData').Mandant,
           auftrag = $( this ).fbDispoRoute('getData').Auftragsnummer,
            // Set Tour-Basisdaten
            TabHeadUrl = Fb.AppBaseUrl + '/touren/ajax/vorgangsdaten/tour_id/'+tour_id+'/format/html-base';
            
       $("div.TourTabbedHead", tabs).load( TabHeadUrl );  //   ,function() {} // CallBack-Function
       
       var tabsByName = new Array();
       $("ul.ui-tabs-nav li", tabs).each(function(index){
           var tabKey = $('a[id|="Anchor-tabs"]:first', this).attr('id');
           tabKey = (tabKey) ? tabKey.split('-').slice(2).join('-') : $( this ).text();
           tabsByName[ tabKey ] = index;
       });
       //var m='tabs:\n';for(i in tabsByName) m+=i+":"+tabsByName[i]+"\n"; alert(m);
      
       //alert( 'tabsByName.Resources: ' + typeof(tabsByName.Resources) + ' : ' + tabsByName.Resources);
       $( tabs ).tabs('url', tabsByName['Resources'],   Fb.AppBaseUrl + '/touren/ajax/vorgangsresourcen/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('url', tabsByName['Timetable'],   Fb.AppBaseUrl + '/touren/ajax/vorgangstimetable/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('url', tabsByName['Touren'],   Fb.AppBaseUrl + '/touren/ajax/tourlinks/mandant/'+mandant+'/auftrag/'+auftrag+'/format/html');
       $( tabs ).tabs('url', tabsByName['Positionen'],  Fb.AppBaseUrl + '/touren/ajax/vorgangspositionen/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('url', tabsByName['Gruppierungen'],  Fb.AppBaseUrl + '/touren/ajax/vorgangsgruppierung/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('url', tabsByName['Details'],     Fb.AppBaseUrl + '/touren/ajax/vorgangsdaten/tour_id/'+tour_id+'/format/html');  
       $( tabs ).tabs('url', tabsByName['Bemerkungen'], Fb.AppBaseUrl + '/touren/ajax/vorgangsbemerkungen/tour_id/'+tour_id+'/format/html');
        $( tabs ).tabs('url', tabsByName['Historie'], Fb.AppBaseUrl + '/touren/ajax/vorgangshistorie/tour_id/'+tour_id+'/format/html');
        $( tabs ).tabs('url', tabsByName['Abschluss'], Fb.AppBaseUrl + '/touren/ajax/vorgangsabschluss/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('load', tabsByName['Resources']);
    },
    'onChangeDate': function(date) {
        //// Refresh-Grid-Data with new Date-Based Data-Url
        // Aber nicht die Vorgaenge-Auswahlliste
        // Ohne unmittelbaren Reload, erst Rsrc-Tab aktiv wird oder bereits aktiv ist
        
        var tabsMap = ['', '#sFormRsrcFP','#sFormRsrcMA','#sFormRsrcWZ'];        
        var rsrcTabs = $("#sFormRsrcFP").closest(".ui-tabs");
        
        var onTabsSelect = function(e,u){
            if (tabsMap[u.index]) {
                var $this = $(tabsMap[u.index]);
//                $(tabsMap[u.index]).each(function() {
                    var oldUrl = $this.fbMultiSearchBox('jqGridSearchUrl');
                    
                    if (!oldUrl) return;
                    
                    var newUrl = oldUrl
                                 .replace(/\/date\/\d+-\d+-\d+\//, "/" )
                                 .replace(/\/date\/\d+-\d+-\d+\b/, "/" )
                                 .replace(/\/DatumVon\/\d+-\d+-\d+\//, "/" )
                                 .replace(/\/DatumBis\/\d+-\d+-\d+\//, "/" )
                                 .replace(/\/ZeitVon\/\d+(:\d+){0,2}\//, "/" )
                                 .replace(/\/ZeitBis\/\d+(:\d+){0,2}\//, "/" )
                                 .replace(/\/tour\/\d+\//, "/" )
                                 .replace(/\/tour\/\d+\b/, "/" )
                                 .replace(/\/gridresponsedata\b/, '/gridresponsedata/date/'+date);
//                    alert("#124 init_DispoCalendarEvents tabsselect "+tabsMap[u.index]+" set date="+date+"\noldUrl:"+oldUrl+"\nnewUrl:"+newUrl);
                    $this.fbMultiSearchBox({'jqGridSearchUrl':newUrl});
//                });
            }
        };
        
        rsrcTabs.unbind('tabsselect').bind('tabsselect', onTabsSelect);
        
        var activeIndex = rsrcTabs.tabs('option', 'selected' );        
        if (tabsMap[activeIndex]) {
            $(tabsMap[activeIndex]).each(function() {
                var oldUrl = $(this).fbMultiSearchBox('jqGridSearchUrl');
                if (!oldUrl) return;
                var newUrl = oldUrl
                             .replace(/\/date\/\d+-\d+-\d+\//, "/" )
                             .replace(/\/date\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/DatumVon\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/DatumBis\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/ZeitVon\/\d+(:\d+){0,2}\//, "/" )
                             .replace(/\/ZeitBis\/\d+(:\d+){0,2}\//, "/" )
                             .replace(/\/tour\/\d+\//, "/" )
                             .replace(/\/tour\/\d+\b/, "/" )
                             .replace(/\/gridresponsedata\b/, '/gridresponsedata/date/'+date);
                $(this).fbMultiSearchBox({'jqGridSearchUrl':newUrl});
            });
        }
        return true;
    },
    'onClickVorgangsEmpfehlungen': function(date) {
        //alert("#138 init_DispoCalendarEvents.base.js onClickVorgangsEmpfehlungen");
        //// Refresh-Grid-Data with new Date-Based Data-Url
        // Nur Vorgaenge - Auswahlliste
        $('#sFormVorgaenge').each(function() {
            var oldUrl = $(this).fbMultiSearchBox('jqGridSearchUrl');
            if (!oldUrl) return;
            var newUrl = oldUrl.split('/gridresponsedata')[0]+'/gridresponsedata/date/'+date;
            $(this).fbMultiSearchBox({'jqGridSearchUrl':newUrl});
            $("#vorgaengeDate").val(date);
        });
    },
    'onSelectRoute': function() {
        var $self = $(this);
        var data = 
                ( !$(this).is('.fbDispoRouteDefaults')) 
                ? $self.fbDispoRoute('getData') 
                : $self.fbDispoRouteDefaults('getData');
                
        var zeitFilter = 
            'DatumVon/'+data.DatumVon+'/DatumBis/'+data.DatumBis+
            '/ZeitVon/'+data.ZeitVon+'/ZeitBis/'+data.ZeitBis+
            '/tour/'+data.id;
        
        var tabsMap  = ['', '#sFormRsrcFP','#sFormRsrcMA','#sFormRsrcWZ'];
        var rsrcTabs = $("#sFormRsrcFP").closest(".ui-tabs");
        
        rsrcTabs.unbind('tabsselect').bind('tabsselect', function(e,u){
            if (tabsMap[u.index]) {
                $(tabsMap[u.index]).each(function() {
                    var oldUrl = $(this).fbMultiSearchBox('jqGridSearchUrl');
                    if (!oldUrl) return;
                    
                    var newUrl = oldUrl
                                .replace(/\/date\/\d+-\d+-\d+\//, "/" )
                                .replace(/\/date\/\d+-\d+-\d+\b/, "/" )
                                .replace(/\/DatumVon\/\d+-\d+-\d+\b/, "/" )
                                .replace(/\/DatumBis\/\d+-\d+-\d+\b/, "/" )
                                .replace(/\/ZeitVon\/\d+(:\d+){0,2}\//, "/" )
                                .replace(/\/ZeitBis\/\d+(:\d+){0,2}\//, "/" )
                                .replace(/\/tour\/\d+\//, "/" )
                                .replace(/\/tour\/\d+\b/, "/" )
                                .replace(/\/gridresponsedata\b/, '/gridresponsedata/'+zeitFilter);
//                    var newUrl = oldUrl.split('/gridresponsedata')[0]+'/gridresponsedata/'+zeitFilter;
                    $(this).fbMultiSearchBox({'jqGridSearchUrl':newUrl});
                });
            }
        });
        
        var activeIndex = rsrcTabs.tabs('option', 'selected' );        
        if (tabsMap[activeIndex]) {
            $(tabsMap[activeIndex]).each(function() {
                var oldUrl = $(this).fbMultiSearchBox('jqGridSearchUrl');
                if (!oldUrl) return;
                var newUrl = oldUrl
                             .replace(/\/date\/\d+-\d+-\d+\//, "/" )
                             .replace(/\/date\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/DatumVon\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/DatumBis\/\d+-\d+-\d+\b/, "/" )
                             .replace(/\/ZeitVon\/\d+(:\d+){0,2}\//, "/" )
                             .replace(/\/ZeitBis\/\d+(:\d+){0,2}\//, "/" )
                             .replace(/\/tour\/\d+\//, "/" )
                             .replace(/\/tour\/\d+\b/, "/" )
                             .replace(/\/gridresponsedata\b/, '/gridresponsedata/'+zeitFilter);
                $(this).fbMultiSearchBox({'jqGridSearchUrl':newUrl});
            });
        }
        return true;
    },
    'onSelectRouteDefaults': function() {
        return Fb.DispoCalendarEvents.onSelectRoute.apply(this, arguments);
    },
    'onShowRouteDefaultsDetails': function(tabs) {        
//       alert('#387 fbDispoCalendar onShowRouteDefaultsDetails!');
       var tour_id = $( this ).fbDispoRouteDefaults('getData').id;
       
       // Set Tour-Basisdaten
       var TabHeadUrl = Fb.AppBaseUrl + '/touren/ajax/vorgangsdatendefaults/tour_id/'+tour_id+'/format/html-base';
       $("div.TourTabbedHead", tabs).load( TabHeadUrl /*,function() {} // CallBack-Function */ );
       
       var tabsByName = new Array();
       $("ul.ui-tabs-nav li", tabs).each(function(index){
           var tabKey = $('a[id|="Anchor-tabs"]:first', this).attr('id');
           tabKey = (tabKey) ? tabKey.split('-').slice(2).join('-') : $( this ).text();
           tabsByName[ tabKey ] = index;
       });
       
       $( tabs ).tabs('url',  tabsByName['Resources'], Fb.AppBaseUrl + '/touren/ajax/vorgangsresourcendefaults/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('url',  tabsByName['Options'],   Fb.AppBaseUrl + '/touren/ajax/timelinedata/tour_id/'+tour_id+'/format/html');
       $( tabs ).tabs('load', tabsByName['Resources']);
    },
    'onHideRouteDefaultsDetails': function(tabs) {        
        alert('#390 fbDispoCalendar onShowRouteDefaultsDetails!');
    },
    'onHoverRouteDefaults': function(elm) {
        var tour_id = $( this ).fbDispoRouteDefaults('getData').id;
        var url = Fb.AppBaseUrl + '/touren/ajax/vorgangsresourcendefaults/tour_id/'+tour_id+'/format/html';        
        Fb.RouteToolTip(this, url, 'below');
    },
    'onLeaveRouteDefaults': function() {
        //Fb.RouteToolTipHide( this );
    }
});