<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public const ROLE_SELLER = 'seller';
    public const ROLE_BUYER = 'buyer';

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isBuyer(): bool
    {
        return $this->role === self::ROLE_BUYER;
    }
        public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'buyer_id');
    }
        // Auctions this user won
    public function wonAuctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }
}
