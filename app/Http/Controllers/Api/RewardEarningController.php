<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\RewardItem;
use App\Models\User;
use App\Models\RewardRedemptionHistory;
use App\Models\RewardEarningHistory;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardEarningController extends ApiController
{
	public function getHistory(Request $request)
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
 		$page = request()->page ?? 1;
 		$limit = 20;

 		$items = RewardEarningHistory::with('rewardActivity')->latest()
 				->select('reward_activity_id', 'point', 'created_at')
 				->where('user_id', $payload->id)
	 			->skip(($page-1) * $limit)->take($limit)
				->get()
				->each(function($item, $i) {
					$item->title = $item->rewardActivity->title;
					$item->created_date = Carbon::parse($item->created_at)->format('d/m/Y');
					unset($item->rewardActivity);
					unset($item->reward_activity_id);
					unset($item->created_at);
				});

		return response()->json( [
			'status' => 'success',
			'label' => [
				'date' => __('reward.history.date', [], $lang),
				'points' => __('reward.history.points', [], $lang),
				'activity' => __('reward.history.activity', [], $lang),
			],
			'results' => $items ?? [],
		]);
 	}

}
