<?php

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::prefix('/')->group(function(){
    Route::get('/', [
        'uses' => 'OverviewController@index',
        'as' => 'overview.index'
    ]);

    Route::get('index', [
        'uses' => 'OverviewController@index',
        'as' => 'overview.index'
    ]);

    Route::get('home', [
        'uses' => 'OverviewController@index',
        'as' => 'overview.index'
    ]);
});

Route::prefix('order')->group(function(){
    Route::prefix('gallon')->group(function(){
        Route::get('/', [
            'uses' => 'OrderGallonController@index',
            'as' => 'order.gallon.index'
        ]);

        Route::get('make', [
            'uses' => 'OrderGallonController@showMake',
            'as' => 'order.gallon.make'
        ]);

        Route::get('inventory', [
            'uses' => 'OrderGallonController@showInventory',
            'as' => 'order.gallon.inventory'
        ]);

        Route::prefix('post')->group(function(){
            Route::post('make', [
                'uses' => 'OrderGallonController@doMake',
                'as' => 'order.gallon.post.make'
            ]);

            Route::post('update', [
                'uses' => 'OrderGallonController@doUpdate',
                'as' => 'order.gallon.post.update'
            ]);

            Route::post('delete', [
                'uses' => 'OrderGallonController@doDelete',
                'as' => 'order.gallon.post.delete'
            ]);
        });
    });
});

/**
 * Auth::routes() are :
 *
 * // Authentication Routes...
 * Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
 * Route::post('login', 'Auth\LoginController@login');
 * Route::post('logout', 'Auth\LoginController@logout');
 *
 * // Registration Routes...
 * Route::get('register', 'Auth\RegisterController@showRegistrationForm');
 * Route::post('register', 'Auth\RegisterController@register');
 *
 * // Password Reset Routes...
 * Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
 * Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
 * Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
 * Route::post('password/reset', 'Auth\ResetPasswordController@reset');
 */

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
