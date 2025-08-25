<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSeatPricing extends Model
{
    protected $fillable = ['event_id','stadium_seat_id','price','status'];
    public function event(){ return $this->belongsTo(Event::class); }
    public function stadiumSeat(){ return $this->belongsTo(StadiumSeat::class); }
}
