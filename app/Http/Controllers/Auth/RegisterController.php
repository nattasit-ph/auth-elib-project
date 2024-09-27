<?php

namespace App\Http\Controllers\Auth;

use DB;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Banner;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserOrg;
use App\Models\ConsentControl;
use App\Models\ConsentUser;
use App\Mail\UserVerify;
use App\Mail\AdminVerify;
use App\Mail\UserWelcome;
use App\Models\userOrgSetting;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
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
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		if (config('bookdose.app.reCaptcha')) {
			return Validator::make($data, [
				'name' => ['nullable', 'string', 'max:255'],
				'email' => ['required', 'string', 'email', 'max:255', /* 'unique:users' */],
				'password' => ['required', 'string', 'min:4', 'max:13', 'confirmed',
					'regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_])/' // must contain a special character
				],
				'g-recaptcha-response' => 'recaptcha',
			]);
		} else {
			return Validator::make($data, [
				'name' => ['nullable', 'string', 'max:255'],
				'email' => ['required', 'string', 'email', 'max:255', /* 'unique:users' */],
				'member_id' => ['string', 'max:255', 'unique:users'],
				'password' => ['required', 'string', 'min:4', 'max:13', 'confirmed',
					'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_])/' // must contain a special character
				]
			]);
		}
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\Models\User
	 */
	protected function create(array $data)
	{
		$user = new User();
		$user['user_org_id'] = ($data['user_org_id'] ?? config('bookdose.default.user_org'));

		//user group
		$user_group_default = UserGroup::isDefault()->where('user_org_id', ($data['user_org_id'] ?? config('bookdose.default.user_org')))->first();
		$user['user_group_id'] = $user_group_default->id ?? 1;

		//ganeral data
		if (array_key_exists('f_name', $data) || array_key_exists('l_name', $data)) {
			$user['name'] = $data['f_name'] . ' ' . $data['l_name'];
		} else {
			$user['name'] = $data['name'];
		}
		if (array_key_exists('contact_number', $data)) {
			$user['contact_number'] = $data['contact_number'];
		}
		if (array_key_exists('gender', $data)) {
			$user['gender'] = $data['gender'];
		}
		if (array_key_exists('user_group', $data)) {
			$user['user_group_id'] = $data['user_group'];
		}
		if (array_key_exists('position', $data)) {
			$user['position'] = $data['position'];
		}

		//data in data_info
		if (array_key_exists('id_card', $data)) {
			$data_info['id_card'] = $data['id_card'];
		}
		if (array_key_exists('f_name', $data)) {
			$data_info['f_name'] = $data['f_name'];
		}
		if (array_key_exists('l_name', $data)) {
			$data_info['l_name'] = $data['l_name'];
		}
		if (array_key_exists('occupation', $data)) {
			$data_info['occupation'] = $data['occupation'];
		}
		if (array_key_exists('department', $data)) {
			$data_info['department'] = $data['department'];
		}
		if (array_key_exists('address', $data)) {
			$data_info['address'] = $data['address'];
		}
		if (array_key_exists('range_age', $data)) {
			$data_info['range_age'] = $data['range_age'];
		}
		if (array_key_exists('line_id', $data)) {
			$data_info['line_id'] = $data['line_id'];
		}
		if (array_key_exists('facebook_id', $data)) {
			$data_info['facebook_id'] = $data['facebook_id'];
		}
		$user['data_info'] = $data_info;

		//get user_info_template
		$user_org = UserOrg::where('id', $user['user_org_id'])->first();
		$user_info_template = $user_org->user_info_template ?? [];

		$setting_regis = userOrgSetting::ofOrg($user['user_org_id'])->where('slug', 'regis')->first();
        $setting_regis = $setting_regis ?? (object)['data_value'=>[]] ;

		if(!empty($user_info_template)){
			$arr_user_info_data = [];
			foreach ($user_info_template as $k => $template) {
				$arr_user_info_data[$template['key']] = $data[$template['key']] ?? '';
			}
			if(!empty($arr_user_info_data)){
				$user['data_info'] = $arr_user_info_data;
			}
		}

		//email and password
		$user['email'] = $data['email'];
		$user['password'] = Hash::make($data['password']);
        // if (config('bookdose.regis.verify')) {
        if (($setting_regis->data_value['regis_verify']??'0') == '1') {
            $user['email_verified_state'] = '1';
        }
        // if (config('bookdose.regis.verify_by_admin')) {
        if (($setting_regis->data_value['regis_verify_by_admin']??'0') == '1') {
            $user['admin_verified_state'] = '1';
        }

		$user['registry_at'] = Carbon::now();
		$user->save();

		// //set member_id
		// if(!empty($data['member_id'])){
		// 	$user['member_id'] = $data['member_id'];
		// }else{
		// 	// $user['member_id'] = str_pad((string)$user->id, 6, "0", STR_PAD_LEFT);
		// 	$user['member_id'] = str_pad((string)(User::ofOrg($user['user_org_id'])->count()), 6, "0", STR_PAD_LEFT);
		// }

		// //check duplicate member_id
		// $member_id = $user['member_id'];
		// if( $this->isDuplicate($user_org->id, ['id' => $user->id, 'member_id' => $member_id]) ){
		// 	$uniqid = uniqid();
		// 	$user['member_id'] = $uniqid;
		// }

		// $user['username'] = $data['email'];

        $user['member_id'] = $data['member_id'];
        $user['username'] = $data['username'];
		$user['created_by'] = $user->id;
		$user['updated_by'] = $user->id;
		$user->save();

		// consent_control 23/09/2022
		if(!empty($data['consent_control'])){
			//get time zone
			//$getloc = json_decode(file_get_contents("http://ipinfo.io/"));
			//get device

			$ua = $this->getBrowser();
			$agent = $ua['name']." ".$ua['version']." on " .$ua['platform'];
			//$device = $agent." at ".$getloc->timezone;
			$device = $agent." at ".$data['timezone'] ?? 'Asia/Bangkok';
			//get last consent_control
			$consent = ConsentControl::first();
			if(!empty($consent)){
				//insert consent_control
				$data_consent['device'] = $device;
				$data_consent['version'] = $consent->version;
				$data_consent['status'] = 1;
				$data_consent['user_id'] = $user->id;
				ConsentUser::create($data_consent);
			}
		}

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
		if (!empty(config('bookdose.app.km_url')) && config('bookdose.regis.km_role') == true) {
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

        // $user->user_org_slug = $user_org->slug ?? '';
		// if (config('bookdose.regis.verify') || config('bookdose.regis.verify_by_admin')) {
		if (($setting_regis->data_value['regis_verify']??'0') == '1' || ($setting_regis->data_value['regis_verify_by_admin']??'0') == '1') {
			// if (config('bookdose.regis.verify')) {
			if (($setting_regis->data_value['regis_verify']??'0') == '1') {
                // dd('RegusterController@create', $user);
				Mail::to($data['email'])->send(new UserVerify($user));
			}
			// if (config('bookdose.regis.verify_by_admin')) {
			if (($setting_regis->data_value['regis_verify_by_admin']??'0') == '1') {
				Mail::to($data['email'])->send(new AdminVerify($user));
			}
		}else{
			Mail::to($data['email'])->send(new UserWelcome($user));
		}

		return $user;
	}

	public function showRegistrationForm(Request $request, $org_slug='')
	{
        $org_slug = $request->org_slug ?? '';

        $org_id = config('bookdose.default.user_org');
        if ($org_slug != '') {
            $user_org = UserOrg::active()->notExpired()->where('slug', $org_slug)->first();
            if (empty($user_org)) {
                return redirect()->route('login');
            }
            $org_id = $user_org->id ?? config('bookdose.default.user_org');
            $org_slug = $user_org->slug ?? '';
        }
		//get user_info_template
		$user_org = UserOrg::ofOrg( $org_id )->first();
        $setting_regis = userOrgSetting::ofOrg( $org_id )->where('slug', 'regis')->first();
        $setting_regis = $setting_regis ?? (object)['data_value'=>[]] ;

        if (($setting_regis->data_value['regis_online'] ?? '0') != '1') {
			return view('message')->withErrors(['error' => 'องค์กร/หน่วยงาน ไม่ได้เปิดให้สมัครสมาชิกในขณะนี้ กรุณาติดต่อผู้ดูแลระบบเพื่อขอเข้าใช้งานระบบ <a href="'.route('login', $org_slug).'">กลับไปหน้า login</a>']);
        }
		$user_info_template = $user_org->user_info_template ?? [];
		// dd($user_info_template);

		$userGroup = UserGroup::ofOrg($org_id)->Active()->get();

		//get consent last version
		$consent = ConsentControl::first();
		if (config('bookdose.regis.consent_enable') === true && empty($consent)) {
			return view('message')->withErrors(['error' => 'ระบบยังไม่มีการกำหนดคำยินยอม (Consent). กรุณาติดต่อผู้ดูแลระบบเพื่อทำการตั้งค่า']);
		}

		$banner = Banner::ofOrg($org_id)->active()
			->where('display_area', 'login')
			->orderBy('created_at', 'desc')
			->first();

		return view('auth.' . config('bookdose.theme_login') . '.register', compact('userGroup', 'user_info_template', 'banner', 'consent', 'org_slug'));
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	public function register(Request $request, $org_slug='')
	{
        $org_slug = $request->org_slug ?? '';

        if (!is_blank($org_slug)) {
            $throw_msg = NULL;
            $page_org = UserOrg::where('slug', $org_slug)->first();
            if (empty($page_org)) {
                $throw_msg = 'auth.register.org_not_found';
            }
            else if (!empty($page_org->expiration_date) && Carbon::parse($page_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.register.org_expired';
            }
            else if ($page_org->status != '1') {
                $throw_msg = 'auth.register.org_inactive';
            }

            if (!empty($throw_msg)) {
                return redirect()->back()
                    ->withInput($request->all())
                    ->withErrors(['error' => trans($throw_msg)]);
            }
            else {
                $request->merge(['user_org_id' => $page_org->id]);
            }
        }
        else {
            $request->merge(['user_org_id' => config('bookdose.default.user_org')]);
        }

		$validator = $this->validator($request->all());

		if ($validator->fails()) {
			return redirect()->back()
				->withInput($request->all())
				->withErrors(['error' => $validator->errors()->all()]);
		}

        if ($this->isUserLimit($request->user_org_id) !== FALSE) {
			$errors = ['email' => __('auth.verify.limit_user')];
			return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['error' => __('auth.register.limit_user')]);
        }

        // ----------------> Generate User Member & username
        $user_count = User::ofOrg( $request->user_org_id )->count() + 1;
        $setting_member_rn = userOrgSetting::ofOrg( $request->user_org_id )->where('slug', 'member_running')->first();
        $last_running = ( (!is_null($setting_member_rn)) ? ($setting_member_rn->data_value['last_running'] ?? $user_count) : $user_count );

        $member_id = $this->getMemberIdFormat($last_running, $setting_member_rn);
        $username = $page_org->slug.$member_id; // Format:: {slug}{member_id}
        if ($this->isDuplicate($request->user_org_id, ['member_id' => $member_id])) {
            // ---------------------- loop on duplicate & not maximum round
            $count = 1;
            $isDuplicate = true;
            $memeber_list = [];
            do {
                $count++;
                $last_running++;
                $member_id = $this->getMemberIdFormat($last_running, $setting_member_rn);
                $memeber_list[] = $last_running.' -> '.$member_id;

                $isDuplicate = $this->isDuplicate($request->user_org_id, ['member_id' => $member_id]);
            } while ( $isDuplicate && $count <= config('bookdose.default.member.max_running') );

            // Erro when out of round generate member_id
            if ($isDuplicate) {
                return redirect()->back()->withInput($request->all())->withErrors('error', 'Process timeout. Please try again.');
            }
            // $user['member_id'] = $member_id;

            // Check Duplicate Of generate username by member_id, uniqueid
            $username = $page_org->slug.$member_id; // Format:: {slug}{member_id}
            if ($this->isDuplicate($request->user_org_id, ['username' => $username])) {
                $username = $page_org->slug.uniqid(); // Format:: {slug}{uniqueid}
                if ($this->isDuplicate($request->user_org_id, ['username' => $username])) {
                    return redirect()->back()->withInput($request->all())->withErrors('error', 'Process timeout. Please try again.');
                }
            }
            // $user['username'] = $username;
        }
        // end -- if isDuplicate(..., ...)
        $request->merge(['member_id' => $member_id]);
        $request->merge(['username' => $username]);

        if (!is_null($setting_member_rn)) {
            $member_setting = $setting_member_rn->data_value ?? [];
            $member_setting['last_running'] = $last_running;
            userOrgSetting::ofOrg( $request->user_org_id )->where('slug', 'member_running')->update(['data_value' => $member_setting]);
        }
        // ----------------> Generate User Member & username

		if ( $this->isDuplicate($request->user_org_id, ['email' => $request->email]) ) {
			return redirect()->back()
				->withInput($request->all())
				->withErrors(['error' => __('auth.register.duplicate.email') ]);
		}

		event(new Registered($user = $this->create($request->all())));

		$setting_regis = userOrgSetting::ofOrg($request->user_org_id)->where('slug', 'regis')->first();
        $setting_regis = $setting_regis ?? (object)['data_value'=>[]] ;
        // if (!config('bookdose.regis.verify') && !config('bookdose.regis.verify_by_admin')) {
        if (($setting_regis->data_value['regis_verify']??'0') != '1' && ($setting_regis->data_value['regis_verify_by_admin']??'0') != '1') {
            $this->guard()->login($user);
        }

		if ($response = $this->registered($request, $user)) {
			return $response;
		}

		// ###### new return 02/03/2022 ########
		$return_msg = "";
		// if(config('bookdose.regis.verify')){
		if(($setting_regis->data_value['regis_verify'] ?? '0') == '1'){
			// $return_msg = 'Already registered, Please activate account in mailbox.';
			$return_msg = __('auth.register.success').'<br>'.__('auth.register.verify');
		}
		// if(config('bookdose.regis.verify_by_admin')){
		if(($setting_regis->data_value['regis_verify_by_admin'] ?? '0') == '1'){
			// $return_msg = 'Already registered, Please wait for an administrator to activate your account.';
			$return_msg = __('auth.register.success').'<br>'.__('auth.register.verify_by_admin');
		}

        if ( ($setting_regis->data_value['regis_verify'] ?? '0') == '1' && ($setting_regis->data_value['regis_verify_by_admin'] ?? '0') == '1' ) {
			$return_msg = __('auth.register.success').'<br>'.__('auth.register.verify_account');
        }

        $this->updateTotalUser( $request->user_org_id );

		return $request->wantsJson()
			? new JsonResponse([], 201)
			: redirect()->route('login', $request->org_slug)->with('success', $return_msg)->with('register', 'success');
	}

	private function genMemberId()
	{
		$memberId = User::count() + 1;
		return str_pad((string)$memberId, 6, "0", STR_PAD_LEFT);
	}

	public function verify(Request $request)
	{
        $org_slug = $request->org_slug ?? '';
        $email = $request->email ?? '';
        $token = $request->token ?? '';

        if (!is_blank($org_slug)) {
            $throw_msg = NULL;
            $page_org = UserOrg::where('slug', $org_slug)->first();
            if (empty($page_org)) {
                $throw_msg = 'auth.register.org_not_found';
            }
            else if (!empty($page_org->expiration_date) && Carbon::parse($page_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
                $throw_msg = 'auth.register.org_expired';
            }
            else if ($page_org->status != '1') {
                $throw_msg = 'auth.register.org_inactive';
            }

            if (!empty($throw_msg)) {
                return redirect()->back()
                    ->withInput($request->all())
                    ->withErrors(['error' => trans($throw_msg)]);
            }
            else {
                $request->merge(['user_org_id' => $page_org->id]);
            }
        }
        else {
            $request->merge(['user_org_id' => config('bookdose.default.user_org')]);
        }

		$user = User::ofOrg($request->user_org_id)->with('org')->where('email', $email)->first();
		if (empty($user)) {
			$errors = ['email' => trans('auth.verify.email')];
			return redirect()->route('login', $user->org->slug)
				->withErrors($errors);
		}

		if ($token !== md5($user->created_at)) {
			$errors = ['email' => trans('auth.verify.token')];
			return redirect()->route('login', $user->org->slug)
				->withErrors($errors);
		}

        if ($this->isUserLimit($user->user_org_id) !== FALSE) {
			$errors = ['email' => __('auth.verify.limit_user')];
			return redirect()->route('login', $user->org->slug)
				->withErrors($errors);
        }

		if (empty($user->email_verified_at)) {
			$user["email_verified_at"] = Carbon::now();
		}
		$user["email_verified_state"] = 0;
		$user->save();

		return redirect()->route('login', $user->org->slug)
			->withSuccess(trans('auth.verify.success'));
	}

	private function getBrowser() {
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}elseif(preg_match('/Firefox/i',$u_agent)){
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}elseif(preg_match('/OPR/i',$u_agent)){
			$bname = 'Opera';
			$ub = "Opera";
		}elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			$bname = 'Apple Safari';
			$ub = "Safari";
		}elseif(preg_match('/Netscape/i',$u_agent)){
			$bname = 'Netscape';
			$ub = "Netscape";
		}elseif(preg_match('/Edge/i',$u_agent)){
			$bname = 'Edge';
			$ub = "Edge";
		}elseif(preg_match('/Trident/i',$u_agent)){
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}else {
				$version= $matches['version'][1];
			}
		}else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}

    public function updateTotalUser($user_org_id) {
        $user_org = UserOrg::ofOrg( $user_org_id )->first();

        if (!is_null($user_org)) {
            $data_summary_of_org = $user_org->data_summary ?? [];
            $data_summary_of_org['total_user'] = User::active()->notExpired()->ofOrg( $user_org_id )->where('is_tester', '0')->count();
        }
		UserOrg::ofOrg( $user_org_id )->update(['data_summary' => $data_summary_of_org]);
    }

	public function getMemberIdFormat($member_id, $format_setting = null) {
		// Set Member-Running Setting
		// $last_running = $member_id;
		$word_start = config('bookdose.default.member.word_start');
		$word_end = config('bookdose.default.member.word_end');
		$pad_length = config('bookdose.default.member.pad_length');
		$pad_string = config('bookdose.default.member.pad_string');
		$pad_type = config('bookdose.default.member.pad_type');
		if (!is_null($format_setting)) {
			$format = $format_setting->data_value ?? [];

			// $last_running = $format['last_running'] ?? $member_id;
			$word_start = $format['word_start'] ?? '';
			$word_end = $format['word_end'] ?? '';
			$pad_length = $format['pad_length'] ?? 0;
			$pad_string = $format['pad_string'] ?? '';
			$pad_type = $format['pad_type'] ?? '';
		}
		$pad_option = ['left' => STR_PAD_LEFT, 'right' => STR_PAD_RIGHT];

		return $word_start.str_pad($member_id, $pad_length, $pad_string, $pad_option[$pad_type]).$word_end;
	}

    private function isUserLimit($user_org_id) {

        $user_org = UserOrg::ofOrg( $user_org_id )->first();

        $org_summary = $user_org->data_summary ?? [];
        $org_limit_user = $user_org->user_limit ?? null;
        $org_total_user = $org_summary['total_user'] ?? null;

        return ((((is_number($org_limit_user) && is_number($org_total_user)) && $org_total_user >= $org_limit_user)) ? ['total' => $org_total_user, 'limit' => $org_limit_user] : FALSE );
    }

    private function isDuplicate($user_org_id, $data = []) {
        $user_id = Arr::pull($data, 'id') ?? '';

        $query = User::where(function($q) use ($user_org_id, $data) {

                    // check duplicate [member_id, email] of user in organize
                    $data_filter = arr::only($data, ['member_id', 'email']);
                    if (!is_blank($data_filter)) {
                        $q->where(function($q) use ($user_org_id, $data_filter) {
                            $q->ofOrg($user_org_id)
                            ->where(function($q) use ($data_filter) {
                                $first = true;
                                foreach ($data_filter as $key => $value) {
                                    $q->{($first?'where':'orWhere')}($key, $value);
                                    $first=false;
                                }
                            });
                        });
                    }

                    // check duplicate [username] of all user in system
                    $username = arr::pull($data, 'username') ?? '';
                    if (!is_blank($username)) {
                        $q->orWhere('username', $username);
                    }
                });

        if (!is_blank($user_id)) {
            $query->where('id', '!=', $user_id);
        }
        // dd(str::replaceArray('?', $query->getBindings(), $query->toSql()));

        return  $query->exists();
    }
}
