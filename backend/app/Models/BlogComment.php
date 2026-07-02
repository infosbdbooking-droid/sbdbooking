<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $table = 'blog_comments';

    protected $fillable = [
        'blog_id',
        'name',
        'email',
        'comment',
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

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
