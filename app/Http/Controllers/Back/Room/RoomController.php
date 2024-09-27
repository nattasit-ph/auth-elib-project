<?php

namespace App\Http\Controllers\Back\Room;

use App\Http\Controllers\Back\BackController;
use DB;
use Auth;
use Session;
use App\Models\User;
use App\Models\Country;
use App\Models\Room;
use App\Models\RoomGallery;
use App\Models\RoomType;
use App\Models\RoomBooking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Room\RoomExport;

class RoomController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
			parent::getSiteConfig();
 			
        	$this->user = Auth::user();
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
        // $room_type = Room::select('room_types.id', 'room_types.title', 'room_types.description')
        // ->distinct()
        // ->leftJoin('room_types','room_types.id', 'rooms.room_type_id')
        // ->where('rooms.status', 1)
        // ->orderby('room_types.title', 'asc')
        // ->get();
        $room_type = RoomType::active()->get();
        return view('back.'.config('bookdose.theme_back').'.modules.room.room.list', compact('room_type'));
    }
 
    public function exportToExcel(Request $request) 
    {
        $status = $request->input('hd_status');
        $keyword = $request->input('hd_keyword') ?? '';
        $sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
        return Excel::download(new RoomExport($status, $keyword, $sort_by), 'report_rooms_'.now().'.xlsx');
    }
 
    public function create(Request $request)
    {
        $step = 'general';
        $room_type = RoomType::active()->myOrg()->orderBy('title', 'asc')->get();
        return view('back.'.config('bookdose.theme_back').'.modules.room.room.form', compact('step', 'room_type'));
    }
 
    public function edit(Request $request, $id)
    {
        $step = $request->step ?? 'general';
        $content = Room::where('id', $id)->with(['room_galleries', 'room_type'])->firstOrFail();
        $room_type = RoomType::orderBy('title', 'asc')->get();
        $page_header = 'Edit '.$content->title;
        return view('back.'.config('bookdose.theme_back').'.modules.room.room.form', compact('step', 'content', 'page_header', 'room_type'));
    }
 
    public function store(Request $request)
    {
 
        $lang = $request->lang;
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'max_seats' => 'required',
            'open_time' => 'nullable',
            'closed_time' => 'nullable',
            'status' => 'boolean',
            'facilities' => 'nullable',
            'room_type_id' => 'required',
        ]);
        
        $open_time = $validatedData['open_time'];
        $closed_time = $validatedData['closed_time'];
        if($open_time == "ไม่ระบุ"|| $open_time==null){
            $validatedData['open_time'] = null;
        }
        if($closed_time == "ไม่ระบุ"|| $closed_time==null){
            $validatedData['closed_time'] = null;
        }

        $validatedData['facilities'] = $validatedData['facilities'];
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['user_org_id'] = Auth::user()->user_org_id;

        $validatedData['slug'] = uniqid();
        $data = $validatedData;
        $room = Room::create($data);
        if ($room) {

          //--- Start log ---//
            $log = collect([ (object)[
                  'module' => 'Room', 
                  'severity' => 'Info', 
                  'title' => 'Insert', 
                  'desc' => '[Succeeded] - '.$room->title,
               ]])->first();
              parent::Log($log);
              //--- End log ---//
              return redirect()->route('admin.room.edit', [$room->id])->with('success', $validatedData['title'].' is successfully saved. Please upload some room photos.');
       }
       else {
            return redirect()->route('admin.room.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
        }
    }
 
    public function update(Request $request)
    {
        $id = $request->input('id');
        $room = Room::findorFail($id);
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'max_seats' => 'required',
            'open_time' => 'nullable',
            'closed_time' => 'nullable',
            'status' => 'boolean',
            'facilities' => 'nullable',
            'room_type_id' => 'required',
        ]);
        if(!$request->facilities){
            $validatedData['facilities'] = array();
        }
        
        $open_time = $validatedData['open_time'];
        $closed_time = $validatedData['closed_time'];
        if($open_time == "ไม่ระบุ"|| $open_time==null){
            $validatedData['open_time'] = null;
        }
        if($closed_time == "ไม่ระบุ"|| $closed_time==null){
            $validatedData['closed_time'] = null;
        }
        $validatedData['updated_by'] = Auth::user()->id;

        $data = $validatedData;
        $room->update($data);
        //--- Start log ---//
         $log = collect([ (object)[
              'module' => 'Room', 
              'severity' => 'Info', 
              'title' => 'Update', 
              'desc' => '[Succeeded] - '.$data['title'],
           ]])->first();
          parent::Log($log);
          //--- End log ---//

        return redirect()->route('admin.room.edit', [$id])->with('success', $data['title'].' is successfully updated.');
    }
 
    public function setStatus(Request $request)
    {
        $id = $request->input('id');
        if (!empty($id)) {
            $room = Room::where('id', $id)->firstOrFail();
            $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
            $rs = Room::where('id', $id)->update($update_data);
            if ($rs) {
                //--- Start log ---//
                 $log = collect([ (object)[
                      'module' => 'Room', 
                      'severity' => 'Info', 
                      'title' => 'Update status', 
                      'desc' => '[Succeeded] - '.$room->title,
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
              'module' => 'Room', 
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
        //Search function
        $room_type_id = $request->room_type_id;
        $room_id = $request->room_id;
        $date_booking = $request->date_booking;

        if(!empty($date_booking)){
            $date_booking = date('Y-m-d',strtotime($date_booking));
        }
        $time_from = $request->time_from;
        $time_to = $request->time_to;
        $start_date =  $date_booking." ".$time_from;
        $end_date =  $date_booking." ".$time_to;
    
        $start_datetime = date('Y-m-d H:i:s',strtotime($start_date));
        $end_datetime = date('Y-m-d H:i:s',strtotime($end_date));

        $replace_time_from = str_replace(":",".",$time_from);
        $replace_time_to = str_replace(":",".",$time_to);
        // dd($room_type_id, $room_id, $date_booking, $start_datetime, $end_datetime);
        
        $room_list = Room::active();
        $room_arr="";
        if(!empty($room_type_id) && empty($room_id) && !empty($date_booking) && empty($time_from) &&  empty($time_to)){
            
            $room_list = $room_list->where('room_type_id', $room_type_id);
            // dd($room_list->toSql());
            $room_list = $room_list->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }

        elseif(!empty($room_type_id) && empty($room_id) && !empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
            $check_booking = RoomBooking::active()->whereDate('start_datetime', '=', $date_booking)
            ->get();
            $arr_room_id = array();
            // dd($check_booking,"DATE & TIME");
            foreach($check_booking as $item){
            
                if($start_datetime >= $item->start_datetime){
                    if($end_datetime <= $item->end_datetime){
                        $arr_room_id[] = $item->room_id;
                    }

                    if($start_datetime < $item->end_datetime){
                        $arr_room_id[] = $item->room_id;
                    }
                }

                if($start_datetime < $item->start_datetime){
                    if($end_datetime > $item->start_datetime){
                        $arr_room_id[] = $item->room_id;
                    }
                }
                
            }	
            
            $room_list = $room_list->where(function($query) use ($replace_time_from, $replace_time_to){
                $query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
                ->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
                ->orWhere('open_time', null);
            })
            ->where('room_type_id', $room_type_id)
            ->whereNotIn('id', $arr_room_id)->get();
            //dd($room_list->toSql());
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($room_id) && !empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
            $check_booking = RoomBooking::active()->where('room_id', $room_id)
            ->whereDate('start_datetime', '=', $date_booking)
            ->get();
            
            foreach($check_booking as $item){
            
                if($start_datetime >= $item->start_datetime){
                    if($end_datetime <= $item->end_datetime){
                        $room_id="This room is not available";
                    }

                    if($start_datetime < $item->end_datetime){
                        $room_id="This room is not available";
                    }
                }

                if($start_datetime < $item->start_datetime){
                    if($end_datetime > $item->start_datetime){
                        $room_id="This room is not available";
                    }
                }
                
            }
            $room_list = $room_list->where('id', $room_id)
            ->where(function($query) use ($replace_time_from, $replace_time_to){
                $query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
                ->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
                ->orWhere('open_time', null);
            });
            //dd($room_list->toSql());
            $room_list = $room_list->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
            $check_booking = RoomBooking::active()->whereDate('start_datetime', '=', $date_booking)
            ->get();
            $arr_room_id = array();
            // dd($check_booking,"DATE & TIME");
            foreach($check_booking as $item){
            
                if($start_datetime >= $item->start_datetime){
                    if($end_datetime <= $item->end_datetime){
                        $arr_room_id[] = $item->room_id;
                    }

                    if($start_datetime < $item->end_datetime){
                        $arr_room_id[] = $item->room_id;
                    }
                }

                if($start_datetime < $item->start_datetime){
                    if($end_datetime > $item->start_datetime){
                        $arr_room_id[] = $item->room_id;
                    }
                }
                
            }	
            
            $room_list = $room_list->where(function($query) use ($replace_time_from, $replace_time_to){
                $query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
                ->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
                ->orWhere('open_time', null);
            })
            ->whereNotIn('id', $arr_room_id)->get();
            //dd($room_list->toSql());
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($room_id) && !empty($date_booking) && empty($time_from)){
            $room_list = $room_list->where('id', $room_id)->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($room_type_id)){
            $room_list = $room_list->where('room_type_id', $room_type_id)->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($room_id)){
            $room_list = $room_list->where('id', $room_id)->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }
        elseif(!empty($date_booking)){
            $room_list = $room_list->get();
            $room_arr=array();
            foreach($room_list as $item){
                $room_arr[] = $item->id;
            }
            // dd($room_arr);
        }

        /// End Search Function

        $order = $request->order;
        
        $columns = $request->columns;
        if (isset($columns) && isset($order[0]['column'])) {
            Session::put('sort_column', $columns[$order[0]['column']]['name']);
            Session::put('sort_by', $order[0]['dir']);
        }

        $filter_status = $request->filter_status ?? '';

        $query = Room::with(['room_galleries'])
            ->select(array_merge(
                array('rooms.*'),
                array(
                    DB::raw('DATE_FORMAT(rooms.updated_at, "%d/%m/%Y") AS updated_date') 
                ),
            ));

        if ($filter_status !== '') {
            $query = $query->where('status', $filter_status);
        }

        if ($room_arr !== ''){
            $query = $query->whereIn('id', $room_arr);
        }

        $datatable = new DataTables;
        return $datatable
            ->eloquent($query)
            ->addColumn('file_path_html', function ($row) {
                $room_gallery = $row["room_galleries"];
                foreach ($room_gallery as $data){
                    $data->is_cover;
                    $data->file_path;
                    if($data->is_cover == 1){
                        return '<a href="'.route('admin.room.edit', [$row->id]).'" class=""><img src="'.Storage::url($data->file_path).'" class="w-50"></a>';
                    }
                }
                return '<a href="'.route('admin.room.edit', [$row->id]).'" class=""><img src="'.asset('' . config('bookdose.app.folder') . '/images/placeholder/default-room.png').'" class="w-50"></a>';
            })
            ->addColumn('title_action', function ($row) {
                return '<a href="'.route('admin.room.edit', [$row->id]).'" class="">'.$row->title.'</a>';
            })
            ->addColumn('max_seats_html', function ($row) {
                return $row->max_seats;
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
            ->addColumn('actions', function ($row) use ($date_booking, $time_from, $time_to){
                if ($row->status == 1)
                    return '
                <span class="dropdown">
                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                        <i class="la la-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href='.route('admin.room.booking.create',['room_id' => $row->id, 'date_booking' => $date_booking, 'time_from' => $time_from, 'time_to' => $time_to]).'><i class="la la-book"></i> Reservation</a>
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
            ->rawColumns(['file_path_html', 'title_action', 'title_secondary', 'country', 'status_html', 'approval_status_html', 'actions'])
            ->addIndexColumn()
            ->make(true);
            
    }
 
    public function ajaxGetDataJSON(Request $request)
    {
        $order_column = $request->order_column ?? 'title';
        $order_by = $request->order_by ?? 'asc';
        $data = Room::active()
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
        return response()->json(['results' => $data]);
    }
 
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if (!empty($id)) {
            $rs = Room::where('id', $id)->firstOrFail();
            $item = $rs;
            $room_gallery = RoomGallery::where('room_id', $item->id)->get();
            foreach($room_gallery as $data){
                if ($data->file_path) Storage::delete($data->file_path);
            }
            $delete_gallery = RoomGallery::where('room_id', $item->id)->delete();

            $rs = Room::where('id', $id)->delete();
            if ($rs) {
                //--- Start log ---//
                 $log = collect([ (object)[
                      'module' => 'Room', 
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
              'module' => 'Room', 
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

    public function ajaxRoomName(Request $request)
	{
		if($request->ajax())
		{
			$room_type_id = $request->room_type_id;
			$room_name = Room::active()->where('room_type_id', $room_type_id)->orderBy('title', 'ASC')->get();
			return response()->json($room_name);
	
		}
	}
}
