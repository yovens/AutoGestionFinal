<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

 protected $fillable = [
    'nom', 'email', 'password', 'role', 'status', 'telephone', 'profile_image', 'google_id', // Ajoute l isit la
];

    protected $hidden = ['password'];

    public function baskets() {
        return $this->hasMany(Basket::class, 'id_clients');
    }

    public function loans() {
        return $this->hasMany(LoanCart::class,'user_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}




