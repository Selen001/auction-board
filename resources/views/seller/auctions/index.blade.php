@extends('layouts.app')

@section('title', 'My Auctions')

@section('content')
<div class="card">
    <div class="page-header">
        <h1>My auctions</h1>
        <a href="{{ route('seller.auctions.create') }}" class="btn">+ New auction</a>
    </div>

    @if ($auctions->isEmpty())
        <p class="muted">You haven't created any auctions yet.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Base price</th>
                        <th>Highest bid</th>
                        <th>Bids</th>
                        <th>Closes</th>
                        <th>Status</th>
                        <th>Winner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($auctions as $auction)
                        <tr>
                            <td>{{ $auction->item_name }}</td>
                            <td>${{ number_format($auction->base_price, 2) }}</td>
                            <td>
                                {{ $auction->bids_max_amount !== null ? '$'.number_format($auction->bids_max_amount, 2) : '—' }}
                            </td>
                            <td>{{ $auction->bids_count }}</td>
                            <td>{{ $auction->closes_at->format('M d, Y H:i') }}</td>
                                                        <td>
                                <span class="status status-{{ $auction->displayStatus() }}">{{ $auction->displayStatus() }}</span>
                            </td>
                            <td>
                                @if ($auction->winner_id)
                                    {{ $auction->winner->name }}
                                    <span class="muted">(${{ number_format($auction->winning_amount, 2) }})</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($auction->isEditable())
                                    <div class="actions">
                                        <a href="{{ route('seller.auctions.edit', $auction) }}" class="btn btn-sm btn-secondary">Edit</a>

                                        <form method="POST"
                                            action="{{ route('seller.auctions.cancel', $auction) }}"
                                            onsubmit="return confirm('Cancel this auction?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="muted">Locked</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection