<?php
namespace App\Exports\Page;

use DB;
use App\User;
use App\Models\Page;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PageExport implements FromView, ShouldAutoSize, WithTitle
{
   public function __construct($system="", $period="", $custom_period_start="", $custom_period_end="", $keyword="")
	{
        $this->system = $system;
	    $this->period = $period;
	    $this->custom_period_start = $custom_period_start;
	    $this->custom_period_end = $custom_period_end;
	    $this->keyword = $keyword;

	}

	public function title(): string
 	{
 		return 'Report - Page';
 	}

   public function view(): View
	{
        $query = Page::MyOrg();

        if (!empty($system)) {
            $query = $query->where('system', $system);
        }

    	switch ($this->period) {
	    	case 'today':
	    		$query = $query->whereDate('pages.created_at', '=', date("Y-m-d", strtotime('today') ));
	    		break;

	    	case 'yesterday':
	    		$query = $query->whereDate('pages.created_at', '=', date("Y-m-d", strtotime('-1 days') ));
	    		break;

	    	case 'last7Days':
	    		$query = $query->whereDate('pages.created_at', '>', date("Y-m-d", strtotime('-7 days') ));
	    		break;

	    	case 'thisMonth':
	    		$query = $query->whereMonth('pages.created_at', '=', date("m", strtotime('this month') ));
	    		break;

	    	case 'lastMonth':
	    		$query = $query->whereMonth('pages.created_at', '=', date("m", strtotime('last month') ));
	    		break;

	    	case 'customPeriod':
	    		if (!empty($this->custom_period_start)) {
					$date = date_create_from_format("d/m/Y", $this->custom_period_start);
					$query = $query->whereDate('pages.created_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($this->custom_period_end)) {
					$date = date_create_from_format("d/m/Y", $this->custom_period_end);
					$query = $query->whereDate('pages.created_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;

	    	default:
	    		break;
    	}

		if (!empty($this->keyword)) {
			$query->where(function ($query) {
				$query->where('pages.title', 'LIKE', '%'.$this->keyword.'%');
			});
		}

    	$results = $query->orderBy('pages.id', 'desc')->get();

		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
	  	return view('back.export.page.page', [
	      'results' => $results,
	  	]);
	}
}
