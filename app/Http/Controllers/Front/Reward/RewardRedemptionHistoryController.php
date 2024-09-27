<?php

namespace App\Http\Controllers\Front\Reward;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Front\FrontController;
use App\Models\RewardRedemptionHistory;
use App\Models\RewardItem;
use App\Models\User;

class RewardRedemptionHistoryController extends FrontController
{
    public function index(Request $request)
    {
        $breadcrumbs = [];
        $breadcrumbs['Reward'] = route('reward.index');
        $breadcrumbs['My Coin History'] = route('reward.redemption.index');

        $limit = 12;
        $content_type = substr(trim($request->content_type), 1);
        $page = trim($request->page);

        $contents = RewardRedemptionHistory::latest('redeemed_at')
            ->with('rewardItem', function ($query) {
                $query->with('rewardGalleries', function ($query) {
                    $query->active();
                });
            })
            ->where('user_id', Auth::user()->id)
            ->paginate($limit)
            // set hash on url paging number
            ->fragment($content_type);

        if ($request->ajax()) {
            return response()->json([
                'status' => 200,
                'html' => view(
                    'front.' . config('bookdose.theme_front') . '.modules.reward.box_redemption',
                    compact('contents')
                )
                    ->render(),
            ]);
        }
        return view('front.' . config('bookdose.theme_front') . '.modules.reward.reward_history', compact('contents'));
    }

    public function show()
    {
        return view('front.' . config('bookdose.theme_front') . '.modules.reward.redemption_history');
    }

    public function ajaxRedeem(Request $request)
    {
        // Check User Point 
        $user = User::where('status', 1)
            ->where('id', Auth::user()->id)
            ->first();
        $user_point = $user->points;


        // Check max_per_user & stock_avail & point 
        $rewards_detail = RewardItem::active()
            ->where('id', $request->id)
            ->first();
        $max_per_user = $rewards_detail->max_per_user;
        $item_stock = $rewards_detail->stock_avail;
        $item_point = $rewards_detail->point;


        //Check SUM history item
        $history = RewardRedemptionHistory::where('user_id', Auth::user()->id)
            ->where('reward_item_id', $request->id)
            ->get()
            ->sum('unit');
        $redeem_qty = $history;

        // Condition Out of stock
        if ($item_stock == 0) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'ขออภัย ของรางวัลหมด'
            ]);
        }
        // Condition User point less than Item point
        elseif ($user_point < $item_point) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'จำนวน Point ของท่านไม่เพียงพอในการแลกของรางวัลชิ้นนี้'
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
                ->where('id', Auth::user()->id)
                ->update(['points' => $remain_point]);
            //update reward stock
            $reward_stock = DB::table('reward_items')
                ->where('id', $request->id)
                ->update(['stock_avail' => $remain_stock]);
            //insert history
            DB::table('reward_redemption_histories')->insert([
                ['user_id' => Auth::user()->id, 'reward_item_id' => $request->id, 'unit' => 1, 'unit_point' => $item_point, 'total_point' => $total_point, 'is_delivered' => 0, 'redeemed_at' => now()]
            ]);

            return response()->json([
                'statusCode' => 200,
                'remainStock' => $remain_stock,
                'remainPoint' => $remain_point
            ]);
        }
        // Condition User redeem limit
        elseif ($redeem_qty >= $max_per_user) {
            $msg_limit =  'คุณแลกถึงขีดจำกัด ' . $max_per_user . ' รายการต่อผู้ใช้แล้ว';
            return response()->json([
                'statusCode' => 404,
                'message' => $msg_limit
            ]);
        }
        // Insert redeem history & Update user point & Update reward stock
        else {
            $remain_point = $user_point - $item_point;
            $remain_stock = $item_stock - 1;
            $total_point = $item_point * 1;

            //update user point
            $user_point = DB::table('users')
                ->where('id', Auth::user()->id)
                ->update(['points' => $remain_point]);
            //update reward stock
            $reward_stock = DB::table('reward_items')
                ->where('id', $request->id)
                ->update(['stock_avail' => $remain_stock]);
            //insert history
            DB::table('reward_redemption_histories')->insert([
                ['user_id' => Auth::user()->id, 'reward_item_id' => $request->id, 'unit' => 1, 'unit_point' => $item_point, 'total_point' => $total_point, 'is_delivered' => 0, 'redeemed_at' => now()]
            ]);

            return response()->json([
                'statusCode' => 200,
                'remainStock' => $remain_stock,
                'remainPoint' => $remain_point
            ]);
        }
    }
}
