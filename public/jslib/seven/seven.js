var Seven = {};

Seven.Class = function() {
    var prototype = {};
    for(var i = 0; i < arguments.length; i++) {
        var extend = arguments[i];
        if(typeof extend == 'object') {
        	prototype = $.extend(prototype, extend);
        } else if(typeof extend == 'function') {
            if(typeof extend.prototype == 'object')
            	prototype = $.extend(prototype, extend.prototype);
        }
    }
    var klass = function() { 
        for(var key in prototype) {
            this[key] = prototype[key]; 
        }
        if(typeof this.init == 'function')
            this.init.apply(this, arguments);
    };
    klass.prototype = prototype;
    return klass;
};

Seven.App = Seven.Class({
    redirect: function(url, args) {
        document.location = _url(url, args);
        return this;
    }
});

Seven.Object = Seven.Class({
    data: {},
    getData: function(property) {
        if(typeof this.data != 'undefined' && typeof this.data[property] != 'undefined') 
            return this.data[property];
        return null;
    },
    setData: function(property, value) {
        this.data[property] = value;
        return this; 
    },
    init: function(data) {
        if(typeof data != 'undefined')
            this.data = data;
    }
});

Seven.Url = Seven.Class(Seven.Object, {

	data: {"controller": "index", "action": "index", "frontname": "default", "base_url": "", "path": "", "parameters": {}},    
    
	init: function(data) {
		if(typeof data != 'undefined')
			this.data = data;
		if(typeof this.data.parameters == 'undefined' || typeof this.data.parameters.pop == 'function')
			this.data.parameters = {};
	},
	
    getBaseUrl: function() {
        var url = this.getData('scheme') + "://" + this.getData('host');
        if(this.getData('port') && this.getData('port') != 80)
            url += ":" + this.getData('port');
        if(this.getData('path'))
            url += "/" + this.getData('path');
        return url + "/";
    },
    parse: function(url, args) {
        var props = ['scheme', 'host', 'port', 'path'];
        for(var i = 0; i < props.length; i++)
            this.setData(props[i], app.url.getData(props[i]));
        this.setRequest(url.replace(/^\/+/, '').replace(/\/+$/, ''));
        this.setData('parameters', args);
        return this;
    },
    toString: function() {
        var url = this.getBaseUrl() + this.getRequest().replace(/^\/+/, '').replace(/\/+$/, '');
        var parameters = this.getData('parameters');
        if(typeof parameters == 'object' && parameters) {
           url += "?" + decodeURIComponent($.param(parameters));
        }
        return url;
    },
    getRequest: function() {
    	if(typeof this.data.website_code == 'undefined')
    		this.data.website_code = app.website.getData('code');
        var parts = [this.getData('website_code'), this.getData('controller'), this.getData('action')];
        return parts.join("/");
    },
    setRequest: function(request) {
        var parts = request.split("/");
        var props = {'website_code': app.website.getData('code'), 'controller': app.url.getData('controller'), 'action': app.url.getData('action')};
        var i = 0;
        for(var k in props) {            
            if(typeof parts[i] != 'undefined')
                this.setData(k, parts[i] == '*' ? props[k] : parts[i]);
            else
                this.setData(k, '');
            i++;
        }
        return this;
    },
    getParam: function(prop) {
        var parameters = this.getData('parameters');
        if(typeof parameters[prop] != 'undefined')
            return parameters[prop];
        return null;
    },
    setParam: function(prop, val) {
        var parameters = this.getData('parameters');
        parameters[prop] = val;
        this.setData('parameters', parameters);
        return this;
    }
});

Seven.I18n = Seven.Class(Seven.Object, {
    translate: function(phrase) {
        var index = 0;
        var args = arguments;
        return phrase.replace(/(%[dsf])/gi, function() { index++; if(typeof args[index] != 'undefined') return args[index]; });
    }
});

Seven.Website = Seven.Class(Seven.Object, {
	
});

_url = function(url, args) {
    var urlObject = new Seven.Url();
    urlObject.parse(url, args);
    return urlObject.toString();
}

__ = function() {
    return app.i18n.translate.apply(app.i18n, arguments);
}