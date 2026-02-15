<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherCost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'price',
        'type',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:4',
        'date'  => 'date:Y-m-d',
        'type'  => 'string',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope a query to only include income records.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope a query to only include expense/cost records.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope a query to records in a specific month/year.
     */
    public function scopeInMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)
                     ->whereMonth('date', $month);
    }

    // -------------------------------------------------------------------------
    // Accessors & Mutators
    // -------------------------------------------------------------------------

    /**
     * Get the formatted price with currency (Rp).
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get nicely formatted date (Indonesian style).
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? $this->date->format('d F Y') : '-';
    }

    /**
     * Check if this is an income entry.
     */
    public function isIncome(): bool
    {
        return $this->type === 'in';
    }

    /**
     * Check if this is an expense/cost entry.
     */
    public function isExpense(): bool
    {
        return $this->type === 'out';
    }
}