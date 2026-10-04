<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuctionRequest;
use App\Models\Auction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Services\AuctionCloser;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
        public function index(Request $request, AuctionCloser $closer): View
    {
        $closer->closeDue();

        $auctions = $request->user()
            ->auctions()
            ->with('winner:id,name')
            ->withCount('bids')
            ->withMax('bids', 'amount')
            ->latest()
            ->get();

        return view('seller.auctions.index', compact('auctions'));
    }

    public function create(): View
    {
        return view('seller.auctions.create', ['auction' => new Auction()]);
    }

    public function store(AuctionRequest $request): RedirectResponse
    {
        $request->user()->auctions()->create($request->validated());

        return redirect()
            ->route('seller.auctions.index')
            ->with('status', 'Auction created successfully.');
    }

    public function edit(Auction $auction): View
    {
        Gate::authorize('update', $auction);

        return view('seller.auctions.edit', compact('auction'));
    }

        public function update(AuctionRequest $request, Auction $auction): RedirectResponse
    {
        DB::transaction(function () use ($request, $auction) {
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();

            Gate::authorize('update', $locked);

            $locked->update($request->validated());
        });

        return redirect()
            ->route('seller.auctions.index')
            ->with('status', 'Auction updated successfully.');
    }

    public function cancel(Auction $auction): RedirectResponse
    {
        DB::transaction(function () use ($auction) {
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();

            Gate::authorize('cancel', $locked);

            $locked->update(['status' => Auction::STATUS_CANCELLED]);
        });

        return redirect()
            ->route('seller.auctions.index')
            ->with('status', 'Auction cancelled.');
    }
}