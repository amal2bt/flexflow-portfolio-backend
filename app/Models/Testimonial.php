<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'body', 'rating', 'status'];

    protected $casts = [
        'rating' => 'integer',
    ];
}
