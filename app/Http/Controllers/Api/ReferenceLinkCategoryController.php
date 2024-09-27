<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\ReferenceLinkCategories;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ReferenceLinkCategoryController extends ApiController
{
    public function list()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
                
        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $items = ReferenceLinkCategories::active()
                    ->select('id', 'slug', 'title', 'cover_image_path')
                    ->get()
                    ->each(function ($item) {
                        $item->cover_image_path = getCoverImage(config('bookdose.app.url').Storage::url($item->cover_image_path), 'information' ,true, '');
                    });
        return response()->json( [
            'status' => 'success',
            'results' => $items ?? [],
        ]);
    }

    public function pushNotification(Request $request)
    {;
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        $payload = null;
        $token = null;
        $user_id = null;
        $users_id = [];
        
        $user_id = $request->user_id;
        if (empty($user_id)) {
            $token = $request->token;
            if (empty($token)) {
                $return_data['msg'] = 'Missing token';
            } elseif ($token == 'all') {
                $users_id = User::active()->get()->pluck('id');
            } elseif (!$payload = parent::parseJWT()) {
                $return_data['msg'] = 'Invalid token';
            }
        } else if($user_id == 'all') {
            $users_id = User::active()->get()->pluck('id');
        } else {
            $users_id = [$user_id];
        }
        
        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $users_id = [$payload->id];
        }

        // 2. Check active push msg ?
        $message = $request->message;

        // Use this when system have function setting active/inactive each notification/user on app
        // $notification = NotificationUser::with(['notification'])
        //                     ->whereHas('notification', function($query) use ($request) {
        //                         $query->where('system', $request->system);
        //                         $query->where('slug', $request->notification_slug);
        //                     })
        //                     ->active()
        //                     ->first();

        $notification = Notification::active()
                            ->where('slug', $request->notification_slug)
                            ->where('system', $request->system)
                            ->first();

        // 3. Push message
        if($notification) {
            $device_list = [];
            $device_android = [];
            $device_ios = [];
            
            foreach($users_id as $user_id) {
                // Check device register
                $device_android_log = [];
                $device_ios_log = [];
                $user_org_id = '';
                $device_register = DeviceRegister::active()
                                    ->where('user_id', $user_id)
                                    ->orderBy('user_id', 'asc')
                                    ->orderBy('device', 'asc')
                                    ->get()
                                    ->each(function ($item) use(&$user_org_id, &$device_android, &$device_ios, &$device_android_log, &$device_ios_log) {
                                        $user_org_id = $item->user->user_org_id;
                                        switch($item->device) {
                                            case 'android':
                                                if($item->device_token != null && $item->device_token != "") {
                                                    $device_android[] = $item->device_token;
                                                    $device_android_log[] = $item->device_token;
                                                }
                                                break;
                                            case 'ios':
                                                if($item->device_token != null && $item->device_token != "") {
                                                    $device_ios[] = $item->device_token;
                                                    $device_ios_log[] = $item->device_token;
                                                }
                                                break;
                                        }
                                    });

                if(count($device_android_log) > 0 || count($device_ios_log) > 0) {
                    // 4. Log push message
                    $data_devices = array(
                        'android' => $device_android_log,
                        'ios' => $device_ios_log
                    );

                    $device_list[] = [
                        'user_id' =>$user_id,
                        'device_list' => $data_devices
                    ];

                    NotificationLog::create([
                        'user_org_id' => $user_org_id ?? 1,
                        'user_id' => $user_id ?? null,
                        'notification_id' =>  $notification->id,
                        'message' => $message,
                        'data_devices' => $data_devices,
                        'created_by' => $user_id ?? null,
                        'updated_by' => $user_id ?? null
                    ]);
                }
            }
            
            if(count($device_android) == 0 && count($device_ios) == 0) {
                $err['status'] = 'warning';
                $err['msg'] = 'No device register found';
                $err['msg_th'] = 'ไม่พบอุปกรณ์ที่ลงทะเบียน';
                $err['result'] = [];
                return response()->json($err, 200);
            }
            
            $hide_title = $request->hide_title;
            if($hide_title == 1) {
                $push_message = $message;
            } else {
                $push_message = $notification->title.": ".$message;
            }

            $type_resource = $request->type_resource;
            $id_resource = $request->id_resource;
            
            // Android
            if(count($device_android) > 0) {
                $result_android = pushNotification('android', $device_android, $push_message, $type_resource, $id_resource);

                if($result_android == false) {
                    $err['status'] = 'error';
                    $err['msg'] = 'Notification have problem something, please try again';
                    $err['msg_th'] = 'การแจ้งเตือนพบปัญหา กรุณาลองใหม่อีกครั้ง';
                    $err['result'] = $device_register;
                    return response()->json($err, 500);
                }
            }

            // iOS
            if(count($device_ios) > 0) {
                $result_ios = pushNotification('ios', $device_ios, $push_message, $type_resource, $id_resource);

                if($result_ios == false) {
                    $err['status'] = 'error';
                    $err['msg'] = 'Notification have problem something, please try again';
                    $err['msg_th'] = 'การแจ้งเตือนพบปัญหา กรุณาลองใหม่อีกครั้ง';
                    $err['result'] = $device_register;
                    return response()->json($err, 500);
                }
            }
        } else {
            $err['status'] = 'error';
            $err['msg'] = 'This notification is inactive';
            $err['msg_th'] = 'แจ้งเตือนข้อความปิดการใช้งาน';
            $err['result'] = [];
            return response()->json($err, 500);
        }

        return response()->json([
            'status' => 'success',
            'results' => $device_list ?? [],
        ]);
    }

    public function pushLineGroup(Request $request)
    {;
        // Get token via https://notify-bot.line.me/
        // Invite "Line Notify" into that Line group

        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        $token = config('bookdose.notification.line_token');
        $token_arr = explode(", ",$token);

        if (empty($token)) {
            $return_data['msg'] = 'Missing token';
        }
        
        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        $message = $request->message;
        $cover_image = $request->cover_image;
        
        $fields = [];
        $fields['message'] = $message;
        $fields['imageThumbnail'] = $cover_image;
        $fields['imageFullsize'] = $cover_image;

        foreach($token_arr as $token_item) {
            $response = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded', 'Authorization' => 'Bearer '.$token_item])
                            ->asForm()->post(config('bookdose.notification.line_api'), $fields);
        }
                        
        if ($response === FALSE) {
            return response()->json([
                'status' => 'error',
                'msg' => '',
                'results' => [],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'results' => [],
        ]);
    }

	public function count(Request $request)
	{
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        $payload = null;
        
        $user_id = $request->user_id;
        if (empty($user_id)) {
            $token = $request->token;
            if (empty($token)) {
                $return_data['msg'] = 'Missing token';
            } elseif ($token == 'all') {
            } elseif (!$payload = parent::parseJWT()) {
                $return_data['msg'] = 'Invalid token';
            }
        }
        
        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $user_id = $payload->id;
        }

        if(empty($user_id)) {
            $count_unread = 0;
        } else {
            $count_unread = NotificationLog::where('user_id', $user_id)->unread()->count();
        }

		return response()->json([
            'status' => 'success',
            'results' => $count_unread,
        ]);
    }

	public function setIsRead(Request $request)
	{
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        $payload = null;
        
        $user_id = $request->user_id;
        if (empty($user_id)) {
            $token = $request->token;
            if (empty($token)) {
                $return_data['msg'] = 'Missing token';
            } elseif ($token == 'all') {
            } elseif (!$payload = parent::parseJWT()) {
                $return_data['msg'] = 'Invalid token';
            }
        }
        
        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $user_id = $payload->id;
        }

		$id = $request->id;
		$update_data = array();
		$update_data['is_read'] = 1;
		NotificationLog::
			when($id != "all", function ($query, $id) {
				return $query->where('id', $id);
			})
			->where('user_id', $user_id)
			->update($update_data);
		
        $count_unread = NotificationLog::where('user_id', $user_id)->unread()->count();

		return response()->json([
            'status' => 'success',
            'results' => $count_unread,
        ]);
	}

	public function listPagination(Request $request)
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        $payload = null;

        $user_id = $request->user_id;
        if (empty($user_id)) {
            $token = $request->token;
            if (empty($token)) {
                $return_data['msg'] = 'Missing token';
            } elseif ($token == 'all') {
            } elseif (!$payload = parent::parseJWT()) {
                $return_data['msg'] = 'Invalid token';
            }
        }

        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $user_id = $payload->id;
        }
        
        $lang = $request->lang;
        if(empty($user_id)) {
            $return_data['msg'] = __('data_not_found', [], $lang);
            return response()->json($return_data);
        }

        $limit = 10;
        $page = trim($request->page);

        $notifications = $this->notifications = NotificationLog::with(['notification' => function ($query) {
                $query->select('id', 'title');
            }])
            ->where('user_id', $user_id)
			->select(array_merge(
				array('id', 'notification_id', 'message', 'url', 'is_read'),
				array(
					DB::raw('DATE_FORMAT(created_at, "%d %b %Y") AS created_date'),
					DB::raw('DATE_FORMAT(created_at, "%H:%i") AS created_time')
				)
			))
            ->orderBy('notification_logs.created_at', 'desc')
            ->orderBy('notification_logs.id', 'desc')
			->paginate($limit);

        $return_data['status'] = 'success';
        $return_data['current_page'] = $notifications->currentPage();
        $return_data['total_page'] = $notifications->lastPage();

        $is_html = $request->is_html;
        if($is_html == 1) {
            $return_data['html'] = view('front.'.config('bookdose.theme_front').'.modules.notification.box_item', 
            compact('notifications'))
            ->render();
        } else {
            $return_data['results'] = $notifications->items();
        }

		return response()->json($return_data);
    }
}
