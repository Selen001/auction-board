@extends('layouts.app')

@section('title', 'Buyer Dashboard')

@section('content')
@php
    $outcomeLabels = [
        'won' => 'Won',
        'leading' => 'Leading',
        'outbid' => 'Outbid',
        'lower' => 'Lower bid',
        'lost' => 'Not won',
        'cancelled' => 'Cancelled',
    ];
@endphp

<div class="board-header">
    <h1>Buyer Dashboard</h1>
    <p class="muted">Welcome, {{ auth()->user()->name }}. Here is where your bidding stands.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Open auctions</div>
        <div class="stat-value">{{ $stats['open'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Bids placed</div>
        <div class="stat-value">{{ $stats['bids'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Auctions won</div>
        <div class="stat-value">{{ $stats['won'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total won</div>
        <div class="stat-value">${{ number_format($stats['spent'], 2) }}</div>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('buyer.dashboard') }}" class="tab {{ $tab === 'active' ? 'active' : '' }}">Active auctions</a>
    <a href="{{ route('buyer.dashboard', ['tab' => 'bids']) }}" class="tab {{ $tab === 'bids' ? 'active' : '' }}">My bids</a>
    <a href="{{ route('buyer.dashboard', ['tab' => 'won']) }}" class="tab {{ $tab === 'won' ? 'active' : '' }}">Won auctions</a>
</div>

<div class="card">
    @if ($items->isEmpty())
        <p class="muted text-center">
            @if ($tab === 'bids')
                You haven't placed any bids yet.
            @elseif ($tab === 'won')
                You haven't won any auctions yet.
            @else
                There are no open auctions right now.
            @endif
        </p>
    @else
        <div class="table-wrap">
            @if ($tab === 'active')
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Seller</th>
                            <th>Current price</th>
                            <th>Your bid</th>
                            <th>Closes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $auction)
                            <tr>
                                <td class="wrap"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></td>
                                <td>{{ $auction->seller->name }}</td>
                                <td>${{ number_format($auction->bids_max_amount ?? $auction->base_price, 2) }}</td>
                                <td>
                                    @if ($auction->my_bid !== null)
                                        ${{ number_format($auction->my_bid, 2) }}
                                        @if ((float) $auction->my_bid === (float) $auction->bids_max_amount)
                                            <span class="status status-leading">leading</span>
                                        @else
                                            <span class="status status-outbid">outbid</span>
                                        @endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $auction->closes_at->diffForHumans() }}</td>
                                <td><span class="status status-{{ $auction->displayStatus() }}">{{ $auction->displayStatus() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif ($tab === 'bids')
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>My bid</th>
                            <th>Highest bid</th>
                            <th>Placed</th>
                            <th>Auction status</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $bid)
                            @php
                                $auction = $bid->auction;
                                $outcome = $bid->outcome();
                            @endphp
                            <tr>
                                <td class="wrap"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></td>
                                <td>${{ number_format($bid->amount, 2) }}</td>
                                <td>
                                    {{ $auction->highestBidAmount() !== null ? '$'.number_format($auction->highestBidAmount(), 2) : '—' }}
                                </td>
                                <td>{{ $bid->created_at->format('M d, Y H:i') }}</td>
                                <td><span class="status status-{{ $auction->displayStatus() }}">{{ $auction->displayStatus() }}</span></td>
                                <td><span class="status status-{{ $outcome }}">{{ $outcomeLabels[$outcome] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Seller</th>
                            <th>Winning bid</th>
                            <th>Won on</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $auction)
                            <tr>
                                <td class="wrap"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->item_name }}</a></td>
                                <td>{{ $auction->seller->name }}</td>
                                <td><strong>${{ number_format($auction->winning_amount, 2) }}</strong></td>
                                <td>{{ $auction->closed_at?->format('M d, Y H:i') ?? '—' }}</td>
                                <td><span class="status status-{{ $auction->displayStatus() }}">{{ $auction->displayStatus() }}</span></td>
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