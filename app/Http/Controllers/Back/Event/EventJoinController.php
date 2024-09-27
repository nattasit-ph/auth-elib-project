<?php

namespace App\Http\Controllers\Back\Event;

use DB;
use Auth;
use Session;
use App\Models\Event;
use App\Models\EventJoin;
use App\Models\User;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Event\EventJoinsExport;
use App\Jobs\SendEventInvitation;

class EventJoinController extends BackController
{

	public function exportToExcel(Request $request) 
	{
		$event_id = $request->input('hd_event_id');
		$keyword = $request->input('hd_keyword') ?? '';
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new EventJoinsExport($event_id, $keyword, $sort_by), 'report_event_participation_'.now().'.xlsx');
	}

}
