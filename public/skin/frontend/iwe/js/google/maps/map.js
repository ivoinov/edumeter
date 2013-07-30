/**
 * Created with JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/13/13
 * Time: 6:40 PM
 * To change this template use File | Settings | File Templates.
 */
var map = {
    map: null,
    marker: [],
    geocoder: this.geocoder,
    default_position: null,
    current_position: null,
    current_position_marker: null,
    current_postition_city: null,
    update_address_line: false,
    schools : [],
    visibleSchoolIds: [],
    radius : null,
    currentRadiusValue: 0,
    infoBubble: null,
    way: '2',
    year: 2012,
    from: false,
    init: function(options) {
        this.map = new google.maps.Map(document.getElementById("map_canvas"), options);
        this.geocoder = new google.maps.Geocoder();
        if (navigator.geolocation) {
            window.navigator.geolocation.getCurrentPosition(this.geolocationCallback.bind(this), this.setDefaultPosition.bind(this));
        } else {
            this.setDefaultPosition();
        }
        this._createShowHideMapControll();
    },
    setSearchAddress: function(searchAddress)
    {
        this.search_address = searchAddress;
    },
    setSchools: function(schools)
    {
        this.schools = schools;
    },
    getSchools: function()
    {
        return this.schools;
    },
    setYear: function(year)
    {
        this.year = year;
    },
    setWay: function(way)
    {
        this.way = way;
    },
    search: function(location) {
        this.update_address_line = false;
        this.setLocationByAddress(location);
        return false;
    },
    setInfoBuble: function(infoBuble) {
      this.infoBubble = infoBuble;
    },
    setDefaultPosition: function() {
        this.setLocationByAddress("Киев");
    },
    setLocationByAddress: function(address) {
        this.updateLocation(address, this.setCurrentPosition.bind(this));
    },
    geolocationCallback: function(position) {
        this.setCurrentPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
    },
    dragableCallback: function(location) {
        this.update_address_line = true;
        this.setCurrentPosition(location);
    },
    setCurrentPosition: function(location, error) {
        if(error) {
            $("#address-error").show('slow');
            return;
        } else {
            $("#address-error").hide('slow');
        }
        if(this.default_position == null)
            this.default_position = location;
        this.map.setCenter(location);
        this._moveCurrentPositionMarker(location);
        this._setupAddressBox(location);
        this.changeRadius(this.currentRadiusValue);
        this.current_position = location;
        school_list.reload('list');
    },
    _moveCurrentPositionMarker: function(location) {
        var that = this;
        if(!this.current_position_marker) {
            this.current_position_marker = new google.maps.Marker({map: this.map, 'draggable': true});
            google.maps.event.addListener(this.current_position_marker, 'dragend', function() {
                that.dragableCallback(that.current_position_marker.position);
            });
        }
        this.current_position_marker.setPosition(location);
    },
    _setupAddressBox: function(location) {
        var that = this;
        this.geocoder.geocode({'latLng': location}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if(results.length) {
                    var city = (results[0].address_components[3].short_name == "UA") ? "" : results[0].address_components[2].short_name;
                    var adress = results[0].address_components[1].short_name+" "+results[0].address_components[0].short_name;
                    that._placeScool(location);
                }
            }
        });
    },
    updateLocation: function(address, callback) {
        var that = this;
        if(!address) {
            $("#address-error").show('slow');
            return;
        } else {
            $("#address-error").hide('slow');
        }
        this.geocoder.geocode({'address': address}, function(results, status) {
            if(results.length) {
                callback(results[0].geometry.location);
            } else if(!results || results.length == 0){
                callback(that.default_position, true);
            }
        });
    },
    _deleteMarkers: function() {
        if (this.marker.length) {
            for (i in this.marker) {
                this.marker[i].setMap(null);
            }
        }
        this.marker.splice(0,this.marker.length);
        this.visibleSchoolIds.splice(0,this.visibleSchoolIds.length);
    },
    _placeScool: function(location) {
        this._deleteMarkers();
        var mapRadius = this._getMapRadius();
        var that = this;
        var url = _url('*/*/getschool',{
            'ajax':1,
            'way': that.way,
            'year': that.year,
            'from': that.from,
            'longitude': that.current_position.lng(),
            'latitude': that.current_position.lat(),
            'viewableRadius': mapRadius
        })
        $.post(url,
            function(data) {
                for(var i = 0; i < data.length; i++) {
                        var location = new google.maps.LatLng(data[i].latitude, data[i].longitude);
                        if(that.map.getBounds().contains(location))
                        {
                            var markers = new google.maps.Marker({
                                position: location,
                                animation: google.maps.Animation.DROP,
                                map: that.map,
                                icon: data[i].icon,
                                title: data[i].title
                            });
                            that._bindEventOnMarker(markers, data[i]);
                            that.marker.push(markers);
                            if( that.radius.getBounds().contains(markers.getPosition()) &&
                                that.visibleSchoolIds.indexOf(data[i].id) == -1 )
                                that.visibleSchoolIds.push(data[i].id);
                        }
                }
            }, 'json'
        );
    },
    _getMapRadius: function() {
        var bounds = this.map.getBounds();

        var center = bounds.getCenter();
        var ne = bounds.getNorthEast();

        var r = 6372795;

        var lat1 = center.lat() / 57.2958;
        var lon1 = center.lng() / 57.2958;
        var lat2 = ne.lat() / 57.2958;
        var lon2 = ne.lng() / 57.2958;

        var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) +
            Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1));

        return dis;
    },
    _bindEventOnMarker: function(marker, item) {
        var that = this;
        google.maps.event.addListener(marker, 'click', function() {
            that.infoBubble.setContent($('<div class="school-infowindow-content"></div>').wrapInner($("#infowindow-template").tmpl(item)).html());
            that.infoBubble.open(this.map,marker);
        });
        google.maps.event.addListener(this.map, 'click', function() {
            if(that.infoBubble.isOpen())
                that.infoBubble.close();
        });
        google.maps.event.addListener(this.radius, 'click', function() {
            if(that.infoBubble.isOpen())
                that.infoBubble.close();
        });
    },
    _radius: function(radiusValue)
    {
        var that = this;
        this.radius = new google.maps.Circle({
            map: this.map,
            draggable:false,
            radius: radiusValue,
            fillColor: '#0055aa',
            fillOpacity: 0.1,
            strokeColor: '#607B8B',
            strokeWeight: 3
        });
        this.radius.bindTo('center', this.current_position_marker, 'position');
        google.maps.event.addListener(this.radius, 'radius_changed', function() {
            that._setupAddressBox(that.current_position);
        });
    },
    changeRadius: function(radiusValue)
    {
        if(this.currentRadiusValue != 0)
            this.currentRadiusValue = radiusValue;
        else
            this.currentRadiusValue = 500;
        if(!this.radius)
            this._radius(this.currentRadiusValue);
        else
            this.radius.setRadius(this.currentRadiusValue);
        this.map.fitBounds(this.radius.getBounds());
    },
    _createShowHideMapControll: function() {
        var hideShowButton = document.createElement('div');
        var hideShowControl = new this.showHideMap(hideShowButton,this);

        hideShowButton.index = 1;
        this.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(hideShowButton);
    },
    showHideMap: function(controlDiv,that) {
        controlDiv.style.padding = '7px';
        var controlUI = document.getElementById('map_show_control');
        controlUI.style.cursor = 'pointer';
        controlUI.title = 'Скрыть/Показать список школ';
        controlDiv.appendChild(controlUI);

        google.maps.event.addDomListener(controlUI, 'click', function() {
            if($('div.school-list').css('display') == 'none') {
                $('#map_canvas').css('height','65%');
                $('div.school-list').toggle('slow');
                $('#map_show_control a').removeClass('hide');
                $('#map_show_control a').addClass('show');
            } else {
                $('#map_canvas').css('height','95%');
                $('div.school-list').toggle('slow');
                $('#map_show_control a').removeClass('show');
                $('#map_show_control a').addClass('hide');
            }
            google.maps.event.trigger(that.map, 'resize');
            that.map.fitBounds(that.radius.getBounds());
        });
    }
}
$(function(){
    map.init({
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(48,32),
        scrollwheel: false
    })
    map.setInfoBuble(new InfoBubble({
        map: map.map,
        shadowStyle: 1,
        padding: 0,
        backgroundColor: 'rgb(14,14,14)',
        borderRadius: 10,
        borderWidth: 0,
        hideCloseButton: true,
        minHeight: '80'
    }) );
});
