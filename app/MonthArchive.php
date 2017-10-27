<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthArchive extends Model
{
    //
    protected $connection = 'archiveSQL';
    protected $guarded =["id"];
    protected $table="T_MONTHLY_12H_AVERAGE";
}
