<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'event_seat_pricing_id',
        'price',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function eventSeatPricing() { return $this->belongsTo(EventSeatPricing::class); }
}