(function($){
	
	$.fn.accessSelect = function() {
		if($(this).length > 1)
			return $(this).each(function() { $(this).accessSelect(); });
		var access_selector = {
			select_obj: null,
			button_obj: null,
			init: function(s) {
				this.select_obj = s;
				this.button_obj = $("<div class='access-node'></div>").insertAfter(s);
				this.button_obj.parent().parent().addClass('state' + s.val());
				this.select_obj.hide();
				var selector = this;
				this.button_obj.click(function(e) {
					$(this).parent().parent().toggleClass('opened');
					e.stopPropagation();
				});/*
				this.button_obj.parent().click(function() {
					var c_opt = $("select option[selected]", this);
					var n_opt = c_opt.next() ? c_opt.next() : $("select option:first-child", this);
					var n_val = n_opt.attr('value');
					$("select", this).val(n_val);
					$(this).parent().removeClass('state').removeClass('stateallow').removeClass('statedeny').addClass('state' + n_val);
				});*/
			}
		};
		access_selector.init($(this));
	}

	$(function() { $("select[name^=permissions]").accessSelect(); });
	
})(jQuery);