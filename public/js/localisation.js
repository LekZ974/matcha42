    $(document).on('click', '#locateUser', function () {

    console.log('toto');
    if ( navigator.geolocation ) {
        console.log('tutu');
        navigator.geolocation.getCurrentPosition(callback, erreur);

        function callback(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            console.log(lat, lng);
            $.getJSON("https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&key=AIzaSyA_ZXYFc53naqHWpByr96LcH9yZUDF0YFY", function (data) {
                var country = data.results[0].address_components[5].long_name;
                console.log(country);
                var region = data.results[0].address_components[4].long_name;
                console.log(region);
                var city = data.results[0].address_components[2].long_name;
                console.log(city);

                $.ajax({
                    url : "/updateLocation",
                    type : "POST",
                    data : { country : country,
                        region : region,
                        city : city,
                        lat : lat,
                        lon : lng
                    },
                    success : function(response){
                        // faire qqchavec la réponse du serveur
                    }
                });
            });
        }
    }
    else {
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
            // alternative();
        }
    }
    //     // On demande d'envoyer la position courante à la fonction callback
    //     navigator.geolocation.getCurrentPosition(callback, erreur);
    //
    //     function callback( position ) {

    //         console.log(lat);
    //         console.log(lng);
    //         $.getJSON("https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&key=AIzaSyA_ZXYFc53naqHWpByr96LcH9yZUDF0YFY", function (data) {
    //             var region = data.results[0].address_components[6].long_name;
    //             console.log(region);
    //             var city = data.results[0].address_components[2].long_name;
    //             console.log(city);
    //
    //         });
    //
    //         $.ajax({
    //             url : "/updateLocation",
    //             type : "POST",
    //             data : { lat : lat,
    //                 lon : lng
    //             },
    //             success : function(response){
    //                 // faire qqchavec la réponse du serveur
    //             }
    //         });
    //     }
    // } else {
    //     function erreur(error) {
    //         switch(error.code) {
    //             case error.PERMISSION_DENIED:
    //                 console.log("User denied the request for Geolocation.");
    //                 break;
    //             case error.POSITION_UNAVAILABLE:
    //                 console.log("Location information is unavailable.");
    //                 break;
    //             case error.TIMEOUT:
    //                 console.log("The request to get user location timed out.");
    //                 break;
    //             case error.UNKNOWN_ERROR:
    //                 console.log("An unknown error occurred.");
    //                 break;
    //         }
    //         // alternative();
    //     }
    // }
});