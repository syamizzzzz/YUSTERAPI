<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPackage extends Model
{
    protected $fillable = ['name', 'description', 'duration_minutes', 'price', 'is_active', 'background_image', 'name_color', 'description_color', 'duration_color', 'price_color', 'package_label_color'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'decimal:2'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
