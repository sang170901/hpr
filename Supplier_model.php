<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'contact_person',
        'website',
        'description',
        'status',
        'payment_terms'
    ];

    protected $casts = [
        'status' => 'boolean',
        'payment_terms' => 'array'
    ];

    // Products relationship
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope for active suppliers
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}