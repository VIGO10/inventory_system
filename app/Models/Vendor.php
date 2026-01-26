<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Vendor extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'prefix',
        'name',
        'phone_number',
        'address',
        'vendor_image',
        // slug is filled automatically — do NOT put it in $fillable
    ];

    // Makes $vendor->full_name available
    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        if ($this->prefix) {
            return trim($this->prefix . ' ' . $this->name);
        }
        return $this->name;
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

    // Optional: quick search helper
    public function scopeSearch($query, string $search)
    {
        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('prefix', 'like', "%{$search}%")
              ->orWhereRaw("CONCAT(prefix, ' ', name) LIKE ?", ["%{$search}%"]);
        });
    }
}