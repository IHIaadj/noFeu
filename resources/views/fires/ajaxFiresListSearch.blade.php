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