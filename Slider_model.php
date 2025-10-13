<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'link_url',
        'link_text',
        'sort_order',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Scope for active sliders
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for current sliders (within date range)
    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query->where(function($q) use ($today) {
            $q->where(function($q1) use ($today) {
                $q1->whereNull('start_date')
                   ->orWhere('start_date', '<=', $today);
            })->where(function($q2) use ($today) {
                $q2->whereNull('end_date')
                   ->orWhere('end_date', '>=', $today);
            });
        });
    }
}