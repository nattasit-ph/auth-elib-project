<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\Banner;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use DB;
use App\Models\User;
use App\Models\Role;
use App\Models\ModelHasRole;
use App\Models\UserGroup;
use App\Models\UserAd;
use SoapClient;
use Adldap\Connections\Ldap;
use App\Models\UserOrg;
use App\Models\userOrgSetting;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Stmt\TryCatch;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        Cookie::queue( Cookie::forget('st_samesite') );
        Cookie::queue( Cookie::forget('00') );
        Cookie::queue( Cookie::forget('TESTCOOKIESENABLED') );
    }

    protected function redirectTo()
    {
        // Insert login history
        $attempt = session('login_attempt', 0);
        // $user = Auth::user()->with('org')->find(Auth::id());
        $user = Auth::user();
        $user->device = 'web';
        $user->device_id = '';
        $user_org_slug = Auth::user()->org->slug;

        parent::insertLoginHistory($user, 'authen-success', $attempt);
        if (request()->session()->has('url.redirect')) {
            $parse_url = parse_url( session('url.redirect') );
            $org_slug_path = explode('/', Str::substr($parse_url['path'], 1));
            if (!Str::of(head($org_slug_path))->trim()->isEmpty()) {
                // Replace Slug
                $user_org = UserOrg::active()->where('slug', head($org_slug_path))->first();
                if(!empty($user_org)) {
                    return Str::replace(head($org_slug_path), $user_org_slug,  session('url.redirect')).'/';
                }
            }
            return Str::replace($parse_url['host'], $parse_url['host'].'/'.$user_org_slug,  session('url.redirect')).'/';
        }
        else if (!empty(config('bookdose.app.main_product_redirect'))) {
            return config('bookdose.app.main_product_redirect').((!empty($user_org_slug))?'/'.$user_org_slug.'/':'');
        }
        else {
            return route('home', $user_org_slug);
        }
    }

    public function username()
    {
        if (config('bookdose.login_adldap') === true) {
            $identity = request()->input('username');
            $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            if ($field == 'email') {
                config(['bookdose.ldap.locate_users_by' => 'userprincipalname']);
            }
            else {
                config(['bookdose.ldap.locate_users_by' => 'samaccountname']);
            }
            request()->merge([$field => $identity]);
            return $field;
        }
        else {
            $identity = request()->input(config('bookdose.login_auth_using_field') ?? 'username');
            $fieldType = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            request()->merge([$fieldType => $identity]);
            return $fieldType;
        }
    }

    /**
    * The user has been authenticated.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  mixed  $user
    * @return mixed
    */
    protected function authenticated(Request $request, $user)
    {
        $user_org = NULL;
        if (!empty($request->org_slug)) {
            $user_org = UserOrg::where('slug', $request->org_slug)->first();
        }
        $status = $user->status;
        $email_verified_state = $user->email_verified_state;
        $admin_verified_state = $user->admin_verified_state;
        $accessible_at = $user->accessible_at;
        $expires_at = $user->expires_at;
        $user_org_id = $user->user_org_id;

        $setting_regis = userOrgSetting::ofOrg($user_org_id)->where('slug', 'regis')->first() ?? (object)['data_value'=>[]];
        if($status == 0){
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.inactive')];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        // if(config('bookdose.regis.verify') && $email_verified_state == '1'){
        if(($setting_regis->data_value['regis_verify']??'0') == '1' && $email_verified_state == '1'){
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.verify')];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        // if(config('bookdose.regis.verify_by_admin') && $admin_verified_state == '1'){
        if(($setting_regis->data_value['regis_verify_by_admin']??'0') == '1' && $admin_verified_state == '1'){
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.verify_by_admin')];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        if(!empty($accessible_at) && $accessible_at > Carbon::now()){
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.accessible', ['date'=>$accessible_at->format('Y-m-d H:i')])];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        if(!empty($expires_at) && $expires_at < Carbon::now()){
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.expired')];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        if(!empty($user_org) && $user_org->id != $user_org_id) {
            $this->guard()->logout();
            $errors = ['email'=>trans('auth.login.user_org_not_found')];
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        if (Schema::hasTable('product_mains')) {
            parent::getProductMains();
        }
        parent::getModules();
    }

    public function logout(Request $request)
    {
        // dd('logout');
        Auth::logout();
        $request->session()->invalidate();

        if(!empty(config('bookdose.tkpark_redirect_logout')) ) {
            $url = config('bookdose.tkpark_url').'/auth/logout';
            $redirect_uri = config('bookdose.tkpark_redirect_logout');
            $state = bin2hex(random_bytes(32/2));

            $fields = [];
            $fields['redirect_uri'] = $redirect_uri;
            $fields['state'] = $state;

            $response = Http::withoutVerifying()->withHeaders(['Content-Type' => 'application/json'])->withOptions(["verify"=>false])->get($url, $fields);
            $result = json_decode($response, true);
        }

       return redirect('/');
    }

    public function showLoginForm(Request $request)
    {
        $org_slug = $request->org_slug ?? '';
        // dd($request->error);

        $org_id = config('bookdose.default.user_org');
        $user_org = NULL;
        if (!is_blank($org_slug)) {
            $user_org = UserOrg::active()->notExpired()->where('slug', $org_slug)->first();
            if (empty($user_org)) {
                return redirect()->route('login');
            }
            $org_id = $user_org->id ?? config('bookdose.default.user_org');
            $org_slug = $user_org->slug ?? config('bookdose.default.org_slug');
        }
        $setting_forgot = userOrgSetting::ofOrg( $org_id )->where('slug', 'forgot')->first() ?? (object)['data_value'=>[]];
        $setting_regis = userOrgSetting::ofOrg( $org_id )->where('slug', 'regis')->first() ?? (object)['data_value'=>[]];

        $banner = Banner::ofOrg( $org_id )->active()
            ->where('display_area', 'login')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('auth.'.config('bookdose.theme_login').'.login', compact('banner', 'org_slug', 'user_org', 'setting_forgot', 'setting_regis'));
    }

    /**
    * Get the failed login response instance.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Symfony\Component\HttpFoundation\Response
    *
    * @throws \Illuminate\Validation\ValidationException
    */
    protected function sendFailedLoginResponse(Request $request)
    {
        $field_msg = 'auth.failed';
        if(strtoupper(config('bookdose.app.name')) == "BEDO"){
            $field_msg = 'auth.bedo.failed';
        }

        throw ValidationException::withMessages([
            trans($field_msg),
        ]);
    }

    public function siteLogin(Request $request) {
        $org_slug = $request->org_slug ?? '';
        if (!is_blank($org_slug)) {
            $user_org = UserOrg::ofSlug($org_slug)->first();
            $org_slug = $user_org->slug ?? '';
        }

        return redirect()->route('login', $org_slug)->withInput();
    }

    public function login(Request $request)
    {
        $org_slug = $request->org_slug ?? '';

        if (!is_blank($org_slug)) {
            $throw_msg = NULL;
            $page_org = UserOrg::where('slug', $org_slug)->first();
            if (empty($page_org)) {
                $throw_msg = 'auth.login.org_not_found';
            }
            else if (!empty($page_org->expiration_date) && Carbon::parse($page_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.login.org_expired';
            }
            else if ($page_org->status != '1') {
                $throw_msg = 'auth.login.org_inactive';
            }

            if (!empty($throw_msg)) {
                // throw ValidationException::withMessages([ trans($throw_msg), ]);
                return redirect()->route('login', $org_slug)->with('error', trans($throw_msg));
            }
            else {
                $request->merge(['user_org_id' => $page_org->id]);
            }
        }

        // -----------------------------------------------------
        // Validate User
        $users = User::active()->notExpired()
                ->with('org')
                ->where( $request->only($this->username(), 'user_org_id') )
                ->whereHas('org', function ($q) {
                    $q->active()->notExpired();
                })
                ->get();
        // dd(str::replaceArray('?', $users->getBindings(), $users->toSql()), $users->get());

        if (empty($users) || (!empty($users) && sizeof($users) <= 0)) {
            $error = ( (!empty($request->user_org_id)) ? 'user_org_not_found' : 'user_not_found_from_project' );
            $user = User::where( $request->only($this->username(), 'user_org_id') )->with('org')->first();
            $user_org = $user->org ?? NULL;

            // Organize
            if (empty($user)) {
                $error = ( (!empty($request->user_org_id)) ? 'user_org_not_found' : 'user_not_found_from_project' );
            }
            else if (empty($user_org)) {
                $error = 'org_not_found';
            }
            else if (!empty($user_org->expires_at) && Carbon::parse($user_org->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $error = 'org_expired';
            }
            else if ($user_org->status != '1') {
                $error = 'org_inactive';
            }
            // User
            else if (!empty($user->expires_at) && Carbon::parse($user->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
                $error = 'expired';
            }
            else if ($user->status != '1') {
                $error = 'inactive';
            }

            // throw ValidationException::withMessages([ 'error' => [$throw_msg] ]);
            $setting_regis = userOrgSetting::ofOrg( config('bookdose.default.user_org') )->where('slug', 'regis')->first() ?? (object)['data_value'=>[]];

            $error_message = __('auth.login.'.$error);
            if ($error == 'user_not_found_from_project') {
                $error_message = __('auth.login.'.$error, ['project' => config('bookdose.app.name')]);
                if (Route::has('register') && is_number_no_zero($setting_regis->data_value['regis_online']??'0')) {
                    return redirect()->route('login', $org_slug)->withInput()->with('error', $error_message)->with('register', true);
                }
            }
            return redirect()->route('login', $org_slug)->withInput()->with('error', $error_message);
        }
        else if (sizeof($users) > 1) {
            if (!empty($request->user_org_id)) {
                // throw ValidationException::withMessages([ 'error' => [trans('auth.login.user_org_duplicate')] ]);
                return redirect()->route('login', $org_slug)->withInput()->with('error', trans('auth.login.user_org_duplicate'));
            }
            $user_orgs = UserOrg::select('id', 'slug', 'name_th', 'name_en', 'status', 'expires_at')->active()->notExpired()
                        ->with('users')
                        ->whereHas('users', function ($q) use ($request) {
                            $q->where( $request->only($this->username()) )->active()->notExpired();
                        })
                        ->get();
            // dd(str::replaceArray('?', $user_orgs->getBindings(), $user_orgs->toSql()), $user_orgs->get());

            return redirect()->route('login', $org_slug)->withInput()->with('success', trans('auth.login.user_duplicate', ['project' => config('bookdose.app.name'), 'total' => $user_orgs->count()]))->with('orgs', $user_orgs);
        }
        else if (is_blank($org_slug) && empty($request->user_org_id)) {
            $request->merge(['user_org_id' => $users[0]->user_org_id]);
            return redirect()->route('login', $users[0]->org->slug)->withInput();
        }
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::guard()->attempt( array_merge($request->only($this->username(), 'password', 'user_org_id'), ['status' => 1]), $request->filled('remember'))) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        //oic api login
        if(config('bookdose.login_oic_oauth2') == true){
            $this->loginOICApiOauth2($request);
        }


        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function loginOICApiOauth2($request)
    {
        $field_login = config('bookdose.oic_oauth2_field_login');
        $request[$field_login] = $request->email ?? $request->username;

        $token_url = config('bookdose.oic_oauth2_token_url');
        $login_url = config('bookdose.oic_oauth2_login_url');
        $client_id = config('bookdose.oic_oauth2_client_id');
        $client_secret = config('bookdose.oic_oauth2_client_secret');
        $default_password = base64_encode(config('bookdose.oic_oauth2_default_pwd'));
        $prefix_name = config('bookdose.oic_oauth2_prefix_name');
        $prefix = explode(",", $prefix_name);

        $token_header_authorization = "Basic ".base64_encode($client_id.":".$client_secret);
        // dd($field_login, $token_url, $login_url, $client_id, $client_secret, $token_header_authorization);
        $get_token = Http::withHeaders([
            'Authorization' => $token_header_authorization,
        ])->post($token_url, [
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);
        $token_attr = $get_token->json();

        if(isset($token_attr['error'])){
            throw ValidationException::withMessages([
                config('bookdose.login_auth_using_field') ?? 'username' => ['The client is not authorized to request a token using this method.'],
            ]);
        }

        if(empty($token_attr['access_token']) || empty($token_attr['token_type'])){
            throw ValidationException::withMessages([
                config('bookdose.login_auth_using_field') ?? 'username' => ['Token is empty, Please contact administrator.'],
            ]);
        }

        $login_header_authorization = $token_attr['token_type']. " ".$token_attr['access_token'];
        $login_body = array('username' => $request[$field_login], 'password' => $request->password);
        // dd($login_body);
        $get_login = Http::withHeaders([
            'Authorization' => $login_header_authorization,
        ])->post($login_url, $login_body);
        $login_attr = $get_login->json();

        if($login_attr['result'] == "FAIL"){
            throw ValidationException::withMessages([
                config('bookdose.login_auth_using_field') ?? 'username' => [trans('auth.failed')],
            ]);
        }
        // dd($login_attr);

        if($login_attr['result'] == "SUCCESS"){
            //get attr name
            $employeeName = explode(" ", $login_attr['employeeName']);
            $firstname = current(str_replace($prefix, "", $employeeName));
            $lastname = end($employeeName);
            $name = $firstname." ".$lastname;
            //get attr position
            $position = $login_attr['positionName'];
            //get attr department
            $department = $login_attr['mapDeptName'];
            //get attr member_id & username
            $member_id = $login_attr['employeeCode'];
            $username = $login_attr['employeeCode'];
            //get attr email
            $email = $request[$field_login]."@oic.or.th";

            $gender = '';
            //check user info form local database (need to load every day at 00.00)
            $user_ad = UserAd::where('member_id', $member_id)->first();
            if($user_ad){
            $user_info = json_decode($user_ad->data_info);
            $email = $user_info->A_EMAIL;
            $name = $user_info->A_TFNAME." ".$user_info->A_TLNAME;
            if(in_array(strtoupper($user_info->A_GENDER), ['ชาย', 'M', 'MEN'])){
                $gender = 'm';
            }else{
                $gender = 'f';
            }
            // dd($user_info, $user_info->A_EMAIL, $user_info->A_GENDER, $user_info->A_TFNAME, $user_info->A_TLNAME);
            }

            $user = DB::table('users')->where('username', $username)->first();
            if($user){
                $update_data['name'] = $name;
                $update_data['email'] = $email;
                $update_data['gender'] = $gender;
                $update_data['data_info'] = array('position' => $position, 'department' => $department);
            User::where('id', $user->id)->update($update_data);
            }else{
                //user group
                $user_group_default = UserGroup::where('name', 'พนักงาน')->where('user_org_id', config('bookdose.default.user_org'))->first();
                $create_data['user_group_id'] = $user_group_default->id ?? 1;
                $create_data['email'] = $email;
                $create_data['gender'] = $gender;
                $create_data['name'] = $name;
                $create_data['data_info'] = array('position' => $position, 'department' => $department);
                $create_data['username'] = $username;
                $create_data['member_id'] = $member_id;
                $create_data['password'] = bcrypt($default_password);
                $create_data['user_org_id'] = config('bookdose.default.user_org');
                $user = User::create($create_data);

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
            }

            $chk_login = Auth::attempt(['username' => $user->username, 'password' => $default_password, 'status' => 1]);
            if($chk_login){
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }
                return $this->sendLoginResponse($request);
            }else{
                throw ValidationException::withMessages([
                    config('bookdose.login_auth_using_field') ?? 'username' => ['The account has been disabled.'],
                ]);
            }

        }else{
            throw ValidationException::withMessages([
                config('bookdose.login_auth_using_field') ?? 'username' => ['Error AD response status.'],
            ]);
        }
    }
}
