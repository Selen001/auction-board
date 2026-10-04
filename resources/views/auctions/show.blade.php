@extends('layouts.app')

@section('title', $auction->item_name)

@section('content')
@php
    $highest = $auction->highestBidAmount();
    $status = $auction->displayStatus();
@endphp

<div class="breadcrumb">
    <a href="{{ route('auctions.index') }}">&larr; Back to auction board</a>
</div>

<div class="detail-layout">
    <div class="card detail-main">
        <span class="status status-{{ $status }}">{{ $status }}</span>
        <h1>{{ $auction->item_name }}</h1>
        <p class="description">{{ $auction->description }}</p>
    </div>

        <aside class="card detail-side">
        @if ($auction->winner_id)
            <div class="price-label">Winning bid</div>
            <div class="price">${{ number_format($auction->winning_amount, 2) }}</div>
        @else
            <div class="price-label">{{ $highest !== null ? 'Current highest bid' : 'No bids yet' }}</div>
            <div class="price">${{ number_format($highest ?? $auction->base_price, 2) }}</div>
        @endif

        <ul class="info-list">
            <li><span>Base price</span><span>${{ number_format($auction->base_price, 2) }}</span></li>
            <li><span>Total bids</span><span>{{ $auction->bids_count }}</span></li>
            <li><span>Seller</span><span>{{ $auction->seller->name }}</span></li>
            <li>
                <span>{{ $auction->isOpen() ? 'Closes' : 'Closed' }}</span>
                <span>{{ $auction->closes_at->format('M d, Y H:i') }}</span>
            </li>
            <li>
                <span>{{ $auction->isOpen() ? 'Time left' : 'Ended' }}</span>
                <span>{{ $auction->closes_at->diffForHumans() }}</span>
            </li>
            <li><span>Status</span><span class="capitalize">{{ $status }}</span></li>
            @if ($status === 'closed')
                <li><span>Winner</span><span>{{ $auction->winner?->name ?? 'No winner' }}</span></li>
            @endif
        </ul>

                @if ($auction->isOpen())
            @guest
                <div class="notice"><a href="{{ route('login') }}">Log in</a> as a buyer to place a bid.</div>
            @elseif (auth()->user()->isSeller())
                <div class="notice">Sellers can't place bids. Use a buyer account to bid.</div>
            @else
                @php $minBid = $highest !== null ? $highest + 0.01 : $auction->base_price; @endphp
                <form method="POST" action="{{ route('auctions.bids.store', $auction) }}"
                      class="bid-form" onsubmit="this.querySelector('button').disabled = true;">
                    @csrf
                    <label for="amount">Your bid</label>
                    <input type="number" id="amount" name="amount" step="0.01"
                           min="{{ number_format($minBid, 2, '.', '') }}"
                           value="{{ old('amount') }}" required>
                    <div class="hint">Minimum: ${{ number_format($minBid, 2) }}</div>
                    @error('amount') <div class="error">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-block mt">Place bid</button>
                </form>
            @endguest
        @elseif ($auction->winner_id)
            <div class="winner-box">
                🏆 Won by <strong>{{ $auction->winner->name }}</strong>
                with a bid of <strong>${{ number_format($auction->winning_amount, 2) }}</strong>.
            </div>
        @elseif ($status === 'closed')
            <div class="notice">This auction closed with no valid bids, so there is no winner.</div>
        @else
            <div class="notice">This auction was {{ $status }} and no longer accepts bids.</div>
        @endif
    </aside>
</div>
@endsection