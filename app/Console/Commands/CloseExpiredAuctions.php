<?php

namespace App\Console\Commands;

use App\Services\AuctionCloser;
use Illuminate\Console\Command;

class CloseExpiredAuctions extends Command
{
    protected $signature = 'auctions:close-expired';

    protected $description = 'Close auctions whose closing time has passed and set their winners';

    public function handle(AuctionCloser $closer): int
    {
        $count = $closer->closeDue();

        $this->info("Closed {$count} auction(s).");

        return self::SUCCESS;
    }
}