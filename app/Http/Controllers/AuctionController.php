<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AuctionCloser;

class AuctionController extends Controller
{
        public function index(Request $request, AuctionCloser $closer): View
    {
        // Safety net in case the scheduler is late
        $closer->closeDue();

        $tab = $request->query('tab') === 'ended' ? 'ended' : 'open';

        $query = Auction::query()
            ->with('seller:id,name', 'winner:id,name')
            ->withCount('bids')
            ->withMax('bids', 'amount');

        if ($tab === 'ended') {
            $query->ended()->orderByDesc('closes_at');
        } else {
            $query->open()->orderBy('closes_at');
        }

        $auctions = $query->simplePaginate(9)->withQueryString();

        return view('auctions.index', compact('auctions', 'tab'));
    }

    public function show(Auction $auction, AuctionCloser $closer): View
    {
        // Close this auction immediately if its time has passed
        $closer->close($auction);
        $auction->refresh();

        $auction->load('seller:id,name', 'winner:id,name')
            ->loadCount('bids')
            ->loadMax('bids', 'amount');

        return view('auctions.show', compact('auction'));
    }
}