
if (!Fb) Fb = {};
if (!Fb.DispoCalendarSettings) Fb.DispoCalendarSettings = {};

$.extend(Fb.DispoCalendarSettings, {
   calendar: {
       isBrowsable: true,
//       isAddable: false,
       isHidable: true,
       isPrintable: true,
       isSortable: false,
       isSearchable: true,
       isContextSearchable: true
   },
   
   portlet: {
       isEditable: false,
       isRemovable: false,
       isPrintable: false,
       isSortable: false,
       isDroppable: false
   },
   
   timelineDropzone:{
       isSortable: false,
       isMovable: false,
       isRemovable: false
   },
   timelineDropzoneHandles: {},
   timelineLineal: {},
   timelineGrid: {},
   
   route: {
       isEditable: true,
       isDroppable: false,
       isDraggable: true,
       isResizable: true,
       isRemovable: false,
       allowedColors: ['Gelb','Blau','Weiss', 'Reklamation', 'Service']

},
   routeDefaults: {
       isDroppable: false
   },
   
   resource: {
       isDraggable: true,
       isRemovable: true
   }
       
});
