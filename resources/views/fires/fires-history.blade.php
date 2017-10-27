@extends("layouts.layout")

@section("content")
    <div class="row">
        @foreach($fires as $fire)
            <a href=""> <!-- LIEN POUR LES DETAILS DU FEU -->
                <div class="col-lg-3">
                    {{$fire->ID_CAPTEUR}}
                </div>
                <div class="col-lg-3">
                    {{$fire->AVG_TEMP}}
                </div>
                <div class="col-lg-3">
                    {{$fire->STARTING}}
                </div>
                <div class="col-lg-3">
                    {{($fire->ENDING)?($fire->ENDING):"FEU EN EVOLUTION"}}
                </div>
            </a>

        @endforeach
    </div>

@endsection