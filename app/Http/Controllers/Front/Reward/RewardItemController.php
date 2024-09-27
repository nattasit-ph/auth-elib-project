<?php

namespace App\Http\Controllers\Front\Reward;

use Illuminate\Support\Facades\Auth;
use App\Models\RewardCategory;
use App\Models\RewardItem;
use App\Models\RewardItemGallery;
use App\Models\UserOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Front\FrontController;
use App\Models\RewardRedemptionHistory;

class RewardItemController extends FrontController
{
    public function index(Request $request) {

        // Get Active rewardCategory
        $categories = RewardCategory::active()->get();

        $category_id = $request->id;

        $now_dt = now();
        $now_dt->format('Y-m-d');

        if(!empty($request->id)) {
        $rewards = RewardItem::active()
            ->where('reward_category_id', $request->id)
            ->where('stock_avail', '<>', 0)
            ->where(function($started_at) {
                $started_at->whereDate('started_at','<=' ,today())
                ->orWhere('started_at' , null);
            })
            ->where( function ($expired_at) use($now_dt) {
                $expired_at->whereDate('expired_at','>=' ,today())
                ->orWhere('expired_at' , null);
            })
            ->with('rewardGalleries', function($query) {
                $query->active();
            })
            ->paginate(6);
        }else{
        $rewards = RewardItem::active()
            ->where('stock_avail', '<>', 0)
            ->Started()
            ->notExpired()
            ->with('rewardGalleries', function($query) {
                $query->active();
            })
            ->paginate(6);
        }

        // LoadMoreData
        if($request->ajax()){
            $success = 200;
            $view = view('front.'.config('bookdose.theme_front').'.modules.reward.load_reward_item', compact('rewards','success'))->render();
            return response()->json([
                'status' => 200,
                'html'=>$view
            ]);
        }

        // site attr
        $breadcrumbs = [
	        __('menu.front.rewards_store') => "",
		];
        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();


        //echo $request->id;
    	return view('front.'.config('bookdose.theme_front').'.modules.reward.main', compact('categories', 'rewards','category_id', 'breadcrumbs', 'footer'));
    }

    public function show(request $request) {

        $history = RewardRedemptionHistory::IsDelivered()
            ->where('user_id', Auth::user()->id)
            ->where('reward_item_id', $request->id)
            ->get()
            ->sum('unit');
        $redeem_qty = $history;

        $detail_items = RewardItem::active()
        ->where('reward_items.id', $request->id)
        ->first();

        $detail_pic = RewardItemGallery::where('reward_item_id', $request->id)
        ->get();

        // site attr
        $breadcrumbs = [
	        __('menu.front.rewards_store') => url('reward'),
            $detail_items->title => "",
		];
        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();

    	return view('front.'.config('bookdose.theme_front').'.modules.reward.reward_detail.detail', compact('detail_items','detail_pic','redeem_qty', 'breadcrumbs' ,'footer'));
    }

}
