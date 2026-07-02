<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'featured_image',
        'gallery_images',
        'category_id',
        'tags',
        'travel_type',
        'destination',
        'state',
        'city',
        'best_time',
        'estimated_budget',
        'trip_duration',
        'distance',
        'google_map',
        'seo_title',
        'meta_description',
        'meta_keywords',
        'author',
        'status',
        'featured',
        'allow_comments',
        'view_count',
        'like_count',
        'share_count',
        'published_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    /**
     * Get tag IDs as an array.
     */
    public function getTagIdsAttribute()
    {
        return $this->tags ? array_filter(explode(',', $this->tags)) : [];
    }

    /**
     * Get related Tag models.
     */
    public function getTagsModelsAttribute()
    {
        $ids = $this->tag_ids;
        if (empty($ids)) {
            return collect();
        }
        return BlogTag::whereIn('id', $ids)->get();
    }
}
