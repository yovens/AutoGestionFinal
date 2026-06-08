<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanCart extends Model
{
    protected $fillable = [
        'vehicle_id', 
        'user_id', 
        'duration_days', 
        'total_amount', 
        'status', 
        'start_date', 
        'end_date',
    'renewal_requested_days'
    ];

    // Pou Laravel toujou trete jaden sa yo tankou Dat otomatikman
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Fonksyon pratik pou tcheke si lokasyon an ekspire oswa li prèt pou l fini
     */
    public function isExpired() {
        if ($this->status === 'expired') return true;
        return $this->end_date && Carbon::now()->gt($this->end_date);
    }

    public function daysLeft() {
        if (!$this->end_date || $this->isExpired()) return 0;
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }
}