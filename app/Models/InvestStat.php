<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestStat extends Model
{
    protected $fillable = ['label', 'value', 'sort'];

    protected $casts = [
        'value' => 'integer',
    ];
}
