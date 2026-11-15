<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'short',
        'icon',
        'color',
        'side_title',
        'side_copy',
        'heading',
        'intro',
        'closing',
        'sort',
    ];

    public function cards(): HasMany
    {
        return $this->hasMany(ServiceCard::class)->orderBy('sort');
    }
}
