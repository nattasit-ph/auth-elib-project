<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\RewardCategory;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardCategoryController extends ApiController
{
	public function index()
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
		$items = RewardCategory::active()->select('id', 'title')->get();
		return response()->json( [
			'status' => 'success',
			'results' => $items ?? [],
		]);
	}
	
}
