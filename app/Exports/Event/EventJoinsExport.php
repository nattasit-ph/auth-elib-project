<?php
namespace App\Exports\Event;

use DB;
use App\Models\User;
use App\Models\Event;
use App\Models\EventJoin;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class EventJoinsExport implements FromView, WithTitle
{
   public function __construct($event_id="", $keyword="", $sort="")
	{
	  $this->event_id = $event_id;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'Event Participations';
 	}

   public function view(): View
	{
		$query = EventJoin::with(['event', 'user'])
	    		->select(array_merge(
	    			array('*'),
	    			array(
	    				DB::raw('IF (invited_at IS NOT NULL, DATE_FORMAT(invited_at, "%d/%m/%Y"), NULL) AS invited_date'), 
	    				DB::raw('IF (joined_at IS NOT NULL, DATE_FORMAT(joined_at, "%d/%m/%Y"), NULL) AS joined_date'), 
	    			)
	    		));

		if ($this->event_id !== '' && !is_null($this->event_id)) {
			$query = $query->where('event_id', $this->event_id);
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('created_at', 'desc')->get();
		}
    	// echo $query->toSql(); exit;
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($options_with_total->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
	  	return view('back.export.event.event_join', [
	      'results' => $results,
	  	]);
	}
}