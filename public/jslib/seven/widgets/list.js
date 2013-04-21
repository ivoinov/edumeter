var ListWidgetClass = Seven.Class({

	items: 					{},
	order: 					[],
	options:				{},
	update_url: 			'',
	location:   			'',
	elements:				'',
	list_animation_speed: 	300,
	pager:					{},
	query:					'',
	sort_order:				'ASC',
	sort_key:				'',

	init: function(options) {
		options = this.options = $.extend({
			list_index: 		'list',
			list_selector: 		'#list',
			pager_selector: 	'#pager',
			limit_selector:		'#limit',
			head_selector:		'#list .head',
			items_selector:		'#list > li',
			item_template:		'#item-template',
			pager_template:		'#pager-template',
			accardion:			true,
		}, options);
		this.elements = {
			list: 	options.list_selector,
			pager: 	options.pager_selector,
			limit: 	options.limit_selector,
			head: 	options.head_selector,
			items:  options.items_selector
		};
		this.templates = {
			item:	options.item_template,
			pager:	options.pager_template
		}
		this._initListControl(); 
	},
	
	setData: function(data) {
		this._setData(data);
		this.sync();
	},

	setItems: function(items) {
		this._setItems(items);
		this.sync();
	},

	hasItems: function() {
		for(var i in this.items)
			if(!isNaN(i))
				return true;
		return false;
	},

	getItem: function(id) {
		if(typeof this.items[id] != 'undefined')
			return this.items[id];
		return null;
	},

	getItemByView: function(element) {
		if(!$(element).length) return null;
		var id = jQuery.data($(element).get(0), '_item_id');
		if(typeof this.items[id] != 'undefined')
			return this.items[id];
		return null;
	},

	addItem: function(item, prepend) {
		this._addItem(item, prepend);
		this.sync();
	},

	reload: function(animation, params) { // animation: false, list or item
		var list = this;
		if(typeof animation == 'undefined') animation = 'item';
		params = list._extendReloadParams(params);
		$.getJSON(list.update_url, params, function(data) {
			list._updateLocation(params);  
			list._setData(!!data[list.options.list_index] ? data[list.options.list_index] : []);
			list.sync(false, animation);
		});
	},

	sync: function(animation) {
		var list = this;
		if(typeof animation == 'undefined') animation = false;

		var view_items = {};
		$(list.elements.items).each(function() {
			view_items[jQuery.data($(this).get(0), '_item_id')] = $(this);
		});

		$(list.elements.list).stop(true, true);
		//
		var list_height = $(list.elements.list).height();
		var new_list_height = 0;
		//
		var insert_to = {"point": list.elements.list, "method": 'prepend'};
		// cleanup campaign list if it's empty
		if(!$(list.elements.items).length)
			$("> *", $(list.elements.items)).slideUp("fast", function() { $(this).remove(); });
		// sync items between view and model
		for(var index = 0; index < this.order.length; index++) {
			var i = this.order[index];
			if(!this.items[i]) continue;
			var item = this.items[i];

			if(typeof view_items[item.id] != 'undefined' && view_items[item.id]) { // item already in view
				this._updateItemView(item, view_items[item.id], insert_to);
				var insert_to = {"point": view_items[item.id], "method": 'after'};
				delete view_items[item.id];
			} else {
				var view_item = list._addItemView(item, insert_to);
				var insert_to = {"point": view_item, "method": 'after'};
			}
			new_list_height += 39;
		}
		// remove remain view items
		for(var i in view_items)
			this._removeItemView(view_items[i], animation);

		// list animation
		if(animation == 'list' || animation == 'list-right') {
			$(list.elements.list).css({"height": list_height}).delay(list.list_animation_speed).animate({"height": new_list_height}, list.list_animation_speed, function() {$(this).css({"height": "auto"});});
		}
		// update events binds and view state
		this.redraw();
	},

	sort: function(element, key, order) {
		if(typeof order == 'undefined')
			order = (key != this.sort_key) ? 'ASC' : (this.sort_order == 'ASC' ? 'DESC' : 'ASC');

		this.sort_order = order;
		this.sort_key   = key;

		$(this.elements.head).find('.sorted')
			.removeClass('sorted')
			.removeClass('sorted-asc')
			.removeClass('sorted-desc');

		if(typeof element != 'undefined')
			$(element)
				.addClass('sorted')
				.addClass(order == 'ASC' ? 'sorted-asc' : 'sorted-desc');

		this.reload('item');
	},

	nextPage: function() {
		this.goPage(parseInt(this.pager.number, 10) + 1);
	},

	prevPage: function() {
		this.goPage(parseInt(this.pager.number, 10) - 1);
	},

	goPage: function(page) { 
		$.cookie("page", page);
		this.reload((page > parseInt(this.pager.number, 10)) ? 'list-right' : 'list', {"page": page});
	},

	redraw: function() { 
		var list = this;
		var toBind = $(".notbinded", $(list.elements.list));
		toBind.on('click', function(e) {
			if($(e.target).is("input, textarea, select, button, a, img"))
				return;
			list.openItemView(this);
		});
		toBind.removeClass('notbinded');
		$(list.elements.items).filter('.even').removeClass('even');
		$(list.elements.items).filter(':nth-child(even)').addClass('even');
	},

	openItemView: function(element, accardion) {
		if(typeof accardion == 'undefined')
			accardion = this.options.accardion;
		var list = this;
		var current_height = $(element).height();
		var opened_height = $(element).css({"height": "auto"}).height();
		var closed_height = $(element).attr('closed_height');
		if(accardion) {
			$(this.elements.items).filter('.opened').each(function() {
				if(this != element) list.openItemView(this, false);
			});
		}
		$(element).toggleClass('opened').stop();
		if($(element).is('.opened')) {
			if(!$(element).attr('closed_height'))
				$(element).attr('closed_height', current_height);
			$(element).css({"height": current_height}).animate({"height": opened_height}, function() { $(this).css({"height": "auto"}); });
		} else {
			$(element).css({"height": current_height}).animate({"height": closed_height});
		}
	},

	_initListControl: function() {
		var list = this;
		$(function() {
			$(list.elements.limit).live('change', function(e) {
				list.pager.limit = $(this).val();
				$.cookie("limit", list.pager.limit); 
				list.goPage(1);
				e.stopPropagation();
				return false;
			});
			$(list.elements.pager).find('input').live('keydown', function(e) { 
				if(e.which == 13) {
					list.goPage($(this).val());
					e.stopPropagation();
					return false;
				}
			});
			$(list.elements.sort).on('click', function(e) {
				list.order = $(this).text().toLowerCase();
				list.sort  = $(this).attr("order");
				list.elements.sort.removeClass("sort").removeClass('order-desc').removeClass('order-asc');
				$(this).addClass('sort').addClass(list.order == 'ASC' ? 'DESC' : 'ASC');
			    list.reload('list');
				e.stopPropagation();
				return false;
			});
			list.location = $.extend({}, app.url);
			list.update_url = _url('*/*/*');
		});
	},

	_addItem: function(item, prepend) {
		this._prepareItem(item);
		if(typeof this.items[item.id] == 'undefined') { // add to order
			if(prepend) {
				var pre = [item.id];
				this.order = pre.concat(this.items_order);
			} else {
				this.order.push(item.id);
			}
		}
		this.items[item.id] = item;
	},

	_prepareItem: function(item) {
	},

	_setItemViewPosition: function(element, position) {
		if(position.method == 'prepend')
			$(element).prependTo(position.point);
		else if(position.method == 'append')
			$(element).appendTo(position.point);
		else if(position.method == 'after')
			$(element).insertAfter(position.point);
		else if(position.method == 'before')
			$(element).insertBefore(position.point);
	},

	_updateItemView: function(item, element, position) {
		var inputState = {};
		element.find('input').each(function() {
			inputState[$(this).attr('name')] = $(this).attr('checked');
		});
		var content = $(this.templates.item).tmpl(item);
		element.html(content.html());
		element.find('input').each(function() {
			if(typeof inputState[$(this).attr('name')] != 'undefined')
				$(this).attr('checked', inputState[$(this).attr('name')]);
		});
		if(typeof position != 'undefined')
			this._setItemViewPosition(element, position);
	},

	_addItemView: function(item, position, animation) {
		var view = $(this.templates.item).tmpl(item);
		if(!view.length)
			return console.log('Can\'t get item template');
		this._setItemViewPosition(view, position);
		jQuery.data(view.get(0), '_item_id', item.id);
		$(view).addClass('notbinded');
		if(animation && position.method != 'replace') {
			if(animation == 'list' || animation == 'list-right')
				view.css({'height': 0, 'padding': 0, 'border-width': 0})
					.delay(this.list_animation_speed)
					.animate({'height': '18px', 'padding': '10px'}, this.list_animation_speed, function() {$(this).css({'border-width': 1});});
			else
				view.css({'height': '0px'}).animate({'height': 18, 'padding': 10}, 'slow');
		}
		return view;
	},

	_removeItemView: function(element, animation) {
		$(element).addClass('removing');
		jQuery.data(element.get(0), '_item_id', '0');
		if(animation == 'list')
			element.animate({"margin-right": -200, "margin-left": 200, "opacity": 0}, this.list_animation_speed, function() {$(this).remove();});
		else if(animation == 'list-right')
			element.animate({"margin-left": -200, "margin-right": 200, "opacity": 0}, this.list_animation_speed, function() {$(this).remove();});
		else if(animation)
			element.slideUp('fast', function() {$(this).remove();});
		else
			element.remove();
	},

	_extendReloadParams: function(params) {
		return $.extend({
			"ajax": 	1,
			"required": this.options.list_index,
			"q": 		typeof this.query 		== 'string'    ? this.query 		 : "",
			"page": 	typeof this.pager 		!= 'undefined' ? this.pager.number 	 : 1,
			"limit": 	typeof this.pager 		!= 'undefined' ? this.pager.limit    : 20,
			"sort": 	typeof this.sort_key  	!= 'undefined' ? this.sort_key 	 	 : '',
			"order": 	typeof this.sort_order 	!= 'undefined' ? this.sort_order 	 : 'ASC'
		}, params);
	},

	_updateLocation: function(params) {
		if(!!params.query) this.location.setParam('q', query);
		else delete this.location.data.parameters.q;

		if(!!params.page && params.page != 1) this.location.setParam('page', params.page);
		else delete this.location.data.parameters.page;

		if(!!params.limit) this.location.setParam('limit', params.limit);
		else delete this.location.data.parameters.limit;

		if(!!params.order) this.location.setParam('order', params.order);
		else delete this.location.data.parameters.order;

		if(!!params.sort) this.location.setParam('sort', params.sort);
		else delete this.location.data.parameters.sort;

		if(typeof window.history.pushState == 'function') {
			window.history.pushState(this.location.getData('parameters'), null, this.location.toString());
		}
	},

	_setData: function(data) { 
		this.data = data;
		if(typeof data.items != 'undefined')
			this._setItems(data.items);
		if(typeof data.pager != 'undefined')
			this._setPager(data.pager);
		if(typeof data.query != 'undefined')
			this._setSearch(data.query);
	},

	_setItems: function(items) {
		this.items = {};
		this.order = [];
		for(var i in items) {
			this._addItem(items[i], false);
		}
	},
	_setPager: function(pager) {
		this.pager = pager;
		$(this.elements.pager).html($(this.templates.pager).tmpl(pager));
		$(this.elements.limit).val(pager.limit);
	},

	_setSearch: function(query) {
		this.query = query;
		//$("#campaign-search").html($("#campaign-search-template").tmpl(query));
	}

});