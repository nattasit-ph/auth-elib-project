<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
Use App\Models\ProductMain;
Use App\Models\Module;
use Session;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public $product_mains = [];
	public $has_library = [];
	public $has_elibrary = [];

	public function insertLoginHistory($user, $status, $attempt)
	{
		LoginHistory::insert(
			array(
				'user_org_id' => $user->user_org_id ?? NULL,
				'user_id' => $user->id ?? NULL,
				'username' => $user->username ?? '',
				'email' => $user->email ?? '',
				'device' => $user->device ?? 'web',
				'device_id' => $user->device_id ?? '',
				'ip' => request()->ip(),
				'status' => $status ?? '',
				'attempt' => $attempt ?? 0,
				'created_at' => now(),
				'updated_at' => now(),
			)
		);
		if (Str::contains($status, 'success')) {
			// Update last login datetime
			User::where('username', $user->username)
				->update(['last_login_at' => now()]);

			// Reset attempt
			session('login_attempt', 0);
		}
	}

	public function Log($log)
	{
		$user = (Auth::check() ? Auth::user() : NULL);
		Log::insert(
			array(
				'channel' => $log->channel ?? 'Web',
				'module' => $log->module ?? NULL,
				'severity' => $log->severity ?? NULL,
				'title' => $log->title ?? NULL,
				'description' => $log->desc ?? NULL,
				'user_org_id' => $user->user_org_id ?? NULL,
				'user_id' => ($user ? ($user->id ?? NULL) : NULL),
				'email' => ($user ? ($user->email ?? NULL) : NULL),
				'ip' => request()->ip(),
				'created_at' => now(),
				'updated_at' => now(),
			)
		);
	}

	public function getAuthenticatedUser()
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			return response()->json(['token_expired'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['token_invalid'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			return response()->json(['token_absent'], $e->getStatusCode());
		}
		// the token is valid and we have found the user via the sub claim
		return response()->json(compact('user'));
	}

	public function getProductMains()
	{
        $user_org_id = $user_org_id ?? ( (Auth::check()) ? Auth::user()->user_org_id : config('bookdose.default.user_org') );
		// if (is_null($this->product_mains)) {
			$this->product_mains = ProductMain::ofOrg($user_org_id)->active()->get();
		// }
		// if (is_null($this->has_library)) {
			$this->has_library = ProductMain::ofOrg($user_org_id)->hasLibrary();
		// }
		// if (is_null($this->has_elibrary)) {
        $this->has_elibrary = ProductMain::ofOrg($user_org_id)->hasElibrary();
		// }
		Session::put('product_mains', $this->product_mains);
		Session::put('has_library', $this->has_library);
		Session::put('has_elibrary', $this->has_elibrary);
	}

	public function getModules()
	{
		// if (is_null($this->modules)) {
		$this->modules = Module::myOrg()->active()->inCenter()->get();
		// echo '<pre>'; print_r($this->modules->toArray()); echo '</pre>'; exit;
		// }
		// if (is_null($this->has_module)) {
		$this->has_module = $this->modules->count() > 0 ? true : false;
		// }
		Session::put('modules', $this->modules);
		Session::put('has_module', $this->has_module);
		Session::put('has_reward', (Module::myOrg()->active()->where('slug','reward')->count())? true : false);
		Session::put('has_reference_link', (Module::myOrg()->active()->where('slug','reference-link')->count())? true : false);
		// return $this->modules;
	}

    public function change_config_for_web_view() {
        $show = session()->get('web_view');
        if ($show == 'show') {
           config(['bookdose.theme_login' => config('bookdose.web_view.theme_login')]);
           config(['bookdose.theme_front' => config('bookdose.web_view.theme_front')]);
           config(['bookdose.app.project' => config('bookdose.web_view.app_project')]);
           config(['bookdose.app.folder' => config('bookdose.web_view.app_folder')]);
        }
    }
}
