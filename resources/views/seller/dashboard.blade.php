@extends('layouts.app')

@section('title', 'Seller Dashboard')

@section('content')
<div class="board-header header-row">
    <div>
        <h1>Seller Dashboard</h1>
        <p class="muted">Welcome, {{ auth()->user()->name }}. Here is how your auctions are doing.</p>
    </div>

    <div class="dashboard-actions">
        <a href="{{ route('seller.auctions.create') }}" class="btn">+ New auction</a>
        <a href="{{ route('seller.auctions.index') }}" class="btn btn-secondary">Manage auctions</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Active auctions</div>
        <div class="stat-value">{{ $stats['active'] }}</div>
        <div class="stat-sub">{{ $stats['active_bids'] }} {{ Str::plural('bid', $stats['active_bids']) }} so far</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Closed auctions</div>
        <div class="stat-value">{{ $stats['closed'] }}</div>
        <div class="stat-sub">{{ $stats['sold'] }} sold, {{ $stats['closed'] - $stats['sold'] }} unsold</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Items sold</div>
        <div class="stat-value">{{ $stats['sold'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total revenue</div>
        <div class="stat-value">${{ number_format($stats['revenue'], 2) }}</div>
        <div class="stat-sub">From winning bids</div>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('seller.dashboard') }}" class="tab {{ $tab === 'active' ? 'active' : '' }}">Active auctions</a>
    <a href="{{ route('seller.dashboard', ['tab' => 'closed']) }}" class="tab {{ $tab === 'closed' ? 'active' : '' }}">Closed auctions</a>
</div>

<div class="card">
    @if ($items->isEmpty())
        <p class="muted text-center">
            @if ($tab === 'closed')
                None of your auctions have closed yet.
            @else
                You have no active auctions.
                <a href="{{ route('seller.auctions.create') }}">Create one</a>.
            @endif
        </p>
    @else
        <div class="table-wrap">
            @if ($tab === 'active')
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Base price</th>
                            <th>Highest bid</th>
                            <th>Leading bidder</th>
                            <th>Bids</th>
                            <th>Closes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $auction)
                            <tr>
                                <td class="wrap"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></td>
                                <td>${{ number_format($auction->base_price, 2) }}</td>
                                <td>
                                    @if ($auction->bids_max_amount !== null)
                                        <strong>${{ number_format($auction->bids_max_amount, 2) }}</strong>
                                    @else
                                        <span class="muted">No bids</span>
                                    @endif
                                </td>
                                <td>{{ $auction->highestBid?->buyer?->name ?? '—' }}</td>
                                <td>{{ $auction->bids_count }}</td>
                                <td>{{ $auction->closes_at->format('M d, H:i') }}
                                    <span class="muted">({{ $auction->closes_at->diffForHumans() }})</span>
                                </td>
                                <td><span class="status status-{{ $auction->displayStatus() }}">{{ $auction->displayStatus() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Base price</th>
                            <th>Highest bid</th>
                            <th>Winner</th>
                            <th>Winning amount</th>
                            <th>Closed</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $auction)
                            <tr>
                                <td class="wrap"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></td>
                                <td>${{ number_format($auction->base_price, 2) }}</td>
                                <td>
                                    {{ $auction->bids_max_amount !== null ? '$'.number_format($auction->bids_max_amount, 2) : '—' }}
                                </td>
                                <td>{{ $auction->winner?->name ?? '—' }}</td>
                                <td>
                                    @if ($auction->winner_id)
                                        <strong>${{ number_format($auction->winning_amount, 2) }}</strong>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $auction->closes_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if ($auction->winner_id)
                                        <span class="status status-sold">sold</span>
                                    @else
                                        <span class="status status-unsold">no winner</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{ $items->links('pagination.simple') }}
    @endif
</div>
@endsection