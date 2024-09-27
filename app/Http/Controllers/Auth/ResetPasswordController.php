<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use App\Models\UserOrg;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $org_slug=null, $token = null){
        $org_slug = $request->org_slug ?? '';

        $banner = Banner::active()
            ->where('display_area', 'login')
            ->orderBy('created_at', 'desc')
            ->first();

	    return view('auth.'.config('bookdose.theme_login').'.passwords.reset')->with(
	        ['org_slug' => $org_slug, 'token' => $token, 'email' => $request->email, 'banner' => $banner]
	    );
	}

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request, $org_slug=NULL)
    {
        $org_slug = $request->org_slug ?? '';
        $request->validate($this->rules(), $this->validationErrorMessages());

        if (!is_blank($org_slug)) {
            $throw_msg = NULL;
            $page_org = UserOrg::where('slug', $org_slug)->first();

            if (empty($page_org)) {
                $throw_msg = 'auth.reset_password.org_not_found';
            }
            else if (!empty($page_org->expiration_date) && Carbon::parse($page_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.reset_password.org_expired';
            }
            else if ($page_org->status != '1') {
                $throw_msg = 'auth.reset_password.org_inactive';
            }

            if (!empty($throw_msg)) {
                throw ValidationException::withMessages([ trans($throw_msg), ]);
            }
            else {
                $request->merge(['user_org_id' => $page_org->id]);
            }
        }


        $users = User::with('org')->active()->notExpired()->where( $request->only('email', 'user_org_id') );
        if (!is_blank($org_slug) && !empty($page_org->id ?? NULL)) {
            $users = $users->whereHas('org', function ($q) {
                $q->active()->notExpired();
            });
        }

        // dd(Str::replaceArray('?', $users->getBindings(), $users->toSql()));
        $users = $users->get();

        if (empty($users) || (!empty($users) && sizeof($users) <= 0)) {
            $throw_msg = 'auth.reset_password.'.( (!empty($request->user_org_id)) ? 'user_org_not_found' : 'user_not_found' );

            $user = User::where( $request->only('email', 'user_org_id') )->first();
            $user_org = (empty($request->user_org_id)) ? UserOrg::where('id', $user->user_org_id)->first() : $user->org ?? NULL;


            if (empty($user)) {
                $throw_msg = 'auth.reset_password.'.( (!empty($request->user_org_id)) ? 'user_org_not_found' : 'user_not_found' );
            }
            else if (empty($user_org)) {
                $throw_msg = 'auth.reset_password.org_not_found';
            }
            // Organize
            else if (!empty($user_org->expires_at) && Carbon::parse($user_org->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.reset_password.org_expired';
            }
            else if ($user_org->status != '1') {
                $throw_msg = 'auth.reset_password.org_inactive';
            }
            // User
            else if (!empty($user->expires_at) && Carbon::parse($user->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.reset_password.expired';
            }
            else if ($user->status != '1') {
                $throw_msg = 'auth.reset_password.inactive';
            }

            if (!empty($throw_msg)) {
                throw ValidationException::withMessages([ trans($throw_msg) ]);
            }
        }
        else if (sizeof($users) > 1) {
            if (!empty($request->user_org_id)) {
                throw ValidationException::withMessages([ trans('auth.reset_password.user_org_duplicate') ]);
            }
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        // dd('test');
        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'user_org_id', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => trans($response)], 200);
        }
        $user_org = UserOrg::select('id', 'slug')->active()->notExpired()->find($request->user_org_id);

        return redirect()->route('home', ($user_org->slug ?? ''))->with('status', trans($response));
    }
}
