<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dispute;

class RentalRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_EXPIRED = 'expired';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'borrower_id',
        'product_id',
        'owner_id',
        'start_date',
        'end_date',
        'total_days',
        'quantity',
        'total_price',
        'notes',
        'rejection_reason',
        'status',
        'order_ref',
        'payment_status',
        'payment_method',
        'snap_token',
        'transaction_id',
        'approved_at',
        'completed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'quantity' => 'integer',
            'total_price' => 'decimal:2',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    public function activeDispute(): HasOne
    {
        return $this->hasOne(Dispute::class)
            ->whereIn('status', ['open', 'in_review'])
            ->latestOfMany();
    }
}
