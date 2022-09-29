@if (Auth::check() && (new Extensions\Wishlists\Models\Wishlist())->isWishlisted($item->id, Auth::user()))
    <i class="fa fa-clipboard-list text-success" data-toggle="tooltip" title="In one of your wishlists!"></i>
@endif
