<?php
Route::group(['prefix' => 'dashboard/admin','middleware' => 'checkSession'], function()
{

	Route::get('/topic', 'Packages\Admin\Controller\TopicController@index');
	//Route::get('/officialcommunication', 'Packages\Admin\Controller\OfficialCommController@index');
	Route::get('/topic/{id}', 'Packages\Admin\Controller\TopicController@getTopicDetails');
    Route::get('/', [
        'as' => 'admin.dashboard', 'uses' => 'Packages\Admin\Controller\IndexController@index'
    ]);

    Route::get('/adminforum', [
    		'as' => 'admin.forums', 'uses' => 'Packages\Admin\Controller\AdminForumController@index'
    ]);

	Route::get('/adminforum/{page}/{search?}',['as' => 'admin.backforums','uses'=>'Packages\Admin\Controller\AdminForumController@backforums']);

    Route::get('/adminforumreply/{id}/{page}/{search?}',['as' => 'admin.forumreply','uses'=>'Packages\Admin\Controller\AdminForumController@forum_reply']);


	Route::get('/noticeboard', [
			'as' => 'admin.noticeboard', 'uses' => 'Packages\Admin\Controller\NoticeBoardController@index'
	]);
	Route::get('noticeboard/old', [ 'as' => 'admin.notice.old', 'uses' => 'Packages\Admin\Controller\NoticeBoardController@old']);

	Route::get('noticeboard/{id}', [ 'as' => 'admin.notice.view', 'uses' => 'Packages\Admin\Controller\NoticeBoardController@view']);

	Route::get('oldNoticeList/{id}', [ 'as' => 'admin.notice.viewOldNotice', 'uses' => 'Packages\Admin\Controller\NoticeBoardController@viewOldNotice']);





    Route::get('noticeboard/edit/{id}', [ 'as' => 'admin.notice.edit', 'uses' => 'Packages\Admin\Controller\NoticeBoardController@edit']);


	Route::get('/officialcommunication', [
    		'as' => 'admin.officialcommunication', 'uses' => 'Packages\Admin\Controller\OfficialCommController@index'
    ]);

	Route::get('user/', [
        'as' => 'admin.users', 'uses' => 'Packages\Admin\Controller\UserController@index'
    ]);

        Route::get('user/edit/{id}',[
            'as' => 'admin.user.edit', 'uses' => 'Packages\Admin\Controller\UserController@edit'
        ]);

        Route::get('user/flat/{id}',[
            'as' => 'admin.user.flat_edit', 'uses' => 'Packages\Admin\Controller\UserController@flat_edit'
        ]);

        Route::get('user/flat/edit/{id}',[
            'as' => 'admin.user.user_flat_edit', 'uses' => 'Packages\Admin\Controller\UserController@user_flat_edit'
        ]);


	// Route::post('posts/',[
        // 'as' => 'posts.users', 'uses' => 'Packages\Admin\Controller\UserController@create'
    // ]);
	Route::get('meeting/',[
        'as' => 'admin.meeting', 'uses' => 'Packages\Admin\Controller\MeetingController@index'
    ]);
        Route::get('meeting/oldmeeting/',[
            'as' => 'admin.oldmeeting', 'uses' => 'Packages\Admin\Controller\MeetingController@oldMeetings'
        ]);
	Route::get('meeting/edit/{id}',['as'=>'admin.meeting.edit','uses'=>'Packages\Admin\Controller\MeetingController@edit']);
	Route::get('meeting/{id}',['as'=>'admin.meeting.view','uses'=>'Packages\Admin\Controller\MeetingController@view']);

    Route::get('parking/',[
        'as' => 'admin.parking', 'uses' => 'Packages\Admin\Controller\ParkingController@index'
    ]);

    Route::get('parking/setup',[
        'as' => 'admin.parking_setup', 'uses' => 'Packages\Admin\Controller\ParkingController@setup'
    ]);

	Route::get('task_category/',[
        'as' => 'admin.task_category', 'uses' => 'Packages\Admin\Controller\TaskCategoryController@index'
    ]);
	Route::get('task_category/edit/{id}','Packages\Admin\Controller\TaskCategoryController@edit');

	Route::get('task_category/{id}','Packages\Admin\Controller\TaskCategoryController@view');

    /*
        Route::get('state/',[
        'as' => 'admin.state', 'uses' => 'Packages\Admin\Controller\StateController@index'
    ]);
        Route::get('city/',[
        'as' => 'admin.city', 'uses' => 'Packages\Admin\Controller\CityController@index'
    ]);
	*/
	Route::get('task/',[
        'as' => 'admin.task', 'uses' => 'Packages\Admin\Controller\TaskController@index'
    ]);

        Route::get('myTasks/',[
        'as' => 'admin.mytasks', 'uses' => 'Packages\Admin\Controller\TaskController@mytasks'
    ]);
        Route::get('task/oldTasks',[
        'as' => 'admin.oldtasks', 'uses' => 'Packages\Admin\Controller\TaskController@oldTasks'
    ]);
	Route::get('task/{id}','Packages\Admin\Controller\TaskController@view');

        Route::get('task/edit/{id}',['as' => 'admin.taskupdate','uses'=>'Packages\Admin\Controller\TaskController@edit']);

	Route::get('block/',[
        'as' => 'admin.block', 'uses' => 'Packages\Admin\Controller\BlockController@index'
    ]);

    Route::get('files/society',[
        'as' => 'admin.society_files', 'uses' => 'Packages\Admin\Controller\FileController@societyFiles'
    ]);
    Route::get('flat/documents',[
        'as' => 'admin.flat_documents', 'uses' => 'Packages\Admin\Controller\FileController@FlatDocument'
    ]);
    Route::get('flat/documents/files/{folder_id}',[
        'as' => 'admin.flat_documents_files', 'uses' => 'Packages\Admin\Controller\FileController@FlatDocumentFiles'
    ]);

    Route::get('files/common',[
        'as' => 'admin.files_common', 'uses' => 'Packages\Admin\Controller\FileController@common'
    ]);

    Route::get('files/{id}',[
        'as' => 'admin.files', 'uses' => 'Packages\Admin\Controller\FileController@fileList'
    ]);

    Route::get('files/edit/{id}',[
        'as' => 'admin.edit_file', 'uses' => 'Packages\Admin\Controller\FileController@edit'

    ]);

    Route::get('societyFiles/edit/{id}',[
        'as' => 'admin.editSociety_file', 'uses' => 'Packages\Admin\Controller\FileController@editSocietyDocument'
    ]);


     Route::get('societyFiles/report',[
        'as' => 'admin.report', 'uses' => 'Packages\Admin\Controller\FileController@societyFileReport'
    ]);
    Route::get('helpdesk',[
    		'as' => 'admin.helpdesk', 'uses' => 'Packages\Admin\Controller\HelpDeskController@index'
    ]);
     Route::get('type',[
    		'as' => 'admin.type', 'uses' => 'Packages\Admin\Controller\TypeController@index'
    ]);
    Route::get('acl/{id?}', ['as'=>'admin.acl','uses' => 'Packages\Admin\Controller\AclController@index']);

    Route::get('building/acl/{id}', ['as'=>'admin.building.acl','uses' => 'Packages\Admin\Controller\BuildingController@acl']);

    Route::get('complex', ['as'=>'admin.buildings','uses' => 'Packages\Admin\Controller\BuildingController@index']);

    Route::get('society_info', ['as'=>'admin.society_info','uses' => 'Packages\Admin\Controller\SocietyController@index']);

		Route::get('billing/config/create',[
        'as' => 'admin.billingconfig', 'uses' => 'Packages\Admin\Controller\BillingConfigController@create'
    ]);

    // Reminders

    Route::get('reminders/', [
        'as' => 'admin.reminders', 'uses' => 'Packages\Admin\Controller\ReminderController@index'
    ]);

    Route::group([
        'namespace' => 'Packages\Admin\Controller',
    ], function() {
        get('flat', [
            'uses' => 'FlatController@index',
            'as' => 'admin.flat'
        ]);
    });

  	Route::get('amenities',[
        'as' => 'admin.amenities', 'uses' => 'Packages\Admin\Controller\AmenitiesController@index'
    ]);
	
	Route::get('notification',[
        'as' => 'admin.notification', 'uses' => 'Packages\Admin\Controller\NotificationController@index'
    ]);

    Route::get('flat_documents/reports/',[
        'as' => 'admin.flat_reports', 'uses' => 'Packages\Admin\Controller\FlatReportsController@index'
    ]);


	Route::get('admin_personal_info',[
			'as' => 'admin_personal_info','middleware' => 'checkSession', 'uses' => 'Packages\Admin\Controller\UserController@user_personalInfo'
	]);

	   Route::get('admin_changePassword',[
		'as' => 'admin_changePassword', 'uses' => 'Packages\Admin\Controller\UserController@changepwd'
    ]);

	Route::get('admin_resetpassword',[
    		'as' => 'admin_reset_password', 'uses' => 'Packages\Admin\Controller\UserController@reset_forgotPwd'
    ]);

	Route::get('society/config',[
        'as' => 'admin.society.config', 'uses' => 'Packages\Admin\Controller\SocietyConfigController@index'
    ]);

	Route::get('import/society/config',[
        'as'    => 'import.society.config', 
        'uses'  => 'Packages\Admin\Controller\SocietyConfigController@import'
    ]);

    Route::get('chairman/config/',[
        'as' => 'admin.chairmanConfig', 'uses' => 'Packages\Admin\Controller\newDesignController@index'
    ]);
});
