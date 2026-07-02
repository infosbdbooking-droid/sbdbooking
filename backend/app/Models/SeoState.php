<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoState extends Model
{
    protected $table = 'seo_states';

    protected $fillable = [
        'state_name',
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

    public function cities()
    {
        return $this->hasMany(SeoCity::class, 'state_id');
    }

    public function pages()
    {
        return $this->hasMany(SeoPage::class, 'state_id');
    }
}
