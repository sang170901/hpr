<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'featured',
        'seo_meta',
        'author_id',
        'published_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'featured' => 'boolean',
        'seo_meta' => 'array',
        'published_at' => 'datetime'
    ];

    // Author relationship
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scope for published posts
    public function scopePublished($query)
    {
        return $query->where('status', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    // Scope for featured posts
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Check if post is published
    public function isPublished(): bool
    {
        return $this->status && $this->published_at && $this->published_at <= now();
    }
}