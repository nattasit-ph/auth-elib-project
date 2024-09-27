<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginSocialController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;

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

Route::get('locale/{locale}', function ($locale) {
	Session::put('locale', $locale);
    return back();
})->name('lang.switch');

if (config('bookdose.login_adldap') === true && (config('bookdose.regis.online') === false)) {
	Auth::routes([
		'register' => false,
	  	'reset' => false,
	  	'verify' => false,
	]);
}
else {
    Auth::routes([
        'register' => config('bookdose.regis.online'),
        'reset' => true,
        'verify' => config('bookdose.regis.verify'),
	]);
    Route::prefix('{org_slug?}')->group(function () {
    	Auth::routes([
    		'register' => config('bookdose.regis.online'),
    		'reset' => true,
    		'verify' => config('bookdose.regis.verify'),
    	]);
        Route::post('site-login', 'App\Http\Controllers\Auth\LoginController@siteLogin')->name('site-login');
    });
}
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('signout');

Route::get('/ajax-set-ldap-config', [App\Http\Controllers\HomeController::class, 'ajaxSetLdapConfig'])->name('ajaxSetLdapConfig');

route::get('login/labour/callback', [App\Http\Controllers\Auth\CallbackController::class, 'callbackLabour'])->name('login.labour.callback');
Route::get('login/{provider}', [LoginSocialController::class, 'redirectToProvider'])->name('login.social');
Route::get('login/acl/callback', 'App\Http\Controllers\Auth\LoginController@loginACL')->name('login.acl.callback');
Route::get('login/{provider}/callback', [LoginSocialController::class, 'handleProviderCallback'])->name('login.social.callback');
Route::get('logout/{provider}/callback', [LoginSocialController::class, 'handleProviderLogoutCallback'])->name('logout.social.callback');

// Route::get('verify/{email}/{token}', [RegisterController::class, 'verify'])->name('verify');
Route::get('{org_slug?}/verify/{email}/{token}', [RegisterController::class, 'verify'])->name('verify');

//search all
Route::get('quickSearch', 'App\Http\Controllers\Api\searchController@index')->name('quickSearch');

//search local all
Route::get('quickSearchLocal', 'App\Http\Controllers\Api\searchController@search_local_api')->name('quickSearchLocal');


Route::prefix('{org_slug?}')->group(function () {
    Route::get('/home', function ($org_slug=NULL) {
        // dd('/home', $org_slug);
        if (Auth::check() && !empty(config('bookdose.app.main_product_redirect')))
            return redirect(config('bookdose.app.main_product_redirect') . ( (!is_blank($org_slug ?? '')) ? '/'.$org_slug : '/'.(Auth::user()->org->slug??'') ) );
        else
            return view('welcome');
    })->middleware('auth')->name('home');
    Route::get('/', function ($org_slug=NULL) {
        // dd('/home', $org_slug);
        if (Auth::check() && !empty(config('bookdose.app.main_product_redirect')))
            return redirect(config('bookdose.app.main_product_redirect') . ( (!is_blank($org_slug ?? '')) ? '/'.$org_slug : '/'.(Auth::user()->org->slug??'') ) );
        else
            return view('welcome');
    })->middleware('auth');


    switch (config('bookdose.theme_front')) {
        case "theme_irid":
            Route::redirect('admin', 'admin/pages/all');
            break;
        case "theme_kusip":
            Route::redirect('admin', 'admin/news/all');
            break;

        default:
        Route::redirect('admin', 'admin/site/info');
    }
});
/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');
Route::get('/delete-user-privacy', [App\Http\Controllers\HomeController::class, 'delete_user_privacy'])->name('delete_user_privacy');
Route::get('/gdpr', [App\Http\Controllers\HomeController::class, 'gdpr'])->name('gdpr');

Route::group(['namespace'=>'App\Http\Controllers\Front'], function() {
	Route::group(['middleware'=>['auth']], function() {
		include_once('web_front.php');
	});
});

Route::get('chatbot/fullscreen', [App\Http\Controllers\Front\Chatbot\ChatbotController::class, 'fullscreen'])->name('fullscreen');

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
*/
// switch (config('bookdose.theme_front')) {
// 	case "theme_irid":
// 		Route::redirect('admin', 'admin/pages/all');
// 	  	break;
// 	case "theme_kusip":
// 		Route::redirect('admin', 'admin/news/all');
// 		break;

// 	default:
// 	Route::redirect('admin', 'admin/site/info');
// }
Route::prefix('{org_slug}')->group(function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
       Route::name('admin.')->namespace('App\Http\Controllers\Back')->group(function () {
           include_once('web_back.php');
        });
    });
});

Route::get('test/template-email', [App\Http\Controllers\TestController::class, 'template_email'])->name('test.templateEmail');

/*
|--------------------------------------------------------------------------
| Route Org Prefix
|--------------------------------------------------------------------------
|
*/

if (config('bookdose.app.prefix_route_org')) {
	// include_once('org/web.php');
}
