<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use App\Models\UserOrg;
use App\Models\userOrgSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm(Request $request){
        $org_slug = $request->org_slug ?? '';
        if (is_blank($org_slug)) {
            return redirect()->route('login');
        }

        $user_org = UserOrg::active()->notExpired()->where('slug', $org_slug)->first();
        if (!$user_org) {
            return redirect()->route('login');
        }
        $setting_forgot = userOrgSetting::ofOrg( $user_org->id )->where('slug', 'forgot')->first() ?? (object)['data_value'=>[]];
        if (!config('bookdose.app.forgot.password') && !is_number_no_zero($setting_forgot->data_value['forgot_password']??'0')) {
            abort(404);
        }

        $banner = Banner::active()
            ->where('display_area', 'login')
            ->orderBy('created_at', 'desc')
            ->first();

	    return view('auth.'.config('bookdose.theme_login').'.passwords.email', compact('org_slug', 'banner'));
	}


    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request, $org_slug=NULL)
    {
        $this->validateEmail($request);

        if (!empty($org_slug)) {
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

        $setting_forgot = userOrgSetting::ofOrg( $page_org->id )->where('slug', 'forgot')->first() ?? (object)['data_value'=>[]];
        if (!config('bookdose.app.forgot.password') && !is_number_no_zero($setting_forgot->data_value['forgot_password']??'0')) {
            abort(404);
        }

        $users = User::active()->notExpired()
                ->with('org')
                ->where( $this->credentials($request) );
        if (!empty($org_slug) && !empty($page_org->id ?? NULL)) {
            $users = $users->whereHas('org', function ($q) {
                $q->active()->notExpired();
            });
        }
        // dd(Str::replaceArray('?', $users->getBindings(), $users->toSql()));
        $users = $users->get();

        if (empty($users) || (!empty($users) && sizeof($users) <= 0)) {
            $throw_msg = 'auth.reset_password.'.( (!empty($request->user_org_id)) ? 'user_org_not_found' : 'user_not_found' );

            $user = User::where( $this->credentials($request) )->first();
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
            $user_orgs = UserOrg::active()->notExpired()
                        ->with('users')
                        ->whereHas('users', function ($q) use ($request) {
                            $q->where( $this->credentials($request) )->active()->notExpired();
                        })
                        ->get();
            // dd(str::replaceArray('?', $user_orgs->getBindings(), $user_orgs->toSql()), $user_orgs->get());

            $orgs = [];
            foreach ($user_orgs AS $org) {
                $orgs[] = '<a href="'.route('password.request', $org->slug).'"><span class="h3 text-primary a-link"> '.$org->{'name_' . app()->getLocale()}.'</span></a>';
            }
            throw ValidationException::withMessages([ trans('auth.reset_password.choose_org', ['user_orgs' => join('<span class="h3 text-secondary"> / </span>', $orgs)]) ]);
        }
        // dd($users);

        if (
            (config('bookdose.app.forgot.password') && config('bookdose.app.forgot.send_mail')) ||
            (is_number_no_zero($setting_forgot->data_value['forgot_password']??'0') && is_number_no_zero($setting_forgot->data_value['forgot_send_mail']??'0'))
        ) {
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                        ? $this->sendResetLinkResponse($request, $response)
                        : $this->sendResetLinkFailedResponse($request, $response);
        }
        else {
            // Generate a token for password reset
            $token = Password::createToken($users->first());

            // Redirect to the password.reset route with the token and email
            return redirect()->route('password.reset', [
                'token' => $token,
                'email' => $request->email,
                'org_slug' => $org_slug
            ]);
        }
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'user_org_id');
    }
}
