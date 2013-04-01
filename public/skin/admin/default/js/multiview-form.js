$(function() {
    $("select[name=view_id]").change(function() {
        if(!$(".value-changed").length || confirm(__("Do you want discard changes?")))
            app.redirect("*/*/*/*", {view_id: $(this).val(), id: app.url.getParam('id')});
        $(this).val(app.url.getParam('view_id'));
    });
    
    $("form .widget-container-content input, form .widget-container-content textarea, form .widget-container-content select").change(function() {
        if($(this).val() == $(this).attr('currentvalue')) {
            $(this).removeClass('value-changed');
    	} else {
            $(this).addClass('value-changed');
            $(this).parents('.field-block').find('.default-value input[type=checkbox]').attr('checked', false);
        }
    });
    
    $("form .widget-container-content input, form .widget-container-content textarea, form .widget-container-content select").each(function() {
        $(this).attr('currentvalue', $(this).val());
    });
    
    $('form .default-value input[type=checkbox]').click(function() {
    	var $input = $(this).parents('.field-block').find('.input input, .input textarea, .input select');
    	if($(this).attr('checked')) {
    		$input.val('[' + __('default') + ']');
    	} else {
    		$input.val($input.attr('currentvalue'));
    	}
    });
    
});
