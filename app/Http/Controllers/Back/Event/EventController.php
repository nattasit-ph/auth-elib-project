<?php

namespace App\Http\Controllers\Back\Event;

use DB;
use Auth;
use Session;
use App\Core\Queue\AppQueue;
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
use App\Exports\Event\EventsExport;
use App\Jobs\SendEventInvitation;

class EventController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
        	$this->user = Auth::user();
        	// parent::getSiteConfig();
        	
        	if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
    			return $next($request);
 			}
 			else {
 				return redirect()->route('home');
 			}
        });
 	}

	public function index(Request $request)
	{
		return view('back.'.config('bookdose.theme_back').'.modules.event.list');
	}

	public function exportToExcel(Request $request) 
	{
		$status = $request->input('hd_status');
		$event_start = $request->input('hd_event_start');
		$event_end = $request->input('hd_event_end');
		$keyword = $request->input('hd_keyword') ?? '';
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new EventsExport($status, $event_start, $event_end, $keyword, $sort_by), 'report_event_'.now().'.xlsx');
	}

	public function create(Request $request)
	{
    	$step = 'general';
		return view('back.'.config('bookdose.theme_back').'.modules.event.form', compact('step'));
	}

	public function edit(Request $request, $id, $step='general')
	{
		$event = Event::where('id', $id)->firstOrFail();
		$all_users = User::active()->orderBy('username', 'asc')->get();
		$page_header = 'Edit event';
		return view('back.'.config('bookdose.theme_back').'.modules.event.form', compact('step', 'event', 'page_header', 'all_users'));
	}

	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'title' => 'required|max:255',
			'description' => 'nullable',
			'venue' => 'nullable',
			'organizer' => 'nullable',
			'email' => 'nullable',
			'website' => 'nullable',
			// 'facebook' => 'nullable',
			// 'youtube' => 'nullable',
			'event_start' => 'nullable',
			'event_end' => 'nullable',
			'cover_image_path' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
			'status' => 'boolean',
		]);
		$validatedData['created_by'] = Auth::user()->id;
		if (!empty($validatedData['website'])) {
			if  ( $ret = parse_url($validatedData['website']) ) {
				if ( !isset($ret["scheme"]) )
					$validatedData['website'] = "http://{$validatedData['website']}";
				}
		}
		if (!empty($validatedData['event_start'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['event_start']);
			$validatedData['event_start'] =  date_format($date, "Y-m-d");
		}
		if (!empty($validatedData['event_end'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['event_end']);
			$validatedData['event_end'] =  date_format($date, "Y-m-d");
		}
		if ($request->cover_image_path) {
    		$path = $request->cover_image_path->store('event_kms');
    		if ($path) {
    			$validatedData['cover_image_path'] = $path;
    		}
    	}
		if(config('bookdose.app.folder') != "gpo"){
			$validatedData['user_org_id'] = Auth::user()->user_org_id;
		}
		$event = Event::create($validatedData);
		if ($event) {

	      //--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Event', 
	      		'severity' => 'Info', 
	      		'title' => 'Insert', 
	      		'desc' => '[Succeeded] - '.$event->title,
	   		]])->first();
	  		parent::Log($log);
	  		//--- End log ---//

			return redirect()->route('admin.event.edit', [$event->id, 'invitation'])->with('success', 'Event is successfully saved.');
	   }
	   else {
			return redirect()->route('admin.event.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
		}
	}

	public function update(Request $request)
	{
		$id = $request->input('id');
		$event = Event::findorFail($id);
		$validatedData = $request->validate([
			'title' => 'required|max:255',
			'description' => 'nullable',
			'venue' => 'nullable',
			'organizer' => 'nullable',
			'email' => 'nullable',
			'website' => 'nullable',
			// 'facebook' => 'nullable',
			// 'youtube' => 'nullable',
			'event_start' => 'nullable',
			'event_end' => 'nullable',
			'cover_image_path' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
			'status' => 'boolean',
		]);
		$validatedData['updated_by'] = Auth::user()->id;
		if (!empty($validatedData['website'])) {
			if  ( $ret = parse_url($validatedData['website']) ) {
				if ( !isset($ret["scheme"]) )
					$validatedData['website'] = "http://{$validatedData['website']}";
				}
		}
		if (!empty($validatedData['event_start'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['event_start']);
			$validatedData['event_start'] =  date_format($date, "Y-m-d");
		}
		if (!empty($validatedData['event_end'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['event_end']);
			$validatedData['event_end'] =  date_format($date, "Y-m-d");
		}
		if ($request->cover_image_path) {
    		if (!empty($event->cover_image_path)) Storage::delete($event->cover_image_path);
    		$path = $request->cover_image_path->store('event_kms');
    		$validatedData['cover_image_path'] = $path;
    	}

		$event->update($validatedData);
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Event', 
      		'severity' => 'Info', 
      		'title' => 'Update', 
      		'desc' => '[Succeeded] - '.$validatedData['title'],
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

		return redirect()->route('admin.event.edit', [$id, 'general'])->with('success', 'Event is successfully updated.');
	}

	public function setStatus(Request $request)
	{
		$id = $request->input('id');
		if (!empty($id)) {
			$event = Event::where('id', $id)->firstOrFail();
			$update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
			$rs = Event::where('id', $id)->update($update_data);
			if ($rs) {
				//--- Start log ---//
		 		$log = collect([ (object)[
		      		'module' => 'Event', 
		      		'severity' => 'Info', 
		      		'title' => 'Update status', 
		      		'desc' => '[Succeeded] - '.$event->title,
		   		]])->first();
		  		parent::Log($log);
		  		//--- End log ---//

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'Status has been updated successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Event', 
      		'severity' => 'Error', 
      		'title' => 'Update status', 
      		'desc' => '[Failed] - Invalid id = '.$id,
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

	public function ajaxGetData(Request $request) 
	{
		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}

		$filter_status = $request->filter_status ?? '';
		$filter_event_start = $request->filter_event_start ?? '';
		$filter_event_end = $request->filter_event_end ?? '';
		$query = Event::withCount('event_joins');

		if ($filter_status !== '') {
			$query = $query->where('status', $filter_status);
		}
		
		if (!empty($filter_event_start)) {
			$_arr = explode("/", $filter_event_start);
			if (count($_arr) == 3) 
				$query = $query->where('event_start', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($filter_event_end)) {
			$_arr = explode("/", $filter_event_end);
			if (count($_arr) == 3) 
				$query = $query->where('event_end', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		$datatable = new DataTables;
		return $datatable
			->eloquent($query)
			->addColumn('title_action', function ($row) {
				return '<a href="'.route('admin.event.edit', [$row->id, 'general']).'" class="">'.$row->title.'</a>';
			})
			->addColumn('event_joins_count', function ($row) {
				return $row->event_joins_count;
			})
			->addColumn('txt_event_start', function ($row) {
				return !empty($row->event_start) ? date('d/m/Y', strtotime($row->event_start)) : '';
			})
			->addColumn('txt_event_end', function ($row) {
				return !empty($row->event_end) ? date('d/m/Y', strtotime($row->event_end)) : '';
			})
			->addColumn('status_html', function ($row) {
				if ($row->status == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
				else 
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
			})
			->addColumn('approval_status_html', function ($row) {
				if ($row->approval_status == '1')
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Approved</span>';
				else if ($row->approval_status == '0')
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Rejected</span>';
				else 
					return '<span class="kt-badge kt-badge--warning kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-warning">Waiting for Approval</span>';
			})
			->addColumn('actions', function ($row) {
				if ($row->status == 1)
					return '
				<span class="dropdown">
					<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
						<i class="la la-ellipsis-h"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
						<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
					</div>
				</span>';
				else
					return '
				<span class="dropdown">
					<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
						<i class="la la-ellipsis-h"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
						<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
					</div>
				</span>';
			})
			->rawColumns(['title_action', 'status_html', 'approval_status_html', 'event_joins_count', 'actions'])
			->addIndexColumn()
			->make(true);
			
	}

	public function ajaxGetInvitationData(Request $request) 
	{
		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}

		$query = EventJoin::with('user')->select(
				array(
					'*',
					DB::raw('DATE_FORMAT(event_join_kms.invited_at, "%d/%m/%Y %H:%i") AS invited_date'),
					DB::raw('DATE_FORMAT(event_join_kms.joined_at, "%d/%m/%Y %H:%i") AS joined_date'),
				)
			)
			->where('event_id', $request->event_id);

		$datatable = new DataTables;
		return $datatable
			->eloquent($query)
			->addColumn('username', function ($row) {
				return $row->user->username;
			})
			->addColumn('name', function ($row) {
				return $row->user->name;
			})
			->addColumn('actions', function ($row) {
				return '
					<span class="dropdown">
						<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->event_id).' data-id2='.json_encode($row->user_id).' data-title='.json_encode($row->user->name, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						</div>
					</span>';
			})
			->rawColumns(['username', 'name', 'actions'])
			->addIndexColumn()
			->make(true);
	}

	public function ajaxSendInvitation(Request $request)
	{
		$event_id = request()->event_id ?? '';
		$user_id = request()->user_id ?? '';
		if ($event_id > 0 && $user_id > 0) {
			$this_event = Event::find($event_id);
			$this_user = User::find($user_id);

			if ($this_event && $this_user) {
				if (EventJoin::where(['event_id' => $event_id, 'user_id' => $user_id ])->doesntExist()) {
					$invitation_code = md5(uniqid());
					$rs = EventJoin::create([
						'event_id' => $event_id,
						'user_id' => $user_id,
						'invitation_code' => $invitation_code,
						'invited_at' => now(),
						'invited_by' => Auth::user()->id,
					]);

					if (!empty($this_user->email)) {
						// Send email (if needed)
						SendEventInvitation::dispatch($this_user, $this_event, $invitation_code)->onQueue(AppQueue::getQWithPrefix(AppQueue::Default));
					}
					if ($rs) {
						return json_encode(array(
								'status' => 200,
								'notify_title' => 'Hooray!',
								'notify_msg' => 'Invitation has been sent successfully.',
								'notify_icon' => 'icon la la-check-circle',
								'notify_type' => 'success',
							));
					}
				}
			}
		}
		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

	public function ajaxDeleteInvitation(Request $request)
	{
		$event_id = $request->input('id');
		$user_id = $request->input('id2');
		if (!empty($event_id) && !empty($user_id)) {
			$rs = EventJoin::with(['user', 'event'])->where(['event_id' => $event_id, 'user_id' => $user_id])->firstOrFail();
			$item = $rs;

			$rs = EventJoin::where(['event_id' => $event_id, 'user_id' => $user_id])->delete();
			if ($rs) {
				//--- Start log ---//
		 		$log = collect([ (object)[
		      		'module' => 'Event Invitation', 
		      		'severity' => 'Info', 
		      		'title' => 'Delete event invitation',
		      		'desc' => '[Succeeded] - Delete '.($item->user->name ?? '').' from event '.$item->event->title,
		   		]])->first();
		  		parent::Log($log);
		  		//--- End log ---//

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'Invitation has been deleted successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Event', 
      		'severity' => 'Error', 
      		'title' => 'Delete', 
      		'desc' => '[Failed] - Invalid event_id = '.$event_id.', or user_id = '.$user_id,
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

	public function ajaxGetDataJSON(Request $request, $content_type_slug, $lang)
	{
		$order_column = $request->order_column ?? 'title';
		$order_by = $request->order_by ?? 'asc';
		$content_type = ContentType::where('slug', $content_type_slug)->firstOrFail();
		$data = Event::active()
			->where('content_type_id', $content_type->id)
			->orderBy($order_column, $order_by)
			->get()
			->transform(function ($item, $key) {
				$_item = [];
				$_item['id'] = $item->id;
				$_item['text'] = $item->title;
				return $_item;
			});

		if ($request->display_all_items_option ?? FALSE) {
			$data->prepend(['id' => '', 'text' => 'ทุกรายการ']);
		}
		// echo '<pre>'; print_r($all_file_list); echo '</pre>'; exit;
		return response()->json(['results' => $data]);
	}

	public function destroy(Request $request)
	{
		$id = $request->input('id');
		if (!empty($id)) {
			$rs = Event::where('id', $id)->firstOrFail();
			$item = $rs;

			$rs = Event::where('id', $id)->delete();
			if ($rs) {
				//--- Start log ---//
		 		$log = collect([ (object)[
		      		'module' => 'Event', 
		      		'severity' => 'Info', 
		      		'title' => 'Delete', 
		      		'desc' => '[Succeeded] - '.$item->title,
		   		]])->first();
		  		parent::Log($log);
		  		//--- End log ---//

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => $item->title.' has been deleted successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Event', 
      		'severity' => 'Error', 
      		'title' => 'Delete', 
      		'desc' => '[Failed] - Invalid id = '.$id,
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

}
