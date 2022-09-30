@extends('layouts.app')

@section('title')
    Trade Listings ::
    @yield('trade-title')
@endsection

@section('sidebar')
    @if (View::exists('trade_listings::_sidebar'))
        @include('trade_listings::_sidebar')
    @endif
@endsection

@section('content')
    @yield('trade-content')
@endsection

@section('scripts')
    @parent
@endsection
