<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reward Store
|--------------------------------------------------------------------------
*/
Route::namespace('Reward')->group(function () {
	// Redemption History
	Route::get('reward/redemption', 'RewardRedemptionHistoryController@index')->name('reward.redemption.index');

	// Main reward
	Route::get('reward', 'RewardItemController@index')->name('reward.index');
	Route::get('reward/detail/{id}', 'RewardItemController@show')->name('reward.detail');
	Route::get('reward/ajax-redeem', 'RewardRedemptionHistoryController@ajaxRedeem')->name('reward.ajaxRedeem');

	//my point
	Route::get('my/point', function () {
		return redirect(config('bookdose.app.belib_url') . '/my/points');
	})->name('my.point');
});

Route::group(['namespace' => 'Event'], function () {
	Route::get('event/accept-invitation/{code}', 'EventController@acceptInvitation')->name('event.invitation.accept');
	// Route::get('event/invitation/{code}/accepted', 'EventController@invitationAccepted')->name('event.invitation.accepted');
});

Route::group(['namespace' => 'Questionnaire'], function () {
	Route::prefix('questionnaire')->name('questionnaire.')->middleware(['auth'])->group(function () {
		Route::get('{slug}', 'QuestionnaireSubmissionController@form')->name('form');
		Route::post('{slug}/submit', 'QuestionnaireSubmissionController@store')->name('submit');
		Route::get('submit/{status}', 'QuestionnaireSubmissionController@showResult')->name('submit.complete');
		Route::get('view/{id}', 'QuestionnaireSubmissionController@view')->name('view');
		// Route::get('/', 'QuestionnaireSubmissionController@detail')->name('detail');
		// Route::post('submit', 'QuestionnaireSubmissionController@store')->name('store');
	});
});

/*
|--------------------------------------------------------------------------
| Interested Topic
|--------------------------------------------------------------------------
*/
Route::namespace('Interested')->group(function () {
	// home interested
	Route::get('interest', 'InterestController@index')->middleware(['auth'])->name('interest.index');
	Route::post('interest/update', 'InterestController@updateInterested')->middleware(['auth'])->name('interest.update');
	Route::get('interest/product', 'InterestController@getMyInterested')->middleware(['auth'])->name('interest.product');

	Route::get('saveCookie', 'InterestController@saveCookie')->middleware(['auth'])->name('saveCookie');
	Route::get('privacy-and-policy', 'InterestController@privacy_and_policy')->middleware(['auth'])->name('privacy-and-policy');
	Route::get('terms-and-conditions', 'InterestController@terms_and_conditions')->middleware(['auth'])->name('terms-and-conditions');
	Route::get('policy-download', 'InterestController@download_policy')->middleware(['auth'])->name('policy-download');
});

/*
|--------------------------------------------------------------------------
| Room Reservation
|--------------------------------------------------------------------------
*/
Route::namespace('Room')->group(function () {

	### start room index ###

	//mainpage
	Route::get('room', 'RoomController@index')->name('room.index');
	//fetch room list
	Route::get('room/ajax-room-list', 'RoomController@fetchList')->name('room.fetchList');
	//get room name option
	Route::get('room/get-room-name', 'RoomController@ajaxRoomName')->name('room.ajaxRoomName');
	//search room + fetch list
	Route::post('room/search', 'RoomController@searchRoom')->name('room.searchRoom');

	### end room index ###

	### start room detail ###
	Route::get('room/detail/{slug}', 'RoomController@detail')->name('room.detail');
	//check
	Route::post('room/check/{room_slug}', 'RoomController@checkRoom')->name('room.checkRoom');
	//reserve
	Route::post('room/reserve/{room_slug}', 'RoomController@reserveRoom')->name('room.reserveRoom');
	//cancel reserve
	Route::post('room/reserve/cancel', 'RoomController@cancelReservation')->name('room.cancel.reserveRoom');
	### end room detail ###


	//my room
	Route::get('my/room', function () {
		return redirect(config('bookdose.app.belib_url') . '/my/room');
	})->name('my.room');
});
/*
|--------------------------------------------------------------------------
| redirect to BELIB
|--------------------------------------------------------------------------
*/

Route::prefix('{org_slug?}')->group(function () {
    Route::group(['namespace' => 'belib'], function () {
        Route::get('belib/index', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/');
        })->name('belib.index');
        Route::get('belib/home', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/home');
        })->name('belib.home');
        Route::get('belib/search', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/advanced-search');
        })->name('belib.search');

        //pages
        Route::get('belib/pages/{slug}', function ($org_slug, $slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/pages/'.$slug);
        })->name('belib.pages.show');
        //poll
        Route::get('belib/poll', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/poll');
        })->name('belib.poll.index');
        //event
        Route::get('belib/event', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/event');
        })->name('belib.event.index');
        //article
        Route::get('belib/article', function ($org_slug) {
            dd('belib/article', $org_slug);
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/article/all');
        })->name('belib.article.index');
        Route::get('belib/article/{slug}', function ($org_slug, $slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/article/'.$slug);
        })->name('belib.article.show');

        //knowledge
        Route::get('belib/knowledge', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/knowledge/all');
        })->name('belib.knowledge.index');

        //my
        Route::get('belib/my/profile', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/profile');
        })->name('belib.my.profile');
        Route::get('belib/my/profile/interest', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/profile?section=interest');
        })->name('belib.my.profile.interest');
        Route::get('belib/myShelf/{product_main_slug}', function ($org_slug, $product_main_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/shelf/' . $product_main_slug);
        })->name('belib.my.shelf');
        Route::get('belib/myStation/', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/station');
        })->name('belib.my.station');
        Route::get('belib/my/point', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/points');
        })->name('belib.my.point');
        Route::get('belib/my/wishlist', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/my/wishlist');
        })->name('belib.my.wishlist');

        //product
        Route::get('belib/{product_main_slug}/{slug}', function ($org_slug, $product_main_slug, $slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/'.$product_main_slug.'/'.$slug);
        })->name('belib.product.show');

        //podcast
        Route::get('belib/podcast/{slug}', function ($org_slug, $slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/podcast/'.$slug);
        })->name('belib.podcast.show');

        //knowledge
        Route::get('belib/knowledge/{slug}', function ($org_slug, $slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/knowledge/'.$slug);
        })->name('belib.knowledge.show');

        //privacy-and-policy
        Route::get('belib/cookie-and-policy', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/cookie-and-policy');
        })->name('belib.cookie-and-policy.show');

        //privacy-and-policy
        Route::get('belib/privacy-and-policy', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/privacy-and-policy');
        })->name('belib.privacy-and-policy.show');

        //terms-and-conditions
        Route::get('belib/terms-and-conditions', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/terms-and-conditions');
        })->name('belib.terms-and-conditions.show');

        //ask-librarian
        Route::get('belib/', function ($org_slug) {
            return redirect(config('bookdose.app.belib_url') . '/' . $org_slug . '/ask-librarian/create');
        })->name('belib.ask-librarian.create');
    });
});
/*
|--------------------------------------------------------------------------
| redirect to LEARNEXT
|--------------------------------------------------------------------------
*/
Route::prefix('learnext')->name('learnext.')->group(function () {
	Route::get('show/{slug}', function ($slug) {
		return redirect(config('bookdose.app.learnext_url') . '/course/'.$slug);
	})->name('show');
});
