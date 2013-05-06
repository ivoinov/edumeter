var SchoolListWidgetClass = Seven.Class(ListWidgetClass, {
    map: null,
    sort_order:				'ASC',
    sort_key:				'rate',
    list_animation_speed: 1000,
    setMap: function(map)
    {
         this.map = map;
    },
    getMap: function()
    {
        if(this.map == null)
            return map;
        return this.map;
    },
    changeRadius: function(radius)
    {
        if(!this.map)
        {
            alert('Please reload page');
            return false;
        }
        this.map.changeRadius(radius);
        $('#radius-filter li.filter.active').removeClass('active');
        $('#raius-filter-' + radius).addClass('active');
        this.reload('list');
    },
    changeWay: function(way)
    {
        this.getMap().setWay(way);
        this.getMap().setCurrentPosition(this.getMap().current_position);
        $('#way-filter li.filter.active').removeClass('active');
        $('#way-filter-' + way).addClass('active');
        this.reload('list');
    },
    changeYear: function(year)
    {
        this.getMap().setYear(year);
        this.getMap().setCurrentPosition(this.getMap().current_position);
        $('#year-filter li.filter.active').removeClass('active');
        $('#year-filter-' + year).addClass('active');
        this.reload('list');

    },
    _extendReloadParams: function(params) {
        var radius = this.getMap().currentRadiusValue;
        var current_longitude = this.getMap().current_position.lng();
        var current_latitude = this.getMap().current_position.lat();
        var way = this.getMap().way;
        var year = this.getMap().year;
        return $.extend(
            ListWidgetClass.prototype._extendReloadParams.apply(this, arguments),
            {
                "radius": radius,
                "current_longitude":  current_longitude,
                "current_latitude": current_latitude,
                "way": way,
                "year": year
            }
        );
    }

});

var school_list = new SchoolListWidgetClass({
	item_template: 	'#school-item-template',
	empty_template: '#school-empty-line-template',
	pager_template: '#school-pager-template',
	pager_selector: '#school-pager',
	head_selector:	'.school-list-header',
	list_selector:  '#school-list',
	items_selector:	'#school-list .school-line',
	limit_selector: '#limit',
	list_index:		'school_list'
});
$(function(){
    school_list.setMap(map);
    $('#raius-filter-500').addClass('active');
    $('#way-filter-2').addClass('active');
    $('#year-filter-2012').addClass('active');
})
