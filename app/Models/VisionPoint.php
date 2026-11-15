<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisionPoint extends Model
{
    protected $fillable = ['vision_section_id', 'point', 'sort'];

    public function visionSection(): BelongsTo
    {
        return $this->belongsTo(VisionSection::class);
    }
}
