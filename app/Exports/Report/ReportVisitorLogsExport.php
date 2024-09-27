<?php
namespace App\Exports\Report;

use DB;
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportVisitorLogsExport implements FromView, ShouldAutoSize, WithTitle
{
   public function __construct($period="", $custom_period_start="", $custom_period_end="", $keyword="", $sort="", $device="", $system="")
	{
	  $this->period = $period;
	  $this->custom_period_start = $custom_period_start;
	  $this->custom_period_end = $custom_period_end;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	  $this->device = $device;
	  $this->system = $system;
	}

	public function title(): string
 	{
 		return 'Report - Visitor Log';
 	}

   public function view(): View
	{
 		$query = DB::table('visitor_logs')
		 			->select(
	    			'visitor_logs.*', 
	    			DB::raw('DATE_FORMAT(visitor_logs.created_at, "%d/%m/%Y %H:%i") AS created_date'),
		 );

    	switch ($this->period) {
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
	    		if (!empty($this->custom_period_start)) {
					$date = date_create_from_format("d/m/Y", $this->custom_period_start);
					$query = $query->whereDate('visitor_logs.created_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($this->custom_period_end)) {
					$date = date_create_from_format("d/m/Y", $this->custom_period_end);
					$query = $query->whereDate('visitor_logs.created_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;

	    	default:
	    		break;
    	}
		if(!empty($this->device)){
			$query = $query->where('visitor_logs.device', $this->device);
		}
		if(!empty($this->system)){
			$query = $query->where('visitor_logs.system', $this->system);
		}
		if (!empty($this->keyword)) {
			$query->where(function ($query) {
				$query->where('visitor_logs.browser_detail', 'LIKE', '%'.$this->keyword.'%')
					->orWhere('visitor_logs.ip', 'LIKE', '%'.$this->keyword.'%')
					->orWhere('visitor_logs.device', 'LIKE', '%'.$this->keyword.'%');
			});
		}

    	if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('created_at', 'desc')
							 ->get();
		}
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
	  	return view('back.export.report.report_visitor_log', [
	      'results' => $results,
	  	]);
	}
}