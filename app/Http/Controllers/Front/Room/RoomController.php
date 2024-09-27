<?php
namespace App\Http\Controllers\Front\Room;

use App\Http\Controllers\Front\FrontController;
use DB;
use Auth;

use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\RoomGallery;
use App\Models\RoomType;
use App\Models\User;
use App\Models\UserOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

use DateTime;
use PhpParser\Node\Expr\FuncCall;

use Illuminate\Support\Facades\Mail;
use App\Mail\RoomReservation;

class RoomController extends FrontController
{
	public function index()
    {
        //site attr
        $breadcrumbs = [
	        __('menu.front.room') => "",
		];
        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        // dd($breadcrumbs, $footer);

        $room_type =  RoomType::active()->myOrg()->get();
        // dd($room_type);
        return view('front.'.config('bookdose.theme_front').'.modules.room.main', compact('breadcrumbs', 'footer', 'room_type'));
    }

    public function ajaxRoomName(Request $request)
	{
		if($request->ajax())
		{
			$room_type_id = $request->room_type_id;
			$room_name = Room::active()->myOrg()->where('room_type_id', $room_type_id)->orderBy('title', 'ASC')->get();
			return response()->json($room_name);
		}
	}

    public function fetchList(Request $request)
    {
        $limit = $request->limit ?? 1;
        $content = Room::with(['room_galleries','room_type'])->active()->myOrg()->paginate(1);
        return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content'))->render();
    }

    public function searchRoom(Request $request)
	{
		if($request->ajax())
		{
            $limit = $request->limit ?? 9;

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
			// dd($replace_time_from, $replace_time_to);
			// dd($room_type_id, $room_id, $date_booking, $start_datetime, $end_datetime);
			$booking_data = array(
				'date_booking' => $date_booking,
				'time_from' => $time_from,
				'time_to' => $time_to,
			);
			$content = Room::with(['room_galleries','room_type'])->active()->myOrg()
			->sortTitle();


			// echo '<pre>'; print_r($room_list->toSql()); echo '</pre>'; exit;
			// $room_list = $room_list->whereDate('start_datetime', '=', $date_booking);

			if(!empty($room_type_id) && empty($room_id) && !empty($date_booking) && empty($time_from) &&  empty($time_to)){
				$content = $content->where('room_type_id', $room_type_id);
				// dd($room_list->toSql());
				$content = $content->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($room_type_id) && empty($room_id) && !empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
				$check_booking = RoomBooking::whereDate('start_datetime', '=', $date_booking)
                ->whereHas('room', function($query){
                    $query->myOrg();
                })
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

				$content = $content->where(function($query) use ($replace_time_from, $replace_time_to){
					$query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
					->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
					->orWhere('open_time', null);
				})
				->where('room_type_id', $room_type_id)
				->whereNotIn('id', $arr_room_id)->paginate($limit);
				//dd($room_list->toSql());
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($room_id) && !empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
				$check_booking = RoomBooking::where('room_id', $room_id)
				->whereDate('start_datetime', '=', $date_booking)
                ->whereHas('room', function($query){
                    $query->myOrg();
                })
				->get();

				foreach($check_booking as $item){

					if($start_datetime >= $item->start_datetime){
						if($end_datetime <= $item->end_datetime){
							$content=array();
                            return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
						}

						if($start_datetime < $item->end_datetime){
							$room_list=array();
                            return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
						}
					}

					if($start_datetime < $item->start_datetime){
						if($end_datetime > $item->start_datetime){
							$room_list=array();
                            return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
						}
					}

				}
				$content = $content->where('id', $room_id)
				->where(function($query) use ($replace_time_from, $replace_time_to){
					$query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
					->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
					->orWhere('open_time', null);
				});
				//dd($content->toSql());
				$content = $content->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($date_booking) && !empty($time_from) &&  !empty($time_to)){
				$check_booking = RoomBooking::whereDate('start_datetime', '=', $date_booking)
                ->whereHas('room', function($query){
                    $query->myOrg();
                })
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

				$content = $content->where(function($query) use ($replace_time_from, $replace_time_to){
					$query->whereRaw("REPLACE(open_time, ':', '.') <= ?",[$replace_time_from])
					->whereRaw("REPLACE(closed_time, ':', '.') >= ?",[$replace_time_to])
					->orWhere('open_time', null);
				})
				->whereNotIn('id', $arr_room_id)->paginate($limit);
				//dd($room_list->toSql());
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($room_id) && !empty($date_booking) && empty($time_from)){
				$content = $content->where('id', $room_id)->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($room_type_id)){
				$content = $content->where('room_type_id', $room_type_id)->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($room_id)){
				$content = $content->where('id', $room_id)->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			if(!empty($date_booking)){
				$content = $content->paginate($limit);
                return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
			}

			$content = $content->paginate($limit);
            return view('front.'.config('bookdose.theme_front').'.modules.room.load_room_list', compact('content', 'booking_data'))->render();
		}
	}

	public function detail(Request $request)
	{
		$date_booking = $request->date_booking;
		$time_from = $request->time_from;
		$time_to = $request->time_to;
		$booking_data = array(
			'date_booking' => $date_booking,
			'time_from' => $time_from,
			'time_to' => $time_to,
		);
		$now_date = date('Y-m-d', strtotime(now()));
		$room_slug = $request->slug;
		// dd($room_slug, $booking_data, $now_date);
		$room_detail = Room::with(['room_galleries','room_type'])->active()->myOrg()
		->where('slug', $room_slug)
		->firstorFail();

		//site attr
		$breadcrumbs = [
	        __('menu.front.room') => route('room.index'),
            $room_detail->title => "",
		];
		$footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();

		$room_booking = RoomBooking::active()
		->whereHas('room', function($query){
			$query->myOrg();
		})
		->where('room_id', $room_detail->id)
		->where('start_datetime','>=',$now_date)
		->get();

		$data_calendar = array();
		if(!empty($room_booking)){
			foreach($room_booking as $row){
				$start = $row->start_datetime;
				$end = $row->end_datetime;
				$data_calendar[] = array("title"=> $row->title, "allDay"=> false, "start"=> $start, "end"=> $end);
			}
		}

		$room_setting = Auth::user()->group;

		return view('front.'.config('bookdose.theme_front').'.modules.room.detail.detail', compact('breadcrumbs', 'footer', 'room_detail','data_calendar','booking_data', 'room_setting'));
	}

	private function calReserveTime($start_date, $end_date)
	{
		$datetime1 = new DateTime($start_date);
		$datetime2 = new DateTime($end_date);
		$interval = $datetime1->diff($datetime2);
		$hour = $interval->h;
		$i = $interval->i;
		$minute = ($i*100)/60;
		$cal_time = $hour.'.'.$minute;
		return $cal_time;
	}

	private function calBooking($user_id, $date_booking)
	{
		$get_booking = RoomBooking::where('user_id', $user_id)
		->whereDate('start_datetime', '=', $date_booking)
		->get();
		$count_booking = $get_booking->count();
		$total_time = 0;
		foreach ($get_booking as $value) {
			$datetime1 = new DateTime($value->start_datetime);
			$datetime2 = new DateTime($value->end_datetime);
			$interval = $datetime1->diff($datetime2);
			$hour = $interval->h;
			$i = $interval->i;
			$minute = ($i*100)/60; //convert to decimal
			$cal_time = $hour.'.'.$minute;
			$total_time = $total_time + $cal_time;
		}
		$data["count_booking"] = $count_booking;
		$data["total_time"] = $total_time;
		return $data;
	}

	public function checkRoom(Request $request)
	{
		$room_slug = $request->room_slug;
		$date_booking = $request->date_booking;
		$date_booking = date('Y-m-d',strtotime($date_booking));
		$time_from = $request->time_from;
		$time_to = $request->time_to;
		$start_date =  $date_booking." ".$time_from;
		$end_date =  $date_booking." ".$time_to;
		$reserve_time = $this->calReserveTime($start_date, $end_date);

		$start_datetime = date('Y-m-d H:i:s',strtotime($start_date));
		$end_datetime = date('Y-m-d H:i:s',strtotime($end_date));

		//room_setting
		$room_setting = Auth::user()->group;
		$setting_per_day = $room_setting->data_rooms['per_day'] ?? config('bookdose.room.per_day');
		$setting_max_hour = $room_setting->data_rooms['max_hour'] ?? config('bookdose.room.max_hour');

		//recheck login
		$user_id = Auth::user()->id;
		if(empty($user_id)){
			return redirect()->route('login');
		}

		$room = Room::active()->myOrg()
		->where('slug', $room_slug)
		->firstorFail();

		if(empty($room)){
			$success = false;
			$header = __('room.found_error');
			$message = __('room.room_not_found');
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		$today_date = now();
		$today_date = date('Y-m-d', strtotime($today_date));

		$today_date_from = $today_date." 00:00:00";
		$today_date_to = $today_date." 23:59:59";

		// get count booking & cal booking
		$get_booking = $this->calBooking($user_id, $date_booking);
		$count_booking = $get_booking["count_booking"];
		$total_time = $get_booking["total_time"];
		$total_time = $total_time + $reserve_time;

		// $count_booking >= (time per day)
		if($count_booking >= $setting_per_day){
			$success = false;
			$header = __('room.found_error');
			$message = str_replace(':num', $setting_per_day, __('room.in_advance_day'));
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		// $total_time > (hr per day)
		if($total_time > $setting_max_hour){
			$success = false;
			$header = __('room.found_error');
			$message = str_replace(':num', $setting_max_hour, __('room.max_hour'));
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		$check_booking = RoomBooking::active()
		->whereHas('room', function($query){
			$query->myOrg();
		})
		->where('room_id', $room->id)
		->whereDate('start_datetime', '=', $date_booking)
		->get();

		foreach($check_booking as $item){

			if($start_datetime >= $item->start_datetime){
				if($end_datetime <= $item->end_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}

				if($start_datetime < $item->end_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}
			}

			if($start_datetime < $item->start_datetime){
				if($end_datetime > $item->start_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}
			}

		}
		$date_booking_msg = date('d/m/Y',strtotime($date_booking));
		$success = true;
		$header = __('room.result');
		if(app()->getLocale() == 'th'){
			$message = 'ห้อง '.$room->title.' สามารถใช้งานได้ในช่วงระหว่าง วันที่ '.$date_booking_msg.' เวลา '.$time_from.'-'.$time_to.'น.';
		}else{
			$message = $room->title.' can be used during '.$date_booking_msg.' from '.date("h:i:sa", strtotime($time_from)).' to '.date("h:i:sa", strtotime($time_to));
		}
		//  Return response
		return response()->json([
			'success' => $success,
			'message' => $message,
			'header' => $header,
			'date_reserve' => $date_booking,
			'start_datetime' => $start_datetime,
			'end_datetime' => $end_datetime,
		]);

	}

	public function reserveRoom(Request $request)
	{
		$room_slug = $request->room_slug;
		$title = $request->title;
		$date_booking = $request->date_reserve;
		$start_datetime = $request->start_datetime;
		$end_datetime = $request->end_datetime;
		$reserve_time = $this->calReserveTime($start_datetime, $end_datetime);

		//room_setting
		$room_setting = Auth::user()->group;
		$setting_per_day = $room_setting->data_rooms['per_day'] ?? config('bookdose.room.per_day');
		$setting_max_hour = $room_setting->data_rooms['max_hour'] ?? config('bookdose.room.max_hour');
		//recheck
		$user_id = Auth::user()->id;
		if(empty($user_id)){
			return redirect()->route('login');
		}

		$room = Room::active()->myOrg()
		->where('slug', $room_slug)
		->first();

		if(empty($room)){
			$success = false;
			$header = __('room.found_error');
			$message = __('room.room_not_found');
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		$today_date = now();
		$today_date = date('Y-m-d', strtotime($today_date));

		$today_date_from = $today_date." 00:00:00";
		$today_date_to = $today_date." 23:59:59";

		// get count booking & cal booking
		$get_booking = $this->calBooking($user_id, $date_booking);
		$count_booking = $get_booking["count_booking"];
		$total_time = $get_booking["total_time"];
		$total_time = $total_time + $reserve_time;

		// $count_booking >= (time per day)
		if($count_booking >= $setting_per_day){
			$success = false;
			$header = __('room.found_error');
			$message = str_replace(':num', $setting_per_day, __('room.in_advance_day'));
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		// $total_time <= (hr per day)
		if($total_time > $setting_max_hour){
			$success = false;
			$header = __('room.found_error');
			$message = str_replace(':num', $setting_max_hour, __('room.max_hour'));
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}

		$check_booking = RoomBooking::where('room_id', $room->id)
		->whereDate('start_datetime', '=', $date_booking)
		->get();

		foreach($check_booking as $item){

			if($start_datetime >= $item->start_datetime){
				if($end_datetime <= $item->end_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}

				if($start_datetime < $item->end_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}
			}

			if($start_datetime < $item->start_datetime){
				if($end_datetime > $item->start_datetime){
					$success = false;
					$header = __('room.found_error');
					$message = __('room.others_reserve_on_time');
					//  Return response
					return response()->json([
						'success' => $success,
						'message' => $message,
						'header' => $header,
					]);
				}
			}

		}

		$data['title'] = $title;
		$data['start_datetime'] = $start_datetime;
		$data['end_datetime'] = $end_datetime;
		$data['room_id'] = $room->id;
		$data['user_id'] = $user_id;
		$data['status'] = 1;
		$data['created_by'] = $user_id;
		$rs = RoomBooking::create($data);
		if($rs){


			//--- send email Registration to user---//

			$email_data_fields = [
				'booking_title' => $rs->title,
				'room_title' => $room->title,
				'start_datetime' => $rs->start_datetime,
				'end_datetime' => $rs->end_datetime,
			];

			// 1. Send email to sender
			$this_user = User::where('id', Auth::user()->id)->first();
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


			$success = true;
			$header = __('room.reserve_success');
			$message = __('room.reserve_save');
			//  Return response
			return response()->json([
				'success' => $success,
				'message' => $message,
				'header' => $header,
			]);
		}
	}

	public function cancelReservation(Request $request)
	{

		//please see this route as below
		// Route::prefix('room')->name('api.room.')->group(function () {
		// 	Route::post('cancel/reserve', 'RoomController@cancelReserve')->name('cancelReserve');
		// });
		// dd($request->booking);
		$message = 'success';
		return response()->json([
			'statusCode' => 200,
			'message' => $message,
			'booking' => $request->booking,

		]);
	}
}
