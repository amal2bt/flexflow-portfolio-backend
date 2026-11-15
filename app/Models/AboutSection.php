<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = ['heading', 'description', 'catchphrase', 'paragraph_2', 'paragraph_3', 'image'];
}
