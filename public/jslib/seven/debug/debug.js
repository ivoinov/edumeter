$(document).keypress(function(e) { 
	if($(e.target).is('form,input,textarea,button,select'))
		return;
	if(e.which == 96) $('#profiler').toggle(); 
});