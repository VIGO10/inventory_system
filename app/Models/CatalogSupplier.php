<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'title_1',
        'title_2',
        'value_per_title_2',
        'title_1_price',
        'title_2_price',
        'minimum_order_title',
        'minimum_order_qty',
        'product_image',
        'supplier_id',
        'is_available',
    ];

    protected $casts = [
        'is_available'       => 'boolean',
        'title_1_price'      => 'decimal:2',
        'title_2_price'      => 'decimal:2',
        'value_per_title_2'  => 'integer',
        'minimum_order_qty'  => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeSearch($query, string $search)
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"])
              ->orWhereRaw('LOWER(description) LIKE ?', ["%" . strtolower($search) . "%"]);
        });
    }
}