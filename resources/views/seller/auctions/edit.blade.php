@extends('layouts.app')

@section('title', 'Edit Auction')

@section('content')
<div class="card card-narrow">
    <h1>Edit auction</h1>

    <form method="POST" action="{{ route('seller.auctions.update', $auction) }}">
        @method('PUT')
        @include('seller.auctions._form')

        <button type="submit" class="btn">Save changes</button>
        <a href="{{ route('seller.auctions.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection