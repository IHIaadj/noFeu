<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use App\Capteur;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Redirect;

class CapteurController extends Controller
{
    //
    public function index(){

        $Capteurs_Join_Events_id= Capteur::all();
        return view("welcome" , ["Capteurs_Join_Events_id" => $Capteurs_Join_Events_id ]);
    }

    public function updateMarkers(Request $request){

        $result = Event::where("ID_CAPTEUR", "=", $request->message)->get()->last();

        $response = array(
            'status' => 'success',
            'TEMPERATURE' => $result["TEMPERATURE"],
            'ID_CAPTEUR' => $result["ID_CAPTEUR"],
            'HUMIDITE' => $result["HUMIDITE"],
            'SMOKE' => $result["SMOKE"],
            'LUMINOSITE' => $result["LUMINOSITE"],
        );
        return response()->json($response);

    }
}
