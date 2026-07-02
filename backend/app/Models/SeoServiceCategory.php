<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoServiceCategory extends Model
{
    protected $table = 'seo_service_categories';

    protected $fillable = [
        'category_name',
        'slug',
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

    public function pages()
    {
        return $this->hasMany(SeoPage::class, 'category_id');
    }
}
