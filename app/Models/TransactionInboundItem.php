<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionInboundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_inbound_id',
        'catalog_supplier_id',
        'supplier_id',
        'price',
        'title_1_qty',
        'title_1_price',
        'title_2_qty',
        'title_2_price',
        'discount',
    ];

    protected $casts = [
        'title_1_qty'   => 'decimal:2',
        'title_2_qty'   => 'decimal:2',
        'price'         => 'decimal:4',
        'title_1_price' => 'decimal:4',
        'title_2_price' => 'decimal:4',
        'discount'      => 'decimal:4',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(TransactionInbound::class, 'transaction_inbound_id');
    }

    public function catalogSupplier(): BelongsTo
    {
        return $this->belongsTo(CatalogSupplier::class, 'catalog_supplier_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}