var SchoolListWidgetClass = Seven.Class(ListWidgetClass, {
    map: null,
    setMap: function(map)
    {
         this.map = map;
    },
    getMap: function()
    {
        return this.map;
    },
    changeRadius: function(radius)
    {
        if(!map)
        {
            alert('Please reload page');
            return false;
        }
        map.changeRadius(radius);
        $('#radius-filter li.filter.active').removeClass('active');
        $('#raius-filter-' + radius).addClass('active');
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
})
