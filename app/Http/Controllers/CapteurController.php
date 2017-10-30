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

        $Capteurs_Join_Events_id= DB::table('T_CAPTEURS')->join('T_EVENTS', 'T_EVENTS.ID_CAPTEUR' , '=', 'T_CAPTEURS.id')->get();

        // dd($eventjoincapteur);

        return view("welcome" , ["Capteurs_Join_Events_id" => $Capteurs_Join_Events_id ]);
    }
}
