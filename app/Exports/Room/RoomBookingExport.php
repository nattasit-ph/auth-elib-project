<?php

namespace App\Exports\Room;

use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RoomBookingExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct($room_id="", $reserve_start="", $reserve_end="", $keyword="", $sort="")
	{

	  $this->room_id = $room_id;
	  $this->reserve_start = $reserve_start;
	  $this->reserve_end = $reserve_end;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'RoomBooking';
 	}

   public function view(): View
	{
		$query = RoomBooking::select('room_bookings.*','users.name as user_name','users.email as email', 'rooms.title as room_title')
		->leftJoin('rooms', 'rooms.id', 'room_bookings.room_id')
		->leftJoin('users', 'users.id', 'room_bookings.user_id');

		if ($this->room_id !== '' && !is_null($this->room_id)) {
			$query = $query->where('rooms.id', $this->room_id);
		}
		
		if (!empty($this->reserve_start)) {
			$_arr = explode("/", $this->reserve_start);
			if (count($_arr) == 3) 
				$query = $query->whereDate('room_bookings.start_datetime', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->reserve_end)) {
			$_arr = explode("/", $this->reserve_end);
			if (count($_arr) == 3) 
				$query = $query->whereDate('room_bookings.end_datetime', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->keyword)) {
			$query = $query->where('events.title_th', 'LIKE', '%'.$this->keyword.'%');
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('rooms.title', 'asc')->get();
		}
		// echo '<pre>'; print_r($query->toSql()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
        return view('back.export.room.roombooking', [
            'results' => $results,
        ]);
	}
}
