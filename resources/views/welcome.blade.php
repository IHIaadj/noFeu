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
    <button onclick="toggleSmokeMarkers()">Toggle Fire</button>
    <button onclick="toggleTemperatureMarkers()">Toggle Temperature</button>
    <button onclick="toggleHumidityMarkers()">Toggle Humidity</button>
    <button onclick="enableMarkers()">Markers</button>
</div>

<script title="Initialize">


    //table issue de la BDD où chaque ligne représente un capteur et son état actuel
    var capteursTable = {!! json_encode($Capteurs_Join_Events_id) !!};

    function initialize()
    {
        createMap();
        createMarkers();
        createHeatMap();
    }



</script>

<script title="Marker_Functions">

    var iconsUrls={
        blue:"{{asset('storage/data/circle-icons/circle-blue.png')}}",
        green:"{{asset('storage/data/circle-icons/circle-green.png')}}",
        grey:"{{asset('storage/data/circle-icons/circle-grey.png')}}",
        red:"{{asset('storage/data/circle-icons/circle-red.png')}}",
        orange:"{{asset('storage/data/circle-icons/circle-orange.png')}}"
    };

    var heatMap;
    var markersTable=[];

    /**
     * Toggle the Humidity Markers
     */
    function toggleHumidityMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.label=marker.capteur.humidite+' %';
            marker.icon.url=iconsUrls.blue;
            marker.setMap(map);
        });
    }

    /**
     * Toggle the Smoke Markers
     */
    function toggleSmokeMarkers()
    {
        markersTable.forEach(function (marker) {

            if(marker.capteur.smoke == 1) marker.icon.url=iconsUrls.red;
            else marker.icon.url=iconsUrls.grey;

            marker.label=marker.capteur.temperature+" °C";
            marker.setMap(map);
        });

    }

    /**
     * Toggle the Temperature Markers
     */
    function toggleTemperatureMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.label=marker.capteur.temperature+" °C";

            if(marker.capteur.temperature>60) marker.icon.url=iconsUrls.red;
            else if(marker.capteur.temperature>45) marker.icon.url=iconsUrls.orange;
            else marker.icon.url=iconsUrls.green;

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
        heatMap.setMap(heatMap.getMap() ? null : map);
    }

    /**
     * Create a marker representing the variable "capteur" and add it to the markersTable (global variable)
     * @param capteur représente un capteur avec tous ses états actuels
     */
    function createMarker(capteur) {

        var marker = new google.maps.Marker({
            position: {lat: capteur["LAT"], lng: capteur["LON"]},
            opacity:0.8,
            title :'ID : '+capteur["id"]+'\nRegion : '+capteur["REGION"],
            label : capteur["id"].toString(),
            icon : createDefaultIcon(),
            capteur : {
                id : capteur["id"],
                region :capteur["REGION"],
                temperature : capteur["TEMPERATURE"],
                humidite :capteur["HUMIDITE"],
                smoke :capteur["SMOKE"]
            }
        });

        marker.addListener('click',function() {

            var smokeString='';
            if(this.capteur.smoke==1) smokeString="OUI"; else smokeString="NON";

            var infoWindowMarker =  new google.maps.InfoWindow({
                content: 'ID : '+this.capteur.id+
                '<br>Region : '+this.capteur.region+
                '<br>Temperature : '+this.capteur.temperature+' °C'+
                '<br>Humidité : '+this.capteur.humidite+' %'+
                '<br>Fumée : '+smokeString+
                '<br>Position : '+this.position
            });

            infoWindowMarker.open(map,this);

        });

        markersTable.push(marker);


    }

    /**
     * Create a default icon for a marker with grey cercle
     * @returns {   {url: string, size: google.maps.Size, origin: Point, anchor: Point} } the Icon
    */
    function createDefaultIcon()
    {
        return  {
            url: iconsUrls.grey,
            size: new google.maps.Size(36, 36),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(18, 18)
        };

    }


</script>

<script title="Map&Markers_Funcitons">

    var map;


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
        //pour chaque ligne du capteur on ajoute un marker pour map
        capteursTable.forEach(function (capteur) {
            //création d'un capteur
            createMarker(capteur);
        });
    }

    /**
     * Creating the HeatMap from markersTable to show temperature in colors Based on Visualization Library
     * Should be called after creating markers
     */
    function createHeatMap()
    {
        if(markersTable.length!=0){
            heatMap = new google.maps.visualization.HeatmapLayer({
                data: markersTable.map(function(value,index) {
                    var heatPoint = {
                        location : value.position,
                        weight : heatPointTemperature(value.capteur.temperature)
                    };

                    return heatPoint;
                }),

                dissipating: true,
                radius : 50
            });
        }
        else{
            alert("MarkersMap not created");
        }

        //A local function used to determine the weight of HeatPoints
        function heatPointTemperature(temperature) {
            var toReturn=0;
            if(temperature>55) toReturn=5;
            else toReturn=0.5;
            return toReturn;
        }

    }


</script>

<script async defer title="Google_Map_API"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3R7bzKOXmqJWdiKgbhtfa_DMQQ3PL1Oo&libraries=visualization&callback=initialize">
</script>

</body>
</html>
