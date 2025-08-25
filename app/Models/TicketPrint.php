<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPrint extends Model
{
    protected $fillable = ['order_id','source','printed_by'];

    public function order(){ return $this->belongsTo(Order::class); }
    public function printer(){ return $this->belongsTo(User::class,'printed_by'); }
}
