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

class ReportRewardRedemptionHistoryExport implements FromView, ShouldAutoSize, WithTitle
{
   public function __construct($is_delivered="", $is_refunded="", $keyword="")
	{
	  $this->is_delivered = $is_delivered;
	  $this->is_refunded = $is_refunded;
	  $this->keyword = $keyword;

	}

	public function title(): string
 	{
 		return 'Report - Redemption History';
 	}

   public function view(): View
	{
		$query = RewardRedemptionHistory::with('user', 'rewardItem');
     	if (in_array($this->is_delivered, ["0", "1"])) {
     		$query = $query->where('is_delivered', $this->is_delivered);
     	}
     	if (in_array($this->is_refunded, ["0", "1"])) {
     		$query = $query->where('is_refunded', $this->is_refunded);
     	}
     	$data = $query->orderBy('id', 'DESC')
        ->get();

 		ob_end_clean();
  		return view('back.export.reward.reward_redemption_history', [
      	'results' => $data,
  		]);
	}
}