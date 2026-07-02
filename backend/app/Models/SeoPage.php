<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoPage extends Model
{
    protected $table = 'seo_pages';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'banner_image',
        'featured_image',
        'gallery_images',
        'category_id',
        'state_id',
        'city_id',
        'route_id',
        'pickup_location',
        'destination_location',
        'best_time_to_visit',
        'starting_price',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'schema_type',
        'author',
        'featured',
        'view_count',
        'status',
        'published_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'starting_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(SeoServiceCategory::class, 'category_id');
    }

    public function state()
    {
        return $this->belongsTo(SeoState::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(SeoCity::class, 'city_id');
    }

    public function route()
    {
        return $this->belongsTo(SeoRoute::class, 'route_id');
    }

    /**
     * Get the file path for this page's extended metadata.
     */
    public function getMetadataPath()
    {
        return "seo_pages/page_{$this->id}.json";
    }

    /**
     * Accessor to get all extended data.
     */
    public function getExtendedDataAttribute()
    {
        $path = $this->getMetadataPath();
        if (Storage::disk('local')->exists($path)) {
            $data = json_decode(Storage::disk('local')->get($path), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    /**
     * Get a specific extended field value with a fallback default.
     */
    public function extended($key, $default = '')
    {
        return $this->extended_data[$key] ?? $default;
    }

    /**
     * Save extended metadata.
     */
    public function saveExtendedData(array $data)
    {
        $path = $this->getMetadataPath();
        
        // Merge with existing metadata
        $existing = $this->extended_data;
        $merged = array_merge($existing, $data);
        
        Storage::disk('local')->put($path, json_encode($merged, JSON_PRETTY_PRINT));
        return true;
    }

    /**
     * Delete extended metadata file.
     */
    public function deleteExtendedData()
    {
        $path = $this->getMetadataPath();
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
