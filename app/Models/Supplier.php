<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Supplier extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'prefix',
        'name',
        'slug',
        'phone_number',
        'address',
        'supplier_image',
    ];

    public function catalogSuppliers()
    {
        return $this->hasMany(CatalogSupplier::class, 'supplier_id');
    }

    /**
     * Configure how the slug is generated
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'full_name',   // uses prefix + name
                'onUpdate' => true,          // regenerate if name or prefix changes
            ]
        ];
    }

    public function availableCatalogSuppliers()
    {
        return $this->hasMany(CatalogSupplier::class, 'supplier_id')
                    ->where('is_available', true);
    }

    // Optional: quick search helper
    public function scopeSearch($query, string $search)
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(prefix) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(CONCAT(prefix, " ", name)) LIKE ?', ["%" . strtolower($search) . "%"]);
        });
    }
}