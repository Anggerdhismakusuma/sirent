<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'rental_request_id',
        'reporter_id',
        'reason',
        'evidence',
        'status',
        'resolution',
        'handled_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getReporterTypeAttribute(): string
    {
        $rentalRequest = $this->rentalRequest;

        if (!$rentalRequest) {
            return 'unknown';
        }

        return (int) $this->reporter_id === (int) $rentalRequest->borrower_id
            ? 'borrower'
            : 'store';
    }

    public function getRespondentAttribute(): ?User
    {
        $rentalRequest = $this->rentalRequest;

        if (!$rentalRequest) {
            return null;
        }

        return $this->reporter_type === 'borrower'
            ? $rentalRequest->owner
            : $rentalRequest->borrower;
    }
}
