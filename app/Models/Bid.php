<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Bid extends Model
{
    protected $fillable = [
        'auction_id',
        'buyer_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
            protected static function booted(): void
    {
        // Every bid must pass these checks, whichever controller or script creates it
        static::creating(function (Bid $bid) {
            $auction = Auction::find($bid->auction_id);

            if (! $auction || ! $auction->isOpen()) {
                throw ValidationException::withMessages([
                    'amount' => 'This auction is closed and no longer accepts bids.',
                ]);
            }

            if ((int) $auction->seller_id === (int) $bid->buyer_id) {
                throw ValidationException::withMessages([
                    'amount' => 'You cannot bid on your own auction.',
                ]);
            }

            if ((float) $bid->amount < (float) $auction->base_price) {
                throw ValidationException::withMessages([
                    'amount' => 'Your bid must be at least the base price.',
                ]);
            }
        });
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
        // Result of this bid. Expects the auction to be loaded with bids_max_amount.
    public function outcome(): string
    {
        $auction = $this->auction;

        if ($auction->status === Auction::STATUS_CANCELLED) {
            return 'cancelled';
        }

        if ($auction->displayStatus() === Auction::STATUS_CLOSED) {
            if ($auction->winning_bid_id === $this->id) {
                return 'won';
            }

            // The buyer won with a different, higher bid
            return $auction->winner_id === $this->buyer_id ? 'lower' : 'lost';
        }

        return (float) $this->amount === (float) $auction->highestBidAmount()
            ? 'leading'
            : 'outbid';
    }
}