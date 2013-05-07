/**
 * Created with JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/17/13
 * Time: 1:37 AM
 * To change this template use File | Settings | File Templates.
 */
/**
 * Created with JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/13/13
 * Time: 6:40 PM
 * To change this template use File | Settings | File Templates.
 */
var infoBubble = new InfoBubble({
    map: map,
    shadowStyle: 1,
    padding: 0,
    backgroundColor: 'rgb(14,14,14)',
    borderRadius: 10,
    borderWidth: 0,
    hideCloseButton: true
});

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
    way: '2',
    year: 2012,
    init: function(options) {
        this.map = new google.maps.Map(document.getElementById("map_canvas"), options);
        this.geocoder = new google.maps.Geocoder();
        if (navigator.geolocation) {
            window.navigator.geolocation.getCurrentPosition(this.geolocationCallback.bind(this), this.setDefaultPosition.bind(this));
        } else {
            this.setDefaultPosition();
        }
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
        var that = this;
        var url = _url('*/*/getschool',{'ajax':1,'way': that.way,'year': that.year })
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
    _bindEventOnMarker: function(marker, item) {
        google.maps.event.addListener(marker, 'click', function() {
            infoBubble.setContent($('<div class="school-infowindow-content"></div>').wrapInner($("#infowindow-template").tmpl(item)).html());
            infoBubble.open(this.map,marker);
        });
        google.maps.event.addListener(this.map, 'click', function() {
            if(infoBubble.isOpen())
                infoBubble.close();
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
            fillOpacity: 0.2
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
    }
}
$(function(){
    map.init({
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(48,32)
    })

});
