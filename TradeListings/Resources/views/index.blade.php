@extends('trade_listings::layout')

@section('trade-title') Index @endsection

@section('trade-content')
{!! breadcrumbs(['Trades' => 'trades/open', 'Listings' => 'trades/listings']) !!}

<h1>
    Trade Listings
</h1>

<div class="text-right mb-2">
    <a href="{{ url('trades/listings/create') }}" class="btn btn-primary">New Trade Listing</a>
</div>

<p>
    Here are all active trade listings. Listings are active for {{ $listingDuration }} days before they expire, after which they can be viewed via their permalink. Note that listings only display what a user is seeking or offering based on the listing as entered, and do not directly interact with the trade system or update automatically.
</p>

{!! $listings->render() !!}
@foreach($listings as $listing)
    @include('trade_listings::widgets._listing')
@endforeach
{!! $listings->render() !!}

@endsection
