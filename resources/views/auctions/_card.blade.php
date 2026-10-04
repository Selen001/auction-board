@php
    $highest = $auction->highestBidAmount();
    $status = $auction->displayStatus();
@endphp

<article class="auction-card">
    <div class="auction-card-top">
        <h2><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></h2>
        <span class="status status-{{ $status }}">{{ $status }}</span>
    </div>

    <p class="excerpt">{{ \Illuminate\Support\Str::limit($auction->description, 110) }}</p>

        <div>
        @if ($auction->winner_id)
            <div class="price-label">Winning bid</div>
            <div class="price">${{ number_format($auction->winning_amount, 2) }}</div>
            <div class="winner-line">Winner: <strong>{{ $auction->winner->name }}</strong></div>
        @elseif ($status === 'closed')
            <div class="price-label">Closed without a winner</div>
            <div class="price">${{ number_format($auction->base_price, 2) }}</div>
        @else
            <div class="price-label">{{ $highest !== null ? 'Current highest bid' : 'Starting price' }}</div>
            <div class="price">${{ number_format($highest ?? $auction->base_price, 2) }}</div>
        @endif
    </div>

    <div class="meta">
        <span>Base price: <strong>${{ number_format($auction->base_price, 2) }}</strong></span>
        <span>Bids: <strong>{{ $auction->bids_count }}</strong></span>
        <span>Seller: <strong>{{ $auction->seller->name }}</strong></span>
        <span>
            {{ $auction->isOpen() ? 'Closes' : 'Closed' }}:
            <strong>{{ $auction->closes_at->format('M d, Y H:i') }}</strong>
            ({{ $auction->closes_at->diffForHumans() }})
        </span>
    </div>

    <a href="{{ route('auctions.show', $auction) }}" class="btn btn-block">View details</a>
</article>