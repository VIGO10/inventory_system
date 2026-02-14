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
        'transaction_image',
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

    public function getDisplayStatusAttribute(): string
    {
        if ($this->is_paid) {
            return 'Paid';
        }

        if ($this->is_completed) {
            return 'Completed';
        }

        if ($this->is_published) {
            return 'Ongoing';
        }

        return 'Pending';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->display_status) {
            'Paid'      => '#10b981',   // green
            'Completed' => '#10b981',   // green (or '#059669')
            'Ongoing' => '#3b82f6',   // blue
            'Pending'   => '#f59e0b',   // amber/yellow
            default     => '#6b7280',   // gray fallback
        };
    }
}