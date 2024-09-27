<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\SiteInfo;
use App\Http\Controllers\Api\ApiController;
use App\Models\Policys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrivacyController extends ApiController
{
	public function index()
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

 		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';
 		elseif (empty(request()->lang))
 			$return_data['msg'] = 'Missing lang';

 		if (!empty($return_data['msg'])) {
 			return response()->json($return_data);
 		}

 		// 2. Query
 		$lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
		$item = SiteInfo::myOrg()->lang($lang)->where('meta_key', 'privacy-policy')->select('meta_label', 'meta_value')->first();
		if ($item) {
			return response()->json( [
				'status' => 'success',
				'result' => [
					'label' => $item->meta_label,
					'value' => preg_replace("/\r\n|\r|\n/", '<br/>', $item->meta_value),
				],
			]);
		}
		else {
			return response()->json( [
				'status' => 'error',
				'msg' => 'Privacy policy not found.',
				'result' => (object)[],
			]);
		}
	}

}
