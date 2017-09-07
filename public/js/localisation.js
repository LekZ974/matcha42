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

$(document).ready(function () {

    function getLocation() {
        if ( navigator.geolocation ) {
            navigator.geolocation.getCurrentPosition(callback, erreur);
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


    var isLocate = $("#isLocated").text();
    if (isLocate == 0)
        getLocation();

    $('body').on('click', '#locateUser', function () {

        getLocation();
        location.reload(true);
    });
});