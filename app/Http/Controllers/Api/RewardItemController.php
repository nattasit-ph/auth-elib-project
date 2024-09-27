<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\RewardItem;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardItemController extends ApiController
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
 		$reward_category_id = request()->reward_category_id ?? '';
 		$page = request()->page ?? 1;
 		$limit = 20;

		$query = RewardItem::active()->inStock()
			->with(['rewardGalleries' => function($qry) {
				$qry->where('is_cover', 1)->select('reward_item_id', 'file_path');
			}])
			->select('id', 'title', 'point');

		if (!empty($reward_category_id)) {
			$query = $query->where('reward_category_id', $reward_category_id);
		}

		$items = $query->skip(($page-1) * $limit)->take($limit)->get()
				->each(function($item, $i) {
					if ($item->rewardGalleries->count() > 0)
						$item->cover_image_path = url(Storage::url($item->rewardGalleries[0]->file_path));
					else
						$item->cover_image_path = url(config('bookdose.app.folder').'/'.config('bookdose.default_image.cover_reward_item'));

					unset($item->rewardGalleries);
				});
		return response()->json( [
			'status' => 'success',
			'msg' => ($items->count() > 0 ? '' : __('reward.no_item_found', [], $lang)),
			'label' => [
				'points' => __('reward.store.points', [], $lang),
				'btn_redeem_reward' => __('reward.store.redeem_reward', [], $lang),
			],
			'results' => $items ?? [],
		]);
	}
	
	public function detail(Request $request)
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
 		if (RewardItem::active()->where('id', request()->reward_id)->doesntExist()) {
			$return_data['msg'] = 'Invalid reward_id';
 			return response()->json($return_data);
 		}

 		$lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
		$item = RewardItem::active()
			->where('id', request()->reward_id)
			->with(['rewardGalleries' => function($qry) {
				$qry->select('reward_item_id', 'is_cover', 'file_path');
			}])
			->select('id', 'title', 'description', 'point', 'stock_avail', 'max_per_user', 'started_at', 'expired_at')
			->get()
			->each(function($item, $i) use ($lang) {
				if ($item->rewardGalleries->count() > 0) {
					foreach($item->rewardGalleries as $k=>$img) {
						$item->rewardGalleries[$k]['file_path'] = url(Storage::url($item->rewardGalleries[$k]['file_path']));
						unset($item->rewardGalleries[$k]['reward_item_id']);
					}
				}
				$item->max_per_user = is_null($item->max_per_user) ? __('reward.detail.unlimited', [], $lang) : $item->max_per_user;

				if(empty($item->started_at) && empty($item->expired_at))
            	$item->period = __('reward.detail.while_stocks_last', [], $lang);
            elseif(empty($item->started_at) && !empty($item->expired_at))
            	$item->period = __('reward.detail.present', [], $lang).' - '.date('d/m/Y', strtotime($item->expired_at));
            elseif(!empty($item->started_at) && empty($item->expired_at))
            	$item->period = __('reward.detail.while_stocks_last', [], $lang);
            else
            	$item->period = date('d/m/Y', strtotime($item->started_at)).' - '.date('d/m/Y', strtotime($item->expired_at));

				unset($item->started_at);
				unset($item->expired_at);
			});
		return response()->json( [
			'status' => 'success',
			'msg' => ($item->count() > 0 ? '' : __('reward.no_item_found', [], $lang)),
			'label' => [
				// 'points' => __('reward.store.points', [], $lang),
				// 'btn_redeem_reward' => __('reward.store.redeem_reward', [], $lang),
				'condition_of_redemption' => __('reward.detail.condition_of_redemption', [], $lang),
				'point_to_redeem' => __('reward.detail.point_to_redeem', [], $lang),
				'quota_to_redeem' => __('reward.detail.quota_to_redeem', [], $lang),
				'remaining_prized' => __('reward.detail.remaining_prized', [], $lang),
				'reward_redemption_period' => __('reward.detail.reward_redemption_period', [], $lang),
				'description' => __('reward.detail.description', [], $lang),
				'btn_redeem_reward' => __('reward.store.redeem_reward', [], $lang),
			],
			'results' => $item ?? [],
		]);
	}
	
}
