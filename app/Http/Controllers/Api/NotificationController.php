<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\NotificationUser;
use App\Models\DeviceRegister;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationController extends ApiController
{
    public function listMaster()
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
        $items = Notification::active()->select('id', 'system', 'slug', 'title')->get();
        return response()->json( [
            'status' => 'success',
            'results' => $items ?? [],
        ]);
    }

    public function pushNotification(Request $request)
    {
        $return_data = array('status' => 'error', 'msg' => '');
        $payload = null;
        $token = null;
        $user_id = null;
        $users_id = [];
        $is_all = false;

        $device_list = [];
        $device_android = [];
        $device_ios = [];

        //link for content when click noti item ( column: url)
        $link = $request->link ?? '';

        $user_id = $request->user_id;
        if (empty($user_id)) {
            $token = $request->token;
            if (empty($token)) {
                $return_data['msg'] = 'Missing token';
            } elseif ($token == 'all') {
                $is_all = true;
                $users_id = User::active()->get()->pluck('id');
            } elseif (!$payload = parent::parseJWT()) {
                $return_data['msg'] = 'Invalid token';
            }
        } else if($user_id == 'all') {
            $is_all = true;
            $users_id = User::active()->get()->pluck('id');
        } else {
            $is_all = false;
            $users_id = [$user_id];
        }

        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $users_id = [$payload->id];
        }

        $message = $request->message;

        // Use this when system have function setting active/inactive each notification/user on app
        $noti_status = 1;
        $notification = Notification::active()
                            ->where('slug', $request->notification_slug)
                            ->where('system', $request->system)
                            ->active()
                            ->first();

        foreach($users_id as $user_id) {
            if($notification) {
                $noti_user = NotificationUser::where('user_id', $user_id)
                            ->where('notification_id', $notification->id)
                            ->select('status')
                            ->first();
                $noti_status = $noti_user->status ?? 1;
            } else {
                if(!$is_all) {
                    $err['status'] = 'warning';
                    $err['msg'] = 'This notification function is inactive';
                    $err['msg_th'] = 'ฟังก์ชั่นการแจ้งเตือนนี้ปิดการใช้งาน';
                    $err['result'] = [];

                    return response()->json($err, 200);
                }
            }

            if($noti_status) {
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
                        'updated_by' => $user_id ?? null,
                        'url' => $link
                    ]);
                }elseif(config('bookdose.notification.has_app') == false){
                    NotificationLog::create([
                        'user_org_id' => 1,
                        'user_id' => $user_id ?? null,
                        'notification_id' =>  $notification->id,
                        'message' => $message,
                        'data_devices' => null,
                        'created_by' => $user_id ?? null,
                        'updated_by' => $user_id ?? null,
                        'url' => $link
                    ]);
                }

                if(count($device_android) == 0 && count($device_ios) == 0 && !$is_all) {
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
                if(!$is_all) {
                    $err['status'] = 'warning';
                    $err['msg'] = 'This notification is inactive';
                    $err['msg_th'] = 'การแจ้งเตือนนี้ปิดการใช้งาน';
                    $err['result'] = [];

                    return response()->json($err, 500);
                }
            }
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
        $system = $request->system ?? '';

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
            $count_unread = NotificationLog::where('user_id', $user_id)->unread();
            if($system){
                $count_unread = $count_unread->whereHas('notification', function($query) use ($system){
                    $query->where('system', $system);
                });
            }
            $count_unread = $count_unread->count();
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
        $system = $request->system ?? '';


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

		$id = $request->id ?? 'all';
		$update_data = array();
		$update_data['is_read'] = 1;
		$rs_noti = NotificationLog::where('user_id', $user_id);
			if($id != "all"){
               $rs_noti = $rs_noti->where('id', $id);
            }
			$rs_noti = $rs_noti->update($update_data);

        $count_unread = NotificationLog::where('user_id', $user_id)->unread();
        if($system){
            $count_unread = $count_unread->whereHas('notification', function($query) use ($system) {
                $query->where('system', $system);
            });
        }
        $count_unread = $count_unread->count();

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

        $system = $request->system ?? '';

        $limit = $request->limit ?? 10;
        $page = trim($request->page);

        $notifications = NotificationLog::with(['notification' => function ($query){
                $query->select('id', 'title', 'system');
            }]);

            if($system){
                $notifications = $notifications->whereHas('notification', function($query) use ($system){
                    $query->where('system', $system);
                });
            }

            $notifications = $notifications->where('user_id', $user_id)
            ->where('is_read', 0)
			->select(array_merge(
				array('id', 'notification_id', 'message', 'url', 'is_read', 'user_id'),
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
            compact('notifications', 'system'))
            ->render();
        } else {
            $return_data['results'] = $notifications->items();
        }

		return response()->json($return_data);
    }

    public function getStatus()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');

        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $user_id = $payload->id;
        }

        // 2. Query
        $status_all = 1;
        $items = Notification::active()
                        ->select('id', 'title', 'weight')
                        ->get()
                        ->each(function ($item) use($user_id, &$status_all) {
                            $notification_id = $item->id;
                            $noti_user = NotificationUser::where('user_id', $user_id)
                                                ->where('notification_id', $notification_id)
                                                ->select('status')
                                                ->first();
                            $noti_status_all = $noti_user->status ?? 1;
                            if(!$noti_status_all) $status_all = 0;
                            $item->status = $noti_status_all;
                        });

        $results = [
            'status_all' => $status_all,
            'list' => $items
        ];

        return response()->json( [
            'status' => 'success',
            'results' => $results ?? [],
        ]);
    }

    public function setStatus()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');

        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';
        else if (empty(request()->notification_id))
            $return_data['msg'] = 'Missing notification_id';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data, 500);
        }

        if ($payload != null) {
            $user_id = $payload->id;
        }

        // 2. Query
        $status = request()->status ?? 1;
        $notification_id = request()->notification_id;
        $items = Notification::active()
                        ->select('id', 'title', 'weight')
                        ->when($notification_id != 'all', function($q) use($notification_id) {
                            $q->where('id', $notification_id);
                        })
                        ->get()
                        ->each(function ($item) use($payload, $user_id, $notification_id, $status) {
                            NotificationUser::updateOrCreate(
                                [
                                    "user_org_id" => $payload->user_org_id ?? 1,
                                    "user_id" => $user_id,
                                    "notification_id" => $item->id,
                                ],
                                [
                                    "user_org_id" => $payload->user_org_id ?? 1,
                                    "user_id" => $user_id,
                                    "notification_id" => $item->id,
                                    "status" => $status,
                                    "created_by" => $user_id,
                                    "updated_by" => $user_id,
                                ]
                            );
                        });

        $status_all = 1;
        $items = Notification::active()
                        ->select('id', 'title', 'weight')
                        ->get()
                        ->each(function ($item) use($user_id, &$status_all) {
                            $notification_id = $item->id;
                            $noti_user = NotificationUser::where('user_id', $user_id)
                                                ->where('notification_id', $notification_id)
                                                ->select('status')
                                                ->first();
                            $noti_status_all = $noti_user->status ?? 1;
                            if(!$noti_status_all) $status_all = 0;
                            $item->status = $noti_status_all;
                        });

        $results = [
            'status_all' => $status_all,
            'list' => $items
        ];

        return response()->json( [
            'status' => 'success',
            'results' => $results ?? [],
        ]);
    }

}
