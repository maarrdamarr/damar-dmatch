<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','event_id','channel','status','subtotal','fee','total','payment_method','reference'];
    public function items(){ return $this->hasMany(OrderItem::class); }
    public function event(){ return $this->belongsTo(Event::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function transactions(){ return $this->hasMany(Transaction::class); }
}
