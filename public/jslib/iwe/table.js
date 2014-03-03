/**
 * Created by b.soroka on 2/24/14.
 */
var SchoolTableWidgetClass = Seven.Class(ListWidgetClass, {
    region: 0,
    district: 0,
    way: '2',
    year: 2012,
    from: false,
    list_animation_speed: 1000,
    changeRegion: function (region) {
        this.region = region;
        this.placeSchool();
        this.reload('list');
    },
    changeDistrict: function (district) {
        this.district = district;
        this.placeSchool();
        this.reload('list');
    },
    changeWay: function (way) {
        this.way = way;
        this.placeSchool();
        $('#way-filter li.filter.active').removeClass('active');
        $('#way-filter-' + way).addClass('active');
        this.reload('list');
    },
    changeYear: function (year, from) {
        this.from = from;
        this.year = year;
        this.placeSchool();
        $('#year-filter li.filter.active').removeClass('active');
        $('#year-filter-' + year).addClass('active');
        this.reload('list');
    },
    _extendReloadParams: function (params) {
        var that = this;
        return $.extend(
            ListWidgetClass.prototype._extendReloadParams.apply(this, arguments),
            {
                "region": that.region,
                "district": that.district,
                "way": that.way,
                "year": that.year,
                "from": that.from
            }
        );
    },
    openItemView: function (element, accardion) {
        ListWidgetClass.prototype.openItemView.apply(this, arguments);
        if ($(element).is('.opened')) {
            $('div.btnHolder a', element).removeClass('close');
            $('div.btnHolder a', element).addClass('open');
        } else {
            $('div.btnHolder a', element).removeClass('open');
            $('div.btnHolder a', element).addClass('close');

        }
    },
    redraw: function () {
        var list = this;
        var toBind = $(".notbinded", $(list.elements.list));
        toBind.on('click', function (e) {
            if ($(e.target).is("input, textarea, select, button,img"))
                return;
            if ($(e.target).is("a")) {
                list.openItemView(this);
                return false;
            }
            list.openItemView(this);
        });
        toBind.removeClass('notbinded');
        $(list.elements.items).filter('.even').removeClass('even');
        $(list.elements.items).filter(':nth-child(even)').addClass('even');
        userVoice.updateSchoolList(school_table.items);
    },
    placeSchool: function () {
        var that = this;
        var url = _url('*/*/getschool', {
            'ajax': 1,
            "region": that.region,
            "district": that.district,
            "way": that.way,
            "year": that.year,
            "from": that.from
        });
        $.post(url,
            function (data) {
                for (var i = 0; i < data.length; i++) {
                    console.log(data.length);
                }
            }, 'json'
        );
    }
});

var school_table = new SchoolTableWidgetClass({
    item_template: '#school-item-template',
    empty_template: '#school-empty-line-template',
    pager_template: '#school-pager-template',
    pager_selector: '#school-pager',
    head_selector: '.school-list-header',
    list_selector: '#school-list',
    items_selector: '#school-list .school-line',
    limit_selector: '#limit',
    list_index: 'school_table'
});

$(function () {
    $("#region").change(function () {
        school_table.changeRegion(this.value);
    });
    $("#district").change(function () {
        school_table.changeDistrict(this.value);
    });
});
