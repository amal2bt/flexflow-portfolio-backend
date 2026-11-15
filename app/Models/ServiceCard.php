<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCard extends Model
{
    protected $fillable = ['service_id', 'title', 'description', 'image', 'sort'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
