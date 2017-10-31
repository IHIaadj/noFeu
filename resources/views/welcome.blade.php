<!DOCTYPE html>
<html>
<head>
    <style>

        #map {
            height: 100%;
            width: 85%;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #voletMenu {
            background:darkgray;
            color: black;
            height: 100%;
            width:15%;

        }
        #floating-panel {
            position: absolute;
            top: 10px;
            left: 25%;
            z-index: 5;
            background-color: #fff;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto','sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }


    </style>
</head>
<body>

<div id="map" style="float: left;"></div>
<div id="voletMenu" style="float: right;">Volet Menu</div>
<div id="floating-panel">
    <button onclick="toggleHeatmap()">Toggle Heatmap</button>
    <button onclick="toggleSmokeMarkers()">Toggle Smoke</button>
    <button onclick="toggleTemperatureMarkers()">Toggle Temperature</button>
    <button onclick="toggleHumidityMarkers()">Toggle Humidity</button>
    <button onclick="enableMarkers()">Markers</button>
</div>

<script title="Initialize">

    var map;
    var heatMap;
    var markersTable=[];

    //table issue de la BDD où chaque ligne représente un capteur et son état actuel
    var capteursTable = {!! json_encode($Capteurs_Join_Events_id) !!};

    function initialize()
    {
        createMap();
        createMarkers();
        createHeatMap();
    }



</script>

<script async defer title="Google_Map_API"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3R7bzKOXmqJWdiKgbhtfa_DMQQ3PL1Oo&libraries=visualization&callback=initialize">
</script>

<script title="Marker_Functions">
    /**
     * Toggle the Humidity Markers
     */
    function toggleHumidityMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.title=marker.title+'\n Humidity'; // will be changed to the icon marker
            marker.setMap(map);
        });
    }

    /**
     * Toggle the Smoke Markers
     */
    function toggleSmokeMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.title=marker.title+'\n Smoke'; // will be changed to the icon marker
            marker.setMap(map);
        });

    }

    /**
     * Toggle the Temperature Markers
     */
    function toggleTemperatureMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.title=marker.title+'\n Temperature';
            marker.setMap(map);
        });

    }


    /**
     * Enable/Disable Markers on the map
     */
    function enableMarkers(){
        markersTable.forEach(function (marker) {
            marker.setMap(marker.getMap() ? null : map);
        })
    }

    /**
     * Toggle HeatMap : a map showing temperature in colors (red,yellow,green) using the Visualization Library.
     */
    function toggleHeatmap() {
        heatMap.setMap(heatmap.getMap() ? null : map);
    }

    /**
     * Create a marker representing the variable "capteur" and add it to the markersTable (global variable)
     * @param capteur représente un capteur avec tous ses états actuels
     */
    function createMarker(capteur) {

        var marker = new google.maps.Marker({
            position: {lat: capteur["LAT"], lng: capteur["LON"]},
            opacity:0.5,
            title :'ID : '+capteur["id"]+'\nRegion : '+capteur["REGION"]

        });

        marker.addListener('click',function() {

            var infoWindowMarker =  new google.maps.InfoWindow({
                content: 'ID : '+capteur["id"]+
                '<br>Region : '+capteur["REGION"]+
                '<br>Temperature : '+capteur["TEMPERATURE"]+
                '<br>Humidité : '+capteur["HUMIDITE"]+
                '<br>Fumée : '+capteur["SMOKE"]
            });

            infoWindowMarker.open(map,this);

        });

        markersTable.push(marker);


    }


</script>

<script title="Map&Markers_Funcitons">

    /**
     * Initialize the map according to Google Map API JavaScript Models
     * This function is called once data is loaded at the beginning according to the callback specification
     */
    function createMap()
    {
        var mapCenter= new google.maps.LatLng(34.946,3.244);

        //création de la map
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: mapCenter,
            mapTypeId: 'terrain'
        });
    }

    /**
     * Creating the markers which will be shown on a map
     */
    function createMarkers()
    {
        //pour chaque ligne du capteur on ajoute un marker pour notre map
        capteursTable.forEach(function (capteur) {
            //création d'un capteur
            createMarker(capteur);
        });
    }

    /**
     * Creating the HeatMap to show temperature in colors Based on Visualization Library
     */
    function createHeatMap()
    {
        //HeatMapData containing (location,weight)
        var heatMapData=[];

        //Populating HeatMapData with Heatpoints(location,weight)
        capteursTable.forEach(function (capteur) {
            var heatPoint= {
                location : new google.maps.LatLng(capteur['LAT'],capteur['LON']),
                weight : heatPointTemperature(capteur['TEMPERATURE'])
            };

        heatMapData.push(heatPoint);
        });

        //Setting the HeatMap on the Map
        heatMap = new google.maps.visualization.HeatmapLayer({
            data: heatMapData,//markersTable.map(function(value,index) { return value['position']; }),
            map: map,
            dissipating: true,
            radius : 50
        });

        //A local function used to determine the weight of HeatPoints
        function heatPointTemperature(temperature) {
            var toReturn=0;
            if(temperature>55) toReturn=5;
            else toReturn=0.5;
            return toReturn;
        }

    }


</script>
</body>
</html>
