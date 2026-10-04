<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function update(User $user, Auction $auction): bool
    {
        return $auction->seller_id === $user->id && $auction->isEditable();
    }

    public function cancel(User $user, Auction $auction): bool
    {
        return $auction->seller_id === $user->id && $auction->isEditable();
    }
}