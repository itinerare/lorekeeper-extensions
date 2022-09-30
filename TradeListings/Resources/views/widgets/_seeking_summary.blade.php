@if ($data)
    @if ($data['items'])
        <div class="row">
            <div class="col-sm-2">
                <strong>Items:</strong>
            </div>
            <div class="col-md">
                <div class="row">
                    @foreach ($data['items'] as $itemRow)
                        <div class="col-sm-4">
                            <a href="/world/items?name={{ $itemRow['asset']->name }}">{!! $itemRow['asset']->name !!}</a> x{!! $itemRow['quantity'] !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if ($data['currencies'])
        <div class="row">
            <div class="col-sm-2">
                <strong>Currencies:</strong>
            </div>
            <div class="col-md">
                <div class="row">
                    @foreach ($data['currencies'] as $currency)
                        <div class="col-sm-3">
                            {!! $currency['asset']->display('') !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif
@if (isset($wishlists) && $wishlists)
    <div class="row">
        <div class="col-sm-2">
            <strong>Wishlist Items:</strong>
        </div>
        <div class="col-md">
            @foreach ($listing->data['seeking_wishlists'] as $wishlist)
                <a
                    href="{{ url('user/' . $user->name . '/wishlists/' . $wishlist) }}">{{ $wishlist == 'default'? 'Default': App\Models\User\Wishlist::where('user_id', $listing->user->id)->where('id', $wishlist)->first()->name }}</a>{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>
    </div>
@endif
@if (isset($etc) && $etc)
    <div class="row">
        <div class="col-sm-2">
            <strong>Other:</strong>
        </div>
        <div class="col-md">
            {!! nl2br(htmlentities($etc)) !!}
        </div>
    </div>
@endif
