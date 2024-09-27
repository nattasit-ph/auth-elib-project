<?php

// Route::get('dashboard', ['as' => 'report.dashboard', 'uses' => 'Dashboard\DashboardController@index']);
	/*
	|--------------------------------------------------------------------------
	| Admin - News (system = center)
	|
	|--------------------------------------------------------------------------
	*/
	Route::prefix('news')->name('news.')->group(function () {

		Route::get('index', ['as' => 'index', 'uses' => 'Article\ArticleController@index']);
		Route::get('create', ['as' => 'create', 'uses' => 'Article\ArticleController@create']);
		Route::post('store', ['as' => 'store', 'uses' => 'Article\ArticleController@store']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleController@delete']);
		Route::post('{article}/update', ['as' => 'update', 'uses' => 'Article\ArticleController@update']);
		Route::get('{article}/edit', ['as' => 'edit', 'uses' => 'Article\ArticleController@edit']);
		//preview page
		Route::post('preview/{slug}', ['as' => 'previewPage', 'uses' => 'Article\ArticleController@previewPage']);
		//export article button
		Route::get('export-excel', ['as' => 'exportToExcel', 'uses' => 'Article\ArticleController@exportToExcel']);

			//manage comment
		Route::prefix('comment/{article_id}')->name('comment.')->group(function () {
			Route::get('all', ['as' => 'index', 'uses' => 'Article\ArticleCommentController@index']);
			Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleCommentController@delete']);
			Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Article\ArticleCommentController@ajaxGetData']);
			Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Article\ArticleCommentController@setStatus']);
		});

		Route::get('all', ['as' => 'index', 'uses' => 'Article\ArticleController@index']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleController@delete']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Article\ArticleController@ajaxGetData']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Article\ArticleController@setStatus']);

		Route::resource('category', 'Article\ArticleCategoryController')->except(['show', 'destroy']);
		Route::prefix('category')->name('category.')->group(function () {
			Route::get('all', ['as' => 'index', 'uses' => 'Article\ArticleCategoryController@index']);
			Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleCategoryController@delete']);
			Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Article\ArticleCategoryController@ajaxGetData']);
			Route::post('ajax-quick-save', ['as' => 'quickSave', 'uses' => 'Article\ArticleCategoryController@ajaxQuickSave']);
			Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Article\ArticleCategoryController@setStatus']);
		});
	});

	/*
	|--------------------------------------------------------------------------
	| Admin - Pages (single page)
	|
	|--------------------------------------------------------------------------
	*/
	Route::prefix('pages')->name('pages.')->group(function () {

		Route::get('index', ['as' => 'index', 'uses' => 'Page\PageController@index']);
		Route::get('create', ['as' => 'create', 'uses' => 'Page\PageController@create']);
		Route::post('store', ['as' => 'store', 'uses' => 'Page\PageController@store']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'Page\PageController@delete']);
		Route::post('{article}/update', ['as' => 'update', 'uses' => 'Page\PageController@update']);
		Route::get('{article}/edit', ['as' => 'edit', 'uses' => 'Page\PageController@edit']);
		//preview page
		Route::post('preview/{slug}', ['as' => 'previewPage', 'uses' => 'Page\PageController@previewPage']);
		//export article button
		Route::get('export-excel', ['as' => 'exportToExcel', 'uses' => 'Page\PageController@exportToExcel']);
        //delete file
		Route::post('file/delete', ['as' => 'file.delete', 'uses' => 'Page\PageController@fileDelete']);

		Route::get('all', ['as' => 'index', 'uses' => 'Page\PageController@index']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'Page\PageController@delete']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Page\PageController@ajaxGetData']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Page\PageController@setStatus']);
	});

/*
|--------------------------------------------------------------------------
| Admin - Reward
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => 'Reward'], function () {
	/*
	|--------------------------------------------------------------------------
	| Admin - Reward item
	|--------------------------------------------------------------------------
	*/
	Route::prefix('reward')->name('reward.')->group(function () {
		Route::get('all', ['as' => 'index', 'uses' => 'RewardItemController@index']);
		Route::post('update/{id}', ['as' => 'update', 'uses' => 'RewardItemController@update']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'RewardItemController@destroy']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'RewardItemController@ajaxGetData']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'RewardItemController@setStatus']);
		Route::post('ajax-upload-image', ['as' => 'ajaxUploadImage', 'uses' => 'RewardItemGalleryController@ajaxUploadImage']);
		Route::post('ajax-load-gallery', ['as' => 'ajaxGetGalleryData', 'uses' => 'RewardItemGalleryController@ajaxGetData']);
		Route::post('ajax-set-cover', ['as' => 'ajaxSetCover', 'uses' => 'RewardItemGalleryController@ajaxSetCover']);
		Route::post('ajax-delete-image', ['as' => 'ajaxDeleteImage', 'uses' => 'RewardItemGalleryController@ajaxDeleteImage']);
	});
	Route::resource('reward', 'RewardItemController')->except(['index', 'update']);

	/*
	|--------------------------------------------------------------------------
	| Admin - Reward Category
	|--------------------------------------------------------------------------
	*/
	Route::prefix('reward-category')->name('reward-category.')->group(function () {
		Route::get('all', ['as' => 'index', 'uses' => 'RewardCategoryController@index']);
		Route::post('update/{id}', ['as' => 'update', 'uses' => 'RewardCategoryController@update']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'RewardCategoryController@destroy']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'RewardCategoryController@ajaxGetData']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'RewardCategoryController@setStatus']);
	});
	Route::resource('reward-category', 'RewardCategoryController')->except(['index', 'update']);

	/*
	|--------------------------------------------------------------------------
	| Admin - Reward - Redemption
	|--------------------------------------------------------------------------
	*/
	Route::prefix('redemption')->name('redemption.')->group(function () {
		Route::get('all', ['as' => 'index', 'uses' => 'RewardRedemptionHistoryController@index']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'RewardRedemptionHistoryController@ajaxGetData']);
		Route::post('set-delivered-status', ['as' => 'ajaxSetDeliveryStatus', 'uses' => 'RewardRedemptionHistoryController@ajaxSetDeliveryStatus']);
		Route::post('refund', ['as' => 'ajaxRefund', 'uses' => 'RewardRedemptionHistoryController@ajaxRefund']);
		Route::get('export-excel', ['as' => 'exportToExcel', 'uses' => 'RewardRedemptionHistoryController@exportToExcel']);
	});

	/*
	|--------------------------------------------------------------------------
	| Admin - Reward Earning
	|--------------------------------------------------------------------------
	*/

	Route::prefix('reward-earn')->name('rewardEarn.')->group(function () {
		Route::get('all/{step}', ['as' => 'index', 'uses' => 'RewardEarningHistoryController@index']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'RewardEarningHistoryController@ajaxGetData']);
		Route::get('export-excel', ['as' => 'exportToExcel', 'uses' => 'RewardEarningHistoryController@exportToExcel']);
	});

	/*
	|--------------------------------------------------------------------------
	| Admin - Reward Report
	|--------------------------------------------------------------------------
	*/

	Route::prefix('reward/report')->name('reward.report.')->group(function () {
		//report reward popular
		Route::get('reward-popular', ['as' => 'rewardPopular', 'uses' => 'RewardPopularController@index']);
		Route::post('reward-popular/ajax-get-data', ['as' => 'rewardPopular.datatable', 'uses' => 'RewardPopularController@ajaxGetData']);
		Route::get('reward-popular/export-excel', ['as' => 'rewardPopular.exportToExcel', 'uses' => 'RewardPopularController@exportToExcel']);
	});

	/*
	|--------------------------------------------------------------------------
	| Admin - Coin Activity
	|--------------------------------------------------------------------------
	*/
	Route::prefix('coin-activity')->name('coin-activity.')->group(function () {
		Route::get('edit', ['as' => 'edit', 'uses' => 'RewardActivityController@edit']);
		Route::post('update', ['as' => 'update', 'uses' => 'RewardActivityController@update']);
	});
});




/*
|--------------------------------------------------------------------------
| Admin - Questionnaire
|--------------------------------------------------------------------------
*/
Route::resource('questionnaire', 'Article\ArticleController')->except(['show', 'destroy']);
Route::prefix('questionnaire')->name('questionnaire.')->group(function () {
	Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleController@delete']);
	Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Article\ArticleController@ajaxGetData']);
	Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Article\ArticleController@setStatus']);

	Route::resource('category', 'Article\ArticleCategoryController')->except(['show', 'destroy']);
	Route::prefix('category')->name('category.')->group(function () {
		Route::post('delete', ['as' => 'delete', 'uses' => 'Article\ArticleCategoryController@delete']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Article\ArticleCategoryController@ajaxGetData']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Article\ArticleCategoryController@setStatus']);
	});
});

/*
|--------------------------------------------------------------------------
| Admin - Room Reservation
|--------------------------------------------------------------------------
*/

//room list
Route::resource('room', 'Room\RoomController')->except(['show', 'destroy']);
Route::prefix('room')->name('room.')->group(function () {
	Route::get('all', ['as' => 'all', 'uses' => 'Room\RoomController@index']);
	Route::post('delete', ['as' => 'delete', 'uses' => 'Room\RoomController@destroy']);
	Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Room\RoomController@ajaxGetData']);
	Route::post('set-status', ['as' => 'setStatus', 'uses' => 'Room\RoomController@setStatus']);
	Route::get('ajax-room-name', ['as' => 'ajaxRoomName', 'uses' => 'Room\RoomController@ajaxRoomName']);
	Route::get('export-excel', ['as' => 'exportToExcel', 'uses' => 'Room\RoomController@exportToExcel']);
});

//room type
Route::resource('room-type', 'Room\RoomTypeController', [
	'names' => [
	'create' => 'roomType.create',
	'store' => 'roomType.store',
	'edit' => 'roomType.edit',
	'update' => 'roomType.update',
]
])->except(['show', 'destroy']);
Route::prefix('room-type')->name('roomType.')->group(function () {
   Route::get('all', ['as' => 'index', 'uses' => 'Room\RoomTypeController@index']);
   Route::post('update', ['as' => 'setStatus', 'uses' => 'Room\RoomTypeController@setStatus']);
   Route::post('delete', ['as' => 'delete', 'uses' => 'Room\RoomTypeController@delete']);
   Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'Room\RoomTypeController@ajaxGetData']);
});

//room booking
Route::prefix('room/booking')->name('room.booking.')->middleware(['auth'])->group(function () {
	Route::get('all', 'Room\RoomBookingController@index')->name('all');
	Route::get('create', 'Room\RoomBookingController@create')->name('create');
	Route::post('store', 'Room\RoomBookingController@store')->name('store');
	Route::get('edit/{booking_id}', 'Room\RoomBookingController@edit')->name('edit');
	Route::post('update', 'Room\RoomBookingController@update')->name('update');
	Route::post('set-status', 'Room\RoomBookingController@setStatus')->name('setStatus');
	Route::post('delete', 'Room\RoomBookingController@destroy')->name('delete');
	Route::get('export-excel', 'Room\RoomBookingController@exportToExcel')->name('exportToExcel');
	Route::post('ajax-get-data', 'Room\RoomBookingController@ajaxGetData')->name('datatable');
	Route::get('ajax-get-user', 'Room\RoomBookingController@ajaxGetUserOrg')->name('getuser');
});
// Route::post('room/booking/ajax-get-data', ['as' => 'room.ajaxDeleteImage', 'uses' => 'Room\RoomGalleryController@ajaxDeleteImage']);

//room gallery
Route::post('room/ajax-upload-image', ['as' => 'room.ajaxUploadImage', 'uses' => 'Room\RoomGalleryController@ajaxUploadImage']);
Route::post('room/ajax-load-gallery', ['as' => 'room.ajaxGetGalleryData', 'uses' => 'Room\RoomGalleryController@ajaxGetData']);
Route::post('room/ajax-set-cover', ['as' => 'room.ajaxSetCover', 'uses' => 'Room\RoomGalleryController@ajaxSetCover']);
Route::post('room/ajax-delete-image', ['as' => 'room.ajaxDeleteImage', 'uses' => 'Room\RoomGalleryController@ajaxDeleteImage']);

//room setting
Route::get('room/setting', ['as' => 'room.setting.all', 'uses' => 'Room\RoomSettingController@index']);
Route::post('room/setting/update', ['as' => 'room.setting.update', 'uses' => 'Room\RoomSettingController@update']);


/*
|--------------------------------------------------------------------------
| Admin - Event
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => 'Event'], function () {
	Route::resource('event', 'EventController')->except(['index', 'edit', 'update', 'show']);
	Route::group(['prefix' => 'event',  'middleware' => 'auth'], function () {
		Route::get('/', ['as' => 'event.index', 'uses' => 'EventController@index']);
		Route::post('ajax-get-data', ['as' => 'event.datatable', 'uses' => 'EventController@ajaxGetData']);
		Route::post('invitation/ajax-get-data', ['as' => 'event.invitation.datatable', 'uses' => 'EventController@ajaxGetInvitationData']);
		Route::post('invitation/ajax-send-invitation', ['as' => 'event.invitation.ajaxSendInvitation', 'uses' => 'EventController@ajaxSendInvitation']);
		Route::post('invitation/ajax-delete-invitation', ['as' => 'event.invitation.ajaxDeleteInvitation', 'uses' => 'EventController@ajaxDeleteInvitation']);
		Route::get('edit/{id}/general', ['as' => 'event.edit', 'uses' => 'EventController@edit']);
		Route::get('edit/{id}/{step_name}', ['as' => 'event.form.step', 'uses' => 'EventController@edit']);
		Route::post('update', ['as' => 'event.update', 'uses' => 'EventController@update']);
		Route::post('set-status', ['as' => 'event.setStatus', 'uses' => 'EventController@setStatus']);
		Route::post('delete', ['as' => 'event.delete', 'uses' => 'EventController@destroy']);
		Route::get('join/export-excel', ['as' => 'event.join.exportToExcel', 'uses' => 'EventJoinController@exportToExcel']);
		Route::get('export-excel', ['as' => 'event.exportToExcel', 'uses' => 'EventController@exportToExcel']);
	});
});

/*
|--------------------------------------------------------------------------
| Admin - Poll
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'poll',  'namespace' => 'Poll'], function () {
	Route::get('/', ['as' => 'poll.index', 'uses' => 'PollController@index']);
	Route::post('ajax-get-data', ['as' => 'poll.datatable', 'uses' => 'PollController@ajaxGetData']);
	Route::get('create', ['as' => 'poll.create', 'uses' => 'PollController@create']);
	Route::post('store', ['as' => 'poll.store', 'uses' => 'PollController@store']);
	Route::get('edit/{id}', ['as' => 'poll.edit', 'uses' => 'PollController@edit']);
	Route::post('update', ['as' => 'poll.update', 'uses' => 'PollController@update']);
	Route::post('set-status', ['as' => 'poll.setStatus', 'uses' => 'PollController@setStatus']);
	Route::post('delete', ['as' => 'poll.delete', 'uses' => 'PollController@destroy']);
	Route::get('export-excel', ['as' => 'poll.exportToExcel', 'uses' => 'PollController@exportToExcel']);

	/*
	|--------------------------------------------------------------------------
	| Admin - Poll - Category
	|--------------------------------------------------------------------------
	*/
	// Route::prefix('category')->name('category.')->group(function () {
	// 	Route::get('/', ['as' => 'index', 'uses' => 'ReferenceLinkCategoryController@index']);
	// 	Route::get('ajax-get-data', ['as' => 'datatable', 'uses' => 'ReferenceLinkCategoryController@ajaxGetData']);
	// 	Route::get('create', ['as' => 'create', 'uses' => 'ReferenceLinkCategoryController@create']);
	// 	Route::post('store', ['as' => 'store', 'uses' => 'ReferenceLinkCategoryController@store']);
	// 	Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ReferenceLinkCategoryController@edit']);
	// 	Route::post('update/{id}', ['as' => 'update', 'uses' => 'ReferenceLinkCategoryController@update']);
	// 	Route::post('set-status', ['as' => 'setStatus', 'uses' => 'ReferenceLinkCategoryController@setStatus']);
	// 	Route::post('delete', ['as' => 'delete', 'uses' => 'ReferenceLinkCategoryController@destroy']);
	// });

	Route::resource('category', 'PollCategoryController')->except(['show', 'destroy']);
	Route::prefix('category')->name('poll.category.')->group(function () {
		Route::get('/', ['as' => 'index', 'uses' => 'PollCategoryController@index']);
		Route::post('ajax-get-data', ['as' => 'datatable', 'uses' => 'PollCategoryController@ajaxGetData']);
		Route::post('ajax-quick-save', ['as' => 'quickSave', 'uses' => 'PollCategoryController@ajaxQuickSave']);
		Route::get('create', ['as' => 'create', 'uses' => 'PollCategoryController@create']);
		Route::post('store', ['as' => 'store', 'uses' => 'PollCategoryController@store']);
		Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'PollCategoryController@edit']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'PollCategoryController@delete']);
		Route::put('update/{id}', ['as' => 'update', 'uses' => 'PollCategoryController@update']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'PollCategoryController@setStatus']);
	});


	Route::get('report', ['as' => 'admin.poll.report', 'uses' => 'PollController@report']);
	Route::post('report/ajax-get-data', ['as' => 'admin.poll.report.datatable', 'uses' => 'PollController@ajaxGetReportData']);
});

/*
|--------------------------------------------------------------------------
| Admin - Site - Info
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'site',  'namespace' => 'Site'], function () {
	Route::get('info', ['as' => 'site.editOrgInfo', 'uses' => 'SiteInfoController@editOrgInfo']);
	Route::put('info/update', ['as' => 'site.updateOrgInfo', 'uses' => 'SiteInfoController@updateOrgInfo']);
	Route::get('privacy-policy', ['as' => 'site.editPrivacyPolicy', 'uses' => 'SiteInfoController@editPrivacyPolicy']);
	Route::put('privacy-policy/update', ['as' => 'site.updatePrivacyPolicy', 'uses' => 'SiteInfoController@updatePrivacyPolicy']);
	Route::get('delete-user-policy', ['as' => 'site.editDeleteUserPolicy', 'uses' => 'SiteInfoController@editDeleteUserPolicy']);
	Route::put('delete-user-policy/update', ['as' => 'site.updateDeleteUserPolicy', 'uses' => 'SiteInfoController@updateDeleteUserPolicy']);
	Route::get('google-analytics', ['as' => 'site.GoogleAnalytics', 'uses' => 'SiteInfoController@GoogleAnalytics']);
	Route::put('google-analytics/update', ['as' => 'site.updateGoogleAnalytics', 'uses' => 'SiteInfoController@updateGoogleAnalytics']);


	Route::get('addCookie', ['as' => 'site.addCookie', 'uses' => 'SiteInfoController@addCookie']);
	Route::get('addPolicy', ['as' => 'site.addPolicy', 'uses' => 'SiteInfoController@addPolicy']);
	Route::get('addTerms', ['as' => 'site.addTerms', 'uses' => 'SiteInfoController@addTerms']);
	Route::post('savePolicyAndTerms', ['as' => 'site.savePolicyAndTerms', 'uses' => 'SiteInfoController@savePolicyAndTerms']);

	// Consent Management
	Route::get('consent', ['as' => 'site.consent', 'uses' => 'SiteInfoController@consent']);
	Route::get('consentLog', ['as' => 'site.consent.log', 'uses' => 'SiteInfoController@consentLog']);

	Route::get('consentAdd', ['as' => 'site.consent.add', 'uses' => 'SiteInfoController@consentAdd']);
	Route::get('consentEdit/{id}', ['as' => 'site.consent.edit', 'uses' => 'SiteInfoController@consentEdit']);

	Route::post('consentSave', ['as' => 'site.consent.save', 'uses' => 'SiteInfoController@consentSave']);
	Route::post('consentUpdate', ['as' => 'site.consent.update', 'uses' => 'SiteInfoController@consentUpdate']);
	Route::post('updateStatusConsentUser', ['as' => 'site.consent.updateStatusConsentUser', 'uses' => 'SiteInfoController@updateStatusConsentUser']);

	Route::get('getConsentControl', ['as' => 'site.consent.getConsentControl', 'uses' => 'SiteInfoController@getConsentControl']);
	Route::post('getConsentUser', ['as' => 'site.consent.getConsentUser', 'uses' => 'SiteInfoController@getConsentUser']);








});



/*
|--------------------------------------------------------------------------
| Admin - Questionnaire
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'questionnaire',  'namespace' => 'Questionnaire'], function () {
	Route::get('/', ['as' => 'questionnaire.index', 'uses' => 'QuestionnaireController@index']);
	Route::post('ajax-get-data', ['as' => 'questionnaire.datatable', 'uses' => 'QuestionnaireController@ajaxGetData']);
	Route::get('create', ['as' => 'questionnaire.create', 'uses' => 'QuestionnaireController@create']);
	Route::post('store', ['as' => 'questionnaire.store', 'uses' => 'QuestionnaireController@store']);
	Route::get('edit/{id}', ['as' => 'questionnaire.edit', 'uses' => 'QuestionnaireController@edit']);
	Route::post('update', ['as' => 'questionnaire.update', 'uses' => 'QuestionnaireController@update']);
	Route::post('set-status', ['as' => 'questionnaire.setStatus', 'uses' => 'QuestionnaireController@setStatus']);
    Route::post('set-system', ['as' => 'questionnaire.setSystem', 'uses' => 'QuestionnaireController@setSystem']);

	Route::post('delete', ['as' => 'questionnaire.delete', 'uses' => 'QuestionnaireController@destroy']);
	Route::post('replicate', ['as' => 'questionnaire.replicate', 'uses' => 'QuestionnaireController@replicate']);
	Route::post('preview', ['as' => 'questionnaire.preview', 'uses' => 'QuestionnaireController@preview']);
	Route::get('export-excel', ['as' => 'questionnaire.exportToExcel', 'uses' => 'QuestionnaireController@exportToExcel']);

	Route::get('submission', ['as' => 'questionnaire.submission.list', 'uses' => 'QuestionnaireSubmissionController@index']);
	Route::post('submission/ajax-get-data', ['as' => 'questionnaire.submission.datatable', 'uses' => 'QuestionnaireSubmissionController@ajaxGetData']);
	Route::get('submission/edit/{id}', ['as' => 'questionnaire.submission.edit', 'uses' => 'QuestionnaireSubmissionController@edit']);
	Route::post('submission/set-status', ['as' => 'questionnaire.submission.setStatus', 'uses' => 'QuestionnaireSubmissionController@setStatus']);
	Route::post('submission/delete', ['as' => 'questionnaire.submission.delete', 'uses' => 'QuestionnaireSubmissionController@destroy']);
	Route::get('submission/export-excel', ['as' => 'questionnaire.submission.exportToExcel', 'uses' => 'QuestionnaireSubmissionController@exportToExcel']);
});


/*
|--------------------------------------------------------------------------
| Admin - Reference - Link
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'reference-link', 'as' => 'reference-link.','namespace' => 'Referencelink'], function () {
	Route::get('/', ['as' => 'index', 'uses' => 'ReferenceLinkController@index']);
	Route::get('ajax-get-data', ['as' => 'datatable', 'uses' => 'ReferenceLinkController@ajaxGetData']);
	Route::get('create', ['as' => 'create', 'uses' => 'ReferenceLinkController@create']);
	Route::post('store', ['as' => 'store', 'uses' => 'ReferenceLinkController@store']);
	Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ReferenceLinkController@edit']);
	Route::post('update/{id}', ['as' => 'update', 'uses' => 'ReferenceLinkController@update']);
	Route::post('set-status', ['as' => 'setStatus', 'uses' => 'ReferenceLinkController@setStatus']);
	Route::post('delete', ['as' => 'delete', 'uses' => 'ReferenceLinkController@destroy']);

	/*
	|--------------------------------------------------------------------------
	| Admin - Reference - Link - Category
	|--------------------------------------------------------------------------
	*/
	Route::prefix('category')->name('category.')->group(function () {
		Route::get('/', ['as' => 'index', 'uses' => 'ReferenceLinkCategoryController@index']);
		Route::get('ajax-get-data', ['as' => 'datatable', 'uses' => 'ReferenceLinkCategoryController@ajaxGetData']);
		Route::get('create', ['as' => 'create', 'uses' => 'ReferenceLinkCategoryController@create']);
		Route::post('store', ['as' => 'store', 'uses' => 'ReferenceLinkCategoryController@store']);
		Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ReferenceLinkCategoryController@edit']);
		Route::post('update/{id}', ['as' => 'update', 'uses' => 'ReferenceLinkCategoryController@update']);
		Route::post('set-status', ['as' => 'setStatus', 'uses' => 'ReferenceLinkCategoryController@setStatus']);
		Route::post('delete', ['as' => 'delete', 'uses' => 'ReferenceLinkCategoryController@destroy']);
	});
});

if(config('bookdose.theme_front') == 'theme_okmd')
{
	/*
	|--------------------------------------------------------------------------
	| Admin - Visitor Log
	|--------------------------------------------------------------------------
	*/
		Route::group(['prefix' => 'visitor-log',  'namespace' => 'Visitorlog'], function () {
			Route::get('/', ['as' => 'visitor-log.index', 'uses' => 'VisitorlogController@index']);
			Route::post('ajax-get-data', ['as' => 'visitor-log.datatable', 'uses' => 'VisitorlogController@ajaxGetData']);
			Route::get('export-excel', ['as' => 'visitor-log.exportToExcel', 'uses' => 'VisitorlogController@exportToExcel']);
		});
}

/*
|--------------------------------------------------------------------------
| Admin - interest
|--------------------------------------------------------------------------
*/
Route::get('interest/all', ['as' => 'interest.index', 'uses' => 'Interested\InterestController@index']);
Route::resource('interest', 'Interested\InterestController')->except(['index', 'destroy']);
Route::post('interest/delete', ['as' => 'interest.delete', 'uses' => 'Interested\InterestController@delete']);
Route::post('interest/ajax-get-data', ['as' => 'interest.datatable', 'uses' => 'Interested\InterestController@ajaxGetData']);
Route::post('interest/set-status', ['as' => 'interest.setStatus', 'uses' => 'Interested\InterestController@setStatus']);

/*
|--------------------------------------------------------------------------
| Admin - User Report
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'report'], function () {
	Route::get('user/overall', ['as' => 'report.user.overall', 'uses' => 'Report\UserReportController@overallForm']);
	Route::post('user/login_device', ['as' => 'report.user.loginDevice', 'uses' => 'Report\UserReportController@ajaxGetLoginDevice']);
	Route::post('user/usually_browser', ['as' => 'report.user.UsuallyBrowser', 'uses' => 'Report\UserReportController@ajaxGetUsuallyBrowser']);
	Route::post('user/gender', ['as' => 'report.user.gender', 'uses' => 'Report\UserReportController@ajaxGetGender']);
	Route::post('user/rangeAge', ['as' => 'report.user.rangeAge', 'uses' => 'Report\UserReportController@ajaxGetRangeAge']);
	Route::post('user/interest_topic', ['as' => 'report.user.interestTopic', 'uses' => 'Report\UserReportController@ajaxGetUserInterestTopic']);
	Route::get('user/export-excel', ['as' => 'report.user.exportToExcel', 'uses' => 'Report\UserReportController@exportToExcel']);
});

/*
|--------------------------------------------------------------------------
| Admin - Uplaod image (Summernote)
|--------------------------------------------------------------------------
*/
Route::post('upload/summernote-image-upload', ['as' => 'upload.summernoteUploadImage', 'uses' => 'SummernoteEditor\SummernoteController@summernoteUploadImage']);
Route::post('upload/summernote-image-remove', ['as' => 'upload.summernoteRemoveImage', 'uses' => 'SummernoteEditor\SummernoteController@summernoteRemoveImage']);


/*
|--------------------------------------------------------------------------
| Admin - Migration
|--------------------------------------------------------------------------
*/
Route::prefix('migration')->name('migration.')->group(function () {
	// if(strtoupper(config('bookdose.app.name')) == "CRA"){
		Route::get('cra/user-img', ['as' => 'cra.userImage', 'uses' => 'Migration\MigrationController@craUserImg']);
	//}

});
