<?php
namespace App\Exports\Event;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\EventJoin;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class EventsExport implements FromView, WithTitle
{
   public function __construct($status="", $event_start='', $event_end='', $keyword="", $sort="")
	{
	  $this->status = $status;
	  $this->event_start = $event_start;
	  $this->event_end = $event_end;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'Events';
 	}

   public function view(): View
	{
		$query = Event::withCount('event_joins');

		// echo $this->status.'<br>';
		// var_dump($this->event_start).'<br>';
		// var_dump($this->event_end).'<br>';
		// echo Carbon::parse($this->event_start)->format('Y-m-d');
		// echo Carbon::parse($this->event_end)->format('Y-m-d');
		// exit;

		if ($this->status !== '' && !is_null($this->status)) {
			// echo 'a';
			$query = $query->where('status', $this->status);
		}

		if (!empty($this->event_start)) {
			$query = $query->whereDate('event_start', '>=', Carbon::createFromFormat('d/m/Y', $this->event_start)->format('Y-m-d'));
		}

		if (!empty($this->event_end)) {
			$query = $query->whereDate('event_end', '<=', Carbon::createFromFormat('d/m/Y', $this->event_end)->format('Y-m-d'));
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('created_at', 'desc')->get();
		}
		// exit;
    	// echo $query->toSql(); exit;
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($options_with_total->toArray()); echo '</pre>'; exit;

    	ob_end_clean();
	  	return view('back.export.event.event', [
	      'results' => $results,
	  	]);
	}
}