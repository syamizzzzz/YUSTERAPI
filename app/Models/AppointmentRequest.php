<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequest extends Model
{
    protected $fillable = ['name', 'phone', 'preferred_at', 'message', 'status', 'treatment_package_id', 'booking_id', 'custom_fields'];

    protected function casts(): array
    {
        return ['preferred_at' => 'datetime', 'custom_fields' => 'array'];
    }

    public function treatmentPackage(): BelongsTo
    {
        return $this->belongsTo(TreatmentPackage::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
