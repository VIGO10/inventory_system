<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionInbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'total_price',
        'discount',
        'deadline_payment_date',
        'created_date',
        'is_published',
        'published_date',
        'is_completed',
        'completed_date',
        'is_paid',
        'paid_date',
    ];

    protected $casts = [
        'created_date'      => 'datetime',
        'published_date'    => 'datetime',
        'completed_date'    => 'datetime',
        'paid_date'         => 'datetime',
        'deadline_payment_date' => 'date',
        'is_published'      => 'boolean',
        'is_completed'      => 'boolean',
        'is_paid'           => 'boolean',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionInboundItem::class);
    }
}