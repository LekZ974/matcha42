function callback(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    $.getJSON("https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&key=AIzaSyA_ZXYFc53naqHWpByr96LcH9yZUDF0YFY", function (data) {
        var country = data.results[0].address_components[5].long_name;
        var region = data.results[0].address_components[4].long_name;
        var city = data.results[0].address_components[2].long_name;
        var zipCode = data.results[0].address_components[6].long_name;

        $.ajax({
            url : "/updateLocation",
            type : "POST",
            data : { country : country,
                region : region,
                city : city,
                zipCode: zipCode,
                lat : lat,
                lon : lng
            },
            success : function(response){
            }
        });
    });
}

function erreur(error) {
    console.log('error');
    switch(error.code) {
        case error.PERMISSION_DENIED:
            console.log("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            console.log("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            console.log("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            console.log("An unknown error occurred.");
            break;
    }
}

$(document).on('click', '#locateUser', function () {

    if ( navigator.geolocation ) {
        if (navigator.geolocation.getCurrentPosition(callback, erreur, {enableHighAccuracy:true, maximumAge:600000, timeout:20000})){
        }
        else {
            $.ajax({
                url : "/updateLocation",
                type : "POST",
                data : {
                },
                success : function(response){
                }
            });
        }
    }
});

$(document).ready(function () {

    if ( navigator.geolocation ) {
        if (navigator.geolocation.getCurrentPosition(callback, erreur, {enableHighAccuracy:true, maximumAge:600000, timeout:20000})){
        }
        else {
            $.ajax({
                url : "/updateLocation",
                type : "POST",
                data : {
                },
                success : function(response){
                }
            });
        }
    }
});


function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(-33.8688, 151.2195),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById('map_canvas'),
        mapOptions);

    var input = document.getElementById('city_form_affinage');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            // Inform the user that the place was not found and return.
            input.className = 'notfound';
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        var image = new google.maps.MarkerImage(
            place.icon,
            new google.maps.Size(71, 71),
            new google.maps.Point(0, 0),
            new google.maps.Point(17, 34),
            new google.maps.Size(35, 35));
        marker.setIcon(image);
        marker.setPosition(place.geometry.location);
        var data = place.address_components.reverse();
        console.log(data);
        $.ajax({
            url: '/updateLocation',
            type: 'POST',
            data : {
                country : data[1].long_name,
                region : data[2].long_name,
                city : data[3].long_name,
                zipCode: data[0].long_name,
                lat : place.geometry.location.lat(),
                lon : place.geometry.location.lng()
            }
        });
    });
}
google.maps.event.addDomListener(window, 'load', initialize);