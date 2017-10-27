<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Capteur extends Model
{
    //
    protected $guarded=["id"];
    protected $table="T_CAPTEURS";
}
