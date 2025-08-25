<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id','type','amount','method','recorded_by','paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
}