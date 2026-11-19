<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSettingRevision extends Model
{
    protected $fillable = ['site_setting_id', 'value'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(SiteSetting::class);
    }
}
