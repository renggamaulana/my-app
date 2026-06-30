<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        
        // Billing Info
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'alt_address',
        'province',
        'city',
        'postal_code',
        
        // Payment & Invoice Details
        'invoice_number',
        'payment_method',
        'payment_status',
        'shipping_cost',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'integer',
        'shipping_cost' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
