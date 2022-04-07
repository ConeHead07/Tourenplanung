
if (!Fb) Fb = {};
if (!Fb.DispoCalendarSettings) Fb.DispoCalendarSettings = {};

$.extend(Fb.DispoCalendarSettings, {
   calendar: {},   
   portlet: {},   
   timelineDropzone:{},
   timelineDropzoneHandles: {},
   timelineLineal: {
     start: '06:00',
     end: '20:00',
     stepWidth: '00:30'
   },
   timelineGrid: {},   
   route: {},
   routeDefaults: {
       askForApplyDefaults: false,
       applyDefaults: true
   },
   resource: {}
});
