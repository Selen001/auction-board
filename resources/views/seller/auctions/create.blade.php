@extends('layouts.app')

@section('title', 'Create Auction')

@section('content')
<div class="card card-narrow">
    <h1>Create auction</h1>

    <form method="POST" action="{{ route('seller.auctions.store') }}">
        @include('seller.auctions._form')

        <button type="submit" class="btn">Create auction</button>
        <a href="{{ route('seller.auctions.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection