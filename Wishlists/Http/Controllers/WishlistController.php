<?php

namespace Extensions\Wishlists\Http\Controllers;

use App\Http\Controllers\Admin\HomeController;
use App\Models\Item\Item;
use Extensions\Wishlists\Models\Wishlist;
use Extensions\Wishlists\Models\WishlistItem;
use Extensions\Wishlists\Models\WishlistUser;
use Extensions\Wishlists\Services\WishlistManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends HomeController {
    /*
    |--------------------------------------------------------------------------
    | Wishlist Controller
    |--------------------------------------------------------------------------
    |
    | Handles wishlist management for the user.
    |
    */

    /**
     * Shows the user's wishlists.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWishlists(Request $request) {
        $query = WishlistUser::find(Auth::user()->id)->wishlists();
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

        return view('wishlists::home.wishlists', [
            'wishlists' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows a wishlist's page.
     *
     * @param mixed|null $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWishlist(Request $request, $id = null) {
        if ($id) {
            $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
            if (!$wishlist) {
                abort(404);
            }

            $query = $wishlist->items();
        } else {
            $wishlist = null;
            $query = WishlistItem::where('wishlist_id', 0)->where('user_id', Auth::user()->id);
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

        return view('wishlists::home.wishlist', [
            'user'     => WishlistUser::find(Auth::user()->id),
            'wishlist' => $wishlist,
            'items'    => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create wishlist modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateWishlist() {
        return view('wishlists::home._create_edit_wishlist', [
            'wishlist' => new Wishlist,
        ]);
    }

    /**
     * Shows the edit wishlist modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditWishlist($id) {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$wishlist) {
            abort(404);
        }

        return view('wishlists::home._create_edit_wishlist', [
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * Creates or edits a wishlist.
     *
     * @param App\Services\WishlistManager $service
     * @param int|null                     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditWishlist(Request $request, WishlistManager $service, $id = null) {
        $data = $request->only(['name']);

        if ($id && $service->updateWishlist($data, Wishlist::find($id), Auth::user())) {
            flash('Wishlist updated successfully.')->success();
        } elseif (!$id && $bookmark = $service->createWishlist($data, Auth::user())) {
            flash('Wishlist created successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Creates or edits a wishlist item.
     *
     * @param App\Services\WishlistManager $service
     * @param int                          $wishlistId
     * @param int                          $itemId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditWishlistItem(Request $request, WishlistManager $service, $wishlistId, $itemId = null) {
        $data = $request->only([
            'count',
        ]);

        if (!$itemId && $wishlistId) {
            $itemId = $wishlistId;
            $wishlist = 0;

            $count = (new Wishlist)->itemCount($itemId, Auth::user());
        } else {
            $wishlist = Wishlist::where('id', $wishlistId)->where('user_id', Auth::user()->id)->first();
            if (!$wishlist) {
                abort(404);
            }

            $count = $wishlist->itemCount($itemId, Auth::user());
        }

        if ($count) {
            $request->validate(WishlistItem::$updateRules);
        }

        if ($count && $service->updateWishlistItem($wishlist, Item::find($itemId), $data, Auth::user())) {
            flash('Wishlist item updated successfully.')->success();
        } elseif (!$count && $service->createWishlistItem($wishlist, Item::find($itemId), Auth::user())) {
            flash('Wishlist item added successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Moves a wishlist item.
     *
     * @param App\Services\WishlistManager $service
     * @param int                          $wishlistId
     * @param int                          $itemId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMoveWishlistItem(Request $request, WishlistManager $service, $wishlistId, $itemId = null) {
        $data = $request->only([
            'source_id',
        ]);

        if (!$itemId && $wishlistId) {
            $itemId = $wishlistId;
            $wishlist = 0;

            $count = (new Wishlist)->itemCount($itemId, Auth::user());
        } else {
            $wishlist = Wishlist::where('id', $wishlistId)->where('user_id', Auth::user()->id)->first();
            if (!$wishlist) {
                abort(404);
            }

            $count = $wishlist->itemCount($itemId, Auth::user());
        }

        if ($service->moveWishlistItem($wishlist, Item::find($itemId), $data, Auth::user())) {
            flash('Wishlist item moved successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the delete wishlist modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteWishlist($id) {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$wishlist) {
            abort(404);
        }

        return view('wishlists::home._delete_wishlist', [
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * Deletes a wishlist.
     *
     * @param App\Services\WishlistManager $service
     * @param int                          $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteWishlist(Request $request, WishlistManager $service, $id) {
        if ($id && $service->deleteWishlist(Wishlist::find($id), Auth::user())) {
            flash('Wishlist deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('wishlists');
    }
}
