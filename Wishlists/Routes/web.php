<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**********************************************************************************************
    Routes accessible to anyone
**********************************************************************************************/

// PROFILES
Route::group(['prefix' => 'user'], function () {
    Route::get('{name}/wishlists', 'UserWishlistController@getWishlists');
    Route::get('{name}/wishlists/{id}', 'UserWishlistController@getWishlist')->where(['id' => '[0-9]+']);
    Route::get('{name}/wishlists/default', 'UserWishlistController@getWishlist');
});

/**********************************************************************************************
    Routes that require login
**********************************************************************************************/
Route::group(['middleware' => ['auth', 'verified']], function () {
    //

    Route::group(['prefix' => 'wishlists'], function () {
        Route::get('/', 'WishlistController@getWishlists');
        Route::get('create', 'WishlistController@getCreateWishlist');
        Route::get('{id}', 'WishlistController@getWishlist')->where('id', '[0-9]+');
        Route::get('default', 'WishlistController@getWishlist');
        Route::get('edit/{id}', 'WishlistController@getEditWishlist');
        Route::get('delete/{id}', 'WishlistController@getDeleteWishlist');
        Route::post('create', 'WishlistController@postCreateEditWishlist');
        Route::post('edit/{id}', 'WishlistController@postCreateEditWishlist');
        Route::post('delete/{id}', 'WishlistController@postDeleteWishlist');
        Route::post('add/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('item_id', '[0-9]+');
        Route::post('{id}/add/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
        Route::post('default/update/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('item_id', '[0-9]+');
        Route::post('{id}/update/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
        Route::post('move/{item_id}', 'WishlistController@postMoveWishlistItem')->where('item_id', '[0-9]+');
        Route::post('{id}/move/{item_id}', 'WishlistController@postMoveWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
    });

    /**********************************************************************************************
        Routes that require having a linked account (also includes blocked routes when banned)
    **********************************************************************************************/
    Route::group(['middleware' => ['alias']], function () {
        //

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['staff']], function () {
            //
        });
    });
});
