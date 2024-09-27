<?php
namespace App\Exports\Reward;

use Illuminate\Support\Facades\Auth;
use App;
use App\Models\RewardEarningHistory;
use App\Models\UserOrg;
use App\Models\UserOrgUnit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RewardEarningHistoryExport implements FromView, ShouldAutoSize, WithTitle
{
   public function __construct($param1="", $param2="", $keyword="", $step="")
	{
	  $this->param1 = $param1;
	  $this->param2 = $param2;
	  $this->keyword = $keyword;
	  $this->step = $step;

	}

	public function title(): string
 	{
 		return 'Report - Redemption History';
 	}

   public function view(): View
	{
		if($this->step == 1){
            $activityModule = $this->param1 ?? '';
            $activityName = $this->param2 ?? '';
            $earningHistory = RewardEarningHistory::with('user','rewardActivity')->join('users', 'reward_earning_histories.user_id', '=', 'users.id');
            if ($activityModule != '') {
                $earningHistory->whereHas('rewardActivity', function ($query) use ($activityModule) {
                    $query->where('module', $activityModule);
                });
                if ($activityName != '') {
                    $earningHistory->whereHas('rewardActivity', function ($query) use ($activityName) {
                        $query->where('action_name', $activityName);
                    });
                }
            }
            $earningHistory = $earningHistory->select([
                DB::raw('count(reward_earning_histories.user_id) as `count`'), 
                DB::raw('DATE(reward_earning_histories.created_at) as day'),
                'reward_earning_histories.reward_activity_id',
                'reward_earning_histories.user_id',
                'reward_earning_histories.point',
                'users.name'
            ])->groupBy('day','reward_activity_id','user_id');
			$data = $earningHistory->get();
			ob_end_clean();
			return view('back.export.reward.reward_earning_step1', [
				'results' => $data,
			]);
        }else{
			$user_org = UserOrg::where('id', Auth::user()->user_org_id)->first();
			$user_info_template = $user_org->user_info_template ?? [];

			$all_org_units = [];
			if (Schema::hasTable('user_org_units')) {
				$all_org_units = UserOrgUnit::get();
			}

            $filter_status = $this->param1 ?? '';
            $filter_role = $this->param2 ?? '';
            $earningHistory = RewardEarningHistory::with('user','rewardActivity')
            ->join('users', 'reward_earning_histories.user_id', '=', 'users.id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
			->join('user_groups', 'user_groups.id', '=', 'users.user_group_id');
            if ($filter_status != '') {
                $earningHistory->whereHas('user', function ($query) use ($filter_status) {
                    $query->where('users.status', $filter_status);
                });
            }
            if ($filter_role != '') {
                $earningHistory->whereHas('user', function ($query) use ($filter_role) {
                    $query->where('model_has_roles.role_id', $filter_role);
                });
            }
            $earningHistory = $earningHistory->select([
                DB::raw('count(reward_earning_histories.user_id) as `count`'), 
                DB::raw('DATE(reward_earning_histories.created_at) as day'),
                'reward_earning_histories.reward_activity_id',
                'reward_earning_histories.user_id',
                'reward_earning_histories.point',
                'users.name',
                'roles.name as role_name',
				'user_groups.name AS user_groups_name',
				'users.data_info'
            ])->groupBy('user_id');
			$data = $earningHistory->get();
			ob_end_clean();
			// dd($data);
			return view('back.export.reward.reward_earning_step2', [
				'results' => $data,
				'user_info_template' => $user_info_template,
		  		'all_org_units' => $all_org_units,
			]);
        }
	}
}