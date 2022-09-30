<?php

namespace Extensions\TradeListings\Http\Controllers;

use App\Facades\Settings;
use App\Models\Character\CharacterCategory;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\User\UserItem;
use Extensions\TradeListings\Models\TradeListing;
use Extensions\TradeListings\Services\TradeListingManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class TradeListingController extends Controller {
    /**********************************************************************************************

        TRADE LISTINGS

    **********************************************************************************************/

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        if (class_exists(\Extensions\Wishlists\Models\Wishlist::class)) {
            $this->wishlistClass = \Extensions\Wishlists\Models\Wishlist::class;
        } else {
            $this->wishlistClass = null;
        }
    }

    /**
     * Shows the trade listing index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getListingIndex(Request $request) {
        return view('trade_listings::index', [
            'listings'        => TradeListing::active()->orderBy('id', 'DESC')->paginate(10),
            'listingDuration' => Settings::get('trade_listing_duration'),
            'wishlists'       => isset($this->wishlistClass),
        ]);
    }

    /**
     * Shows the user's expired trade listings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getExpiredListings(Request $request) {
        return view('trade_listings::expired', [
            'listings'        => TradeListing::expired()->where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10),
            'listingDuration' => Settings::get('trade_listing_duration'),
            'wishlists'       => isset($this->wishlistClass),
        ]);
    }

    /**
     * Shows a trade.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getListing($id) {
        $listing = TradeListing::find($id);
        if (!$listing) {
            abort(404);
        }

        return view('trade_listings::view_listing', [
            'listing'      => $listing,
            'seekingData'  => isset($listing->data['seeking']) ? parseAssetData($listing->data['seeking']) : null,
            'offeringData' => isset($listing->data['offering']) ? parseAssetData($listing->data['offering']) : null,
            'items'        => Item::all()->keyBy('id'),
            'wishlists'    => isset($this->wishlistClass) ? ['default' => 'Default'] + $this->wishlistClass::where('user_id', $listing->user->id)->pluck('name', 'id')->toArray() : null,
        ]);
    }

    /**
     * Shows the create trade listing page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateListing(Request $request) {
        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)
        ->get()
        ->filter(function ($userItem) {
            return $userItem->isTransferrable == true;
        })
        ->sortBy('item.name');
        $currencies = Currency::where('is_user_owned', 1)->where('allow_user_to_user', 1)->orderBy('sort_user', 'DESC')->get();

        return view('trade_listings::create_listing', [
            'items'               => Item::orderBy('name')->where('allow_transfer', 1)->pluck('name', 'id'),
            'currencies'          => $currencies,
            'categories'          => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter'         => Item::orderBy('name')->get()->keyBy('id'),
            'inventory'           => $inventory,
            'characters'          => Auth::user()->allCharacters()->visible()->tradable()->with('designUpdate')->get(),
            'characterCategories' => CharacterCategory::orderBy('sort', 'DESC')->get(),
            'page'                => 'listing',
            'listingDuration'     => Settings::get('trade_listing_duration'),
            'wishlists'           => isset($this->wishlistClass) ? ['default' => 'Default'] + $this->wishlistClass::where('user_id', Auth::user()->id)->pluck('name', 'id')->toArray() : null,
        ]);
    }

    /**
     * Creates a new trade listing.
     *
     * @param \Illuminate\Http\Request         $request
     * @param App\Services\TradeListingManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateListing(Request $request, TradeListingManager $service) {
        if ($listing = $service->createTradeListing($request->only(['title', 'comments', 'contact', 'item_ids', 'quantities', 'stack_id', 'stack_quantity', 'offer_currency_ids', 'seeking_currency_ids', 'character_id', 'offering_etc', 'seeking_etc', 'seeking_wishlists']), Auth::user())) {
            flash('Trade listing created successfully.')->success();

            return redirect()->to($listing->url);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Manually marks a trade listing as expired.
     *
     * @param \Illuminate\Http\Request         $request
     * @param App\Services\TradeListingManager $service
     * @param int                              $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postExpireListing(Request $request, TradeListingManager $service, $id) {
        $listing = TradeListing::find($id);
        if (!$listing) {
            abort(404);
        }

        if ($service->markExpired(['id' => $id], Auth::user())) {
            flash('Listing expired successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
