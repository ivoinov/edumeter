$(function() {

    $('.widget-input-date').each(function() {
         var id = $(this).attr('id') + "_datepicker_value";
         $("<input value='' id='" + id + "' name='" + $(this).attr('name') + "' type='hidden'>").insertAfter(this);
         $(this).attr('name', '');
         $(this).datepicker({"dateFormat" : 'yy-mm-dd', 
                             "altFormat" : 'yy-mm-dd', 
                             "altField" : "#" + id
                             });
         $(this).datepicker("option", "dateFormat", "mm/dd/yy");
    });

    $('.widget-input-datetime').each(function() {
         $(this).datetimepicker({
             "dateFormat" : 'yy-mm-dd', 
             "timeFormat" : 'hh:mm:ss',
             "showSecond": true,
             "separator": ' '
         });
    });

});