<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'title_1',
        'title_2',
        'title_1_qty',
        'title_2_qty',
        'value_per_title_2',
        'title_1_price',
        'title_2_price',
        'minimum_order_title',
        'minimum_order_qty',
        'product_image',
        'is_available',
    ];

    protected $casts = [
        'is_available'       => 'boolean',
        'title_1_qty'        => 'integer',
        'title_2_qty'        => 'integer',
        'title_1_price'      => 'decimal:2',
        'title_2_price'      => 'decimal:2',
        'value_per_title_2'  => 'integer',
        'minimum_order_qty'  => 'integer',
    ];

    public function getDisplayStockAttribute(): HtmlString
    {
        // Not available
        if (!$this->is_available) {
            return new HtmlString(
                '<span style="color:#ef4444; font-weight:500;">Not available</span>'
            );
        }

        // Out of stock
        if (
            $this->title_1_qty == 0 &&
            (!$this->title_2 || $this->title_2_qty == 0)
        ) {
            return new HtmlString(
                '<span style="color:#ef4444; font-weight:600;">Out of stock</span>'
            );
        }

        $parts = [];

        // Unit 2
        if ($this->title_2 && $this->title_2_qty !== null) {
            $style = $this->title_2_qty <= 5
                ? 'style="color:#f59e0b; font-weight:600;"'
                : '';

            $parts[] = sprintf(
                '<span %s>%s %s</span>',
                $style,
                number_format($this->title_2_qty, 0, ',', '.'),
                $this->title_2
            );
        }

        // Unit 1
        if ($this->title_1_qty !== null) {
            $style = $this->title_1_qty <= 10
                ? 'style="color:#f59e0b; font-weight:600;"'
                : '';

            $parts[] = sprintf(
                '<span %s>%s %s</span>',
                $style,
                number_format($this->title_1_qty, 0, ',', '.'),
                $this->title_1 ?? 'pcs'
            );
        }

        return new HtmlString(implode(' • ', $parts));
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