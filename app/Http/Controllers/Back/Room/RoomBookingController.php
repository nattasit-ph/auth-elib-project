<?php

namespace App\Http\Controllers\Back\Room;

use DB;
use Auth;
use Session;
use App\Http\Controllers\Back\BackController;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Room\RoomBookingExport;

use Illuminate\Support\Facades\Mail;
use App\Mail\RoomReservation;
use App\Mail\RoomCancelReservation;

class RoomBookingController extends BackController
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
       $room_list = Room::active()->myOrg()->get();
       return view('back.'.config('bookdose.theme_back').'.modules.room.booking.list', compact('room_list'));
    }
 
    public function create(Request $request)
    {
        $room_id = $request->room_id;
        $date_booking = $request->date_booking;
        $time_from = $request->time_from;
        $time_to = $request->time_to;
        // dd($room_id, $date_booking, $time_from, $time_to);
        $booking_data = array(
            'date_booking' => $date_booking,
            'time_from' => $time_from,
            'time_to' => $time_to,
        );

        // $user_list = User::active()->myOrg()->get();
        $room_list = Room::active()->myOrg()->where('id', $room_id)->first();
        if(empty($room_list)){
            return  abort(404);
        }
        return view('back.'.config('bookdose.theme_back').'.modules.room.booking.form', compact('room_list','booking_data'));
    }
 
    public function edit(Request $request)
    {	
        $booking_id = $request->booking_id;
        $booking_detail = RoomBooking::where('room_bookings.id', $booking_id)
        ->leftJoin('rooms', 'rooms.id', 'room_bookings.room_id')
        ->leftJoin('users', 'users.id', 'room_bookings.user_id')
        ->select('room_bookings.*','users.name as user_name','rooms.title as room_title')
        ->first();
        if(empty($booking_detail)){
            return  abort(404);
        }
        return view('back.'.config('bookdose.theme_back').'.modules.room.booking.form', compact('booking_detail'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'booking_id' => 'required',
            'reserve_title' => 'required',
        ]);
        $booking_id = $validatedData['booking_id'];
        $reserve_title = $validatedData['reserve_title'];
        $data['title'] = $validatedData['reserve_title'];

        $booking = RoomBooking::where('id', $booking_id)->update($data);
        if($booking){
            //--- Start log ---//
            $log = collect([ (object)[
                'module' => 'RoomBooking', 
                'severity' => 'Info', 
                'title' => 'UpdateReservation', 
                'desc' => '[Succeeded] - '.$data['title'],
            ]])->first();
            parent::Log($log);
            //--- End log ---//
            return redirect()->route('admin.room.booking.edit', ['booking_id' => $booking_id])->with('success', $reserve_title.' is successfully updated.');
        }
    }
     
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required',
            'reserve_title' => 'required|max:255',
            'reserve_user_id' => 'required',
            'date_booking' => 'required',
            'time_from' => 'nullable',
            'time_to' => 'nullable',
        ]);
        $reserve_user_id = $validatedData['reserve_user_id'];
        $reserve_title = $validatedData['reserve_title'];
        $room_id = $validatedData['room_id'];
        $date_booking = $validatedData['date_booking'];
        $time_from = $validatedData['time_from'];
        $time_to = $validatedData['time_to'];

        $date_booking = date('Y-m-d',strtotime($date_booking));
        $start_date =  $date_booking." ".$time_from;
        $end_date =  $date_booking." ".$time_to;
    
        $start_datetime = date('Y-m-d H:i:s',strtotime($start_date));
        $end_datetime = date('Y-m-d H:i:s',strtotime($end_date));
        
        $user_id = Auth::user()->id;
        if(empty($user_id)){
            return redirect()->route('login');
        }

        // dd($user_id);
        $room = Room::active()->myOrg()
        ->where('id', $room_id)
        ->first();

        if(empty($room)){
            return redirect()->route('admin.room.booking.create', ['room_id' => $room_id])->with('error', 'Oops! Room not found. Please refresh this page and then try again.');
        }

        $today_date = now();
        $today_date = date('Y-m-d', strtotime($today_date));

        $today_date_from = $today_date." 00:00:00";
        $today_date_to = $today_date." 23:59:59";
        
        $count_booking = RoomBooking::active()->where('user_id', $reserve_user_id)
        ->whereDate('start_datetime', '=', $date_booking)
        ->count();

        // check booking count
        // if($count_booking >= 2){
        //     return redirect()->route('admin.room.booking.create', ['room_id' => $room_id])->with('error', 'Oops! Booking count is limit 2 time per day.');
        // }

        $check_booking = RoomBooking::active()->where('room_id', $room_id)
        ->whereDate('start_datetime', '=', $date_booking)
        ->get();
        
        foreach($check_booking as $item){
        
            if($start_datetime >= $item->start_datetime){
                if($end_datetime <= $item->end_datetime){
                    return redirect()->route('admin.room.booking.create', ['room_id' => $room_id])->with('error', 'Oops! It has been booked during this period.');
                }

                if($start_datetime < $item->end_datetime){
                    return redirect()->route('admin.room.booking.create', ['room_id' => $room_id])->with('error', 'Oops! It has been booked during this period.');
                }
            }

            if($start_datetime < $item->start_datetime){
                if($end_datetime > $item->start_datetime){
                    return redirect()->route('admin.room.booking.create', ['room_id' => $room_id])->with('error', 'Oops! It has been booked during this period.');
                }
            }
            
        }
        
        $data['title'] = $reserve_title;
        $data['start_datetime'] = $start_datetime;
        $data['end_datetime'] = $end_datetime;
        $data['room_id'] = $room_id;
        $data['user_id'] = $reserve_user_id;
        $data['status'] = 1;
        $data['created_by'] = $user_id;	
        $rs = RoomBooking::create($data);
;
        if($rs){
            //--- send email Registration to user---//

            $email_data_fields = [
                'booking_title' => $rs->title,
                'room_title' => $room->title,
                'start_datetime' => $rs->start_datetime,
                'end_datetime' => $rs->end_datetime,
            ];

            // 1. Send email to sender
            $this_user = User::where('id', $reserve_user_id)->first();
            if ($this_user && !empty($this_user->email)) {
                $room_booking = RoomBooking::with(['room','user'])->where('id', $rs->id)->first();
                Mail::to($this_user->email)->send(new RoomReservation($room_booking));
            }
            //--- End send email Registration ---//

            
            //--- Start log ---//
            $log = collect([ (object)[
                'module' => 'RoomBooking', 
                'severity' => 'Info', 
                'title' => 'Reservation',
                'desc' => '[Succeeded] - '.$room->title,
             ]])->first();
            parent::Log($log);
            //--- End log ---//

            return redirect()->route('admin.room.booking.edit', ['booking_id' => $rs->id])->with('success', $reserve_title.' is successfully booked.');

        } 	
    }
 
    public function ajaxGetData(Request $request) 
    {
        $order = $request->order;
        $columns = $request->columns;
        if (isset($columns) && isset($order[0]['column'])) {
            Session::put('sort_column', $columns[$order[0]['column']]['name']);
            Session::put('sort_by', $order[0]['dir']);
        }

        $filter_title = $request->filter_title ?? '';
        $filter_reserve_start = $request->filter_reserve_start ?? '';
        $filter_reserve_end = $request->filter_reserve_end ?? '';

        $query = RoomBooking::select('room_bookings.*','users.name as user_name','rooms.title as room_title')
        ->leftJoin('rooms', 'rooms.id', 'room_bookings.room_id')
        ->leftJoin('users', 'users.id', 'room_bookings.user_id');


        if ($filter_title !== '') {
            $query = $query->where('rooms.id', $filter_title);
        }
    
        if (!empty($filter_reserve_start)) {
            $_arr = explode("/", $filter_reserve_start);
            if (count($_arr) == 3) 
                $query = $query->whereDate('room_bookings.start_datetime', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
        }

        if (!empty($filter_reserve_end)) {
            $_arr = explode("/", $filter_reserve_end);
            if (count($_arr) == 3) 
                $query = $query->whereDate('room_bookings.end_datetime', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
        }
        // $query = $query->orderBy('room_bookings.start_datetime', 'desc');

        $datatable = new DataTables;
        return $datatable
            ->eloquent($query)
            ->addColumn('title_action', function ($row) {

                return '<a href="'.route('admin.room.booking.edit', ['booking_id' => $row->id]).'" class="">'.$row->room_title.'</a>';
            })
            ->addColumn('date_html', function ($row) {
                return !empty($row->start_datetime) ? date('d/m/Y', strtotime($row->start_datetime)) : '';
            })
            ->addColumn('time_start_html', function ($row) {
                return !empty($row->start_datetime) ? date('H:i', strtotime($row->start_datetime)) : '';
            })
            ->addColumn('time_end_html', function ($row) {
                return !empty($row->end_datetime) ? date('H:i', strtotime($row->end_datetime)) : '';
            })
            ->addColumn('title_html', function ($row) {
                return $row->title;
            })
            ->addColumn('user_name_html', function ($row) {
                return $row->user_name;
            })
            ->addColumn('status_html', function ($row) {
                if ($row->status == 1)
                    return '<div class="badge badge-success">จอง</div>';
                else
                return '<div class="badge badge-danger">ยกเลิก</div>';
            })
            ->addColumn('actions', function ($row){
                if ($row->status == 1)
                    return '
                <span class="dropdown">
                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                        <i class="la la-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-ban"></i> Cancel Reservation</a>
                    </div>
                </span>';
                // else
                //     return '
                // <span class="dropdown">
                //     <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                //         <i class="la la-ellipsis-h"></i>
                //     </a>
                //     <div class="dropdown-menu dropdown-menu-right">
                //         <a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
                //     </div>
                // </span>';
            })
            ->rawColumns(['title_action', 'date_html', 'time_start_html', 'time_end_html', 'title_html', 'user_name_html', 'status_html', 'actions'])
            ->addIndexColumn()
            ->make(true);

    }
 
    public function setStatus(Request $request)
    {
        $id = $request->input('id');
        if (!empty($id)) {
            $room_booking = RoomBooking::with(['room', 'user'])->where('id', $id)->firstOrFail();
            $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
            $rs = RoomBooking::where('id', $id)->update($update_data);
            if ($rs) {
                //--- send email Registration to user---//
                // 1. Send email to sender
                if ($room_booking->user && !empty($room_booking->user->email)) {
                    Mail::to($room_booking->user->email)->send(new RoomCancelReservation($room_booking));
                }
                //--- End send email Registration ---//			

                //--- Start log ---//
                 $log = collect([ (object)[
                      'module' => 'RoomBooking', 
                      'severity' => 'Info', 
                      'title' => 'Update status', 
                      'desc' => '[Succeeded] - '.$room_booking->title,
                   ]])->first();
                  parent::Log($log);
                  //--- End log ---//

                return json_encode(array(
                    'status' => 200,
                    'notify_title' => 'Hooray!',
                    'notify_msg' => 'This booking has been cancel.',
                    'notify_icon' => 'icon la la-check-circle',
                    'notify_type' => 'success',
                ));
            }
        }
        //--- Start log ---//
         $log = collect([ (object)[
              'module' => 'RoomBooking', 
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

    public function exportToExcel(Request $request) 
    {
        $room_id = $request->input('hd_room_id');
        $reserve_start = $request->input('hd_reserve_start') ?? '';
        $reserve_end = $request->input('hd_reserve_end') ?? '';
        $keyword = $request->input('hd_keyword') ?? '';
        $sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
          return Excel::download(new RoomBookingExport($room_id, $reserve_start, $reserve_end, $keyword, $sort_by), 'report_roombooking_'.now().'.xlsx');
    }

    public function ajaxGetUserOrg(Request $request){
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data =User::active()->myOrg()->select("id","name", "email")
                    ->where(function ($q) use($search){
                        $q->where('name','LIKE',"%$search%")
                        ->orWhere('email', 'LIKE', "%$search%");
                    })
            		->get();
        }
        return response()->json($data);
    }


    // public function destroy(Request $request)
    // {
    //     $id = $request->input('id');

    //     if (!empty($id)) {
    //         $booking = RoomBooking::with(['room', 'user'])->where('id', $id)->firstOrFail();
    //         if($booking->id){
    //         $rs = RoomBooking::where('id', $booking->id)->delete();
    //             if ($rs) {
    //                 //--- send email Registration to user---//
    //                 // 1. Send email to sender
    //                 if ($booking->user && !empty($booking->user->email)) {
    //                     Mail::to($booking->user->email)->send(new RoomCancelReservation($booking));
    //                 }
    //                 //--- End send email Registration ---//							

    //                 //--- Start log ---//
    //                 $log = collect([ (object)[
    //                     'module' => 'RoomBooking', 
    //                     'severity' => 'Info', 
    //                     'title' => 'DeleteReservation', 
    //                     'desc' => '[Succeeded] - '.$booking->title,
    //                 ]])->first();
    //                 parent::Log($log);
    //                 //--- End log ---//

    //                 return json_encode(array(
    //                     'status' => 200,
    //                     'notify_title' => 'Hooray!',
    //                     'notify_msg' => $booking->title.' has been deleted successfully.',
    //                     'notify_icon' => 'icon la la-check-circle',
    //                     'notify_type' => 'success',
    //                 ));
    //             }
    //         }
    //     }
    //     //--- Start log ---//
    //      $log = collect([ (object)[
    //           'module' => 'RoomBooking', 
    //           'severity' => 'Error', 
    //           'title' => 'Delete', 
    //           'desc' => '[Failed] - Invalid id = '.$id,
    //        ]])->first();
    //       parent::Log($log);
    //       //--- End log ---//

    //     return json_encode(array(
    //         'status' => 500,
    //         'notify_title' => 'Oops!',
    //         'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
    //         'notify_icon' => 'icon la la-warning',
    //         'notify_type' => 'danger',
    //     ));
    // }
}
