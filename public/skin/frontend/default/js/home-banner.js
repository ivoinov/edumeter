(function($) {
    $.fn.homeSlider = function(options) {
        var homeSlider = {
            "target": null,
            "items": {},
            "options": {"ycell": 0, "xcell": 0, "preview_width": 50, "preview_height": 30},
            "init": function(_target, _items, _options) {
                if(typeof _options != 'object')
                    _options = {};
                this.options = $.extend(_options, this.options);
                this.target = _target;
                this.items = _items;
                // init
                if(this.items.length <= 1)
                    return;
                if(!this.options.ycell && !this.options.xcell)
                    this.options.ycell = this.items.length >= 9 ? 3 : 2;
                if(this.options.ycell && !this.options.xcell)
                    this.options.xcell = Math.ceil(this.items.length / this.options.ycell);
                else if(!this.options.ycell && this.options.xcell)
                    this.options.ycell = Math.ceil(this.items.length / this.options.xcell);
                this.updateItems();
                var that = this;
                this.items.click(function() {
                   if($(this).hasClass('fullsize'))
                        that.closeSlide(this); 
                    else
                        that.openSlide(this); 
                });
            },
            "openSlide": function(slide) {
                this.items.stop().removeClass('preview').removeClass('fullsize');
                $(slide).addClass('fullsize').removeClass('thumbnail').stop().animate({"top": 0, "left": 0, "width": this.target.width(), "height": this.target.height()}).css('z-index', 90);
                var m = 0;
                var that = this;
                var previews = this.items.filter(":not(.fullsize)");
                left_offset = (this.target.width() - previews.length * (10 + that.options.preview_width)) / 2;
                previews.addClass('thumbnail').each(function() {
                    $(this).animate({
                       "width":  that.options.preview_width + "px",
                       "height": that.options.preview_height + "px",
                       "top": that.target.height() - 10 - that.options.preview_height,
                       "left": left_offset + (10 + that.options.preview_width) * m
                    }).css('z-index', 100);
                    m++;
                });
            },
            "closeSlide": function(slide) {
                var cw = this.target.width() / this.options.xcell, ch = this.target.height() / this.options.ycell;
                $(this.items).removeClass('fullsize').removeClass('thumbnail').addClass('preview').stop().each(function() { 
                    var x = $(this).attr('x-banner'), y = $(this).attr('y-banner');
                    $(this).animate({
                        "top": (ch * y) + "px",
                        "left": (cw * x) + "px",
                        "width": cw + "px",
                        "height": ch + "px"
                    }, function() { $(this).css('z-index', 100) });
                });
            },
            "updateItems": function() {
                var x = 0, y = 0, cw = this.target.width() / this.options.xcell, ch = this.target.height() / this.options.ycell; 
                for(var i = 0; i < this.items.length; i++) {
                    x = i % this.options.xcell;
                    y = Math.floor(i / this.options.xcell);
                    $(this.items[i]).css({
                        "position": "absolute",
                        "top": (ch * y) + "px",
                        "left": (cw * x) + "px",
                        "width": cw + "px",
                        "height": ch + "px",
                        "overflow": "hidden",
                        "display": "block",
                        "z-index": "100"
                    }).addClass('preview');
                    $(this.items[i]).attr('x-banner', x);
                    $(this.items[i]).attr('y-banner', y);
                }
                this.target.css('position', 'relative');
            }
        }
        homeSlider.init($(this), $("li", this), options);
        
    };
})(jQuery);