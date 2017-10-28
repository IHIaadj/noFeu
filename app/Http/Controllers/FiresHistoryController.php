<?php

namespace App\Http\Controllers;

use App\Capteur;
use App\Fire;
use Illuminate\Http\Request;

class FiresHistoryController extends Controller
{
    public function showFiresHistory(){
        $NUMBER_OF_FIRES_PER_PAGE=15;
        $fires=Fire::paginate($NUMBER_OF_FIRES_PER_PAGE);

        //Régions
        $regions=Capteur::select("REGION")->groupBy("REGION")->get();

        return view('fires.fires-history', ['fires' => $fires,'regions'=>$regions]);
    }

    public function ajaxFireFilter(Request $request){
        $type_rech=$request->get("typeRech");
        $result=Fire::where("id",">","-1");
        if($type_rech){
            if(in_array("region",$type_rech)){
                $region=$request->get("region");
                $capteur=$request->get("capteur");
                if($capteur!==null){
                    $result=$result->where("ID_CAPTEUR","=",$capteur);
                }
                else if($region!=="0")
                {
                    $capteursReg=Capteur::select("id")->where("REGION","=",$region)->get();
                    $result=$result->whereIn("ID_CAPTEUR",$capteursReg);
                }
            }
            if(in_array("temp",$type_rech)){
                $temp=$request->get("temperature");
                $oper=$request->get("operation");
                if($temp!==null){
                    $result=$result->where("AVG_TEMP",$oper,$temp);
                }

            }
            if(in_array("date",$type_rech)){
                $oper=$request->get("operation-date");
                $begTime=$request->get("beg-time");
                if($begTime)
                    switch($oper){
                        case "avant":
                            $result=$result->where("STARTING","<",$begTime);
                            break;
                        case "apres":
                            $result=$result->where("STARTING",">",$begTime);
                            break;
                        case "entre":
                            $endTime=$request->get("end-time");
                            if($endTime){
                                $result=$result->where("STARTING",">",$begTime)->where("ENDING","<",$endTime);
                            }
                            break;
                    }
            }
        }
        $result=$result->get();
        return view("fires.ajaxFiresListSearch",["fires"=>$result]);
    }
}
