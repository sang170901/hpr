<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'track_stock',
        'status',
        'featured',
        'images',
        'specifications',
        'seo_meta',
        'category_id',
        'supplier_id',
        'weight',
        'dimensions'
    ];

    protected $casts = [
        'status' => 'boolean',
        'featured' => 'boolean',
        'track_stock' => 'boolean',
        'images' => 'array',
        'specifications' => 'array',
        'seo_meta' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer'
    ];

    // Category relationship
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Supplier relationship
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for featured products
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Scope for in stock products
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Check if product is in stock
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    // Check if product is low stock
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }
}