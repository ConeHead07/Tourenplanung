if (!Fb) var Fb = {};

Fb.log = '';
Fb.logAjaxError = function(srcAddress, xhr, status, error) {
    var log = srcAddress;
    if (typeof(xhr)=='object') log+= ''+
        '\n\nxhr.repsonseText:'+xhr.responseText+
        '\n\nxhr.statusText:'+xhr.statusText+
        '\n\nxhr.status:'+xhr.status;
    
    if (status) log+= '\n\nstatus: '+status;
    if (error)  log+= '\n\nerror:'  +error;
    
    Fb.log+= '\n++++++++++\n' + log + '\n';
    if (error) alert( log );
}
Fb.showLog = function() {
    alert( Fb.log );
}
Fb.convertDate = function( date, dstFormat, srcFormat ) {
    if ( typeof(date) == "string" ) {
        date = $.datepicker.parseDate(srcFormat, date);
    }
    return $.datepicker.formatDate(dstFormat, date);
}

