<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title','description','start_at','end_at','venue','capacity','status'];

    protected $casts = [
        'start_at'=>'datetime',
        'end_at'=>'datetime',
    ];

    public function seatPricings() {
        return $this->hasMany(EventSeatPricing::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
}
