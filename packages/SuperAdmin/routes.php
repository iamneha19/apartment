<?php
Route::group([
    'prefix' => 'dashboard/superadmin',
    'namespace' => 'Packages\SuperAdmin\Controller',
    'middleware' => 'App\Http\Middleware\SuperAdminSession'
],
function()
{
    Route::get('/', [
        'as' => 'super_admin.login', 'uses' => 'IndexController@index'
    ]);

    Route::get('logout', [
        'as' => 'super_admin.logout', 'uses' => 'IndexController@logout'
    ]);

     Route::get('dashboard/', [
        'as' => 'super_admin.dashboard', 'uses' => 'IndexController@dashboard'
    ]);

    Route::post('storesession/', [
        'as' => 'super_admin.storesession', 'uses' => 'IndexController@storeSession'
    ]);

    Route::get('societies/', [
        'as' => 'super_admin.societies', 'uses' => 'SocietyController@index'
    ]);

     Route::get('state/',[
        'as' => 'super_admin.state', 'uses' => 'StateController@index'
    ]);
        Route::get('city/',[
        'as' => 'super_admin.city', 'uses' => 'CityController@index'
    ]);
     Route::get('societytype/',[
        'as' => 'super_admin.societytype', 'uses' => 'SocietyTypeController@index'
    ]);
    Route::get('type/',[
        'as' => 'super_admin.type', 'uses' => 'TypeController@index'
    ]);
     Route::get('files/',[
        'as' => 'super_admin.files', 'uses' => 'FileController@index'
    ]);
     Route::get('division/',[
        'as' => 'super_admin.division', 'uses' => 'DivisionController@index'
    ]);
      Route::get('region/',[
        'as' => 'super_admin.region', 'uses' => 'RegionController@index'
    ]);
       Route::get('district/',[
        'as' => 'super_admin.district', 'uses' => 'DistrictController@index'
    ]);
	
	 Route::get('societyrole/',[
		'as' => 'super_admin.societyrole', 'uses' => 'SocietyRoleController@index'
		]);
	
});
