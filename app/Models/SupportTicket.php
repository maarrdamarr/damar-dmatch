<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model {
    protected $fillable = ['user_id','type','reference','payload','status','assigned_to'];
    protected $casts = ['payload'=>'array'];
    public function user(){ return $this->belongsTo(User::class); }
    public function assignee(){ return $this->belongsTo(User::class,'assigned_to'); }
}