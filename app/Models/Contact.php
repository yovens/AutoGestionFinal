<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model {
    protected $fillable = ['user_id', 'subject', 'message', 'is_read'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}