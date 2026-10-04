<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    protected $table = 'blog_tags';

    protected $fillable = [
        'tag_name',
        'slug',
    ];

    // Table only has created_at
    public $timestamps = false;

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->tag_name;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
