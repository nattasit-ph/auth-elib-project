<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
	'middleware' => 'api',
	'namespace' => 'App\Http\Controllers\Api',
	'prefix' => 'v1'
], function ($api) {
    // //route for login webview (labor)
    // $api->post('login/labour', [AuthController::class, 'loginLabour'])->name('api.auth.loginLabour');
	//route api for hardware library
	$api->post('hw/login_hardware', [AuthController::class, 'login_hardware'])->name('api.auth.login_hardware');

	$api->post('request/login', [AuthController::class, 'request_login'])->name('api.auth.request_login');
	$api->post('login_ad', [AuthController::class, 'login'])->name('api.auth.login_ad');
	$api->post('login', [AuthController::class, 'login'])->name('api.auth.login');
	$api->post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');
	$api->get('me', [AuthController::class, 'me'])->name('api.auth.me');
	$api->get('request_login_social', [AuthController::class, 'request_login_social'])->name('api.auth.request_login_social');
	$api->get('login_social', [AuthController::class, 'login_social'])->name('api.auth.login_social');


    Route::get('forgot', 'ForgotPasswordController@forgotPwdEmail')->name('forgotPwdEmail');

	Route::post('register/form/submit', 'AuthController@registerSubmit')->name('registerSubmit');

	Route::get('privacy', 'PrivacyController@index')->name('api.auth.privacy');

	Route::prefix('notification')->name('notification.')->group(function () {
		Route::get('list/master', 'NotificationController@listMaster')->name('list.master');
		Route::post('push-notification', 'NotificationController@pushNotification')->name('pushNotification');
		Route::post('push-line-group', 'NotificationController@pushLineGroup')->name('pushLineGroup');
		Route::get('count', 'NotificationController@count')->name('count');
		Route::post('set-is-read', 'NotificationController@setIsRead')->name('setIsRead');
		Route::get('list/pagination', 'NotificationController@listPagination')->name('list.pagination');
		Route::get('get-status', 'NotificationController@getStatus')->name('get.status');
		Route::get('set-status', 'NotificationController@setStatus')->name('set.status');
	});

	Route::prefix('reward')->name('reward.')->group(function () {
		Route::get('category/list', 'RewardCategoryController@index')->name('category.index');
		Route::get('list', 'RewardItemController@index')->name('index');
		Route::get('detail', 'RewardItemController@detail')->name('detail');
		Route::get('redeem', 'RewardRedemptionController@redeem')->name('redeem');
		Route::get('history/redemption', 'RewardRedemptionController@getHistory')->name('history.redemption');
		Route::get('history/earning', 'RewardEarningController@getHistory')->name('history.earning');
	});

	//room
	Route::prefix('room')->name('api.room.')->group(function () {
		Route::post('cancel/reserve', 'RoomController@cancelReserve')->name('cancelReserve');
	});

	Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
		Route::get('form', 'QuestionnaireController@form')->name('form');
		Route::post('form/submit', 'QuestionnaireSubmissionController@store')->name('form.submit');
	});

	Route::prefix('user')->name('user.')->group(function () {
		Route::post('update/avatar', 'UserController@updateAvatar')->name('update.avatar');
		Route::get('my-points', 'UserController@getMyPoints')->name('getMyPoints');
	});

	Route::prefix('my')->name('my.')->group(function () {
		Route::get('profile', 'UserController@getMyProfile')->name('getMyProfile');
		Route::post('profile/update', 'UserController@updateMyProfile')->name('updateMyProfile');
		Route::get('organize', 'UserController@getMyOrganize')->name('getMyOrganize');
	});

	Route::prefix('organize')->name('organize.')->group(function () {
		Route::post('upload/logo', 'UserOrgController@upload_logo')->name('upload.logo');
		Route::post('upload/banner', 'UserOrgController@upload_banner')->name('upload.banner');
		Route::get('list', 'UserOrgController@index')->name('list');
		Route::get('detail', 'UserOrgController@detail')->name('detail');
	});


	//chatbot
	Route::prefix('chatbot')->name('chatbot.')->group(function(){
		Route::post('ajax-register-session', 'ChatbotController@ajax_register_session');

		// Different structure 2 chatType
		// ChatType => chatbot, in future update about session to use log_chat_session
		Route::post('ajax-save-log-chatbot', 'ChatbotController@ajax_save_log_chatbot');
		// ChatType => chatadmin
		Route::post('ajax-save-log-chatadmin', 'ChatbotController@ajax_save_log_chatadmin');
		Route::get('ajax-get-msg-chatadmin', 'ChatbotController@ajax_get_msg_chatadmin');
	});

	//interest
	Route::prefix('interest')->name('interest.')->group(function () {
		Route::get('all_topic', 'InterestTopicController@allTopic')->name('allTopic');
		Route::get('user_interest_topic', 'InterestTopicController@userInserestTopic')->name('userIntesrest');
		Route::post('user_interest_topic/update', 'InterestTopicController@updateUserInserestTopic')->name('updateuserIntesrest');
		Route::get('product', 'InterestTopicController@product')->name('product');
	});

	//device
	Route::prefix('device')->name('device.')->group(function () {
		Route::post('register', 'DeviceController@store')->name('register');
		Route::post('remove', 'DeviceController@destroy')->name('remove');
	});

	Route::prefix('reference-link/category')->name('reference-link.category')->group(function () {
		Route::get('list', 'ReferenceLinkCategoryController@list')->name('list');
	});
});
