<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Models\Banner;
use App\Models\UserOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserOrgController extends ApiController
{
	public function index()
	{
		$response_data = array('status' => 'error', 'msg' => '', 'result' => (object)[]);

		$user_orgs = UserOrg::select('id', 'slug', 'name_th', 'name_en', 'logo_path')->get();

        if (!is_null($user_orgs)) {
            $response_data['status'] = 'success';
            $response_data['result'] = (object)['user_orgs' => $user_orgs->toArray()];
        }
        else {
            $response_data['msg'] = 'data not found.';
        }
		return response()->json($response_data);
	}

    public function detail() {
		$response_data = array('status' => 'error', 'msg' => '', 'result' => (object)[]);
        $filter_user_org = request()->user_org_id ?? '';
        if (is_blank($filter_user_org)) {
            $response_data['msg'] = 'user_org_id not found.';
        }

		$user_org = UserOrg::select('id', 'slug', 'name_th', 'name_en', 'logo_path', 'is_bd', 'status', 'working_time', 'working_day', 'data_info', 'data_contact', 'user_info_template', 'registry_at', 'accessible_at', 'plan_id', 'user_limit', 'storage_limit')->find($filter_user_org);
        if (!is_null($user_org)) {
            $user_org->logo_path = (!is_blank($user_org->logo_path ?? '')) ? (Storage::disk('s3')->exists($user_org->logo_path) ? Storage::disk('s3')->url($user_org->logo_path) : url(Storage::url($user_org->logo_path))) : NULL;
            $response_data['status'] = 'success';
            $response_data['result'] = (object)['user_orgs' => $user_org->toArray()];
        }
        else {
            $response_data['msg'] = 'data not found.';
        }
        return response()->json($response_data);
    }

    public function upload_logo(Request $request) {
		// 1. Pre-check parameters
		$response_data = array('status' => 'error', 'msg' => '', 'results' => (object)[]);

 		if (empty($request->token)) {
 			$response_data['msg'] = 'Missing token';
             return response()->json($response_data, 500);
        }
 		else if (! $payload = parent::parseJWT()) {
 			$response_data['msg'] = 'Invalid token';
             return response()->json($response_data, 500);
        }
        else if ( !(isBookdose() && isAdminOrHigher()) ) {
			$response_data['msg'] = 'Access denined.';
			return response()->json($response_data, 405);
        }
        else if (empty($request->user_org_id) && empty($request->ori_org_id)) {
			$response_data['msg'] = 'Missing user_org.';
			return response()->json($response_data, 500);
        }
        else if (empty($request->file_upload)) {
			$response_data['msg'] = 'Missing logo.';
			return response()->json($response_data, 500);
        }

        $user_org_id = $request->user_org_id ?? '';
        $ori_org_id = $request->ori_org_id ?? '';
        $org_slug = $request->org_slug ?? '';
        $lang = !in_array($request->lang, ['en', 'th']) ? 'th' : $request->lang;

        $data_con = [];
        if (!is_blank($user_org_id)) {
            $data_con = ['id' => $user_org_id];
        }
        else if (!is_blank($ori_org_id)) {
            $data_con = ['ori_org_id' => $ori_org_id];
        }
        if (!is_blank($org_slug)) {
            $data_con['slug'] = $org_slug;
        }
        $user_org = UserOrg::where($data_con)->first();
        if (!$user_org) {
            $response_data['msg'] = 'data not found.';
            return response()->json($response_data);
        }

		$upload_dir = join('/', [config('bookdose.app.store_prefix'), 'logos', $user_org->id]);
        $path = $request->file_upload->store( $upload_dir );
        if ($path) {
            try {
                // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
                $path = Storage::disk('s3')->putFileAs( pathinfo($path)['dirname'], getcwd().Storage::url($path), basename($path), ['ACL' => 'public-read',]);
            } catch (\Exception $e) {
                // return "File upload failed: " . $e->getMessage();
                $response_data['msg'] = 'Upload S3 failed.';
                return response()->json($response_data);
            }
            Storage::delete($path);
        }
        else {
            return response()->json( [
                'status' => 'error',
                'msg' => 'Upload failed.',
            ]);
        }

        $res = UserOrg::where('id', $user_org->id)->update(['logo_path' => $path]);

        if ($res) {
            if ($user_org->logo_path) {
                if (Storage::disk('s3')->exists($user_org->logo_path)) {
                    Storage::disk('s3')->delete($user_org->logo_path);
                }
                if (Storage::exists($user_org->logo_path)) {
                    Storage::delete($user_org->logo_path);
                }
            }
            return response()->json( [
                'status' => 'success',
                'msg' => __('user.profile_update_success', [], $lang),
            ]);
        }
        else {
            if ($path) {
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
            return response()->json( [
                'status' => 'error',
                'msg' => 'Upload failed.',
            ]);
        }
    }

    public function upload_banner(Request $request) {
		// 1. Pre-check parameters
		$response_data = array('status' => 'error', 'msg' => '', 'results' => (object)[]);

 		if (empty($request->token)) {
 			$response_data['msg'] = 'Missing token';
             return response()->json($response_data, 500);
        }
 		else if (! $payload = parent::parseJWT()) {
 			$response_data['msg'] = 'Invalid token';
             return response()->json($response_data, 500);
        }
        else if ( !(isBookdose() && isAdminOrHigher()) ) {
			$response_data['msg'] = 'Access denined.';
			return response()->json($response_data, 405);
        }
        else if (empty($request->user_org_id) && empty($request->ori_org_id)) {
			$response_data['msg'] = 'Missing user_org.';
			return response()->json($response_data, 500);
        }
        else if (empty($request->title)) {
			$response_data['msg'] = 'Missing title.';
			return response()->json($response_data, 500);
        }
        else if (empty($request->file_upload)) {
			$response_data['msg'] = 'Missing file.';
			return response()->json($response_data, 500);
        }

        $user_org_id = $request->user_org_id ?? '';
        $ori_org_id = $request->ori_org_id ?? '';
        $org_slug = $request->org_slug ?? '';

        $title = $request->title;
        $weight = $request->weight ?? 0;
        $status = $request->status ?? 1;
        $external_url = $request->external_url ?? '';

        $dup_field = $request->dup_field ?? '';
        $dup_option = $request->dup_option ?? 'skip';
        $lang = !in_array($request->lang, ['en', 'th']) ? 'th' : $request->lang;

        $data_con = [];
        if (!is_blank($user_org_id)) {
            $data_con = ['id' => $user_org_id];
        }
        else if (!is_blank($ori_org_id)) {
            $data_con = ['ori_org_id' => $ori_org_id];
        }
        if (!is_blank($org_slug)) {
            $data_con['slug'] = $org_slug;
        }
        $user_org = UserOrg::where($data_con)->first();
        if (!$user_org) {
            $response_data['msg'] = 'data not found.';
            return response()->json($response_data);
        }

        $banner_id = NULL;
        if (!is_blank($dup_field) && !is_blank($request->{$dup_field})) {
            $banner = Banner::ofOrg($user_org->id)->where($dup_field, $request->{$dup_field})->first();
            if ($banner) {
                if ($dup_option == 'skip') {
                    $response_data['msg'] = 'banner exists.';
                    return response()->json($response_data);
                }
            }
        }

		$upload_dir = join('/', [config('bookdose.app.store_prefix'), 'banners', $user_org->id]);
        $path = $request->file_upload->store( $upload_dir );
        if ($path) {
            try {
                // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
                $path = Storage::disk('s3')->putFileAs( pathinfo($path)['dirname'], getcwd().Storage::url($path), basename($path), ['ACL' => 'public-read',]);
            } catch (\Exception $e) {
                $response_data['msg'] = 'Upload S3 failed.';
                return response()->json($response_data);
            }
            $data_banner['file_path'] = $path;
            $data_banner['file_size'] = $request->file_upload->getSize();

            Storage::delete($path);
        }
        else {
            $response_data['msg'] = 'Upload Failed.';
            return response()->json($response_data);
        }

        $data_banner['title'] = $title;
        if (!is_blank($external_url)) {
            $data_banner['external_url'] = $external_url;
        }
        $data_banner['weight'] = $weight;
        $data_banner['status'] = $status;
        $data_banner['updated_by'] = Auth::user()->id;

        $res_banner = NULL;
        if ($dup_option == 'update' && $banner) {
            $res_banner = Banner::ofOrg($user_org->id)->where('id', $banner->id)->update($data_banner);
        }
        else {
            $data_banner['user_org_id'] = $user_org->id;
            $data_banner['system'] = 'belib';
            $data_banner['for_mobile'] = 0;
            $data_banner['for_web'] = 1;
            $data_banner['display_area'] = 'elibrary';
            $data_banner['text_color'] = '#384158';
            $data_banner['created_by'] = Auth::user()->id;

            $res_banner = Banner::create($data_banner);
        }
        // UserOrg::where('id', $user_org->id)->update(['logo_path' => $path]);
        if ($res_banner) {
            if ($banner) {
                if ($dup_option == 'update') {
                    if ($banner->file_path) {
                        if (Storage::disk('s3')->exists($banner->file_path)) {
                            Storage::disk('s3')->delete($banner->file_path);
                        }
                        if (Storage::exists($banner->file_path)) {
                            Storage::delete($banner->file_path);
                        }
                    }
                }
            }
            return response()->json( [
                'status' => 'success',
                'msg' => __('user.profile_update_success', [], $lang),
            ]);
        }
        else {
            if ($path) {
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
            return response()->json( [
                'status' => 'error',
                'msg' => 'Update Data Failed.',
            ]);
        }
    }
}
