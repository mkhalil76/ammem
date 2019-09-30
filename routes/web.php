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
//UJhk9ubLs6m4IsT7
//00970597234815

Route::get('admin', function () {
    return redirect(admin_vw() . '/home');
});
Auth::routes();

Route::get('/logout', 'HomeController@logout');

Route::get('/', function () {
    return view('site.index');
});

Route::get('/home', function () {
    return redirect(admin_vw() . '/home');
});

Route::get(admin_vw() . '/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'admin', 'prefix' => admin_vw()], function () {

    Route::get('users', 'UserController@index');
    Route::get('users-data/{group_id?}', 'UserController@getData');
    Route::get('user-status/{user_id}/{status}', 'UserController@userStatus');
    Route::get('user-groups/{user_id}', 'UserController@userGroups');
    Route::get('user-edit/{user_id}', 'UserController@edit');
    Route::put('user-edit/{user_id}', 'UserController@putUser');

    Route::get('profile/{user_id?}', 'UserController@edit');
    Route::put('profile/{user_id?}', 'UserController@putUser');

    Route::get('activities', 'ConstantController@getActivities');
    Route::get('organizations', 'ConstantController@getOrganizations');
    Route::get('group-types', 'ConstantController@getGroupTypes');
    Route::get('interests', 'ConstantController@getInterests');
    Route::get('jobs', 'ConstantController@getJobs');
    Route::get('constant-edit/{constant_id}/{type}', 'ConstantController@edit');
    Route::put('constant-edit/{constant_id}/{type}', 'ConstantController@update');
    Route::get('constant/{type}', 'ConstantController@create');
    Route::post('constant/{type}', 'ConstantController@store');
    Route::get('constant-data/{type}', 'ConstantController@getConstantData');
    Route::delete('constant/{constant_id}', 'ConstantController@destroy');

    Route::get('groups', 'GroupController@index');
    Route::get('group/{group_id}', 'GroupController@edit');
    Route::put('group/{group_id}', 'GroupController@update');
    Route::get('group-create', 'GroupController@create');
    Route::post('group', 'GroupController@store');
    Route::delete('group/{group_id}', 'GroupController@destroy');
    Route::get('groups-data', 'GroupController@getData');
    Route::get('delete-member/{group_id}/{member_id}', 'GroupController@deleteMemberGroup');
    Route::get('group-members/{group_id}', 'GroupController@userGroups');
    Route::get('add-group-member/{group_id}', 'GroupController@addMember');
    Route::post('add-group-member/{group_id}/{user_id}', 'GroupController@postAddMember');
    Route::post('change-group-type/{group_id}', 'GroupController@changeGroupType');
    Route::post('change-group-privilege/{group_id}', 'GroupController@changeGroupPrivilege');

    Route::get('messages', 'MessageController@index');
    Route::get('messages-data', 'MessageController@getData');
    Route::get('delete-message/{message_id}', 'MessageController@destroy');

    Route::get('payments', 'PaymentController@index');
    Route::get('payment-data', 'PaymentController@getData');

    Route::get('send-public-notification', 'NotificationController@sendPublicNotification');
    Route::post('send-public-notification', 'NotificationController@postSendPubNotification');
    Route::get('send-pub-notification', 'NotificationController@getSendPubNotification');
    Route::get('public-notification-data', 'NotificationController@publicNotificationData');

});

Route::get(user_vw().'/login', 'HomeController@UserLogin');
Route::post(user_vw().'/sign-up', 'UserController@postUser');
Route::post(user_vw().'/login', 'HomeController@postUserLogin');
Route::post(user_vw().'/send-activation', 'HomeController@sendActivationCode');
Route::group(['middleware' => 'user', 'prefix' => user_vw()], function () {
    
    Route::get('profile/{user_id?}', 'UserController@edit');
    Route::put('profile/{user_id?}', 'UserController@putUser');

    Route::get('contacts', 'MessageController@getContacts');
    Route::post('contacts', 'MessageController@postContacts');

    Route::get('messages', 'MessageController@getMessages');
    Route::get('conversation/{group_id}', 'MessageController@getMessageConversation');
    Route::get('my-groups', 'MessageController@getGroupConversation');
    Route::post('send-reply/{message_id}', 'MessageController@sendReplyMessage');
    Route::get('send-message/{group_id}', 'MessageController@getSendMessage');
    Route::post('send-message/{group_id}', 'MessageController@sendMessage');
    Route::get('user-group-create', 'GroupController@user_group_create');
    Route::post('user-group-create', 'GroupController@user_store');

});