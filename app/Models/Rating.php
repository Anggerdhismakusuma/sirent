<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    const TYPE_TO_OWNER = 'to_owner';
    const TYPE_TO_BORROWER = 'to_borrower';

    protected $fillable = [
        'rental_request_id',
        'rater_id',
        'ratee_id',
        'type',
        'score',
        'review',
    ];

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}
