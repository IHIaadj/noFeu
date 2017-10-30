<!DOCTYPE html>
<html>
  <head>
    <style>
       #map {
        height: 500px;
        width: 80%;
		margin: 0 auto;
       }
    </style>
  </head>
  <body>
    <h3 style="text-align:center" >Map</h3>
    <div id="map"></div>

    <script>
      	function initMap() 
		{ 
		    var uluru = {lat: 29.245869, lng:  3.546264};
		    var map = new google.maps.Map(document.getElementById('map'), {
		      zoom: 5,
		      center: uluru
		    });
      	
		
			var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			var markers = locations.map(function(location, i)
			{
		      	return new google.maps.Marker({position: location,label: labels[i % labels.length]});
			});

			
			var markerCluster = new MarkerClusterer(map, markers,
            {
				imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
			});

		}

		var locations = 
			[ 
				{lat: 36.705685, lng: 3.172000},
				{lat: 36.761030, lng: 3.012398},
				{lat: 36.768506, lng: 3.057334},
				{lat: 36.703744, lng: 3.172047}
		  	]
			
    </script>

	<script>

      /*function initMap() {

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 3,
          center: {lat: -28.024, lng: 140.887}
        });

        // Create an array of alphabetical characters used to label the markers.
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        var markers = locations.map(function(location, i) {
          return new google.maps.Marker({
            position: location,
            label: labels[i % labels.length]
          });
        });

        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      }
      var locations = [
        {lat: -31.563910, lng: 147.154312},
        {lat: -33.718234, lng: 150.363181},
        {lat: -33.727111, lng: 150.371124},
        {lat: -33.848588, lng: 151.209834},
        {lat: -33.851702, lng: 151.216968},
        {lat: -34.671264, lng: 150.863657},
        {lat: -35.304724, lng: 148.662905},
        {lat: -36.817685, lng: 175.699196},
        {lat: -36.828611, lng: 175.790222},
        {lat: -37.750000, lng: 145.116667},
        {lat: -37.759859, lng: 145.128708},
        {lat: -37.765015, lng: 145.133858},
        {lat: -37.770104, lng: 145.143299},
        {lat: -37.773700, lng: 145.145187},
        {lat: -37.774785, lng: 145.137978},
        {lat: -37.819616, lng: 144.968119},
        {lat: -38.330766, lng: 144.695692},
        {lat: -39.927193, lng: 175.053218},
        {lat: -41.330162, lng: 174.865694},
        {lat: -42.734358, lng: 147.439506},
        {lat: -42.734358, lng: 147.501315},
        {lat: -42.735258, lng: 147.438000},
        {lat: -43.999792, lng: 170.463352}
      ]*/
    </script>

	<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key= AIzaSyBU3FMQyb0PBoypMlilC7IAus3WMolTsFw &callback=initMap">
    </script>
  </body>
</html>
