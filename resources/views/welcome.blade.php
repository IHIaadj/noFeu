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
    <button onclick="updateMarkers()">U</button>
    <button onclick="toggleHeatmap()">Toggle Heatmap</button>
    <button onclick="toggleSmokeMarkers()">Toggle Fire</button>
    <button onclick="toggleTemperatureMarkers()">Toggle Temperature</button>
    <button onclick="toggleHumidityMarkers()">Toggle Humidity</button>
    <button onclick="enableMarkers()">Markers</button>
</div>

<script title="Variables_Constants">

    //table issue de la BDD où chaque ligne représente un capteur et son état actuel
    var capteursTable = {!! json_encode($Capteurs_Join_Events_id) !!};

    var markersIconsUrls={
        blue:"{{asset('storage/data/circle-icons/circle-blue.png')}}",
        green:"{{asset('storage/data/circle-icons/circle-green.png')}}",
        grey:"{{asset('storage/data/circle-icons/circle-grey.png')}}",
        red:"{{asset('storage/data/circle-icons/circle-red.png')}}",
        orange:"{{asset('storage/data/circle-icons/circle-orange.png')}}"
    };

    var markerClusIconsUrl="{{asset('storage/data/clus-icons/m')}}";

    var heatMap;
    var heatMapData=[];
    var markersTable=[];
    var map;
    const UPDATE_INTERVAL=300000; //5 min
    var markerCluster;
    var locationsClus=[];

</script>

<script title="Initialize">

   function initialize()
    {
        createMap();
        createMarkers();
        createMarkerClus();
        createHeatMap();

    }

</script>

<script title="Marker_Functions">

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
                region : capteur["REGION"],
                temperature : 0,
                humidite :0,
                smoke : 0
            }


        });

        marker.addListener('click',function() {

            var smokeString='';
            if(this.capteur.smoke==0) smokeString="NON"; else smokeString="OUI";

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
        markersTable.display='';

    }



    /**
     * update the marker.capteur fields with the recent features in capteur
     * @param data represents a line in the EVENTS table in database
     */
    function  updateMarker(data) {

        markersTable.find(x=> x.capteur.id === data.ID_CAPTEUR).capteur.temperature = data.TEMPERATURE;
        markersTable.find(x=> x.capteur.id === data.ID_CAPTEUR).capteur.humidite = data.HUMIDITE;
        markersTable.find(x=> x.capteur.id === data.ID_CAPTEUR).capteur.smoke = data.SMOKE;
        markersTable.find(x=> x.capteur.id === data.ID_CAPTEUR).capteur.luminosite = data.LUMINOSITE;


    }

</script>

<script title="Map&Markers_Funcitons">

    //update the markersTable from the DataBase
    setInterval(updateMarkers, UPDATE_INTERVAL);

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
     * updating the markers fields in marker.capteur according to the last change in the database using Ajax
     */
    function updateMarkers(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        for (var i=0; i<capteursTable.length; i++){

            var capteurID = capteursTable[i]["id"];

            $.ajax({

                url : "/update",
                type : 'POST' ,
                dataType : 'JSON',
                data : {_token: CSRF_TOKEN, message:capteurID},

                success : function(data){
                    console.log(data);
                    updateMarker(data);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            updateMarkersDisplay(markersTable.display);
            updateHeatMapData();
            updateHeatMapDisplay();
        }

    }

</script>

<script title="MarkerClustrer_Functions">

    /**
     * Creates a markerClestrer to displaying the markersTable markers (in a compact way)
     * MarkersTable must be created before calling the function
     */
    function createMarkerClus(){
        markerCluster = new MarkerClusterer(map, markersTable, {
            imagePath: markerClusIconsUrl});
        //setting the calculator based on markers on fire number
        markerCluster.setCalculator(function(markers, numStyles) {
            var nbrMarkersOnFire=0,nbrMarkers=0;

            markers.forEach(function (marker) {
               if(marker.capteur.smoke==1) nbrMarkersOnFire++;
               nbrMarkers++;
            });

            //setting the index of the icon to choose (1==blue,2==yellow,3==red)
            var indexIcon =0;
            switch (nbrMarkersOnFire)
            {
                case 0 : indexIcon=1; break;
                case 1 : indexIcon=2; break;
                default: indexIcon=3; break;
            }

            return {
                text: nbrMarkersOnFire+'/'+nbrMarkers,
                index: indexIcon
            };
        });
    }

    /**
     * Clear markers from the markerClusterer
     */
    function clearMarkerClus(){
        markerCluster.clearMarkers();
    }

    /**
     * update the markers in the markerClusterer
     */
    function updateMarkerClus(){
        clearMarkerClus();
        createMarkerClus();
    }


</script>

<script title="HeatMap_Functions">

    /**
     * Creating the HeatMap from markersTable to show temperature in colors Based on Visualization Library
     * Should be called after creating markers
     */
    function createHeatMap()
    {
        if(markersTable.length!=0){

            //Creating the data of HeatMap as a MVCArray to bind the changings in updating
            heatMapData= new google.maps.MVCArray(markersTable.map(function(value,index) {
                var heatPoint = {
                    location : value.position,
                    weight : 1,
                    id : value.capteur.id,
                    displayed:'no'
                };

                return heatPoint;
            }));

            heatMap = new google.maps.visualization.HeatmapLayer({
                data: heatMapData,
                dissipating: true,
                radius : 50
            });
        }
        else{
            alert("MarkersMap not created");
        }

    }


    /**
     * Update the heatMapData weights (of heatPoints) according to the markersTable features
     */
    function updateHeatMapData() {

        //iterating the MVCArray and changing the weight according to the markersTable Temperature
        heatMapData.b.forEach(function (heatPoint) {
            heatPoint.weight=heatPointTemperature(markersTable.find(x=> x.capteur.id === heatPoint.id).capteur.temperature);
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

<script title="Display_Functions">

    /**
     * Toggle the Humidity Display Markers
     */
    function toggleHumidityMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.label=marker.capteur.humidite+' %';
            marker.icon.url=markersIconsUrls.blue;

        });

        updateMarkerClus();
        markersTable.display="humidityDisplayed";
    }

    /**
     * Toggle the Smoke Display Markers
     */
    function toggleSmokeMarkers()
    {
        markersTable.forEach(function (marker) {

            if(marker.capteur.smoke == 1) marker.icon.url=markersIconsUrls.red;
            else marker.icon.url=markersIconsUrls.grey;
            marker.label=marker.capteur.temperature+" °C";

        });

        updateMarkerClus();
        markersTable.display="smokeDisplayed";

    }

    /**
     * Toggle the Temperature Display Markers
     */
    function toggleTemperatureMarkers()
    {
        markersTable.forEach(function (marker) {
            marker.label=marker.capteur.temperature+" °C";
            if(marker.capteur.temperature>60) marker.icon.url=markersIconsUrls.red;
            else if(marker.capteur.temperature>45) marker.icon.url=markersIconsUrls.orange;
            else marker.icon.url=markersIconsUrls.green;


        });

        updateMarkerClus();
        markersTable.display="temperatureDisplayed";
    }


    /**
     * Enable/Disable Markers on the map
     */
    function enableMarkers(){
        markerCluster.setMap(null);
        console.log(markerCluster.getMap());
        markersTable.display='';
    }

    /**
     * Toggle HeatMap : a map showing temperature in colors (red,yellow,green) using the Visualization Library.
     */
    function toggleHeatmap() {
        heatMap.setMap(heatMap.getMap() ? null : map);
        if(heatMap.getMap() == null)heatMap.displayed='no'; else heatMap.displayed='yes';
    }

    /**
     * Create a default icon for a marker with grey cercle
     * @returns {   {url: string, size: google.maps.Size, origin: Point, anchor: Point} } the Icon
     */
    function createDefaultIcon()
    {
        return  {
            url: markersIconsUrls.grey,
            size: new google.maps.Size(36, 36),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(18, 18)
        };

    }

    /**
     * updating the display mode on the map (humidity,temperature,smoke) changing icons and labels
     * By calling the appropriate function according to the current display
     * @param markerDisplayed can be one of these strings (temperatureDisplayed,smokeDisplayed,humidityDisplayed)
     */
    function updateMarkersDisplay(markerDisplayed) {

        switch (markerDisplayed){
            case "temperatureDisplayed": toggleTemperatureMarkers();break;
            case "smokeDisplayed":toggleSmokeMarkers();break;
            case "humidityDisplayed":toggleHumidityMarkers();break;
        }

    }

    /**
     * update the display of heatMap from Visualization Library
     */
    function updateHeatMapDisplay(){
        if(heatMap.displayed=='yes'){
            toggleHeatmap();
            toggleHeatmap();
        }
    }


</script>


<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>

<script async defer title="Google_Map_API"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3R7bzKOXmqJWdiKgbhtfa_DMQQ3PL1Oo&libraries=visualization&callback=initialize">
</script>

<script title="Ajax_Library"
        src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">

</script>


<meta name="csrf-token" content="{{ csrf_token() }}" />

</body>
</html>
