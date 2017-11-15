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

Route::prefix('shipment')->group(function(){
    Route::get('/', [
        'uses' => 'ShipmentController@index',
        'as' => 'shipment.index'
    ]);

    Route::get('make', [
        'uses' => 'ShipmentController@showMake',
        'as' => 'shipment.make'
    ]);

    Route::get('track/{shipment_id}', [
        'uses' => 'ShipmentController@track',
        'as' => 'shipment.track'
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

        Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'OrderGallonController@doMake',
                'as' => 'order.gallon.do.make'
            ]);

            Route::post('update', [
                'uses' => 'OrderGallonController@doUpdate',
                'as' => 'order.gallon.do.update'
            ]);

            Route::post('delete', [
                'uses' => 'OrderGallonController@doDelete',
                'as' => 'order.gallon.do.delete'
            ]);
        });
    });
    Route::prefix('water')->group(function(){
        Route::get('/', [
            'uses' => 'OrderWaterController@index',
            'as' => 'order.water.index'
        ]);
        Route::get('create', [
            'uses' => 'OrderWaterController@showMake',
            'as' => 'order.water.make'
        ]);
        Route::get('issue/{id}', [
            'uses' => 'OrderWaterController@createIssue',
            'as' => 'order.water.issue'
        ]);
    });
    Route::prefix('customer')->group(function(){
        Route::get('/', [
            'uses' => 'OrderCustomerController@index',
            'as' => 'order.customer.index'
        ]);
        Route::get('create', [
            'uses' => 'OrderCustomerController@showMake',
            'as' => 'order.customer.make'
        ]);
    });
});


Route::prefix('setting')->group(function(){
    Route::prefix('outsourcing')->group(function(){
        Route::get('/', [
            'uses' => 'OutsourcingController@index',
            'as' => 'setting.outsourcing.index'
        ]);
         Route::get('create', [
            'uses' => 'OutsourcingController@showMake',
            'as' => 'setting.outsourcing.make'
        ]);    
    });

    Route::prefix('user_management')->group(function(){
        Route::get('/', [
            'uses' => 'UserController@index',
            'as' => 'setting.user_management.index'
        ]);
         Route::get('/create', [
            'uses' => 'UserController@showMake',
            'as' => 'setting.user_management.make'
        ]);    
    });

//    Route::prefix('user_role')->group(function(){
//        Route::get('/', [
//            'uses' => 'RoleController@index',
//            'as' => 'setting.user_role.index'
//        ]);
//         Route::get('/create', [
//            'uses' => 'RoleController@showMake',
//            'as' => 'setting.user_role.make'
//        ]);
//    });
//
//    Route::prefix('module_access')->group(function(){
//        Route::get('/', [
//            'uses' => 'ModuleAccessController@index',
//            'as' => 'setting.module_access.index'
//        ]);
//    });
    
});

Route::get('/profile', [
            'uses' => 'UserController@showProfile',
            'as' => 'profile.index'
]);

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
