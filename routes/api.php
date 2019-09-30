<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//database : @lC_ef7h[95#

//ftp: @%-GsWfmMEx@

//ssh key name: id_rsa
//ssh password: d*{TqNT+y{-;
//https://p3plcpnl0822.prod.phx3.secureserver.net

Route::group(['prefix' => version_api(), 'namespace' => namespace_api(), 'as' => 'api.'], function () {

    Route::post('login', 'UserController@access_token');
    Route::post('user', 'UserController@postUser');
    Route::post('confirm_activation', 'UserController@confirmActivationCode');
    Route::post('resend-activation-code','UserController@resendActivationCode');
    Route::get('activities', 'ConstantController@getActivities');
    Route::get('organizations', 'ConstantController@getOrganizations');
    Route::get('interests', 'ConstantController@getInterests');
    Route::get('jobs', 'ConstantController@getJobs');
    Route::post('send-activation-code', 'UserController@sendActivationCode');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('login-to-group', 'GroupController@loginToGroup');
        Route::put('user', 'UserController@putUser');
        Route::get('user/{user_id?}', 'UserController@getUser');
        Route::post('contacts','UserController@checkContacts');
        Route::get('/delete-my-account', 'UserController@deleteMyAccount');
        Route::get('confirm-delete-my-account/{activation_code}', 'UserController@confirmDeleteMyAccount');
        Route::post('group/{group_id?}', 'GroupController@postGroup');
        Route::put('group', 'GroupController@putGroup');
        Route::get('groups', 'GroupController@getGroups');
        Route::post('change-group-wallpaper', 'GroupController@changeGroupWallpaper');
        Route::get('group/{group_id}/{password?}', 'GroupController@getGroup');
        Route::get('waiting-group', 'GroupController@getWaitingGroupList');
        Route::post('accept-group', 'GroupController@postAcceptInvitation');
        Route::get('send-virification-code', 'UserController@sendVirificationCode');
        Route::post('reset-mobile-number', 'UserController@resetMobileNumber');

        Route::get('group-types', 'ConstantController@getGroupTypes');
        Route::post('media', 'MessageController@postMedia');
        Route::get('media/{media_id?}', 'MessageController@getMedia');
        Route::post('message/{message_id?}', 'MessageController@postMessage');
        Route::put('message', 'MessageController@putMessage');
        Route::post('survey-result', 'MessageController@postChoiceSurveyResult');
        Route::get('message/{id}', 'MessageController@getMessage');
        Route::get('messages', 'MessageController@getMessages');
        Route::post('reply', 'MessageController@postReply');
        Route::post('search', 'MessageController@postSearch');
        Route::get('archive', 'MessageController@getArchive');
        Route::post('archive', 'MessageController@postArchive');
        Route::get('draft', 'MessageController@getDraft');
        Route::post('draft', 'MessageController@postDraft');
        Route::get('users-message-seen/{message_id}', 'MessageController@getUserMessageSeen');
        Route::post('device', 'NotificationController@postDevice');
        Route::get('status/{token}', 'NotificationController@getStatus');
        Route::get('notification', 'NotificationController@getNotification');
        Route::post('bank-request','PaymentController@postBankRequirement');
        Route::get('requests','PaymentController@getPaymentRequest');
        Route::get('get-user-info', 'UserController@getUserInfo');
        Route::get('search-for-contact', 'UserController@searchForContact');
        Route::post('user-accept-or-reject-group', 'UserController@userAcceptOrRejectGroup');
    });
});
