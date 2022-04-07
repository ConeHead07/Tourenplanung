
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
       isEditable: false,
       isDroppable: false,
       isDraggable: false,
       isResizable: false,
       isRemovable: false
   },
   routeDefaults: {
       isDroppable: false
   },
   
   resource: {
       isDraggable: true,
       isRemovable: true
   }
       
});
