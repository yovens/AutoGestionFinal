<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    protected $fillable = ['id_clients','id_vehicles','status','date'];

    public function user() {
        return $this->belongsTo(User::class,'id_clients');
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class,'id_vehicles');
    }
}
