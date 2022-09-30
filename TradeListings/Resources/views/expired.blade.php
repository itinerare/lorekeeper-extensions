@extends('trade_listings::layout')

@section('trade-title') Expired @endsection

@section('trade-content')
{!! breadcrumbs(['Trades' => 'trades/open', 'Listings' => 'trades/listings', 'Expired' => 'trades/listings/expired']) !!}

<h1>
    My Expired Trade Listings
</h1>

<p>Here are all of your expired trade listings. Listings are active for {{ $listingDuration }} days before they expire, after which they can be viewed via their permalink.</p>

{!! $listings->render() !!}
@foreach($listings as $listing)
    @include('trade_listings::widgets._listing')
@endforeach
{!! $listings->render() !!}


@endsection
