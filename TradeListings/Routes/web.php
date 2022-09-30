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

Route::prefix('tradelistings')->group(function () {
    Route::get('/', 'TradeListingsController@index');
});

/**********************************************************************************************
    Routes that require login
**********************************************************************************************/
Route::group(['middleware' => ['auth', 'verified']], function () {
    //

    /**********************************************************************************************
        Routes that require having a linked account (also includes blocked routes when banned)
    **********************************************************************************************/
    Route::group(['middleware' => ['alias']], function () {
        //

        Route::group(['prefix' => 'trades'], function () {
            Route::get('listings', 'TradeListingController@getListingIndex');
            Route::get('listings/expired', 'TradeListingController@getExpiredListings');
            Route::get('listings/create', 'TradeListingController@getCreateListing');
            Route::post('listings/create', 'TradeListingController@postCreateListing');
            Route::get('listings/{id}', 'TradeListingController@getListing')->where('id', '[0-9]+');
            Route::post('listings/{id}/expire', 'TradeListingController@postExpireListing')->where('id', '[0-9]+');
        });

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['staff']], function () {
            //
        });
    });
});
