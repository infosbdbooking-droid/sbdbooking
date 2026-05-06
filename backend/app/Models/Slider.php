<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'slider_id',
        'title',
        'alt',
        'meta_title',
        'image_path',
        'order',
        'status',
    ];
}
