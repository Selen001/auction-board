<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Services\AuctionCloser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Sends each user to the dashboard for their role
    public function index(Request $request): RedirectResponse
    {
        return $request->user()->isSeller()
            ? redirect()->route('seller.dashboard')
            : redirect()->route('buyer.dashboard');
    }

    public function seller(Request $request, AuctionCloser $closer): View
    {
        // Make sure expired auctions are closed and winners are set
        $closer->closeDue();

        $user = $request->user();
        $tab = $request->query('tab') === 'closed' ? 'closed' : 'active';

        $stats = [
            'active' => $user->auctions()->open()->count(),
            'closed' => $user->auctions()->ended()->count(),
            'sold' => $user->auctions()->whereNotNull('winner_id')->count(),
            'active_bids' => Bid::whereIn('auction_id', $user->auctions()->open()->select('id'))->count(),
            'revenue' => $user->auctions()->whereNotNull('winner_id')->sum('winning_amount'),
        ];

        $items = $tab === 'closed'
            ? $user->auctions()
                ->ended()
                ->with('winner:id,name')
                ->withCount('bids')
                ->withMax('bids', 'amount')
                ->orderByDesc('closes_at')
                ->simplePaginate(10)
            : $user->auctions()
                ->open()
                ->with('highestBid.buyer:id,name')
                ->withCount('bids')
                ->withMax('bids', 'amount')
                ->orderBy('closes_at')
                ->simplePaginate(10);

        $items->withQueryString();

        return view('seller.dashboard', compact('tab', 'stats', 'items'));
    }

    public function buyer(Request $request, AuctionCloser $closer): View
    {
        // Make sure winners are up to date before showing results
        $closer->closeDue();

        $user = $request->user();
        $tab = in_array($request->query('tab'), ['bids', 'won'], true)
            ? $request->query('tab')
            : 'active';

        $stats = [
            'open' => Auction::open()->count(),
            'bids' => $user->bids()->count(),
            'won' => $user->wonAuctions()->count(),
            'spent' => $user->wonAuctions()->sum('winning_amount'),
        ];

        $items = match ($tab) {
            'bids' => $user->bids()
                ->with(['auction' => fn ($q) => $q->withMax('bids', 'amount')])
                ->latest()
                ->simplePaginate(10),

            'won' => $user->wonAuctions()
                ->with('seller:id,name')
                ->orderByDesc('closed_at')
                ->simplePaginate(10),

            default => Auction::open()
                ->with('seller:id,name')
                ->withCount('bids')
                ->withMax('bids', 'amount')
                ->withMax(['bids as my_bid' => fn ($q) => $q->where('buyer_id', $user->id)], 'amount')
                ->orderBy('closes_at')
                ->simplePaginate(10),
        };

        $items->withQueryString();

        return view('buyer.dashboard', compact('tab', 'stats', 'items'));
    }
}