@extends('layouts.app')

@section('title', 'Bid With Confidence. Win What Matters.')
@section('meta_description', 'Discover live auctions, place competitive bids, and turn opportunities into wins in one trusted auction marketplace.')

@section('content')
@php
    $isLanding = $tab === 'open'
        && request()->query('view') !== 'all'
        && (int) request()->query('page', 1) <= 1;

    $exploreUrl = route('auctions.index', ['view' => 'all']);

    // Selling CTAs: guests register, sellers create. Buyers can't sell, so they see none.
    $user = auth()->user();
    $sellUrl = ! $user ? route('register') : ($user->isSeller() ? route('seller.auctions.create') : null);
@endphp

@if ($isLanding)
<div class="landing">

    {{-- 1. Hero --}}
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy">
            <p class="eyebrow">Online auction marketplace</p>
            <h1 id="hero-title">Bid With Confidence. Win What Matters.</h1>
            <p class="hero-lead">
                Discover live auctions, place competitive bids, and turn opportunities into wins — all in one trusted marketplace.
            </p>
            <div class="hero-actions">
                <x-button :href="$exploreUrl" size="lg">Explore Auctions</x-button>
                @if ($sellUrl)
                    <x-button :href="$sellUrl" variant="secondary" size="lg">Start Selling</x-button>
                @endif
            </div>
        </div>

        {{-- Presentation-only demo card. Not real data. --}}
        <article class="preview-card" aria-label="Example auction preview (demo only)">
            <div class="auction-visual" aria-hidden="true">
                <span class="live-badge">Live</span>
                <span class="auction-visual-initial">V</span>
            </div>
            <h2>Vintage Film Camera</h2>
            <div class="price-label">Current bid</div>
            <div class="price">$245.00</div>
            <ul class="info-list">
                <li><span>Bids</span><span>18</span></li>
                <li><span>Time remaining</span><span>02h 14m</span></li>
            </ul>
            <button type="button" class="btn btn-block" disabled>Place Bid</button>
            <p class="preview-note">Demo preview, not a real auction.</p>
        </article>
    </section>

    {{-- 2. Value strip --}}
    <section aria-label="Why Auction Board">
        <div class="feature-grid">
            <div class="feature-card">
                <span class="feature-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></span>
                <h3>Live Auctions</h3>
                <p>Browse auctions that are open right now.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></span>
                <h3>Secure Bidding</h3>
                <p>Every bid is validated on the server.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg></span>
                <h3>Trusted Marketplace</h3>
                <p>Transparent auction activity and results.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span>
                <h3>Built for Sellers</h3>
                <p>Simple auction management.</p>
            </div>
        </div>
    </section>

    {{-- 3. Featured auctions (first 3 already provided by the controller) --}}
    @php $featured = $auctions->getCollection()->take(3); @endphp
    <section aria-labelledby="live-title">
        <div class="section-head">
            <h2 id="live-title">Discover What's Live</h2>
            <p class="muted">Open auctions closing soonest.</p>
        </div>

        @if ($featured->isEmpty())
            <x-empty-state card>There are no open auctions right now. Check back soon.</x-empty-state>
        @else
            <div class="auction-grid">
                @foreach ($featured as $auction)
                    <x-auction-card :auction="$auction" featured />
                @endforeach
            </div>
        @endif

        <div class="section-foot">
            <x-button :href="$exploreUrl" variant="secondary">View all open auctions</x-button>
            <x-button :href="route('auctions.index', ['tab' => 'ended'])" variant="secondary">See ended auctions</x-button>
        </div>
    </section>

    {{-- 4. How it works --}}
    <section aria-labelledby="how-title">
        <div class="section-head">
            <h2 id="how-title">How It Works</h2>
            <p class="muted">Three simple steps from browsing to winning.</p>
        </div>
        <ol class="steps">
            <li class="step">
                <span class="step-number" aria-hidden="true">01</span>
                <h3>Discover</h3>
                <p>Find auctions worth bidding on.</p>
            </li>
            <li class="step">
                <span class="step-number" aria-hidden="true">02</span>
                <h3>Bid</h3>
                <p>Place a competitive bid with confidence.</p>
            </li>
            <li class="step">
                <span class="step-number" aria-hidden="true">03</span>
                <h3>Win</h3>
                <p>Highest valid bid wins when the auction closes.</p>
            </li>
        </ol>
    </section>

    {{-- 5. Seller CTA --}}
    @if ($sellUrl)
    <section class="seller-cta" aria-labelledby="seller-title">
        <div>
            <h2 id="seller-title">Turn Your Items Into Opportunities</h2>
            <p>Create auctions, attract competitive bidders, and manage your listings from one powerful dashboard.</p>
        </div>
        <x-button :href="$sellUrl" size="lg">Start Selling</x-button>
    </section>
    @endif

    {{-- 6. Trust --}}
    <section aria-labelledby="trust-title">
        <div class="section-head">
            <h2 id="trust-title">Built on Clear Rules</h2>
            <p class="muted">How the marketplace keeps bidding fair.</p>
        </div>
        <ul class="trust-grid">
            <li class="trust-item">
                <h3>Validated bidding</h3>
                <p>Bids must meet the base price and beat the current highest bid. Sellers can't bid on their own auctions.</p>
            </li>
            <li class="trust-item">
                <h3>Time-bound auctions</h3>
                <p>Closing times are enforced by the server clock, so late bids are rejected.</p>
            </li>
            <li class="trust-item">
                <h3>Transparent winners</h3>
                <p>The winner and winning amount are shown once an auction closes.</p>
            </li>
            <li class="trust-item">
                <h3>Role-based access</h3>
                <p>Buyers bid and sellers manage listings, each with their own dashboard.</p>
            </li>
        </ul>
    </section>

    {{-- 7. Final CTA --}}
    <section class="cta-panel" aria-labelledby="cta-title">
        <h2 id="cta-title">Ready to Make Your Next Move?</h2>
        <p>Explore live auctions or start selling today.</p>
        <div class="cta-actions">
            <x-button :href="$exploreUrl" size="lg">Explore Auctions</x-button>
            @if ($sellUrl)
                <x-button :href="$sellUrl" variant="secondary" size="lg">Create an Auction</x-button>
            @endif
        </div>
    </section>

</div>

@else
    {{-- Board mode: existing tabs, grid and pagination --}}
    <div class="board-header">
        <h1>Auction Board</h1>
        <p class="muted">Browse items and see where the bidding stands.</p>
    </div>

    <x-tabs :active="$tab" :items="[
        'open'  => ['label' => 'Open',  'url' => route('auctions.index', ['view' => 'all'])],
        'ended' => ['label' => 'Ended', 'url' => route('auctions.index', ['tab' => 'ended'])],
    ]" />

    @if ($auctions->isEmpty())
        <x-empty-state card>
            {{ $tab === 'ended' ? 'No ended auctions yet.' : 'There are no open auctions right now.' }}
        </x-empty-state>
    @else
        <div class="auction-grid">
            @foreach ($auctions as $auction)
                <x-auction-card :auction="$auction" />
            @endforeach
        </div>

        {{ $auctions->links('pagination.simple') }}
    @endif
@endif
@endsection