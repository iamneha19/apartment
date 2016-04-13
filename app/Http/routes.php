<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group([
    'prefix' => 'dashboard'
], function()
{
    Route::get('/', [ 'as' => 'home', 'uses' => 'HomeController@index']);

Route::post('login', [ 'as' => 'login', 'uses' => 'SiteController@login']);
Route::post('reset_password_session',[ 'as' => 'reset_password_session', 'uses' => 'SiteController@reset_password_session']);
Route::get('logout', [ 'as' => 'logout', 'uses' => 'UserController@logout']);
Route::post('switch', [ 'as' => 'switch', 'uses' => 'UserController@switchSociety']);
Route::post('updateflat', [ 'as' => 'updateflat', 'uses' => 'UserController@updateFlat']);

    Route::get('changePassword',[
		'as' => 'change_password', 'uses' => 'ChangePasswordController@index'
    ]);
Route::get('resetpassword',[
    		'as' => 'reset_password', 'uses' => 'ChangePasswordController@reset_forgotPwd'
    ]);
Route::get('home', 'HomeController@index');

    Route::get('home', 'HomeController@index');

    Route::get('myflat', [ 'as' => 'myflat', 'uses' => 'UserController@myflat']);
    Route::get('folders', [ 'as' => 'folders', 'uses' => 'DocumentController@index']);
//    Route::get('documents/resident/{id}', ['as' => 'document.resident', 'uses' => 'DocumentController@residentFiles']);
    Route::get('documents/official/{id}', ['as' => 'document.official', 'uses' => 'DocumentController@officialFiles']);
    Route::get('documents/resident/', ['as' => 'document.resident', 'uses' => 'DocumentController@residentFiles']);
    //Route::get('documents/{id}', ['as' => 'documents', 'uses' => 'DocumentController@files']);
    Route::get('documents/edit/{id}', ['as' => 'documents.edit', 'uses' => 'DocumentController@edit']);
    Route::get('flat/documents/edit/{id}', ['as' => 'documents.flat_edit', 'uses' => 'DocumentController@EditFlatDocument']);
    Route::get('documents/download', ['as' => 'documents.download', 'uses' => 'DocumentController@download']);
    Route::get('documents/downloadotherfile', ['as' => 'documents.downloadother', 'uses' => 'DocumentController@downloadotherfile']);

    Route::get('notice', [ 'as' => 'notice', 'uses' => 'NoticeController@index']);
   // Route::get('notice/old', [ 'as' => 'notice.old', 'uses' => 'NoticeController@old']);
    Route::get('notice/{id}', [ 'as' => 'notice.view', 'uses' => 'NoticeController@view']);
//Route::get('notice/edit/{id}', [ 'as' => 'notice.edit', 'uses' => 'NoticeController@edit']);

    Route::get('members', [ 'as' => 'members', 'uses' => 'UserController@members']);

    Route::get('officialcommunication', [ 'as' => 'officialcommunication', 'uses' => 'OfficialCommController@index']);


    Route::get('albums', [ 'as' => 'albums', 'uses' => 'AlbumController@index']);
    Route::get('album/photos/{id}', [ 'as' => 'album.photos', 'uses' => 'AlbumController@photos']);
    Route::get('albums/upload', [ 'as' => 'album.upload', '`uses`' => 'AlbumController@upload']);


    Route::controllers([
    	'auth' => 'Auth\AuthController',
    	'password' => 'Auth\PasswordController',
    ]);
    Route::get('about',['as'=>'about',function()
    {
        return View::make('pages.about');
    }] );

    Route::get('contact',['as'=>'contact',function()
    {
        return View::make('pages.contact');
    }] );

Route::get('/conversations', ['as'=>'conversations','middleware' => 'checkSession',function(){

    if (strtolower(session()->get('role_name')) == 'admin') {
        return  redirect()->route('admin.dashboard');
    }   
    if( !session()->get('acl.admin') && !session()->get('acl.resident'))
    {
        return view('user.warning');
    }else{
//        dd(session()->get('acl.resident')[0]->route);
        if(array_key_exists('res_conversation', session()->get('acl.resident'))){
          return view('communication.conversations');
        }else{
//          dd(current(session()->get('acl.resident'))['route']);
          return redirect()->route(current(session()->get('acl.resident'))['route']);
        }


    }

}]);

Route::get('/helpdesk',['as'=>'helpdesk','middleware' => 'checkSession',function(){
	return view('helpdesk.index');
}]);
Route::get('events',[
		'as' => 'events','middleware' => 'checkSession', 'uses' => 'CalendarController@index'
]);

Route::get('personal_info',[
		'as' => 'personal_info','middleware' => 'checkSession', 'uses' => 'UserController@user_personalInfo'
]);
});
