<?php

Route::group([
    'prefix'    => 'dashboard/admin',
    'namespace' => 'Packages\Billing\Controllers',
    'middleware'=> 'App\Http\Middleware\CheckSession'
], function() {

    // Billing configuration
	get('billing/config/create',[
        'uses' => 'BillingConfigController@create',
        'as'   => 'billing.config',
    ]);

    // Billing
    get('billings', [
            'uses' => 'BillingController@index',
            'as'   => 'billing.index'
        ]);
    get('billing/create', [
            'uses' => 'BillingController@create',
            'as'   => 'billing.create'
        ]);

    // Bill item
    get('billing/items', [
            'uses' => 'ItemController@index',
            'as'   => 'billing.item.index'
        ]);
    get('billing/item/create', [
            'uses' => 'ItemController@create',
            'as'   => 'billing.item.create'
        ]);
    get('billing/item/config', [
            'uses' => 'ItemController@config',
            'as'   => 'billing.item.config'
        ]);

    // Flat Bill
    get('bills/{year}/{month}', [
        'uses' => 'FlatBillController@index',
        'as' => 'billing.bills'
    ]);

    get('bill/dashboard', [
        'uses' => 'FlatBillController@dashboard',
        'as' => 'billing.dashboard'
    ]);
});
