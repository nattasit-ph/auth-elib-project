<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use JWTAuth;
use Carbon\Carbon;
use App\Models\ModelHasRole;
use App\Models\User;
use App\Models\UserOrg;
use App\Models\Role;
use App\Models\UserGroup;
use App\Mail\UserVerify;
use App\Mail\UserWelcome;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Models\UserAd;
use App\Models\userOrgSetting;
use Illuminate\Http\Request;

class AuthController extends Controller
{

	/**
	 * Create a new AuthController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api', ['except' => ['request_login', 'login', 'getLoginSSO', 'request_login_social', 'login_social', 'logout', 'me', 'registerSubmit', 'login_hardware', 'loginLabour']]);
	}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getLoginSSO()
	{
		$postdata = file_get_contents("php://input");
		$input_data = json_decode($postdata, true);

		$status= $input_data['status'] ?? "";
		$my_result= $input_data['result'] ?? "";

		if($status === false) {
			$return_data = array('status' => 'error', 'msg' => 'Status error.', 'data_result' => '');
			return response()->json($return_data);
		}

		if($my_result === false) {
			$return_data = array('status' => 'error', 'msg' => 'Invalid user object.', 'data_result' => '');
			return response()->json($return_data);
		}

		if(is_array($my_result))
		{
			if (isset($my_result['aid']) && !empty($my_result['aid']))
			{
				$log = collect([ (object)[
						'module' => 'SSO Authentication',
						'severity' => 'Info',
						'title' => 'Found this user on Belib',
						'desc' => 'Belib user_aid = '.$my_result['aid'],
					]])->first();
				parent::Log($log);

				$elib_user_aid = $my_result['aid'];
				$result = User::where('username', $my_result['username'])->first();
				$active_result = User::where('username', $my_result['username'])->where('status', 1)->first();

				if ($result) {
					// If inactive status
					if (!$active_result) {
						$return_data = array('status' => 'error', 'msg' => 'Inactive user.', 'data_result' => '');
						return response()->json($return_data);
					}

					// Found, update
					$log = collect([ (object)[
							'module' => 'SSO Authentication',
							'severity' => 'Info',
							'title' => 'Found this user on E-learning',
							'desc' => 'Update info for user_id = '.$result->id,
						]])->first();
					parent::Log($log);

					User::where('username', $my_result['username'])
						->update(
							array(
								'name' => trim($my_result['first_name_th'].' '.$my_result['last_name_th']),
								'username' => $my_result['username'],
								'email' => $my_result['email'],
								'password' => bcrypt(''),
								'gender' => $my_result['gender'],
								'position' => $my_result['position'],
								'contact' => $my_result['contact_number'],
								// 'user_role_id' => $my_result['user_role_aid'],
								'elib_user_id' => $my_result['aid'],
								'status' => $my_result['status'],
								'last_login_at' => now(),
								'updated_at' => now(),
								'updated_by' => $result->id,
							)
						);
				}
				else {
					// Not found, insert
					//$default_user_role = Role::where('name', config('bookdose.login_social_default_role'))->first();
					User::insert(
						array(
							'name' => trim($my_result['first_name_th'].' '.$my_result['last_name_th']) ?? $my_result['email'],
							'username' => $my_result['username'],
							'email' => $my_result['email'],
							'password' => bcrypt(''),
							'gender' => $my_result['gender'],
							'position' => $my_result['position'],
							'contact' => $my_result['contact_number'],
							'user_role_id' => $my_result['user_role_aid'],
							'elib_user_id' => $my_result['aid'],
							'status' => $my_result['status'],
							'last_login_at' => now(),
							'created_at' => now(),
							'updated_at' => now(),
						)
					);
					$result = User::where('username', $my_result['username'])->first();
					User::where('username', $my_result['username'])
						->update(
							array(
								'created_by' => $result->id,
								'updated_by' => $result->id,
							)
						);
					$log = collect([ (object)[
							'module' => 'SSO Authentication',
							'severity' => 'Info',
							'title' => 'Not found this user on E-learning',
							'desc' => 'Insert new user, user_id = '.$result->id,
						]])->first();
					parent::Log($log);
				}
			}
			else
			{
				$return_data = array('status' => 'error', 'msg' => 'Invalid user object.', 'data_result' => '');
				return response()->json($return_data);
			}
		}
		else
		{
			$return_data = array('status' => 'error', 'msg' => 'Invalid user object.', 'data_result' => '');
			return response()->json($return_data);
		}

		// Insert login history
	 	$attempt = session('login_attempt', 0);
		$result->device = $my_result['device'];
		$result->device_id = $my_result['device_id'];
	 	parent::insertLoginHistory($result, 'authen-success', $attempt);

		$nounce = hash('sha256', now());
		$payloadable = array(
			'id' => $result->id,
			'firstname' => trim($my_result['first_name_th']),
			'lastname' => trim($my_result['last_name_th']),
			'fullname' => $result->name,
			'email' => $result->email,
			'username' => $my_result['username'],
			// 'password' => '',
			'telephone' => $result->contact,
			'image_url' => $result->avatar_path,
			'role_id' => $result->user_role_id,
			'nounce' => $nounce,
			'user_aid' => $my_result['aid'],
			'status' => $my_result['status'],
			'authen_status' => 1,
		);
		// $token = JWTAuth::encode( JWTAuth::factory()->make( $payloadable ) );
		$credentials = Arr::add([], 'email', $result->email);
		$credentials = Arr::add($credentials, 'password', '');
		if (! $token = JWTAuth::claims($payloadable)->attempt($credentials)) {
			$credentials = Arr::add([], 'username', $my_result['username']);
			$credentials = Arr::add($credentials, 'password', '');
			if (! $token = JWTAuth::claims($payloadable)->attempt($credentials)) {
				return response()->json(['error' => 'Unauthorized'], 401);
			}
		}
		return response()->json($token);
	}

    public function request_login() {
		$err = ['status' => 'error', 'msg' => '', 'result' => (object)[]];

		$device = request()->device ?? '';
		if (empty($device) || !in_array($device, ['android', 'ios'])) {
			$err['msg'] = 'Missing or invalid device.';
			return response()->json($err, 500);
		}
		$device_id = request()->device_id ?? '';
		if (empty($device_id)) {
			$err['msg'] = 'Missing parameter. Please specify device_id.';
			return response()->json($err, 200);
		}


		$fieldType = filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		request()->merge([$fieldType => request()->username]);


        $request_only = [$fieldType];
        // -----------------------------------------------------
		// Validate User
		$users = User::active()->notExpired()
				->with('org')
				->where( request()->only($request_only) )
				->whereHas('org', function ($q) {
					$q->active()->notExpired();
				})
				->get();
		// dd(str::replaceArray('?', $users->getBindings(), $users->toSql()));

		if (empty($users) || (!empty($users) && sizeof($users) <= 0)) {
			$err_msg = NULL;
			$err_msg = ( (!empty($user_org_id)) ? 'user_org_not_found' : 'user_not_found' );

			$user = User::where( request()->only($request_only) )->first();
			// $user_org = (empty($user_org_id)) ? $user->org ?? NULL : NULL;

			if (empty($user)) {
				$err_msg = ( (!empty($user_org_id)) ? 'user_org_not_found' : 'user_not_found' );
			}
			// else if (empty($user_org)) {
			// 	$err_msg = 'org_not_found';
			// }
			// // Organize
			// else if (!empty($user_org->expires_at) && Carbon::parse($user_org->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
			// 	$err_msg = 'org_expired';
			// }
			// else if ($user_org->status != '1') {
			// 	$err_msg = 'org_inactive';
			// }
			// User
			else if (!empty($user->expires_at) && Carbon::parse($user->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
				$err_msg = 'expired';
			}
			else if ($user->status != '1') {
				$err_msg = 'inactive';
			}

			$err['msg'] = trans('auth.login.'.$err_msg);
			return response()->json($err, 200);
		}
		else {
            $err['status'] = 'success';
            $user_orgs = UserOrg::active()->notExpired()
                        ->select('id', 'slug', 'name_th', 'name_en', 'logo_path', 'logo_path AS logo_login', 'logo_path AS logo_header', 'logo_path AS logo_setting')
                        ->whereHas('users', function ($q) use ($request_only) {
                            $q->where( request()->only($request_only) )->active()->notExpired();
                        })
                        ->get();

            $logo_path = asset(config('bookdose.app.project').'/images/logo/logo.png');
            foreach ($user_orgs as &$user_org) {
                if (!is_blank($user_org->logo_path ?? '')) {
                    if (Storage::disk('s3')->exists($user_org->logo_path ?? '')) {
                        $logo_path = Storage::disk('s3')->url($user_org->logo_path);
                    }
                    else {
                        $logo_path = url(Storage::url($user_org->logo_path));
                    }
                }
                $user_org->logo_path = $logo_path;
                $user_org->logo_login = $logo_path;
                $user_org->logo_header = $logo_path;
                $user_org->logo_setting = $logo_path;
            }

            if (sizeof($users) > 1 || sizeof($user_orgs) > 1) {
                if (!empty($user_org_id)) {
                    $err['msg'] = trans('auth.login.user_org_duplicate');
                    return response()->json($err, 200);
                }
                // error
                // $err['msg'] = trans('auth.login.duplicate');
                $err['msg'] = 'duplicate';
                $err['result'] = ['username' => request()->username, 'user_orgs' => $user_orgs->toArray()];
                return response()->json($err, 200);
            }
            else {
                $err['msg'] = '';
                $err['result'] = ['username' => request()->username, 'user_orgs' => $user_orgs->toArray()];
                return response()->json($err, 200);
            }
        }
    }

	public function login()
	{
		$err = ['status' => 'error', 'msg' => '', 'result' => (object)[]];

		$device = request()->device ?? '';
		if (empty($device) || !in_array($device, ['android', 'ios'])) {
			$err['msg'] = 'Missing or invalid device.';
			return response()->json($err, 500);
		}
		$device_id = request()->device_id ?? '';
		if (empty($device_id)) {
			$err['msg'] = 'Missing parameter. Please specify device_id.';
			return response()->json($err, 200);
		}

		$fieldType = filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		request()->merge([$fieldType => request()->username]);

        $user_org_id = request()->user_org_id ?? null;

		if (!empty($user_org_id)) {
			$err_msg = NULL;

			$request_org = UserOrg::where('id', $user_org_id)->first();
			if (empty($request_org)) {
				$err_msg = 'org_not_found';
			}
			else if (!empty($request_org->expiration_date) && Carbon::parse($request_org->expiration_date)->startOfDay() < Carbon::now()->startOfDay()) {
				$err_msg = 'org_expired';
			}
			else if ($request_org->status != '1') {
				$err_msg = 'org_inactive';
			}

			if (!empty($err_msg)) {
				$err['msg'] = trans('auth.login.'.$err_msg);
				return response()->json($err, 200);
			}
		}

        $request_only = [$fieldType];
        if (!empty($user_org_id)) {
            $request_only[] = 'user_org_id';
        }
		// -----------------------------------------------------
		// Validate User
		$users = User::active()->notExpired()
				->with('org')
				->where( request()->only($request_only) )
				->whereHas('org', function ($q) {
					$q->active()->notExpired();
				})
				->get();
		// dd(str::replaceArray('?', $users->getBindings(), $users->toSql()));

		if (empty($users) || (!empty($users) && sizeof($users) <= 0)) {
			$err_msg = NULL;
			$err_msg = ( (!empty($user_org_id)) ? 'user_org_not_found' : 'user_not_found' );

			$user = User::where( request()->only($request_only) )->first();
			$user_org = (empty($user_org_id)) ? $user->org ?? NULL : NULL;

			if (empty($user)) {
				$err_msg = ( (!empty($user_org_id)) ? 'user_org_not_found' : 'user_not_found' );
			}
			else if (empty($user_org)) {
				$err_msg = 'org_not_found';
			}
			// Organize
			else if (!empty($user_org->expires_at) && Carbon::parse($user_org->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
				$err_msg = 'org_expired';
			}
			else if ($user_org->status != '1') {
				$err_msg = 'org_inactive';
			}
			// User
			else if (!empty($user->expires_at) && Carbon::parse($user->expires_at)->startOfDay() < Carbon::now()->startOfDay()) {
				$err_msg = 'expired';
			}
			else if ($user->status != '1') {
				$err_msg = 'inactive';
			}

			$err['msg'] = trans('auth.login.'.$err_msg);
			return response()->json($err, 200);
		}
		else if (sizeof($users) > 1) {
			if (!empty($user_org_id)) {
				$err['msg'] = trans('auth.login.user_org_duplicate');
				return response()->json($err, 200);
			}

			$user_orgs = UserOrg::active()->notExpired()
						->select('id', 'slug', 'name_th', 'name_en')
						->whereHas('users', function ($q) use ($request_only) {
							$q->where( request()->only($request_only) )->active()->notExpired();
						})
						->get();
			// error
			// $err['msg'] = trans('auth.login.duplicate');
			$err['msg'] = 'duplicate';
			$err['result'] = ['user_orgs' => $user_orgs->toArray()];
			return response()->json($err, 200);
		}
		else if (empty($user_org_id)) {
			request()->merge(['user_org_id' => $users[0]->user_org_id]);
		}

		// $username = request('username');
		// $password = request('password');
		// $credentials = Arr::add([], 'username', $username);
		// $credentials = Arr::add($credentials, 'password', $password);

		Auth::logout();

		User::where( array_merge(request()->only($fieldType, 'user_org_id'), ['status' => 1]) )->update(['jwt' => NULL]);
		$credentials = array_merge(request()->only($fieldType, 'password', 'user_org_id'), ['status' => 1]);

		$token = JWTAuth::attempt($credentials);

		//OIC API LOGIN => when token invalid and login_oic_oauth2 == true need to check user on api oauth2
		if(!$token && config('bookdose.login_oic_oauth2') == true){
			$token = $this->OIC_ApiOauth2(request()->username, request()->password);
			if(isset($token['msg'])){
				return response()->json($token, 200);
			}
		}

		// // etech login => when token invalid and login_oic_oauth2 == true need to check user on api oauth2
		// else if(!$token && config('bookdose.login_etech') == true){
		//	$token = $this->loginEtech(request()->username, request()->password);
		//	if(isset($token['msg'])){
		//		return response()->json($token, 200);
		//	}
		// } else if(config('bookdose.app.folder') == 'acl') {
		//	$result = $this->loginACL(request()->username, request()->password);
		//	if(!empty(($result['msg'] ?? ""))) {
		//		// login acl unsuccess, password incorrect
		//		$return_data = array(
		//			'status' => 'error',
		//			'msg' => $result['msg'] ?? "",
		//			'msg_th' => $result['msg_th'] ?? "",
		//			'result' => "",
		//		);
		//		return $return_data;
		//	} else if(!empty(($result))) {
		//		// login acl success
		//		$token = $result;
		//	}
		// }

		if (!$token) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		// $_token = auth()->tokenById(auth()->user()->id);
		// JWTAuth::setToken($token)->refresh();
		$_user_data = Arr::add(JWTAuth::user(), 'jwt', $token);
		auth()->login(JWTAuth::user());

		// Adjust data
		$user_data = array();
		if (isset($_user_data['id'])) {
			$names = explode(" ", $_user_data['name']);
			$user_data['id'] = $_user_data['id'];
			$user_data['username'] = $_user_data['email'];
			$user_data['email'] = $_user_data['email'];
			$user_data['first_name'] = $names[0];
			$user_data['last_name'] = $names[1] ?? '';
			$user_data['avatar_path'] = getAvatarImage($_user_data['avatar_path'], true);
			$user_data['status'] = $_user_data['status'];
			// Organize
			$user_data['org_id'] = $_user_data['user_org_id'];
			$user_data['org_name_th'] = $_user_data['org']['name_th'];
			$user_data['org_name_en'] = $_user_data['org']['name_en'];
			$user_data['org_slug'] = $_user_data['org']['slug'];
			// $user_data['token'] = auth('api')->tokenById(auth()->user()->id);
			// $user_data['token'] = Str::random(32);
			$user_data['interested'] = is_array(json_decode($_user_data['data_interested'])) ? 1 : 0;
			$user_data['token'] = $_user_data['jwt'];
			// User::where('id', $_user_data['id'])->update(['jwt' => $user_data['jwt']]);
		}

		// Insert login history
		$attempt = session('login_attempt', 0);
		$_user_data['device'] = $device;
		$_user_data['device_id'] = $device_id;
		parent::insertLoginHistory($_user_data, 'authen-success', $attempt);

		$return_data = array(
				'status' => 'success',
				'msg' => '',
				'result' => $user_data,
		);

		return response()->json($return_data, 200);
		// return $this->respondWithToken($token);
	}

	private function OIC_ApiOauth2($username, $password)
	{
		$field_login = config('bookdose.oic_oauth2_field_login');

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
			$err['msg'] = 'The client is not authorized to request a token using this method.';
			return $err;
		}

		if(empty($token_attr['access_token']) || empty($token_attr['token_type'])){
			$err['msg'] = 'Token is empty, Please contact administrator.';
			return $err;
		}

		$login_header_authorization = $token_attr['token_type']. " ".$token_attr['access_token'];
		$login_body = array('username' => $username, 'password' => $password);
		// dd($login_body);
		$get_login = Http::withHeaders([
			'Authorization' => $login_header_authorization,
		])->post($login_url, $login_body);
		$login_attr = $get_login->json();

		if($login_attr['result'] == "FAIL"){
			$err['msg'] = 'These credentials do not match our records.';
			return $err;
		}

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
			$email = $username."@oic.or.th";

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
			$credentials = [$field_login => $user->username, 'password' => $default_password, 'status' => 1];
			$token = JWTAuth::attempt($credentials);
			return $token;
		}else{
			$err['msg'] = 'Error AD response status.';
			return $err;
		}
	}

	function loginEtech($user, $pass)
	{
		$username = $user;
		$password = bcrypt($pass);

		$fields = [];
		$fields['user'] = $user;
		$fields['pass'] = $pass;
		$response = Http::get(config('bookdose.login_etech_url').'/EtechAuthJ', $fields);
		$response = json_decode($response);

		if(isset($response->auth)) {
			if($response->auth == true){
				$fields = [];
				$fields['user'] = $user;
				$response = Http::get(config('bookdose.login_etech_url').'/EtechPer', $fields);
				$response = json_decode($response->body());

				$member_id = trim($response->id_Code) ?? '';
				$surname = trim($response->surname) ?? '';
				$fname = trim($response->fname) ?? '';
				$lname = trim($response->lname) ?? '';
				$name = $surname." ".$fname." ".$lname;
				$display_name = $fname." ".$lname;
				$email = trim($response->email) ?? '';
				$gender = trim(strtolower($response->sex)) ?? '';
				$avatar_path = trim($response->picts) ?? '';
				$section = trim($response->status) ?? '';
				$class = trim($response->id_class) ?? '';
				$per_type = trim($response->per_type) ?? '';
				switch($per_type) {
					case 1:
						$user_group_id = 1;
					break;
					default:
						$user_group_id = 2;
					break;
				}

				$user = User::where('username', $username)->first();

				if($user) {
					$update_data['username'] = $username;
					$update_data['password'] = $password;
					$update_data['name'] = $name;
					$update_data['display_name'] = $display_name;
					$update_data['email'] = $email;
					$update_data['gender'] = $gender;
					// $update_data['avatar_path'] = $avatar_path;
					$update_data['data_info'] = array(
						'section' => $section,
						'class' => $class
					);
					User::where('id', $user->id)->update($update_data);
				} else {
					$user_group_default = UserGroup::isDefault()->where('user_org_id', config('bookdose.default.user_org'))->first();
					$user_role_default = Role::defaultBelib()->first();
					$create_data['user_group_id'] = $user_group_id ?? $user_group_default;
					$create_data['user_role_id'] = $user_role_default;
					$create_data['user_org_id'] = config('bookdose.default.user_org');
					$create_data['user_role_id'] = $user_group_id;
					$create_data['member_id'] = $member_id;
					$create_data['username'] = $username;
					$create_data['password'] = $password;
					$create_data['name'] = $name;
					$create_data['display_name'] = $display_name;
					$create_data['email'] = $email;
					$create_data['gender'] = $gender;
					$create_data['avatar_path'] = $avatar_path;
					$create_data['data_info'] = array(
						'section' => $section,
						'class' => $class
					);
					$user = User::create($create_data);
				}

				$credentials = [
					'username' => $username,
					'password' => $password,
					'status' => 1
				];

				$chk_login = Auth::attempt($credentials);
				if($chk_login) {
					$token = JWTAuth::attempt($credentials);
					return $token;
				} else {
					$err['msg'] = 'The account has been disabled.';
					return $err;
				}
			}
		}
		$err['msg'] = 'Username or password is wrong.';
		return $err;
	}

	public function login_hardware()
	{
		$device = request()->device ?? '';
		$device_id = request()->device_id ?? '';
		$username = request()->username ?? '';
		$password = request()->password ?? '';

		if (empty($device) || !in_array($device, ['door', 'borrow', 'return'])) {
            $err['status'] = 'error';
			$err['msg'] = 'Missing or invalid device.';
			$err['msg_th'] = 'โปรดระบุอุปกรณ์ หรืออุปกรณ์ไม่ถูกต้อง';
            $err['result'] = '';
			return response()->json($err, 500);
		}

		if (empty($device_id)) {
            $err['status'] = 'error';
			$err['msg'] = 'Missing parameter. Please specify device_id.';
			$err['msg_th'] = 'โปรดระบุ device_id';
            $err['result'] = '';
			return response()->json($err, 200);
		}

		if (empty($username) || empty($password)) {
            $err['status'] = 'error';
			$err['msg'] = 'Missing parameter. Please specify username and password.';
			$err['msg_th'] = 'โปรดระบุ username & password';
            $err['result'] = '';
			return response()->json($err, 200);
		}


        Auth::logout();



		if (!filter_var(request()->username, FILTER_VALIDATE_EMAIL)) {
			User::where('username', request()->username)->update(['jwt' => NULL]);
			$credentials = ['username' => request()->username, 'password' => request()->password, 'status' => 1];
			if (! $token = JWTAuth::attempt($credentials)) {
				$err['status'] = 'error';
				$err['msg'] = 'Unauthorized';
				$err['msg_th'] = 'ไม่ได้รับอนุญาต';
				$err['result'] = '';
			return response()->json($err, 401);
			}
		}else{
			User::where('email', request()->username)->update(['jwt' => NULL]);
			$credentials = ['email' => request()->username, 'password' => request()->password, 'status' => 1];
			if (! $token = JWTAuth::attempt($credentials)) {
				$err['status'] = 'error';
				$err['msg'] = 'Unauthorized';
				$err['msg_th'] = 'ไม่ได้รับอนุญาต';
				$err['result'] = '';
			return response()->json($err, 401);
			}
		}

		$_user_data = Arr::add(JWTAuth::user(), 'jwt', $token);
		auth()->login(JWTAuth::user());

		// Adjust data
		$user_data = array();
		$user_data['token'] = $_user_data['jwt'];
		// if (isset($_user_data['id'])) {
		//		$names = explode(" ", $_user_data['name']);
		//		$user_data['id'] = $_user_data['id'];
		//		$user_data['username'] = $_user_data['email'];
		//		$user_data['email'] = $_user_data['email'];
		//		$user_data['first_name'] = $names[0];
		//		$user_data['last_name'] = $names[1] ?? '';
		//		$user_data['avatar_path'] = getAvatarImage($_user_data['avatar_path'], true);
		//		$user_data['status'] = $_user_data['status'];
		//		// $user_data['token'] = auth('api')->tokenById(auth()->user()->id);
		//		// $user_data['token'] = Str::random(32);
		//		$user_data['token'] = $_user_data['jwt'];
		//		// User::where('id', $_user_data['id'])->update(['jwt' => $user_data['jwt']]);
		// }

		// Insert login history
	 	$attempt = session('login_attempt', 0);
		$_user_data['device'] = $device;
		$_user_data['device_id'] = $device_id;
	 	parent::insertLoginHistory($_user_data, 'authen-success', $attempt);

		$return_data = array(
				'status' => 'success',
				'msg' => '',
				'msg_th' => '',
				'result' => $user_data,
		);

		return response()->json($return_data);
		// return $this->respondWithToken($token);
	}

	/**
	 * Get the authenticated User.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function me()
	{
		$return_array = array('status' => 'error', 'msg' => '', 'result' => (object)[],);
		if (empty(request()->token)) {
			$return_array['msg'] = 'Missing token';
		}

		$jwt = JWTAuth::getToken();
		// print_r(JWTAuth::parseToken()->authenticate()); exit;
		try {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				$return_array['msg'] = 'User not found';
			}
		} catch (\Exception $e) {
			$return_array['msg'] = 'Invalid token';
		}

		if (!empty($return_array['msg'])) {
			return response()->json($return_array);
		}

		// echo 'meeee'; exit;
		return parent::getAuthenticatedUser();
		// return response()->json(auth()->user());
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function _logout() {

		// $token = $request->header( 'Authorization' );

		try {
			JWTAuth::parseToken()->invalidate( JWTAuth::getToken() );

			return response()->json( [
				'error'	=> false,
				'message' => trans( 'auth.logged_out' )
			] );
		} catch ( TokenExpiredException $exception ) {
			return response()->json( [
				'error'	=> true,
				'message' => trans( 'auth.token.expired' )

			], 401 );
		} catch ( TokenInvalidException $exception ) {
			return response()->json( [
				'error'	=> true,
				'message' => trans( 'auth.token.invalid' )
			], 401 );

		} catch ( JWTException $exception ) {
			return response()->json( [
				'error'	=> true,
				'message' => trans( 'auth.token.missing' )
			], 500 );
		}
	}

	public function logout(Request $request)
	{
		$token = request()->token ?? '';
		if (empty($token)) {
			$err['msg'] = 'Missing parameter. Please specify token.';
			return response()->json($err, 200);
		}
		$jwt = request()->token ?? '';

		$payload = JWTAuth::parseToken()->getPayload();

		$device_token = request()->device_token ?? '';
		if (!empty($device_token)) {
			$device = new DeviceController();
			$result = $device->destroy($request);
			$return_data = json_decode($result->getContent());
			if ($return_data->status == 'error') {
				return response()->json($return_data);
			}
		}

		auth()->logout();

		// if (empty($token))
		//	$return_data['msg'] = 'Missing parameter token';

		// if (!empty($return_data['msg']))
		//	return response()->json($return_data);

		// 2. Begin query
		// User::where('token', $token)->update(['token' => null, 'player_id' => '']);
		User::where('id', $payload->get('id'))->update(['jwt' => null, 'token' => null, 'player_id' => '']);

		JWTAuth::invalidate(JWTAuth::getToken());

		return response()->json([
			'status' => 'success',
			'msg' => 'Successfully logged out',
			'msg_th' => '',
		]);
	}

	/**
	 * Refresh a token.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function refresh()
	{
		return $this->respondWithToken(auth()->refresh());
	}

	public function request_login_social()
	{
		$err = [
			'status' => 'error',
			'msg' => '',
			'msg_th' => '',
			'result' => '',
		];
		$device = request()->device ?? '';
		if (empty($device) || !in_array($device, ['android', 'ios'])) {
			$err['msg'] = 'Missing or invalid device.';
			return response()->json($err, 500);
		}
		$device_id = request()->device_id ?? '';
		if (empty($device_id)) {
			$err['msg'] = 'Missing parameter. Please specify device_id.';
			return response()->json($err, 200);
		}
		$email = request()->email ?? '';
		if (empty($email)) {
			$err['msg'] = 'Missing parameter. Please specify email.';
			return response()->json($err, 200);
		}

		$result = [];
		$result["token"] = Str::random(32);
		$result["email"] = $email;
		return response()->json([
				'status' => 'success',
				'msg' => '',
				'msg_th' => '',
				'result' => $result,
			], 200);
	}

	public function login_social()
	{
		$err = [
			'status' => 'error',
			'msg' => '',
			'msg_th' => '',
			'result' => '',
		];
		$token = request()->token ?? '';
		if (empty($token)) {
			$err['msg'] = 'Missing parameter. Please specify token.';
			return response()->json($err, 200);
		}
		$social_type = request()->social_type ?? '';
		if (empty($social_type) || !in_array($social_type, ['facebook', 'google', 'apple'])) {
			$err['msg'] = 'Missing or invalid social_type.';
			return response()->json($err, 500);
		}
		$device = request()->device ?? '';
		if (empty($device) || !in_array($device, ['android', 'ios'])) {
			$err['msg'] = 'Missing or invalid device.';
			return response()->json($err, 500);
		}
		$email = request()->email ?? '';
		if (empty($email)) {
			$err['msg'] = 'Missing parameter. Please specify email.';
			return response()->json($err, 200);
		}
		$device_id = request()->device_id ?? '';
		$id = request()->id ?? '';
		$name = request()->name ?? '';
		$first_name = request()->first_name ?? '';
		$last_name = request()->last_name ?? '';
		$gender = request()->gender ?? 'm';
		if ($gender == 'female') $gender = 'f';
		else $gender = 'm';
		$link = request()->link ?? '';
		$locale = request()->locale ?? '';
		$timezone = request()->timezone ?? '';
		$updated_time = request()->updated_time ?? '';
		$verified = request()->verified ?? '';
		$location = request()->location ?? '';
		$mobile_phone = request()->mobile_phone ?? '';
		$department = request()->department ?? '';

		if ($social_type == 'facebook') {
			$avatar_path = "https://graph.facebook.com/" . $id . "/picture";
			$avatar_original_path = "https://graph.facebook.com/" . $id . "/picture?type=large";
		}
		else if ($social_type == 'google') {
			$avatar_path = $link;
			$avatar_original_path = $link;
		}

		if (User::where('email', $email)->exists()) {
			// Found
			$is_first_login = false;
			$user = User::where('email', $email)->first();

			// Get $jwt by JWTAuth::attempt()
			$credentials = [];
			$credentials = Arr::add([], 'username', $email);
			$credentials = Arr::add($credentials, 'password', '');
			if (! $jwt = JWTAuth::attempt($credentials)) {
				$jwt = JWTAuth::fromUser($user, ['username' => $user->username]);
			}

			User::where('email', $email)->update([
				// 'name' => $name,
				'password' => bcrypt(''),
				// 'provider' => $social_type,
				// 'provider_user_id' => $id ?? '',
				'avatar_path' => $avatar_original_path ?? '',
				'jwt' => $jwt,
				'token' => $token,
				'last_login_at' => now(),
			]);
		}
		else {
			// Not found, new user
			$is_first_login = true;
			//$default_user_role = Role::where('name', config('bookdose.login_social_default_role'))->first();
			$user_group_default = UserGroup::isDefault()->where('user_org_id', config('bookdose.default.user_org'))->first();
			User::create([
				'name' => $name,
				'password' => bcrypt(''),
				'user_org_id' => config('bookdose.default.user_org'),
				'username' => $email,
				'email' => $email,
				'provider' => $social_type,
				'provider_user_id' => $id ?? '',
				'avatar_path' => $avatar_original_path ?? '',
				'token' => $token,
				'last_login_at' => now(),
				'user_group_id' => $user_group_default->id ?? 1,
			]);
			$user = User::where('email', $email)->first();
			$user['member_id'] = str_pad((string)$user->id, 6, "0", STR_PAD_LEFT);
			$user->save();

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

			// Get $jwt by JWTAuth::attempt()
			$credentials = [];
			$credentials = Arr::add([], 'username', $email);
			$credentials = Arr::add($credentials, 'password', '');
			if (! $jwt = JWTAuth::attempt($credentials)) {
				$jwt = JWTAuth::fromUser($user, ['username' => $user->username]);
			}
			//$user->syncRoles([config('bookdose.login_social_default_role')]);
		}

		$names = explode(" ", $user->name);
		$result = [];
		$result["aid"] = $user->id;
		$result["avatar"] = getAvatarImage($user->avatar_path, true);
		$result["first_name"] = $names[0];
		$result["last_name"] = $names[1] ?? '';
		$result["mobile_phone"] = $user->contact;
		$result["is_first_login"] = $is_first_login==true ? '1' : '0';
		$result["jwt"] = $jwt;
		$result["token"] = $token;
		return response()->json([
				'status' => 'success',
				'msg' => '',
				'msg_th' => '',
				'result' => $result,
			], 200);

	}

	public function registerSubmit()
	{
		$return_data = ['status' => 'error', 'msg' => '', 'result' => (object)[]];
		// 1. Pre-check parameters
		// if (empty(request()->bd_key))
		//	$return_data['msg'] = 'Missing bd_key';
		// elseif (request()->bd_key !== 'B00kd0se4993!')
		//	$return_data['msg'] = 'Invalid bd_key';
		// else
		// $demo = User::where( (!empty(request()->user_org_id)) ? request()->only(['email', 'user_org_id']) : request()->only(['email']) );
		// $demo = User::where( ((!empty(request()->user_org_id)) ? request()->only(['member_id', 'user_org_id']) : request()->only(['email'])) );
		// $demo = User::where( request()->only(['email', 'user_org_id']) );
		// dd(str::replaceArray('?', $demo->getBindings(), $demo->toSql()), ($demo->exists()?'TRUE':'FALSE'));

		request()->merge(['user_org_id' => (request()->user_org_id ?? config('bookdose.default.user_org'))]);

		if (empty(request()->email))
			$return_data['msg'] = 'Missing email';
		elseif (empty(request()->password))
			$return_data['msg'] = 'Missing password';
		elseif (empty(request()->first_name))
			$return_data['msg'] = 'Missing first_name';
		elseif (empty(request()->last_name))
			$return_data['msg'] = 'Missing last_name';
		elseif (User::where( request()->only(['email', 'user_org_id']) )->exists()) {
			$return_data['msg'] = __('auth.register.duplicate.email');
		}

		if (!empty($return_data['msg'])) {
			return response()->json($return_data);
		}
		// dd(request()->all());

		$user = new User();
		$user['user_org_id'] = request()->user_org_id ?? config('bookdose.default.user_org');

		//user group
		$user_group_default = UserGroup::isDefault()->where('user_org_id', $user['user_org_id'])->first();
		$user['user_group_id'] = $user_group_default->id ?? 1;


		// general data
		$user['name'] = trim(request()->first_name) . ' ' . trim(request()->last_name);
		if (!empty(request()->contact_number)) {
			$user['contact_number'] = request()->contact_number;
		}
		if (!empty(request()->gender)) {
			$user['gender'] = request()->gender;
		}
		if (!empty(request()->user_group)) {
			$user['user_group_id'] = request()->user_group;
		}

		//get user_info_template
		$user_org = UserOrg::where('id', $user['user_org_id'])->first();
		$user_info_template = $user_org->user_info_template ?? [];

		$setting_regis = userOrgSetting::ofOrg($user['user_org_id'])->where('slug', 'regis')->first() ?? (object)['data_value'=>[]];

		//data in data_info
		$data_info = [];

		//data info from user_info_template (tbl: user_org)
		if(!empty($user_info_template)){
			foreach ($user_info_template as $k => $template) {
				$data_info[$template['key']] = request()->{$template['key']} ?? '';
			}
		}else{
			if (!empty(request()->department)) {
				$data_info['department'] = request()->department;
			}
			if (!empty(request()->range_age)) {
				$data_info['range_age'] = request()->range_age;
			}
		}


		$user['data_info'] = $data_info;


		//email and password
		$user['email'] = request()->email;
		$user['password'] = Hash::make(request()->password);
		// if (config('bookdose.regis.verify')) {
		if (($setting_regis->data_value['regis_verify']??'0') == '1') {
			$user['email_verified_state'] = '1';
		}
		// if (config('bookdose.regis.verify_by_admin')) {
		if (($setting_regis->data_value['regis_verify_by_admin']??'0') == '1') {
			$user['admin_verified_state'] = '1';
		}

		// $demo = User::where( request()->only(['member_id', 'user_org_id']) );
		// dd(str::replaceArray('?', $demo->getBindings(), $demo->toSql(), 'isExists.: '.($demo->exists())?'true':'false'), (User::ofOrg($user['user_org_id'])->count()+1));
		//check member_id before save 1
		if(!empty(request()->member_id)){
			$data_member_id = request()->member_id;
			if( User::where( request()->only(['member_id', 'user_org_id']) )->exists() ){
				$return_data['msg'] = __('auth.register.duplicate.email');
				return response()->json( [
					'status' => 'error',
					'msg' => __('auth.register.duplicate.member_id'),
				]);
			}
		}

		$user['registry_at'] = Carbon::now();
		$user->save();

		//set member_id before save 2
		if (!empty(request()->member_id)) {
			$user['member_id'] = request()->member_id;
		} else {
			$user['member_id'] = str_pad((string)(User::ofOrg($user['user_org_id'])->count()), 6, "0", STR_PAD_LEFT);
		}
		//check duplicate member_id
		$member_id = $user['member_id'];
		if( User::ofOrg($user_org->id)->where('member_id', $member_id)->exists() ){
			$uniqid = uniqid();
			$user['member_id'] = $uniqid;
		}

		$user['username'] = request()->email;
		$user['created_by'] = $user->id;
		$user['updated_by'] = $user->id;
		$user->save();


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

		// if (config('bookdose.regis.verify')) {
		if (($setting_regis->data_value['regis_verify']??'0') == '1') {
			Mail::to($user->email)->send(new UserVerify($user));
		} else {
            $user->regis_verify = ($setting_regis->data_value['regis_verify']??'0');
            $user->regis_verify_by_admin = ($setting_regis->data_value['regis_verify_by_admin']??'0');
			Mail::to($user->email)->send(new UserWelcome($user));
		}

		return response()->json([
			'status' => 'success',
			'result' => $user,
			'msg' => 'กรุณาตรวจสอบ email ของท่านและทำการยืนยันตัวตนก่อนเข้าใช้งาน',
		]);
	}

	/**
	 * Get the token array structure.
	 *
	 * @param  string $token
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function respondWithToken($token)
	{
		return response()->json([
			'access_token' => $token,
			'token_type' => 'bearer',
			'expires_in' => JWTAuth::factory()->getTTL() * 60
		]);
	}

	function loginACL($user, $pass)
	{
		$username = $user;
		$password = $pass;

		$fields = [];
		$fields['ituserid'] = $username;
		$fields['itpasswd'] = $password;
		$bcrypt_password = bcrypt($password);

		try {
			$response = Http::asForm()->withoutVerifying()->post('https://wappmob.admincourt.go.th/cms/usr_loginQuery_json.php', $fields);
		} catch(\Illuminate\Http\Client\ConnectionException $e)
		{
			throw ValidationException::withMessages([
				'password' => ['Connect to login ACL server fail, please contact adminstrator.'],
			]);
		}

		$data = trim(preg_replace('/[-﻿]/', '', $response->body()));
		$data = json_decode($data, true);

		// print_r($fields);
		// print_r($data);
		// exit;
		$status = $data['STATUS'] ?? '';

		if($status == "success") {
			$member_id = trim($data['PER_SESSION'] ?? '');
			$type_id = trim($data['TYPE_ID'] ?? '');
			$t_name = trim($data['PN_NAME'] ?? '');
			$f_name = trim($data['PER_NAME'] ?? '');
			$l_name = trim($data['PER_SURNAME'] ?? '');
			$username = trim($data['LOG_NAME'] ?? '');
			$name = trim($data['NAME_TH'] ?? '');
			$display_name = $name;
			$email = trim($data['EMAIL'] ?? '');
			$gender = '';
			$avatar_path = '';
			$org_id = trim($data['ORG_ID'] ?? '');
			$org_name = trim($data['ORG_NAME'] ?? '');
			$org_id_1 = trim($data['ORG_ID_1'] ?? '');
			$org_name_1 = trim($data['ORG_NAME1'] ?? '');
			$org_id_2 = trim($data['ORG_ID_2'] ?? '');
			$org_name_2 = trim($data['ORG_NAME2'] ?? '');
			$pl_code = trim($data['PL_CODE'] ?? '');
			$pl_name = trim($data['PL_NAME'] ?? '');
			$pm_code = trim($data['PM_CODE'] ?? '');
			$pm_name = trim($data['PM_NAME'] ?? '');
			$pl_level = trim($data['PL_LEVEL'] ?? '');
			$level_no = trim($data['LEVEL_NO'] ?? '');
			$position_level = trim($data['POSITION_LEVEL'] ?? '');

			$user = User::where('username', $username)->orWhere('member_id', $member_id)->orWhere('email', $email)->first();

			if($user) {
				$data_info = $user->data_info;

				$update_data['member_id'] = $member_id;
				$update_data['username'] = $username;
				$update_data['password'] = $bcrypt_password;
				$update_data['name'] = $name;
				$update_data['display_name'] = $display_name;
				$update_data['email'] = $email;
				$update_data['provider'] = 'acl';
				$update_data['provider_user_id'] = $member_id;
				$update_data['position'] = $pl_level;

				$data_info['t_name'] = $t_name;
				$data_info['f_name'] = $f_name;
				$data_info['l_name'] = $l_name;
				$data_info['org_id'] = $org_id;
				$data_info['org_name'] = $org_name;
				$data_info['org_id_1'] = $org_id_1;
				$data_info['org_name_1'] = $org_name_1;
				$data_info['org_id_2'] = $org_id_2;
				$data_info['org_name_2'] = $org_name_2;
				$data_info['pl_code'] = $pl_code;
				$data_info['pl_name'] = $pl_name;
				$data_info['pm_code'] = $pm_code;
				$data_info['pm_name'] = $pm_name;
				$data_info['pl_level'] = $pl_level;
				$data_info['level_no'] = $level_no;
				$data_info['position_level'] = $position_level;

				$update_data['data_info'] = $data_info;

				User::where('id', $user->id)->update($update_data);
			} else {
				// TYPE_ID / PER_TYPE=1:ข้าราชการ,2:ลูกจ้างประจำ,3:พนักงานราชการ,4:ลูกจ้างชั่วคราว,5:ตุลาการ
				switch($type_id) {
					case 5:
						$user_group_id = 7; // ตุลาการศาลปกครองสูงสุด
						break;
					default:
						$user_group_id = 2;
						break;
				}

				$user_role_default = Role::defaultBelib()->first();
				$create_data['user_group_id'] = $user_group_id;
				$create_data['user_role_id'] = $user_role_default;
				$create_data['user_org_id'] = config('bookdose.default.user_org');
				$create_data['member_id'] = $member_id;
				$create_data['username'] = $username;
				$create_data['password'] = $bcrypt_password;
				$create_data['name'] = $name;
				$create_data['display_name'] = $display_name;
				$create_data['email'] = $email;
				$create_data['gender'] = $gender;
				$create_data['avatar_path'] = $avatar_path;
				$create_data['provider'] = 'acl';
				$create_data['provider_user_id'] = $member_id;
				$create_data['position'] = $pl_level;
				$create_data['data_info'] = array(
					't_name' => $t_name,
					'f_name' => $f_name,
					'l_name' => $l_name,
					'org_id' => $org_id,
					'org_name' => $org_name,
					'org_id_1' => $org_id_1,
					'org_name_1' => $org_name_1,
					'org_id_2' => $org_id_2,
					'org_name_2' => $org_name_2,
					'pl_code' => $pl_code,
					'pl_name' => $pl_name,
					'pm_code' => $pm_code,
					'pm_name' => $pm_name,
					'pl_level' => $pl_level,
					'level_no' => $level_no,
					'position_level' => $position_level
				);
				$user = User::create($create_data);
				$user = User::where('id', $user->id)->first();

				//belib system check
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

				//set default learnext member
				if(config('bookdose.app.learnext_url'))
				{
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
			}

			$credentials = [
				'username' => $username,
				'password' => $password,
				'status' => 1
			];

			$chk_login = Auth::attempt($credentials);
			if($chk_login) {
				$token = JWTAuth::attempt($credentials);
				return $token;
			} else {
				$err['msg'] = 'The account has been disabled.';
				return $err;
			}
		} else if($status == "password" && !filter_var($username, FILTER_VALIDATE_EMAIL))
		{
			$err['msg'] = 'Password is incorrect, please try again';
			$err['msg_th'] = 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
			return $err;
		} else if($status == "unssuccess" && !filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$err['msg'] = 'Username not found, please try again';
			$err['msg_th'] = 'ไม่พบชื่อผู้ใช้งาน กรุณาลองใหม่อีกครั้ง';
			return $err;
		} else if(!filter_var($username, FILTER_VALIDATE_EMAIL))
		{
			$err['msg'] = 'Username or password is incorrect';

			return $err;
		}

	}

	// public function loginLabour(Request $request)
	// {
	// 	//defind url
	// 	$auth_url = config('bookdose.app.url');
	// 	$belib_url = config('bookdose.app.belib_url');
	// 	$km_url = config('bookdose.app.km_url');

	// 	// 1. Pre-check parameters
	// 	$type = request()->type;
	// 	$device = request()->device;
	// 	$token = request()->token;
	// 	$user_info = json_decode(request()->user_info);
	// 	$word = request()->word;
	// 	// dd($type, $device, $token, $user_info);
	// 	$return_data = array('status' => 'error', 'msg' => '');
	// 	if (empty($type) || empty($device) || empty($token) || empty($user_info)) {
	// 		$return_data['msg'] = 'Missing parameter. Please specify parameter.';
	// 	}

	// 	if(empty($user_info->id)){
	// 		$return_data['msg'] = 'Missing id of user info. Please specify parameter.';
	// 	}

	// 	if (!empty($return_data['msg'])) {
	// 		return response()->json($return_data);
	// 	}

	// 	// 2. Store data
	// 		// - user data
	// 		// - assign role
	// 		// - add group
	// 		// - update temp_token
	// 	$default_pwd = uniqid();
	// 	$user = User::where('username', $user_info->id)->first();
	// 	if($user){
	// 		$update_data['name'] = $user_info->name;
	// 		$update_data['display_name'] = $user_info->name;
	// 		$update_data['avatar_path'] = $user_info->file_image;
	// 		$update_data['password'] = bcrypt($default_pwd);
	// 		User::where('id', $user->id)->update($update_data);
	// 	}else{
	// 		//user group
	// 		$user_group_default = UserGroup::where('name', 'พนักงาน')->where('user_org_id', config('bookdose.default.user_org'))->first();

	// 		//create user
	// 		$create_data['user_group_id'] = $user_group_default->id ?? 2;
	// 		$create_data['name'] = $user_info->name;
	// 		$create_data['display_name'] = $user_info->name;
	// 		$create_data['email'] = $user_info->email == "" ? $user_info->id."@labour.go.th" : $user_info->email;
	// 		$create_data['member_id'] = $user_info->id;
	// 		$create_data['username'] = $user_info->id;
	// 		$create_data['user_org_id'] = config('bookdose.default.user_org');
	// 		$create_data['status'] = 1;
	// 		$create_data['avatar_path'] = $user_info->file_image;
	// 		$create_data['password'] = bcrypt($default_pwd);
	// 		$user = User::create($create_data);

	// 		// Belib system check
	// 		if (!empty(config('bookdose.app.belib_url'))) {
	// 			$default_role = Role::defaultBelib()->first();
	// 			if (!empty($default_role)) {
	// 				$has_role = new ModelHasRole();
	// 				$has_role->role_id = $default_role->id;
	// 				$has_role->model_type = 'App\Models\User';
	// 				$has_role->model_id = $user->id;
	// 				$has_role->timestamps = false;
	// 				$has_role->save();
	// 			}
	// 		}

	// 		// KM system check
	// 		if (!empty(config('bookdose.app.km_url'))) {
	// 			$default_role = Role::defaultKm()->first();
	// 			if (!empty($default_role)) {
	// 				$has_role = new ModelHasRole();
	// 				$has_role->role_id = $default_role->id;
	// 				$has_role->model_type = 'App\Models\User';
	// 				$has_role->model_id = $user->id;
	// 				$has_role->timestamps = false;
	// 				$has_role->save();
	// 			}
	// 		}

	// 		// KM set division
	// 		// $division = trim(get_array_value($data_row,"division"));
	// 		// if (UserOrgUnit::where('title', $division)->doesntExist()) {
	// 		//	$user_org_unit = UserOrgUnit::create(['title' => $division, 'status' => 1]);
	// 		// }
	// 		// else {
	// 		//	$user_org_unit = UserOrgUnit::firstWhere('title', $division);
	// 		// }
	// 	}

	// 	//set token
	// 	if(in_array($device, ['ios', 'android'])){
	// 		// User::where('username', $user->username)->update(['jwt' => NULL]);
	// 		$credentials = ['username' => $user->username, 'password' => $default_pwd];
	// 		$token = JWTAuth::attempt($credentials);
	// 		$payload = $this->respondWithToken($token);
	// 		$payload = $payload->getOriginalContent();
	// 		$set_data['jwt'] = $token;
	// 		User::where('id', $user->id)->update($set_data);
	// 	}else{
	// 		$set_data['temp_token'] = $token;
	// 		User::where('id', $user->id)->update($set_data);
	// 	}

	// 	//set return_url
	// 	$return_url = $auth_url."/login/labour/callback?type=".$type."&device=".$device."&token=".$token."&word=".$word;

	// 	$results = [];
	// 	$results['url'] = $return_url;
	// 	$results['token'] = $token;
	// 	return response()->json([
	// 		'status' => 'success',
	// 		'results' => $results ?? (object)[],
	// 	]);
	// }
}
