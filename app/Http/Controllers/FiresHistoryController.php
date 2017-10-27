<?php

namespace App\Http\Controllers;

use App\Fire;
use Illuminate\Http\Request;

class FiresHistoryController extends Controller
{
    public function showFiresHistory(){
        $NUMBER_OF_FIRES_PER_PAGE=15;
        $fires=Fire::paginate($NUMBER_OF_FIRES_PER_PAGE);

        return view('fires.fires-history', ['fires' => $fires]);
    }
}
