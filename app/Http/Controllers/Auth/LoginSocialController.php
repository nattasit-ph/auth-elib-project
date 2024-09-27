<?php

// Setup Google SSO
// Go to https://console.cloud.google.com/ via dev.bookdose@gmail.com
// Create project
// Create oAuth consent screen
// Create Credentials "OAuth 2.0 Client IDs"
// Setup data below
// Application Type => Web Application
// Application Name => Login Social
// Authorized JavaScript origins => https://auth-bedo.belib.app
// Authorized redirect URIs => https://auth-bedo.belib.app/login/google/callback

// Setup Facebook SSO
// Go to https://developers.facebook.com/ via dev.bookdose@gmail.com
// Create project
// Add login Facebook module
// Add developer user and this user accept
// Go to basic info and click for show ID, secret

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginSocialController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

  	public function redirectToProvider($provider)
 	{
		return Socialite::driver($provider)->redirect();
 	}

	public function handleProviderCallback($provider)
	{
		$user = Socialite::driver($provider)->stateless()->user();
		// echo '<pre>'; print_r($user); echo '</pre>'; exit;

		if (User::where('email', $user->email)->exists()) {
			// Found
			User::where('email', $user->email)->update([
				// 'name' => $user->name,
				'password' => bcrypt(''),
				// 'provider' => $provider,
				// 'provider_user_id' => $user->id,
				'avatar_path' => $user->avatar_original,
				'last_login_at' => now(),
			]);
			$user = User::active()->where('email', $user->email)->first();
		}
		else {
			// Not found, new user
			//$default_user_role = Role::where('name', config('bookdose.login_social_default_role'))->first();
			$user_group_default = UserGroup::isDefault()->where('user_org_id', config('bookdose.default.user_org'))->first();

			User::create([
				'name' => $user->name ?? $user->email,
				'user_org_id' => config('bookdose.default.user_org'),
				'username' => $user->email,
				'password' => bcrypt(''),
				'email' => $user->email,
				'provider' => $provider,
				'provider_user_id' => $user->id,
				'avatar_path' => $user->avatar_original,
				'last_login_at' => now(),
				'user_group_id' => $user_group_default->id ?? 1,
			]);

			$user = User::active()->where('email', $user->email)->first();
			$user['member_id'] = str_pad((string)$user->id, 6, "0", STR_PAD_LEFT);
			$user->save();
			// $user->syncRoles([config('bookdose.login_social_default_role')]);

			// Belib system check
			if (!empty(config('bookdose.app.belib_url'))) {
				$default_role = Role::defaultBelib()->first();
				if (!empty($default_role)) {
					$has_role = new ModelHasRole();
					$has_role->role_id = $default_role->id;
					$has_role->model_type = 'App\Models\User';
					$has_role->model_id = $user->id;
					$has_role->timestamps = false;
					$has_role->save();
				}
			}

			// Learnext system check
			if (!empty(config('bookdose.app.learnext_url'))) {
				$default_role = Role::defaultLearnext()->first();
				if (!empty($default_role)) {
					$has_role = new ModelHasRole();
					$has_role->role_id = $default_role->id;
					$has_role->model_type = 'App\Models\User';
					$has_role->model_id = $user->id;
					$has_role->timestamps = false;
					$has_role->save();
				}
			}

			// KM system check
			if (!empty(config('bookdose.app.km_url'))) {
				$default_role = Role::defaultKm()->first();
				if (!empty($default_role)) {
					$has_role = new ModelHasRole();
					$has_role->role_id = $default_role->id;
					$has_role->model_type = 'App\Models\User';
					$has_role->model_id = $user->id;
					$has_role->timestamps = false;
					$has_role->save();
				}
			}
		}

		Auth::login($user);

		// Insert login history
      	$attempt = session('login_attempt', 0);
		$user->device = 'web';
		$user->device_id = '';
      	parent::insertLoginHistory($user, 'authen-success', $attempt);

		// return redirect()->route('home');
		return redirect(config('bookdose.app.main_product_redirect'));
	}

	public function handleProviderLogoutCallback()
	{
		//--- Start log ---//
		$log = collect([(object)[
			'module' => 'User',
			'severity' => 'Info',
			'title' => 'Logout SSO',
			'desc' => '[Succeeded] - TKPark ' . Auth::user()->email,
		]])->first();
		parent::Log($log);
		//--- End log ---//
	}
}
