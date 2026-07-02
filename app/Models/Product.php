<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const CONDITION_NEW = 'new';
    const CONDITION_LIKE_NEW = 'like_new';
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'owner_id',
        'category_id',
        'title',
        'slug',
        'description',
        'condition',
        'price_per_day',
        'deposit_amount',
        'location_city',
        'location_detail',
        'status',
        'rating_avg',
        'total_rented',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'rating_avg' => 'decimal:2',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function availabilities()
    {
        return $this->hasMany(ProductAvailability::class);
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // ============================================
    // Scopes for Search & Filter
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    public function scopeInCity($query, ?string $city)
    {
        if ($city) {
            return $query->where('location_city', $city);
        }
        return $query;
    }

    public function scopePriceBetween($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price_per_day', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price_per_day', '<=', $max);
        }
        return $query;
    }

    public function scopeMinRating($query, ?float $rating)
    {
        if ($rating) {
            return $query->where('rating_avg', '>=', $rating);
        }
        return $query;
    }

    public function scopeVerifiedOwner($query, ?bool $verified)
    {
        if ($verified) {
            return $query->whereHas('owner', function ($q) {
                $q->where('verification_status', User::VERIFICATION_VERIFIED);
            });
        }
        return $query;
    }

    public function scopeSortBy($query, ?string $sort)
    {
        return match ($sort) {
            'oldest'          => $query->oldest(),
            'cheapest'        => $query->orderBy('price_per_day', 'asc'),
            'most_expensive'  => $query->orderBy('price_per_day', 'desc'),
            'highest_rated'   => $query->orderBy('rating_avg', 'desc'),
            'most_rented'     => $query->orderBy('total_rented', 'desc'),
            default           => $query->latest(),
        };
    }
}
