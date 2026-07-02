<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoCity extends Model
{
    protected $table = 'seo_cities';

    protected $fillable = [
        'state_id',
        'city_name',
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

    public function state()
    {
        return $this->belongsTo(SeoState::class, 'state_id');
    }

    public function pages()
    {
        return $this->hasMany(SeoPage::class, 'city_id');
    }
}
