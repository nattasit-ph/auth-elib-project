<?php

namespace App\Http\Controllers\Back\Visitorlog;


use Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Back\BackController;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Interested;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report\ReportUserInformationExport;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Exports\Report\ReportVisitorLogsExport;

class VisitorlogController extends BackController
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

	public function index()
	{
		$device = LoginHistory::myOrg()->distinct()->get('device');
		return view('back.' . config('bookdose.theme_back') . '.modules.visitorlog.list',compact('device'));
	}

	public function ajaxGetData(Request $request) 
	{
		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}
		$period = $request->input('period');
		$device = $request->input('device');
		$system= $request->input('system');
 		$query = VisitorLog::select(
	    			'visitor_logs.*', 
	    			DB::raw('DATE_FORMAT(visitor_logs.created_at, "%d/%m/%Y %H:%i") AS created_date'),
		 );
	    switch ($period) {
	    	case 'today':
	    		$query = $query->whereDate('visitor_logs.created_at', '=', date("Y-m-d", strtotime('today') ));
	    		break;

	    	case 'yesterday':
	    		$query = $query->whereDate('visitor_logs.created_at', '=', date("Y-m-d", strtotime('-1 days') ));
	    		break;
	    	
	    	case 'last7Days':
	    		$query = $query->whereDate('visitor_logs.created_at', '>', date("Y-m-d", strtotime('-7 days') ));
	    		break;
	    	
	    	case 'thisMonth':
	    		$query = $query->whereMonth('visitor_logs.created_at', '=', date("m", strtotime('this month') ));
	    		break;
	    	
	    	case 'lastMonth':
	    		$query = $query->whereMonth('visitor_logs.created_at', '=', date("m", strtotime('last month') ));
	    		break;

	    	case 'customPeriod':
	    		if (!empty($request->period_start)) {
					$date = date_create_from_format("d/m/Y", $request->period_start);
					$query = $query->whereDate('visitor_logs.created_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($request->period_end)) {
					$date = date_create_from_format("d/m/Y", $request->period_end);
					$query = $query->whereDate('visitor_logs.created_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;
	    	
	    	default:
	    		break;
	    }
		if(!empty($device)){
			$query = $query->where('visitor_logs.device', $device);
		}
		if(!empty($system)){
			$query = $query->where('visitor_logs.system', $system);
		}
	    $datatable = new DataTables;
	    return $datatable
				->eloquent($query)
				->addColumn('browser', function ($row) {
					$browser = 'Unknown';
					if (preg_match('/Firefox/i', $row->browser)) $browser = 'Firefox';
					elseif (preg_match('/Mac/i', $row->browser)) $browser = 'Mac';
					elseif (preg_match('/Chrome/i', $row->browser)) $browser = 'Chrome';
					elseif (preg_match('/Opera/i', $row->browser)) $browser = 'Opera';
					elseif (preg_match('/MSIE/i', $row->browser)) $browser = 'IE';
	    			return $browser;
	    		})
	    		->rawColumns(['browser'])
	    		->addIndexColumn()
	    		->make(true);
 	}


	public function exportToExcel(Request $request)
	{
		$period = $request->input('hd_period');
		$device = $request->input('hd_device');
		$system = $request->input('hd_system');
		$custom_period_start = $request->input('hd_custom_period_start');
		$custom_period_end = $request->input('hd_custom_period_end');
		$keyword = $request->input('hd_keyword');
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new ReportVisitorLogsExport($period, $custom_period_start, $custom_period_end, $keyword, $sort_by, $device, $system), 'report_visitor_log_'.now().'.xlsx');
	}



}
