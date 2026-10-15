<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\FleetException;

class Vehicle extends Model
{
    protected $fillable = [
        'brand', 'model', 'plate', 'year', 'image',
        'price', 'loan_price', 'status', 'quantity',
    ];

    protected $casts = [
        'year'       => 'integer',
        'price'      => 'integer',
        'loan_price' => 'integer',
        'quantity'   => 'integer',
        'status'     => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(LoanCart::class, 'vehicle_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->quantity > 0;
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->brand} {$this->model}");
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Retire yon inite nan stock la. Dwe rele andedan yon DB::transaction
     * sou yon modèl ki chaje ak lockForUpdate() pou evite 2 rezèvasyon an menm tan.
     */
    public function takeOne(): void
    {
        if ($this->quantity <= 0) {
            throw new FleetException('Ce véhicule n’est plus disponible.');
        }

        $this->quantity -= 1;
        $this->status = $this->quantity > 0;
        $this->save();
    }

    /** Remèt yon inite nan stock la. */
    public function releaseOne(): void
    {
        $this->quantity += 1;
        $this->status = true;
        $this->save();
    }
}
