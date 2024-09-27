<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserOrg;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends ApiController
{
	public function index()
	{
		$users = User::all();
		return response()->json($users->toArray());
	}

	public function show($id)
	{
		$user = User::findOrFail($id);
		return response()->json($user->toArray());
	}

	public function me()
	{
		return parent::getAuthenticatedUser();
		// echo 'meeeee';
		// exit;
	}

	public function getMyProfile()
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

 		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';

 		if (!empty($return_data['msg'])) {
 			return response()->json($return_data);
 		}

 		// 2. Query

		// //get user_info_template
		// $userOrg = UserOrg::where('id', $payload->user_org_id)->first();
		// $user_info_template = $userOrg->user_info_template ?? [];
		// // dd($user_info_template);

 		//get user_info_template
		$userOrg = UserOrg::where('id', $payload->user_org_id)->first();
		$user_info_template = $userOrg->user_info_template ?? [];
		// dd($user_info_template);

 		$user = User::active()
            ->with('org')
		 	->select('id', 'member_id', 'username', 'email', 'name', DB::raw('DATE_FORMAT(birthday, "%d/%m/%Y") AS birthday'), 'avatar_path', 'gender', 'contact_number', 'position', 'user_org_id', 'data_info')
 			->where('id', $payload->id)
 			->first();

		$user_group = UserGroup::where('id', $payload->user_group_id)->first();

        // Results Return
        $user_data = [
            'id'                => $user->id ?? '',
            'member_id'         => $user->member_id ?? '',
            'username'          => $user->username ?? '',
            'email'             => $user->email ?? '',
            'name'              => $user->name ?? '',
            'birthday'          => $user->birthday ?? '',
            'avatar_path'       => $user->avatar_path ?? '',
            'gender'            => $user->gender ?? '',
            'contact_number'    => $user->contact_number ?? '',
            'position'          => $user->position ?? '',
            'user_group_id'     => $user_group->id ?? '',
            'user_group_name'   => $user_group->name ?? '',
            // Organize
            'org_id'            => $user->user_org_id ?? '',
            'org_name_th'       => $user->org->name_th ?? '',
            'org_name_en'       => $user->org->name_en ?? '',
            'org_slug'          => $user->org->slug ?? '',
        ];

		if (empty($user->avatar_path)) {
			if (empty(config('bookdose.default_image.avatar'))) {
				// $user->avatar_path = url('images/default_avatar.png');
                $user_data['avatar_path'] = url('images/default_avatar.png');
            }
			else {
				// $user->avatar_path = url(config('bookdose.default_image.avatar'));
                $user_data['avatar_path'] = url(config('bookdose.default_image.avatar'));
            }
		}
		else {
			// $user->avatar_path = url(Storage::url($user->avatar_path));
            $user_data['avatar_path'] = (Storage::disk('s3')->exists($user->avatar_path)) ? Storage::disk('s3')->url($user->avatar_path) : url(Storage::url($user->avatar_path));
		}

		// $user->username = is_null($user->username) ? '' : $user->username;
		// $user->member_id = is_null($user->member_id) ? '' : $user->member_id;
		// $user->email = is_null($user->email) ? '' : $user->email;
		// $user->contact_number = is_null($user->contact_number) ? '' : $user->contact_number;
		// $user->position = is_null($user->position) ? '' : $user->position;
		// $user->user_group_id = is_null($user_group->id) ? '' : $user_group->id;
		// $user->user_group_name = is_null($user_group->name) ? '' : $user_group->name;

		//data info from user_info_template (tbl: user_org)
		if(!empty($user_info_template)){
			foreach ($user_info_template as $k => $template) {
				// $user->{$template['key']} = $user->data_info[$template['key']] ?? '';
                $user_data[$template['key']] = $user->data_info[$template['key']] ?? '';
			}
		}else{
			// $user->department = $user->data_info['department'] ?? '';
            $user_data['department'] = $user->data_info['department'] ?? '';
		}
		unset($user->data_info);

		if ($user) {
			return response()->json( [
				'status' => 'success',
				'result' => $user_data ?? (object)[],
			]);
		}

 		return response()->json( [
			'status' => 'error',
			'msg' => 'User not found.',
		]);
	}

	public function updateMyProfile(Request $request)
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

 		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';
 		elseif (empty(request()->lang))
 			$return_data['msg'] = 'Missing lang';
 		elseif (empty(request()->email))
 			$return_data['msg'] = 'Missing email';
 		elseif (empty(request()->name) && !in_array(config('bookdose.app.folder'), ['acl']))
 			$return_data['msg'] = 'Missing name';

        if (!empty(request()->gender) && !in_array(request()->gender, ['f', 'm'])) {
            $return_data['msg'] = 'Please enter gender only f:Female|m:Male';
        }

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        if (!empty(request()->birthday)) {
            try {
                Carbon::createFromFormat('d/m/Y', $request->birthday);
            } catch (InvalidFormatException $exception) {
                $return_data['msg'] = 'Invalid date format. please enter (dd/mm/yyyy)';
                return response()->json($return_data);
            }
        }

 		// 2. Query
 		$lang = !in_array(request()->lang, ['en', 'th']) ? 'th' : request()->lang;
		if (User::myOrg()->where('email', $request->email)->where('id', '!=', $payload->id)->exists()) {
			return response()->json( [
				'status' => 'error',
				'msg' => __('user.duplicate_email', [], $lang),
			]);
		}

		// copy from MyProfileController elibrary site
		$user = User::find($payload->id);

		//data general
		$name = $request->name ?? null;
		if (!empty($request->firstName) && !empty($request->lastName)) {
			$name = $request->firstName . " " . $request->lastName;
		}
		if (!empty($request->f_name) && !empty($request->l_name)) {
			$name = $request->f_name . " " . $request->l_name;
		}
		$displayName = $request->display_name ?? $name;
		$gender = $request->gender ?? null;
		$birthday = (isset($request->birthday)) ? Carbon::createFromFormat('d/m/Y', $request->birthday) : null;
		$contactNumber = $request->contact_number ?? null;
		$position = $request->position ?? null;

		//data info from user_info_template (tbl: user_org)
		$user_info_template = $user->org->user_info_template ?? [];
		$dataInfo  = $user->data_info ?? [];
		if(!empty($user_info_template)){
			$arr_rules = [];
			foreach ($user_info_template as $k => $template) {
				$rules = [];
				if ($template['is_required'] == '1') $rules[] = 'required';
				else $rules[] = 'nullable';
				if ($template['input_type'] == 'text') $rules[] = 'max:255';
				$arr_rules[$template['key']] = implode('|', $rules);
			}
			// $dataInfo = $request->validate($arr_rules);

			// This set all input follow on user_info_template
			foreach ($user_info_template as $k => $template) {
				if(!empty($request->{$template['key']})) {
					$dataInfo[$template['key']] = $request->{$template['key']} ?? '';
				}
			}
			// This set all input follow show on view
			// if(!empty($validatedData)){
			// 	$dataInfo = $validatedData;
			// }
		} else {
			$dataInfo['department'] = $request->department ?? '';
			$dataInfo['position'] = $request->position ?? '';
			$dataInfo['branch'] = $request->branch ?? ''; // AIS this problem do not have data user_info_template
			$dataInfo['contact_number_work'] = $request->contact_number_work ?? ''; // AIS this problem do not have data user_info_template
			$dataInfo['age_range'] = $request->rangeAge ?? ''; // OKMD this problem do not have data user_info_template
			$dataInfo['address'] = $request->address ?? '';
			$dataInfo['other_address'] = $request->other_address ?? ''; // AIS this problem do not have data user_info_template
			$dataInfo['line'] = $request->line ?? '';
			$dataInfo['id_card'] = $request->id_card ?? '';
		}
		// print_r($dataInfo);
		// exit;

		$data = [
			'email' => $request->email,
			'name' => $name,
			'display_name' => $displayName,
			'gender' => $gender,
			'birthday' => $birthday,
			'data_info' => $dataInfo,
			'contact_number' => $contactNumber,
			'position' => $position,
		];

 		if (!empty($request->avatar)) {
            if (Storage::disk('s3')->exists($user->avatar_path)) {
                Storage::disk('s3')->delete($user->avatar_path);
            }
            if (Storage::exists($user->avatar_path)) {
                Storage::delete($user->avatar_path);
            }

            //  $path = $avatar->store( $upload_dir );
            //  if ($path) {
            //     try {
            //         // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
            //         $path = Storage::disk('s3')->putFileAs( pathinfo($path)['dirname'],  getcwd().Storage::url($path), basename($path), ['ACL' => 'public-read',]);
            //         Storage::delete($path);
            //     } catch (\Exception $e) {
            //         // return "File upload failed: " . $e->getMessage();
            //     }
            // }

 			$image = $request->avatar;  // base64 encoded
			$image = str_replace('data:image/gif;base64,', '', $image);
			$image = str_replace('data:image/jpeg;base64,', '', $image);
			$image = str_replace('data:image/png;base64,', '', $image);
			$image = str_replace(' ', '+', $image);
			$file_path = Str::random(16).'.png';

            $upload_dir = join('/', [config('bookdose.app.store_prefix'), 'avatars', $user->user_org_id]);
			$avatar_path = $upload_dir. '/' . $file_path;
			Storage::put($avatar_path, base64_decode($image));

            try {
                // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
                $avatar_path = Storage::disk('s3')->putFileAs( pathinfo($avatar_path)['dirname'],  getcwd().Storage::url($avatar_path), basename($avatar_path), ['ACL' => 'public-read',]);
                Storage::delete($avatar_path);
            } catch (\Exception $e) {
                // return "File upload failed: " . $e->getMessage();
            }
			$data['avatar_path'] = $avatar_path;
 		}
		// print_r($data);
		// exit;
 		User::where('id', $payload->id)->update($data);

		//get user_info_template
		$userOrg = UserOrg::where('id', $payload->user_org_id)->first();
		$user_info_template = $userOrg->user_info_template ?? [];
		// print_r($user_info_template);
		// exit;
 		$user = User::active()->notExpired()
            ->with('org')
 			->select('id', 'member_id', 'email', 'name', 'avatar_path', 'gender', 'contact_number', 'position', 'user_group_id', 'user_org_id', 'data_info')
 			->where('id', $payload->id)
 			->first();

		$user_group = UserGroup::where('id', $user->user_group_id)->first();

        // Results Return
        $user_data = [
            'id'                => $user->id ?? '',
            'member_id'         => $user->member_id ?? '',
            'username'          => $user->username ?? '',
            'email'             => $user->email ?? '',
            'name'              => $user->name ?? '',
            'avatar_path'       => $user->avatar_path ?? '',
            'gender'            => $user->gender ?? '',
            'contact_number'    => $user->contact_number ?? '',
            'position'          => $user->position ?? '',
            'user_group_id'     => $user_group->id ?? '',
            'user_group_name'   => $user_group->name ?? '',
            // Organize
            'org_id'            => $user->user_org_id ?? '',
            'org_name_th'       => $user->org->name_th ?? '',
            'org_name_en'       => $user->org->name_en ?? '',
            'org_slug'          => $user->org->slug ?? '',
        ];

		if (empty($user->avatar_path)) {
			if (empty(config('bookdose.default_image.avatar'))){
				// $user->avatar_path = url('images/default_avatar.png');
                $user_data['avatar_path'] = url('images/default_avatar.png');
            }
			else{
				// $user->avatar_path = url(config('bookdose.default_image.avatar'));
                $user_data['avatar_path'] = url(config('bookdose.default_image.avatar'));
            }
		}
		else {
			// $user->avatar_path = url(Storage::url($user->avatar_path));
            $user_data['avatar_path'] = ( Storage::disk('s3')->exists($user->avatar_path) ? Storage::disk('s3')->url($user->avatar_path) : url(Storage::url($user->avatar_path)) );
		}
		// $user->member_id = is_null($user->member_id) ? '' : $user->member_id;
		// $user->email = is_null($user->email) ? '' : $user->email;
		// $user->contact_number = is_null($user->contact_number) ? '' : $user->contact_number;
		if(!empty($user_info_template)){
			foreach ($user_info_template as $k => $template) {
				// $user->{$template['key']} = $user->data_info[$template['key']] ?? '';
                $user_data[$template['key']] = $user->data_info[$template['key']] ?? '';
			}
		}else{
			// $user->department = $user->data_info['department'] ?? '';
            $user_data['department'] = $user->data_info['department'] ?? '';
		}
		unset($user->data_info);

 		if ($user) {
 			return response()->json( [
				'status' => 'success',
				'msg' => __('user.profile_update_success', [], $lang),
				'result' => $user_data ?? (object)[],
			]);
 		}
	}

	public function getMyPoints()
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

 		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';

 		if (!empty($return_data['msg'])) {
 			return response()->json($return_data);
 		}

 		// 2. Query
 		$user = User::active()->where('id', $payload->id)->first();
 		if ($user) {
 			return response()->json( [
				'status' => 'success',
				'points' => $user->points ?? 0,
			]);
 		}
 		return response()->json( [
			'status' => 'error',
			'msg' => 'User not found.',
		]);
	}

	public function updateAvatar()
	{
        $err = ['status' => 'error', 'msg' => 'Something wrong.', 'result' => (object)[]];
		$token = request()->token ?? '';
		$token_api = request()->token_api ?? '';
		$avatar = request()->avatar ?? '';
		$username = request()->username ?? '';

		if (empty($token) && empty($token_api)) {
			$err['msg'] = 'Missing token.';
			return response()->json($err, 500);
		}
        // if (!empty($token) && !($payload = parent::parseJWT())) {
        //     $err['msg'] = 'Invalid token';
		// 	return response()->json($err, 500);
        // }
		if (empty($avatar)) {
			$err['msg'] = 'Missing avatar';
			return response()->json($err, 500);
		}

		if (!empty($token))
            $user = User::where('temp_token', $token)->first();
		else
			$user = User::where('id', $token_api->id)->first();

		if ($user) {
            if (Storage::disk('s3')->exists($user->avatar_path)) {
                Storage::disk('s3')->delete($user->avatar_path);
            }
            if (Storage::exists($user->avatar_path)) {
                Storage::delete($user->avatar_path);
            }

            $upload_dir = join('/', [config('bookdose.app.store_prefix'), 'avatars', $user->user_org_id]);
			$path = $avatar->store( $upload_dir );
            if ($path) {
                try {
                    // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
                    $path = Storage::disk('s3')->putFileAs( pathinfo($path)['dirname'],  getcwd().Storage::url($path), basename($path), ['ACL' => 'public-read',]);
                    Storage::delete($path);
                } catch (\Exception $e) {
                    // return "File upload failed: " . $e->getMessage();
                }

                $q = User::where('id', $user->id)->update(['avatar_path' => $path]);
            }

			if ($q) {
				return response()->json([
					'status' => 'success',
					'results' => 'Updated avater successfully.',
				]);
			} else {
				return response()->json([
					'status' => 'error',
					'results' => 'Oops! Something went wrong, please refresh this page and then try again.',
				]);
			}
		}

		return response()->json([
				'status' => 'error',
				'results' => 'User not found.',
			]);
	}

    public function getMyOrganize() {
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '', 'results' => (object)[]);

 		if (empty(request()->token)) {
 			$return_data['msg'] = 'Missing token';
        }
 		else if (! $payload = parent::parseJWT()) {
 			$return_data['msg'] = 'Invalid token';
        }
        $lang = !in_array((request()->lang ?? 'th'), ['en', 'th']) ? 'th' : (request()->lang ?? 'th');

        $user_org = UserOrg::MyOrg()->active()->notExpired()->select('id', 'slug', 'is_bd', 'name_'.$lang, 'logo_path', 'data_contact')->first();

        if (is_null($user_org)) {
            $return_data['msg'] = 'organize not found';
            return response()->json($return_data);
        }
		return response()->json([
				'status' => 'success',
                'msg' => '',
				'results' => (object)['user_org' => $user_org->toArray()],
			]);
    }
}
