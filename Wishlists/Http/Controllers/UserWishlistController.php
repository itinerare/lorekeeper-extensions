<?php

namespace Extensions\Wishlists\Http\Controllers;

use App\Http\Controllers\Users\UserController;
use App\Models\Item\Item;
use Extensions\Wishlists\Models\Wishlist;
use Extensions\Wishlists\Models\WishlistItem;
use Extensions\Wishlists\Models\WishlistUser;
use Illuminate\Http\Request;

class UserWishlistController extends UserController {
    /**
     * Shows the user's wishlists.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWishlists(Request $request) {
        $query = WishlistUser::find($this->user->id)->wishlists();

        $data = $request->only(['name', 'sort']);

        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->orderBy('name', 'ASC');
                    break;
                case 'alpha-reverse':
                    $query->orderBy('name', 'DESC');
                    break;
                case 'newest':
                    $query->orderBy('id', 'DESC');
                    break;
                case 'oldest':
                    $query->orderBy('id', 'ASC');
                    break;
            }
        } else {
            $query->orderBy('name', 'ASC');
        }

        return view('wishlists::user.wishlists', [
            'user'      => $this->user,
            'wishlists' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows a wishlist's page.
     *
     * @param mixed      $name
     * @param mixed|null $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWishlist($name, Request $request, $id = null) {
        if ($id) {
            $wishlist = Wishlist::where('id', $id)->where('user_id', $this->user->id)->first();
            if (!$wishlist) {
                abort(404);
            }

            $query = $wishlist->items();
        } else {
            $wishlist = null;
            $query = WishlistItem::where('wishlist_id', 0)->where('user_id', $this->user->id);
        }

        $data = $request->only(['name', 'sort']);

        if (isset($data['name'])) {
            $query->where(Item::select('name')->whereColumn('items.id', 'user_wishlist_items.item_id'), 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->orderBy(Item::select('name')->whereColumn('items.id', 'user_wishlist_items.item_id'), 'ASC');
                    break;
                case 'alpha-reverse':
                    $query->orderBy(Item::select('name')->whereColumn('items.id', 'user_wishlist_items.item_id'), 'DESC');
                    break;
                case 'newest':
                    $query->orderBy('id', 'DESC');
                    break;
                case 'oldest':
                    $query->orderBy('id', 'ASC');
                    break;
            }
        } else {
            $query->orderBy(Item::select('name')->whereColumn('items.id', 'user_wishlist_items.item_id'), 'ASC');
        }

        return view('wishlists::user.wishlist', [
            'user'     => $this->user,
            'wishlist' => $wishlist,
            'items'    => $query->paginate(20)->appends($request->query()),
        ]);
    }
}
