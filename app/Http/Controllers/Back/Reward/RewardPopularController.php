<?php

namespace App\Http\Controllers\Back\Reward;

use DB;
use Auth;
use App\Models\RewardItem;
use App\Models\RewardCategory;
use App\Http\Controllers\Back\BackController;
use App\Models\RewardRedemptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Reward\ReportRewardPopularExport;

class RewardPopularController extends BackController
{
   public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
			parent::getSiteConfig();

        	$this->user = Auth::user();
    		if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
    			return $next($request);
 			}
 			else {
 				return redirect()->route('home');
 			}
		});
 	}

	public function index()
	{
        $org_slug = Auth::user()->org->slug;

		return view('back.'.config('bookdose.theme_back').'.modules.reward.report.reward_popular_list', compact('org_slug'));
	}



	public function ajaxGetData(Request $request)
	{

        $query = RewardRedemptionHistory::select(
            DB::raw('count(reward_redemption_histories.reward_item_id) as redempt_qty, reward_items.title as reward_name')
        )
        ->leftJoin('reward_items', 'reward_items.id', 'reward_redemption_histories.reward_item_id')
        ->where('reward_redemption_histories.is_delivered', 1)
        ->where('reward_redemption_histories.is_refunded', 0);


        $period = $request->input('period');
		switch ($period) {
			case 'today':
				$query = $query->whereDate('reward_redemption_histories.redeemed_at', '=', date("Y-m-d", strtotime('today')));
				break;

			case 'yesterday':
				$query = $query->whereDate('reward_redemption_histories.redeemed_at', '=', date("Y-m-d", strtotime('-1 days')));
				break;

			case 'last7Days':
				$query = $query->whereDate('reward_redemption_histories.redeemed_at', '>', date("Y-m-d", strtotime('-7 days')));
				break;

			case 'thisMonth':
				$query = $query->whereMonth('reward_redemption_histories.redeemed_at', '=', date("m", strtotime('this month')));
				break;

			case 'lastMonth':
				$query = $query->whereMonth('reward_redemption_histories.redeemed_at', '=', date("m", strtotime('last month')));
				break;

			case 'customPeriod':
				if (!empty($request->period_start)) {
					$date = date_create_from_format("d/m/Y", $request->period_start);
					$query = $query->whereDate('reward_redemption_histories.redeemed_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($request->period_end)) {
					$date = date_create_from_format("d/m/Y", $request->period_end);
					$query = $query->whereDate('reward_redemption_histories.redeemed_at', '<=', date_format($date, "Y-m-d"));
				}
				break;

			default:
				break;
		}
        $query = $query->groupBy('reward_name')
        ->orderBy('redempt_qty', 'DESC')
        ->get();

        $data = array();
        foreach ($query as $key => $value) {
            array_push($data, (object) array(
                'id_row' => $key + 1,
                'reward_name' => $value->reward_name,
                'redempt_qty' => $value->redempt_qty,
            ));
        }

        return Datatables::of($data)->make(true);
 	}

  public function exportToExcel(Request $request)
  {
		$period = $request->input('hd_period');
		$custom_period_start = $request->input('hd_custom_period_start');
		$custom_period_end = $request->input('hd_custom_period_end');
		$keyword = $request->input('hd_keyword');
		  // dd($period, $custom_period_start, $custom_period_end, $keyword);
		return Excel::download(new ReportRewardPopularExport($period, $custom_period_start, $custom_period_end, $keyword), 'report_popular_reward_'.now().'.xlsx');
  }

}
