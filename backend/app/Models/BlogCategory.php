<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';

    protected $fillable = [
        'category_name',
        'slug',
        'status',
    ];

    // Table only has created_at
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
