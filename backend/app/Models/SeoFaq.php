<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoFaq extends Model
{
    protected $table = 'seo_faqs';

    protected $fillable = [
        'question',
        'answer',
        'status',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
