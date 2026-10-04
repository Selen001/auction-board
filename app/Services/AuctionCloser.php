<?php

namespace App\Services;

use App\Models\Auction;
use Illuminate\Support\Facades\DB;

class AuctionCloser
{
    /**
     * Close one auction if it is due. Returns true if it was closed by this call.
     */
    public function close(Auction $auction): bool
    {
        return DB::transaction(function () use ($auction) {
            // Lock the row so two processes can't close the same auction twice
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->first();

            if (! $locked || ! $locked->isActive() || $locked->closes_at->isFuture()) {
                return false;
            }

            // Highest valid bid; ties go to the earliest bid
            $winning = $locked->bids()
                ->where('created_at', '<=', $locked->closes_at)
                ->where('buyer_id', '!=', $locked->seller_id)
                ->where('amount', '>=', $locked->base_price)
                ->orderByDesc('amount')
                ->orderBy('created_at')
                ->orderBy('id')
                ->first();

            $locked->forceFill([
                'status' => Auction::STATUS_CLOSED,
                'closed_at' => now(),
                'winner_id' => $winning?->buyer_id,
                'winning_bid_id' => $winning?->id,
                'winning_amount' => $winning?->amount,
            ])->save();

            return true;
        });
    }

    /**
     * Close every auction whose closing time has passed. Returns how many were closed.
     */
    public function closeDue(): int
    {
        $closed = 0;

        Auction::query()->due()->chunkById(100, function ($auctions) use (&$closed) {
            foreach ($auctions as $auction) {
                if ($this->close($auction)) {
                    $closed++;
                }
            }
        });

        return $closed;
    }
}