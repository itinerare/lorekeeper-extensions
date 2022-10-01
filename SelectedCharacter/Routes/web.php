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
        Route::group(['prefix' => 'characters'], function () {
            Route::post('select-character', 'SelectedCharacterController@postSelectCharacter');
        });

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['staff']], function () {
            //
        });
    });
});
