<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumSeat extends Model
{
        protected $fillable = ['section','row_label','seat_number','seat_class','ring','angle_deg'];
    protected $casts = ['ring'=>'integer','angle_deg'=>'integer'];
}
