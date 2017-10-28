@extends("layouts.layout")

@section("content")
    <h1>Historique des feux</h1>
    <div id="recherche">
        <form id="search-form" action="{{route('ajaxFiresFilter')}}" method="post">
            {{csrf_field()}}
            <div class="form-group form-inline">
                <label>
                    Recherche par
                    <label><input id="search-reg " class="form-control rechT" type="checkbox" name="typeRech[]" value="region"> Région     </label>
                    <label><input id="search-temp" class="form-control rechT" type="checkbox" name="typeRech[]" value="temp"  > Température</label>
                    <label><input id="search-date" class="form-control rechT" type="checkbox" name="typeRech[]" value="date"  > Date       </label>
                </label>
            </div>
            <!--REGION/CAPTEUR PART-->
            <div class="form-group form-inline">
                <label class="control-label" for="region">Région</label>
                <select class="form-control r-region" name="region" id="region" disabled>
                    <option value="0" selected>Toutes</option>
                    @foreach($regions as $region)
                        <option value="{{$region->REGION}}">{{$region->REGION}}</option>
                    @endforeach
                </select>
                <label for="capteur">Identifiant du capteur</label>
                <input name="capteur" class="form-control r-capteur" id="capteur" type="number" disabled>
            </div>

            <!--TEMPERATURE PART-->
            <div class="form-group form-inline">
                <label for="">TEMPERATURE</label>
                <select class="form-control r-temperature" name="operation" id="operation-compare" disabled>
                    <option value=">">&gt;</option>
                    <option value="<">&lt;</option>
                    <option value="=">=</option>
                </select>
                <input id="temperature-compare" name="temperature" class="form-control r-temperature" type="number" disabled>
            </div>

            <!--TIME PART-->
            <div class="form-group form-inline">
                <label for="">Feux </label>
                <select class="form-control r-date" name="operation-date" id="compare-date" disabled>
                    <option value="avant">Avant</option>
                    <option value="apres">Après</option>
                    <option id="entre" value="entre">Entre</option>
                </select>
                <input id="beg-time" name="beg-time" class="form-control r-date" type="datetime-local" disabled>
                <input id="end-time" name="end-time" class="form-control r-date" type="datetime-local" disabled>
            </div>

        </form>
        <button onclick="refreshSearch()">Click here</button>
    </div>
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>ID DU CAPTEUR</th>
                <th>REGION</th>
                <th>TEMPERATURE MOYENNE</th>
                <th>DEBUT DU FEU</th>
                <th>FIN DU FEU</th>
                <th>DETAILS</th>
            </tr>
        </thead>
        <tbody id="fires-list">
        @php
            $parity=false;
        @endphp
        @foreach($fires as $fire)
            @php
                $parity=!$parity;
                //TROUVER LE NOM DE LA REGION
                $region="CAPTEUR INEXISTANT";
                $capteur=\App\Capteur::find($fire->ID_CAPTEUR);
                if($capteur)
                    $region=$capteur->REGION;
            @endphp
            <!-- LIEN POUR LES DETAILS DU FEU -->
            <tr class="{{$parity? "btn-primary": ""}}">
                <th scope="row">{{$fire->ID_CAPTEUR}}</th>
                <td>{{$region}}</td>
                <td>{{$fire->AVG_TEMP}}</td>
                <td>{{$fire->STARTING}}</td>
                <td>{{($fire->ENDING)?($fire->ENDING):"FEU EN EVOLUTION"}}</td>
                <td style="position:relative;">
                    <a href="" class="btn btn-info"><i class="glyphicon glyphicon-book"></i></a>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
    <script>
        //Pour le choix du type de la recherche
        $(".rechT").on("change",function () {
            var state=!$(this).prop("checked");
            switch($(this).val()){
                case "region":
                    $(".r-region").prop("disabled",state);
                    $(".r-capteur").prop("disabled",state);
                    break;
                case "temp":
                    $(".r-temperature").prop("disabled",state);
                    break;
                case "date":
                    $(".r-date").prop("disabled",state);
                    if(!state)
                        $("#end-time").prop("disabled",$("#compare-date").val()!=="entre");
                    break;
            }
        });
        //Pour la date entre deux dates
        $("#compare-date").on("change",function (){
            var state=$(this).val();
            $("#end-time").prop("disabled",state!=="entre");
        });
        function refreshSearch(){
            $("#search-form").ajaxSubmit({
                url: '{{route('ajaxFiresFilter')}}',
                type: 'post',
                success: function(data) {
                    $("#fires-list").html(data);
                }
            });
        }
         $("#region").on("change",refreshSearch);
         $("#capteur").on("input",refreshSearch);

         $("#operation-compare").on("change",refreshSearch);
         $("#temperature-compare").on("input",refreshSearch);

         $("#compare-date").on("change",refreshSearch);
         $("#beg-time").on("input",refreshSearch);
         $("#end-time").on("input",refreshSearch);
    </script>
@endsection
