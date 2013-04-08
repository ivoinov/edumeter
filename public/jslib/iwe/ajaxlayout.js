$(function() {
    $("#ajax-loading-layout").ajaxStop(function() { $(this).fadeOut(); });
    $("#ajax-loading-layout").ajaxStart(function() {
        if(typeof ajax_layer == 'undefined' || ajax_layer == true)
            $(this).fadeIn();
        else return;
    });
});