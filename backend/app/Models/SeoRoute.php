<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRoute extends Model
{
    protected $table = 'seo_routes';

    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'distance',
        'estimated_time',
        'starting_price',
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

    public function fromCity()
    {
        return $this->belongsTo(SeoCity::class, 'from_city_id');
    }

    public function toCity()
    {
        return $this->belongsTo(SeoCity::class, 'to_city_id');
    }

    public function pages()
    {
        return $this->hasMany(SeoPage::class, 'route_id');
    }

    /**
     * Helper to get route title (e.g. "Delhi to Agra")
     */
    public function getRouteNameAttribute()
    {
        $from = $this->fromCity ? $this->fromCity->city_name : 'Unknown';
        $to = $this->toCity ? $this->toCity->city_name : 'Unknown';
        return "{$from} to {$to}";
    }
}
