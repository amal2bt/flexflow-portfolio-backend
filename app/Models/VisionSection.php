<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisionSection extends Model
{
    protected $fillable = ['vision_text', 'vision_text_2', 'vision_text_3', 'image'];

    public function points(): HasMany
    {
        return $this->hasMany(VisionPoint::class)->orderBy('sort');
    }
}
