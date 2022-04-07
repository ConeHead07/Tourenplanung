
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
   route: {
//       allowedColors: ['Gruen', 'Gelb','Blau','Weiss', 'Reklamation', 'Service']
       allowedColors: ['VIP',   'Anker', 'Fuell', 'Projekt',    'Reklamation', 'Service', 'Specials']
       //              'Gruen', 'Gelb',  'Blau',  'Rot',        'Creme',       'Grau',    'Weiss',
   },
   routeDefaults: {
       askForApplyDefaults: false,
       applyDefaults: true
   },
   resource: {}
});
