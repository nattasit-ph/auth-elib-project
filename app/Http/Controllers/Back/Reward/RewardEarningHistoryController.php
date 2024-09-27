<?php

namespace App\Http\Controllers\Back\Reward;

use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RewardActivity;
use App\Models\Role;
use App\Models\RewardEarningHistory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Reward\RewardEarningHistoryExport;

class RewardEarningHistoryController extends BackController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            parent::getSiteConfig();

            $this->user = Auth::user();
            if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
                return $next($request);
            } else {
                return redirect()->route('home');
            }
        });
    }

    public function index(Request $request)
    {
        $org_slug = Auth::user()->org->slug;

        $step = ($request->step == 'step-1')?1:2;
        $activity = RewardActivity::Active()->get();
        $articleActivity = $activity->where('module', 'belib_article');
        $resourceActivity = $activity->where('module', 'belib_resource');
        $all_roles = Role::where('system', 'belib')->get();
        return view('back.' . config('bookdose.theme_back') . '.modules.reward.reward_earning.list')
            ->with(compact('org_slug', 'articleActivity', 'resourceActivity', 'step', 'all_roles'));
    }

    public function ajaxGetData(Request $request)
    {
        if($request->step == 1){
            $activityModule = $request->activity_type ?? '';
            $activityName = $request->activity_name ?? '';
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
            //   ->orderBy('day')
            //   ->orderBy('users.name');
            $datatable = new Datatables;
            return $datatable
                ->eloquent($earningHistory)
                ->addColumn('user_fullname', function ($query) {
                    return $query->user->name;
                })
                ->addColumn('point', function ($query){
                    return $query->point.' x ('.$query->count.' ครั้ง)';
                })
                ->addColumn('activity_type', function ($query) {
                    if ($query->rewardActivity->module == 'belib_resource')
                        return 'ทรัพยากร';
                    elseif ($query->rewardActivity->module == 'belib_article')
                        return 'บทความ';
                    else
                        return 'KM_'.$query->rewardActivity->module;
                })
                ->addColumn('activity_name', function ($query) {
                    return $query->rewardActivity->title;
                })
                ->addIndexColumn()
                ->make(true);
        }else{
            $filter_status = $request->filter_status ?? '';
            $filter_role = $request->filter_role ?? '';
            $earningHistory = RewardEarningHistory::with('user','rewardActivity')
            ->join('users', 'reward_earning_histories.user_id', '=', 'users.id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id');
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
                'roles.name as role_name'
            ])->groupBy('user_id');
            //   ->orderBy('day')
            //   ->orderBy('users.name');
            $datatable = new Datatables;
            return $datatable
                ->eloquent($earningHistory)
                ->addColumn('member_id', function ($query) {
                    return $query->user->member_id;
                })
                ->addColumn('user_fullname', function ($query) {
                    return $query->user->name;
                })
                ->addColumn('user_role', function ($query) {
                    return $query->role_name;
                })
                ->addColumn('point', function ($query){
                    return $query->count;
                })
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function exportToExcel(Request $request)
  {
        $step = $request->input('step');
        $keyword = $request->input('hd_keyword');
        if ($step==1) {
            $param1 = $request->input('hd_is_activity_type');
            $param2 = $request->input('hd_is_activity');
        } else {
            $param1 = $request->input('hd_is_status');
            $param2 = $request->input('hd_is_role');
        }
        return Excel::download(new RewardEarningHistoryExport($param1, $param2, $keyword, $step), 'report_earning_history_'.now().'.xlsx');

  }
}
