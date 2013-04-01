$(function() {
    $("select[name=scope]").change(function() {
        if(!$(".value-changed").length || confirm(__("Do you want discard changes?")))
            app.redirect("*/*/*/*", {scope: $(this).val()});
        $(this).val(app.url.getParam('scope'));
    });
    
    $("form .widget-container-content input, form .widget-container-content textarea, form .widget-container-content select").change(function() {
        if($(this).val() == $(this).attr('currentvalue'))
            $(this).removeClass('value-changed');
        else
            $(this).addClass('value-changed');
    });
    $("form .widget-container-content input, form .widget-container-content textarea, form .widget-container-content select").each(function() {
        $(this).attr('currentvalue', $(this).val());
    });
    
    var update_default_state = function() {
        var input = $(this).parents(".field-block").find('.input select, .input input, .input textarea');
        if(!input.length) return;
        var is_default = $(this).attr('checked') == 'checked';
        input.attr('disabled', is_default);
        if(is_default) {
            input.attr('excluded-name', input.attr('name'));
            input.removeAttr('name');
            input.val(input.attr('currentvalue'));
        } else {
            input.attr('name', input.attr('excluded-name'));
            input.removeAttr('excluded-name');
        }
    };
    
    $(".default-value input[type=checkbox]").change(update_default_state);
    $(".default-value input[type=checkbox]").each(update_default_state);
    
});
