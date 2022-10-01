<div class="row">
    <div class="col-md-6">
        @include('selectedcharacter::_selected_character', ['character' => Extensions\SelectedCharacter\Models\SelectedCharacterUserSettings::find($user->id)->selectedCharacter, 'user' => $user, 'fullImage' => config('selectedcharacter.use_full_image')])
    </div>
    <div class="col-md-6 mb-4 profile-assets" style="clear:both;">
        <div class="card profile-currencies profile-assets-card mb-4">
            <div class="card-body text-center">
                <h5 class="card-title">Bank</h5>
                <div class="profile-assets-content">
                    @foreach($user->getCurrencies(false) as $currency)
                        <div>{!! $currency->display($currency->quantity) !!}</div>
                    @endforeach
                </div>
                <div class="text-right"><a href="{{ $user->url.'/bank' }}">View all...</a></div>
            </div>
        </div>
        <div class="card profile-inventory profile-assets-card">
            <div class="card-body text-center">
                <h5 class="card-title">Inventory</h5>
                <div class="profile-assets-content">
                    @if(count($items))
                        <div class="row">
                            @foreach($items as $item)
                                <div class="col-md-3 col-6 profile-inventory-item">
                                    @if($item->imageUrl)
                                        <img src="{{ $item->imageUrl }}" data-toggle="tooltip" title="{{ $item->name }}" alt="{{ $item->name }}"/>
                                    @else
                                        <p>{{ $item->name }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div>No items owned.</div>
                    @endif
                </div>
                <div class="text-right"><a href="{{ $user->url.'/inventory' }}">View all...</a></div>
            </div>
        </div>
        @hook('users_profile_content_assets_addition')
    </div>
</div>
