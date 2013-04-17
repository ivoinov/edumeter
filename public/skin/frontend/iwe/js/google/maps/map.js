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
        this.current_position = location;
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
                    var city = (results[0].address_components[3].short_name == "UA") ? "" : results[0].address_components[3].short_name;
                    var adress = results[0].address_components[1].short_name+" "+results[0].address_components[0].short_name;
//                    var infowindow = document.getElementById('map-info');
//                    var adressForInput = "г. "+city+", "+adress;
//                    infowindow.innerHTML = 'Ваш адрес: ' + adress + '<br />Ваш город: '+ city;
                    that._placeScool(location, city);
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
    },
    _placeScool: function(location, city) {
        this._deleteMarkers();
        var that = this;
        var url = _url('*/*/getschool',{'ajax':1,'city' :city})
        $.post(url,
            function(data) {
                for(var i = 0; i < data.length; i++) {
                    var location = new google.maps.LatLng(data[i].latitude, data[i].longitude);
//                    var image = new google.maps.MarkerImage(
//                        _url('*/image/index', {'infoOnMarker': data[i].rate}),
//                        new google.maps.Size(100,100),
//                        new google.maps.Point(0,0),
//                        new google.maps.Point(12,26)
//                    );
                    var markers = new google.maps.Marker({
                        position: location,
                        animation: google.maps.Animation.DROP,
                        map: that.map,
                        //icon: image,
                        title: data[i].title
                    });
                    that._bindEventOnMarker(markers, data[i]);
                    that.marker.push(markers);
                }
            }, 'json');
    },
    _bindEventOnMarker: function(marker, item) {
        var infowindow = new google.maps.InfoWindow({
            content: $('<div></div>').wrapInner($("#infowindow-template").tmpl(item)).html()
        });
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(this.map, marker);
        });
    }
}
$(function(){
    map.init({
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(48,32)
    })

});


