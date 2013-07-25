// TODO:
// Clean code up (IE Split function)
// Write functionality to resolve enterances
// Write functionality to use sensor
// Recieve input from website/user



function handleRequest(){
	//Pull building data from DB, get lat and lng of possible enterances

	//Get current position
	var currentPos;
	if(navigator.geolocation) {
    	navigator.geolocation.getCurrentPosition(function(position) {
      	currentPos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);

      	// var infowindow = new google.maps.InfoWindow({
       //  	map: map,
       //  	position: pos,
       //  	content: 'Location found using HTML5.'
      	// });

      	map.setCenter(pos);
    	}, function() {
      		currentPos = handleNoGeolocation(true);
    	});
  	} else {
    	// Browser doesn't support Geolocation, so offer a possible known starting place.
    	currentPos = handleNoGeolocation(false);

  	}

	//find closest enterance
	var minInd=0;
	var minDist=1000000;
	for (var i = 0; i < enterances.length; i++){
		var currDist = google.maps.geometry.spherical.computeDistanceBetween(enterances[i],currentPos);
		if (currDist < minDist){
			minDist = currDist;
			minInd = i;
		}
	}
	var closest = enterances[minInd];

	//route to closest enterance

	var mapOptions = {
    	center: new google.maps.LatLng(42.73016, -73.67876),
        zoom: 17,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };
    // Initalized literal mapOptions to pass to the map element
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

    // Directions service uses route to route, takes in a literal. Example of literal is variable request
    var directionsService = new google.maps.DirectionsService();

    var directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);

	var request = {
      	origin: currentPos,
       	destination: closest,
       	travelMode: google.maps.TravelMode.WALKING,
    }
    directionsService.route(request, function(result, status) {
    	if (status == google.maps.DirectionsStatus.OK) {
      		directionsDisplay.setDirections(result);
    	}
  	});
}



// function initializeWithGeolocation() {
// 	// Great example for geolocation
//   var mapOptions = {
//     zoom: 6,
//     mapTypeId: google.maps.MapTypeId.ROADMAP
//   };
//   map = new google.maps.Map(document.getElementById('map-canvas'),
//       mapOptions);

//   // Try HTML5 geolocation
//   if(navigator.geolocation) {
//     navigator.geolocation.getCurrentPosition(function(position) {
//       var pos = new google.maps.LatLng(position.coords.latitude,
//                                        position.coords.longitude);

//       var infowindow = new google.maps.InfoWindow({
//         map: map,
//         position: pos,
//         content: 'Location found using HTML5.'
//       });

//       map.setCenter(pos);
//     }, function() {
//       handleNoGeolocation(true);
//     });
//   } else {
//     // Browser doesn't support Geolocation, so offer a possible known starting place.
//     handleNoGeolocation(false);
//   }
// }

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
  }
  // Could not find your position offer all buildings; let user choose starting point.
  return new google.maps.LatLng(42.73011, -73.68224);

}

 function initialize() {
 	// Template function to initalize map
    var mapOptions = {
    	center: new google.maps.LatLng(42.73016, -73.67876),
        zoom: 17,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };
    // Initalized literal mapOptions to pass to the map element
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

    // Directions service uses route to route, takes in a literal. Example of literal is variable request
    var directionsService = new google.maps.DirectionsService();

    var directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);

    var request = {
      	origin: new google.maps.LatLng(42.73011, -73.68224),
       	destination: new google.maps.LatLng(42.72834, -73.68052),
       	travelMode: google.maps.TravelMode.WALKING,
    }
    directionsService.route(request, function(result, status) {
    	// lambdas are fun!
    	if (status == google.maps.DirectionsStatus.OK) {
      		directionsDisplay.setDirections(result);
    	}
  	});
}