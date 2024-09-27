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

class RewardRedemptionController extends ApiController
{
	public function redeem(Request $request)
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

 		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';
 		elseif (empty(request()->lang))
 			$return_data['msg'] = 'Missing lang';
 		elseif (empty(request()->reward_id))
 			$return_data['msg'] = 'Missing reward_id';

 		if (!empty($return_data['msg'])) {
 			return response()->json($return_data);
 		}

 		// 2. Query
 		$lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
 		$user = User::active()->where('id', $payload->id)->first();
 		$rewards_detail = RewardItem::active()
            ->where('id', request()->reward_id)
            ->first();

 		if ($user && $rewards_detail) {
 			$user_point = $user->points;
        	$max_per_user = $rewards_detail->max_per_user;
        	$item_stock = $rewards_detail->stock_avail;
        	$item_point = $rewards_detail->point;

         $history = RewardRedemptionHistory::where('user_id', $payload->id)
            ->where('reward_item_id', request()->reward_id)
            ->get()
            ->sum('unit');
        	$redeem_qty = $history;

        // Condition Out of stock
        if ($item_stock == 0) {
            return response()->json([
            	'status' => 'error',
            	'msg' => __('reward.detail.out_of_stock', [], $lang),
            ]);
        }
        // Condition User point less than Item point
        elseif ($user_point < $item_point) {
            return response()->json([
            	'status' => 'error',
            	'msg' => __('reward.detail.not_enough_points', [], $lang),
            ]);
        }
        // Condition Unlimited Redeem
        elseif ($item_stock > 0 && $user_point >= $item_point && $max_per_user == null) {
            // Insert redeem history & Update user point & Update reward stock
            $remain_point = $user_point - $item_point;
            $remain_stock = $item_stock - 1;
            $total_point = $item_point * 1;

            //update user point
            $user_point = DB::table('users')
                ->where('id', $payload->id)
                ->update(['points' => $remain_point]);
            //update reward stock
            $reward_stock = DB::table('reward_items')
                ->where('id', request()->reward_id)
                ->update(['stock_avail' => $remain_stock]);
            //insert history
            DB::table('reward_redemption_histories')->insert([
                [
                	'user_id' => $payload->id,
                	'reward_item_id' => request()->reward_id,
                	'unit' => 1,
                	'unit_point' => $item_point,
                	'total_point' => $total_point,
                	'is_delivered' => 0,
                	'redeemed_at' => now()
                ]
            ]);

            return response()->json([
            	'status' => 'success',
            	'msg' => __('reward.detail.redeem_success', [], $lang),
            ]);
        }
        // Condition User redeem limit
        elseif ($redeem_qty >= $max_per_user) {
            return response()->json([
            	'status' => 'error',
            	'msg' => __('reward.detail.quota_exceeded', [], $lang),
            ]);
        }
        // Insert redeem history & Update user point & Update reward stock
        else {
            $remain_point = $user_point - $item_point;
            $remain_stock = $item_stock - 1;
            $total_point = $item_point * 1;

            //update user point
            $user_point = DB::table('users')
                ->where('id', $payload->id)
                ->update(['points' => $remain_point]);
            //update reward stock
            $reward_stock = DB::table('reward_items')
                ->where('id', request()->reward_id)
                ->update(['stock_avail' => $remain_stock]);
            //insert history
            DB::table('reward_redemption_histories')->insert([
                [
                	'user_id' => $payload->id,
                	'reward_item_id' => request()->reward_id,
                	'unit' => 1,
                	'unit_point' => $item_point,
                	'total_point' => $total_point,
                	'is_delivered' => 0,
                	'redeemed_at' => now()
                ]
            ]);

            return response()->json([
            	'status' => 'success',
            	'msg' => __('reward.detail.redeem_success', [], $lang),
            ]);
        }
 		}
	}

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

 		$items = RewardRedemptionHistory::with('rewardItem')->latest()
 				->select('reward_item_id', 'total_point', 'created_at')
 				->where('user_id', $payload->id)
	 			->skip(($page-1) * $limit)->take($limit)
				->get()
				->each(function($item, $i) {
					$item->title = $item->rewardItem->title;
					$item->point = $item->total_point;
					$item->created_date = Carbon::parse($item->created_at)->format('d/m/Y');
					unset($item->rewardItem);
					unset($item->reward_item_id);
					unset($item->total_point);
					unset($item->created_at);
				});

		return response()->json( [
			'status' => 'success',
			'label' => [
				'date' => __('reward.history.date', [], $lang),
				'points' => __('reward.history.points', [], $lang),
				'reward_item' => __('reward.history.reward_item', [], $lang),
			],
			'results' => $items ?? [],
		]);

 	}

}
