<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['order_id','amount','reason','processed_by'];

    public function order(){ return $this->belongsTo(Order::class); }
    public function processor(){ return $this->belongsTo(User::class,'processed_by'); }
}
