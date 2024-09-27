<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use App\Models\UserOrg;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
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

    use SendsPasswordResetEmails;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

    public function forgotPwdEmail(Request $request)
    {
        $err = ['status' => 'error', 'msg' => '', 'result' => (object)[]];

        $email = request()->email ?? '';
        $user_org_id = request()->user_org_id ?? '';
        $lang = request()->lang ?? 'th';

		if (is_blank($email)) {
			$err['msg'] = 'Missing parameter. Please specify email.';
			return response()->json($err, 200);
		}

		if (!is_blank($user_org_id)) {
            $err_flag = NULL;

            $request_org = UserOrg::where('id', $user_org_id)->first();
            if (empty($request_org)) {
                $err_flag = 'org_not_found';
            }
            else if (!empty($request_org->expiration_date) && Carbon::parse($request_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
                $err_flag = 'org_expired';
            }
            else if ($request_org->status != '1') {
                $err_flag = 'org_inactive';
            }

            if (!empty($err_flag)) {
                $err['msg'] = trans('auth.reset_password.'.$err_flag);
                return response()->json($err, 200);
            }
		}

        $users = User::active()->notExpired()
                ->with('org')
                ->where( $this->credentials($request) );
        if (!is_blank($user_org_id) && !empty($request_org->id ?? NULL)) {
            $users = $users->whereHas('org', function ($q) {
                $q->active()->notExpired();
            });
        }
        // dd(Str::replaceArray('?', $users->getBindings(), $users->toSql()));
        $users = $users->get();

        if(empty($users) || (!empty($users) && sizeof($users) <= 0)){
            $err_msg = ( (!is_blank($user_org_id)) ? 'auth.reset_password.user_org_not_found' : 'app.msg.error.email_not_found' );

            $user = User::where( $this->credentials($request) )->first();
            $user_org = (is_blank($user_org_id)) ? UserOrg::where('id', $user->user_org_id)->first() : $user->org ?? NULL;


            if (empty($user)) {
                $err_msg = ( (!is_blank($user_org_id)) ? 'auth.reset_password.user_org_not_found' : 'app.msg.error.email_not_found' );
            }
            else if (empty($user_org)) {
                $err_msg = 'auth.reset_password.org_not_found';
            }
            // Organize
            else if (!empty($user_org->expires_at) && Carbon::parse($user_org->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $err_msg = 'auth.reset_password.org_expired';
            }
            else if ($user_org->status != '1') {
                $err_msg = 'auth.reset_password.org_inactive';
            }
            // User
            else if (!empty($user->expires_at) && Carbon::parse($user->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $err_msg = 'auth.reset_password.expired';
            }
            else if ($user->status != '1') {
                $err_msg = 'auth.reset_password.inactive';
            }
            if (!empty($err_msg)) {
                $err['msg'] = __($err_msg, [], $lang);
                return response()->json($err);
            }
        }
        else if (sizeof($users) > 1) {
            if (!is_blank($user_org_id)) {
                $err['msg'] = trans('auth.login.user_org_duplicate');
                return response()->json($err, 200);
            }

            $user_orgs = UserOrg::active()->notExpired()
                        ->select('id', 'slug', 'name_th', 'name_en')
                        ->whereHas('users', function ($q) use ($request) {
                            $q->where( $this->credentials($request) )->active()->notExpired();
                        })
                        ->get();
            // error
            // $err['msg'] = trans('auth.login.duplicate');
            $err['msg'] = 'duplicate';
            $err['result'] = ['user_orgs' => $user_orgs->toArray()];
            return response()->json($err, 200);
        }
        else if (is_blank($user_org_id)) {
            request()->merge(['user_org_id' => $users[0]->user_org_id]);
        }

        // dd(request()->all(), $request->all());
        try {
            $this->sendResetLinkEmail($request);
            return response()->json( [
                'status' => 'success',
                'msg' => __('app.msg.success.forgot', [], $lang),
                'result' => (object)[],
            ]);
        } catch (\Throwable $e) {
            $err['status'] = 'error';
			$err['msg'] = $e->getMessage();
			return response()->json($err, 200);
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
