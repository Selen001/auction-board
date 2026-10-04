<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Auction extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'seller_id',
        'item_name',
        'description',
        'base_price',
        'closes_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'winning_amount' => 'decimal:2',
            'closes_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    // The single bid with the highest amount
        // Highest amount; if amounts tie, the earliest bid wins
    public function highestBid(): HasOne
    {
        return $this->hasOne(Bid::class)->ofMany(['amount' => 'max', 'id' => 'min']);
    }
        public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Uses the preloaded bids_count when available to avoid extra queries
    public function hasBids(): bool
    {
        $count = $this->getAttribute('bids_count');

        return $count !== null ? $count > 0 : $this->bids()->exists();
    }

    // Editable/cancellable only while active, still open, and without bids
    public function isEditable(): bool
    {
        return $this->isActive()
            && $this->closes_at->isFuture()
            && ! $this->hasBids();
    }
        // Active and not yet past its closing time
    public function isOpen(): bool
    {
        return $this->isActive() && $this->closes_at->isFuture();
    }

    // Status shown to users: an "active" auction past its closing time counts as closed
    public function displayStatus(): string
    {
        if ($this->isActive() && $this->closes_at->isPast()) {
            return self::STATUS_CLOSED;
        }

        return $this->status;
    }

    // Uses the preloaded bids_max_amount when available to avoid extra queries
    public function highestBidAmount(): ?string
    {
        if (array_key_exists('bids_max_amount', $this->attributes)) {
            return $this->attributes['bids_max_amount'];
        }

        return $this->bids()->max('amount');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('closes_at', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_CLOSED)
            ->orWhere(function ($q) {
            $q->where('status', self::STATUS_ACTIVE)
            ->where('closes_at', '<=', now());
            });
        });
    }
        public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winningBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'winning_bid_id');
    }

    // Still marked active, but its closing time has passed
    public function scopeDue($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('closes_at', '<=', now());
    }
}