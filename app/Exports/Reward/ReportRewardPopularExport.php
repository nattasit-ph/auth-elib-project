<?php
namespace App\Exports\Reward;

use DB;
use Auth;
use App;
use App\Models\RewardRedemptionHistory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportRewardPopularExport implements FromView, ShouldAutoSize, WithTitle
{
   public function __construct($period="", $custom_period_start="", $custom_period_end="", $keyword="")
	{
	  $this->period = $period;
	  $this->custom_period_start = $custom_period_start;
	  $this->custom_period_end = $custom_period_end;
	  $this->keyword = $keyword;

	}

	public function title(): string
 	{
 		return 'Report - Popular Rewards';
 	}

   public function view(): View
	{
        $period = $this->period;
        $period_start = $this->custom_period_start;
        $period_end = $this->custom_period_end;

        $query = RewardRedemptionHistory::select(
            DB::raw('count(reward_redemption_histories.reward_item_id) as redempt_qty, reward_items.title as reward_name')
        )
        ->leftJoin('reward_items', 'reward_items.id', 'reward_redemption_histories.reward_item_id')
        ->where('reward_redemption_histories.is_delivered', 1)
        ->where('reward_redemption_histories.is_refunded', 0);
       

        
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
				if (!empty($period_start)) {
					$date = date_create_from_format("d/m/Y", $period_start);
					$query = $query->whereDate('reward_redemption_histories.redeemed_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($period_end)) {
					$date = date_create_from_format("d/m/Y", $period_end);
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
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;

    	ob_end_clean();
	  	return view('back.export.reward.reward_popular', [
	      'results' => $data,
	  	]);
	}
}