<?php

namespace App\Exports\Room;

use App\Models\Room;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RoomExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct($status="", $keyword="", $sort="")
	{
	    $this->status = $status;
	    $this->keyword = $keyword;
	    $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'Rooms';
 	}

    public function view(): View
	{
		$query = Room::myOrg()
			->select(array_merge(
				array('rooms.*')
			));

		if ($this->status !== '' && !is_null($this->status)) {
			$query = $query->where('status', $this->status);
		}
		

		if (!empty($this->keyword)) {
			$query = $query->where('rooms.title', 'LIKE', '%'.$this->keyword.'%');
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('title', 'asc')->get();
		}

		// echo '<pre>'; print_r($query->toSql()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
        return view('back.export.room.room', [
            'results' => $results,
        ]);
	}
}
