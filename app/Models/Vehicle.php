<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
   protected $fillable = [
    'brand','model','plate','year','image',
    'price','loan_price','status','quantity'
];

    public function baskets() {
        return $this->hasMany(Basket::class, 'id_vehicles');
    }

    public function loans() {
        return $this->hasMany(LoanCart::class,'vehicle_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
    public function getIsAvailableAttribute()
{
    return $this->quantity > 0;
}
}



