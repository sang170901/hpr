<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'status' => 'boolean',
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Scope for active vouchers
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for current vouchers (within date range)
    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    // Scope for available vouchers (not exceeded usage limit)
    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    // Check if voucher is valid
    public function isValid(): bool
    {
        $today = now()->toDateString();
        
        return $this->status 
            && $this->start_date <= $today 
            && $this->end_date >= $today
            && (is_null($this->usage_limit) || $this->used_count < $this->usage_limit);
    }

    // Calculate discount amount
    public function calculateDiscount($amount): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;
        
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return $discount;
    }
}