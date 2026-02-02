<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionOutboundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_outbound_id',
        'catalog_id',
        'price',           // selling price (customer pays this)
        'buy_price',       // ← NEW: cost price (what you paid / supplier price)
        'title_1_qty',
        'title_1_price',
        'title_2_qty',
        'title_2_price',
        'discount',
    ];

    protected $casts = [
        'title_1_qty'    => 'decimal:2',
        'title_2_qty'    => 'decimal:2',
        'price'          => 'decimal:4',
        'buy_price'      => 'decimal:4',     // ← added
        'title_1_price'  => 'decimal:4',
        'title_2_price'  => 'decimal:4',
        'discount'       => 'decimal:4',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(TransactionOutbound::class, 'transaction_outbound_id');
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }
}