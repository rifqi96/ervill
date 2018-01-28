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

Route::prefix('profile')->group(function(){
    Route::get('/', [
        'uses' => 'UserController@showProfile',
        'as' => 'profile.index'
    ]);

    Route::prefix('do')->group(function(){
        Route::post('update', [
            'uses' => 'UserController@doUpdateProfile',
            'as' => 'profile.do.updateProfile'
        ]);
    });
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

    Route::prefix('do')->group(function(){
        Route::post('make', [
            'uses' => 'ShipmentController@doMake',
            'as' => 'shipment.do.make'
        ]);

        Route::post('update', [
            'uses' => 'ShipmentController@doUpdate',
            'as' => 'shipment.do.update'
        ]);

        Route::post('delete', [
            'uses' => 'ShipmentController@doDelete',
            'as' => 'shipment.do.delete'
        ]);
    });
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

            Route::post('confirm', [
                'uses' => 'OrderGallonController@doConfirm',
                'as' => 'order.gallon.do.confirm'
            ]);

            Route::post('cancel', [
                'uses' => 'OrderGallonController@doCancel',
                'as' => 'order.gallon.do.cancel'
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
        Route::get('issue/{orderWater}', [
            'uses' => 'OrderWaterController@createIssue',
            'as' => 'order.water.issue'
        ]);

        Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'OrderWaterController@doMake',
                'as' => 'order.water.do.make'
            ]);

            Route::post('update', [
                'uses' => 'OrderWaterController@doUpdate',
                'as' => 'order.water.do.update'
            ]);

            Route::post('delete', [
                'uses' => 'OrderWaterController@doDelete',
                'as' => 'order.water.do.delete'
            ]);

            Route::post('confirm', [
                'uses' => 'OrderWaterController@doConfirm',
                'as' => 'order.water.do.confirm'
            ]);

            Route::post('cancel', [
                'uses' => 'OrderWaterController@doCancel',
                'as' => 'order.water.do.cancel'
            ]);

            Route::post('confirmWithIssue', [
                'uses' => 'OrderWaterController@doConfirmWithIssue',
                'as' => 'order.water.do.confirmWithIssue'
            ]);
        });

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
        Route::get('id/{id}', [
            'uses' => 'OrderCustomerController@showDetails',
            'as' => 'order.customer.details'
        ]);

        Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'OrderCustomerController@doMake',
                'as' => 'order.customer.do.make'
            ]);

            Route::post('update', [
                'uses' => 'OrderCustomerController@doUpdate',
                'as' => 'order.customer.do.update'
            ]);

            Route::post('delete', [
                'uses' => 'OrderCustomerController@doDelete',
                'as' => 'order.customer.do.delete'
            ]);

            Route::post('confirm', [
                'uses' => 'OrderCustomerController@doConfirm',
                'as' => 'order.customer.do.confirm'
            ]);

            Route::post('addIssue', [
                'uses' => 'OrderCustomerController@addIssueByAdmin',
                'as' => 'order.customer.do.addIssue'
            ]);

            Route::post('', [
                'uses' => 'OrderCustomerController@filterBy',
                'as' => 'order.customer.do.filterby'
            ]);
        });

        Route::prefix('buy')->group(function(){
            Route::get('/', [
                'uses' => 'OrderCustomerBuyController@index',
                'as' => 'order.customer.buy.index'
            ]);

            Route::get('create', [
                'uses' => 'OrderCustomerBuyController@showMake',
                'as' => 'order.customer.buy.make'
            ]);

            Route::prefix('do')->group(function(){
                Route::post('make', [
                    'uses' => 'OrderCustomerBuyController@doMake',
                    'as' => 'order.customer.buy.do.make'
                ]);

                Route::post('delete', [
                    'uses' => 'OrderCustomerBuyController@doDelete',
                    'as' => 'order.customer.buy.do.delete'
                ]);
            });

        });
    });
});

Route::prefix('return')->group(function(){
    Route::get('/', [
        'uses' => 'OrderCustomerReturnController@index',
        'as' => 'return.index'
    ]);

    Route::get('create', [
        'uses' => 'OrderCustomerReturnController@showMake',
        'as' => 'return.make'
    ]);

    Route::prefix('do')->group(function(){
        Route::post('make', [
            'uses' => 'OrderCustomerReturnController@doMake',
            'as' => 'return.do.make'
        ]);

        Route::post('confirm', [
            'uses' => 'OrderCustomerReturnController@doConfirm',
            'as' => 'return.do.confirm'
        ]);

        Route::post('cancel', [
            'uses' => 'OrderCustomerReturnController@doCancel',
            'as' => 'return.do.cancel'
        ]);
    });

});

Route::prefix('inventory')->group(function(){
    Route::get('/', [
            'uses' => 'InventoryController@index',
            'as' => 'inventory.index'
        ]);

    Route::prefix('do')->group(function(){
        Route::post('update', [
            'uses' => 'InventoryController@doUpdate',
            'as' => 'inventory.do.update'
        ]);            
    });
});

Route::prefix('history')->group(function(){

    Route::get('edit', [
        'uses' => 'HistoryController@showEdit',
        'as' => 'history.edit.index'
    ]);

    Route::get('delete', [
        'uses' => 'HistoryController@showDelete',
        'as' => 'history.delete.index'
    ]);

    Route::post('do/restore-or-delete', [
        'uses' => 'HistoryController@doRestoreOrDelete',
        'as' => 'history.do.restore_or_delete'
    ]);

    Route::post('do/mass-restore-or-delete', [
        'uses' => 'HistoryController@doMassRestoreOrDelete',
        'as' => 'history.do.mass_restore_or_delete'
    ]);

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

         Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'OutsourcingController@doMake',
                'as' => 'setting.outsourcing.do.make'
            ]);
            Route::post('updateWater', [
                'uses' => 'OutsourcingController@doUpdateWater',
                'as' => 'setting.outsourcing.do.updateWater'
            ]);
            Route::post('updateDriver', [
                'uses' => 'OutsourcingController@doUpdateDriver',
                'as' => 'setting.outsourcing.do.updateDriver'
            ]);
            Route::post('deleteWater', [
                'uses' => 'OutsourcingController@doDeleteWater',
                'as' => 'setting.outsourcing.do.deleteWater'
            ]);
            Route::post('deleteDriver', [
                'uses' => 'OutsourcingController@doDeleteDriver',
                'as' => 'setting.outsourcing.do.deleteDriver'
            ]);
        });
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

        Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'UserController@doMake',
                'as' => 'setting.user_management.do.make'
            ]);
            Route::post('update', [
                'uses' => 'UserController@doUpdate',
                'as' => 'setting.user_management.do.update'
            ]);
            Route::post('delete', [
                'uses' => 'UserController@doDelete',
                'as' => 'setting.user_management.do.delete'
            ]);
        });

    });

    Route::prefix('customers')->group(function(){
        Route::get('/', [
            'uses' => 'CustomerController@index',
            'as' => 'setting.customers.index'
        ]);
        Route::get('/create', [
            'uses' => 'CustomerController@showMake',
            'as' => 'setting.customers.make'
        ]);

        Route::prefix('do')->group(function(){
            Route::post('make', [
                'uses' => 'CustomerController@doMake',
                'as' => 'setting.customers.do.make'
            ]);
            Route::post('update', [
                'uses' => 'CustomerController@doUpdate',
                'as' => 'setting.customers.do.update'
            ]);
            Route::post('delete', [
                'uses' => 'CustomerController@doDelete',
                'as' => 'setting.customers.do.delete'
            ]);
        });

    });
});

Route::prefix('issue')->group(function(){
    Route::prefix('do')->group(function(){
        Route::post('delete', [
            'uses' => 'IssueController@doDelete',
            'as' => 'issue.do.delete'
        ]);            
    });
});


// Route::group(['middleware' => ['cors']], function() {
//     Route::post('api','ServiceController@api');
// });
Route::post('api','ServiceController@api');


Route::get('/getUsers', 'UserController@getUsers');
Route::get('/getOutsourcingWaters', 'OutsourcingController@getOutsourcingWaters');
Route::get('/getOutsourcingDrivers', 'OutsourcingController@getOutsourcingDrivers');
Route::get('/getEditHistories', 'HistoryController@getEditHistories');
Route::get('/getOrderGallons', 'OrderGallonController@getOrderGallons');
Route::get('/getInventories', 'InventoryController@getInventories');
Route::get('/getOrderWaters', 'OrderWaterController@getAll');

Route::get('/getCustomers', 'CustomerController@getAll');
Route::get('/getOrderCustomers', 'OrderCustomerController@getAll');
Route::get('/getFinishedShipments', 'ShipmentController@getAllFinished');
Route::get('/getUnfinishedShipments', 'ShipmentController@getAllUnfinished');
Route::post('/getAvailableShipmentsByDate', 'ShipmentController@getAvailableShipmentsByDate');
Route::post('/getUnshippedOrders', 'OrderCustomerController@getUnshippedOrders');
Route::get('/getAllDrivers', 'UserController@getAllDrivers');
Route::get('/getShipmentById/{shipment_id}', 'ShipmentController@getShipmentById');
Route::get('/getCustomerGallon', 'CustomerGallonController@getCustomerGallon');

Route::get('/getReturns', 'OrderCustomerReturnController@getAll');
Route::get('/getOrderCustomerBuys', 'OrderCustomerBuyController@getAll');

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
