<?php
namespace App\Exports\Poll;

use DB;
use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PollsExport implements FromView, WithTitle
{
   public function __construct($status="",  $poll_start="", $poll_end="", $keyword="", $sort="")
	{
	  $this->status = $status;
	  $this->poll_start = $poll_start;
	  $this->poll_end = $poll_end;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'Polls';
 	}

   public function view(): View
	{
		$query = Poll::with('pollOptions')
	    		->select(array_merge(
	    			array('id', 'question', 'poll_start', 'poll_end', 'total_votes', 'total_options', 'status', 'created_at', 'updated_at'),
	    			array(
	    				DB::raw('IF (poll_start IS NOT NULL, DATE_FORMAT(polls.poll_start, "%d/%m/%Y"), NULL) AS poll_start_date'), 
	    				DB::raw('IF (poll_end IS NOT NULL, DATE_FORMAT(polls.poll_end, "%d/%m/%Y"), NULL) AS poll_end_date'), 
	    				DB::raw('DATE_FORMAT(polls.created_at, "%d/%m/%Y") AS created_date'), 
		      		DB::raw('DATE_FORMAT(polls.updated_at, "%d/%m/%Y") AS updated_date') 
	    			)
	    		));

		if ($this->status !== '' && !is_null($this->status)) {
			$query = $query->where('status', $this->status);
		}

		if (!empty($this->poll_start)) {
			$_arr = explode("/", $this->poll_start);
			if (count($_arr) == 3) 
				$query = $query->where('poll_start', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->poll_end)) {
			$_arr = explode("/", $this->poll_end);
			if (count($_arr) == 3) 
				$query = $query->where('poll_end', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('created_at', 'desc')->get();
		}
    	// echo $query->toSql(); exit;

    	$options_with_total = PollVote::select('poll_id', 'poll_option_id', DB::raw('count(*) as total'))
                 ->groupBy(['poll_id', 'poll_option_id'])
                 ->get()
                 ->keyBy('poll_option_id');
                 
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($options_with_total->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
	  	return view('back.export.poll.poll', [
	      'results' => $results,
	      'options_with_total' => $options_with_total,
	  	]);
	}
}