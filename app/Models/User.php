<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmail;

    const ROLE_BORROWER = 'borrower';
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';

    const VERIFICATION_UNVERIFIED = 'unverified';
    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_VERIFIED = 'verified';
    const VERIFICATION_REJECTED = 'rejected';

    const ACCOUNT_ACTIVE = 'active';
    const ACCOUNT_SUSPENDED = 'suspended';
    const ACCOUNT_BANNED = 'banned';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'bio',
        'role',
        'is_owner_active',
        'identity_doc',
        'verification_status',
        'verification_note',
        'dob',
        'domicile',
        'gender',
        'interests',
        'rating_avg_as_borrower',
        'rating_avg_as_owner',
        'account_status',
        'suspended_until',
        'theme',
        'language',
        'banner',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'whatsapp_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_owner_active' => 'boolean',
            'rating_avg_as_borrower' => 'decimal:2',
            'rating_avg_as_owner' => 'decimal:2',
            'suspended_until' => 'datetime',
        ];
    }

    /**
     * Override the MustVerifyEmail trait method so that email verification
     * also sets the identity verification_status to 'verified'.
     */
    public function markEmailAsVerified(): bool
    {
        $this->verification_status = self::VERIFICATION_VERIFIED;
        return parent::markEmailAsVerified();
    }

    /**
     * A user is considered "verified" when their email is verified.
     * No KTP or WhatsApp verification is required.
     */
    public function isVerified(): bool
    {
        return $this->hasVerifiedEmail();
    }

    // Relations
    public function products()
    {
        return $this->hasMany(Product::class, 'owner_id');
    }

    public function rentalRequestsAsBorrower()
    {
        return $this->hasMany(RentalRequest::class, 'borrower_id');
    }

    public function rentalRequestsAsOwner()
    {
        return $this->hasMany(RentalRequest::class, 'owner_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function conversationsAsBorrower()
    {
        return $this->hasMany(Conversation::class, 'borrower_id');
    }

    public function conversationsAsOwner()
    {
        return $this->hasMany(Conversation::class, 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // ── Follow system ──
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id')
            ->withTimestamps();
    }

    public function disputesReported()
    {
        return $this->hasMany(Dispute::class, 'reporter_id');
    }

    public function disputesHandled()
    {
        return $this->hasMany(Dispute::class, 'handled_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER || $this->is_owner_active;
    }

    public function isBorrower(): bool
    {
        return $this->role === self::ROLE_BORROWER;
    }

    public function isActive(): bool
    {
        return $this->account_status === self::ACCOUNT_ACTIVE;
    }
}
