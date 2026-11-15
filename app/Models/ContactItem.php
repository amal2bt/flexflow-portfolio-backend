<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactItem extends Model
{
    protected $fillable = ['icon', 'label', 'value', 'sort'];
}
