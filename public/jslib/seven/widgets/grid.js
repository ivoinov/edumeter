Seven.WidgetGrid = Seven.Class(Seven.Object, {
	
    init: function(data) {
        if(typeof data != 'undefined')
            this.data = data;
        var that = this;
        $(function() {
        	that.setFilters(that.getGrid().find('[name^=filter]').serialize());
        	that.filters.page = app.url.getParam('page');
        	that.afterUpdate();
		});
    },
    
	search: function(filters) {
    	if(typeof filters == 'undefined')
    		filters = this.getGrid().find('[name^=filter]').serialize() + "&page=1";
    	this.setFilters(filters);
    	this.updateGrid();
    	return false;
    },
    
    goPage: function(page) {
    	this.filters.page = page;
    	this.updateGrid();
    },
    
    nextPage: function() {
    	var page = parseInt(this.filters.page, 10);
    	if(isNaN(page)) page = 0;
    	this.goPage(Math.max(2, page + 1));
    },
    
    prevPage: function() {
    	var page = parseInt(this.filters.page, 10);
    	if(isNaN(page)) page = 0;
    	this.goPage(Math.max(1, page - 1));
    },
    
    disable: function() {
    	this.getGrid().find('.grid tbody tr').animate({'opacity': '0.2'});
    	this.getGrid().find('input, textarea, select').attr('disabled', 'disabled');
    },
    
    enable: function() {
    },
    
    setFilters: function(filters) {
    	if(typeof filters != 'object') 
    		filters = this._convertFiltersToArray(filters);
    	this.filters = filters;
    },
    
    _convertFiltersToArray: function(filter_string) {
    	if(!filter_string) return {};
    	var filters = {};
    	var pairs = filter_string.split('&');
    	for (var i = 0; i < pairs.length; i++) {
    		var pair = pairs[i].split('=');
    		if(pair.length == 1) pair.push('');
    		filters[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    	}
    	return filters;
    },
    
    getUpdateUrl: function() {
    	if(this.getData('ajax_url'))
    		return _url(this.getData('ajax_url'));
    	return _url('*/*/*');
    },
    
    updateGrid: function() {
    	var that = this;
    	var params = jQuery.extend(true, {}, app.url.getData('parameters'));
    	if(params) params.filter = {};
    	query = $.param($.extend(params, this.filters ? this.filters : {}));
    	if(that.getData('use_ajax')) {
    		that.disable();
	    	$.getJSON(that.getUpdateUrl() + "?" + query + '&ajax=1', function(data) {
	    		that.enable();
	    		if(typeof data.grid == 'string')
	    			that.getGrid().html(data.grid);
	    		that.afterUpdate();
	    	});
    	} else {
    		document.location = _url('*/*/*') + "?" + query;
    	}
    },
    
    afterUpdate: function() {
    	var that = this;
    	this.getGrid().find('[name^=filter]').keypress(function(e) {
    		if(e.which == 13) {
    			that.search();
    			e.stopPropagation();
    			return false;
    		}
    	});
    },
    
    getGrid: function() {
    	return $("#" + this.getData('html_id'));
    }
	
});
