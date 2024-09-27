<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DeviceRegister;

class DeviceController extends ApiController
{
    public function store(Request $request)
    {
        // 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (! $payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';
        elseif (empty(request()->lang))
            $return_data['msg'] = 'Missing lang';

		$device = request()->device ?? '';
		if (empty($device) || !in_array($device, ['android', 'ios'])) {
			$return_data['msg'] = 'Missing or invalid device';
		}
		$device_id = request()->device_id ?? '';
		if (empty($device_id)) {
			$return_data['msg'] = 'Missing parameter. Please specify device_id';
		}
		$device_token = request()->device_token ?? '';
		if (empty($device_token)) {
			$return_data['msg'] = 'Missing parameter. Please specify device_token';
		}

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $register = DeviceRegister::updateOrCreate([
            "device" => $request->device,
            "device_id" => $request->device_id,
            "device_token" => $request->device_token,
            "status" => 1,
            "user_id" => $payload->id,
        ]);

        $lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
        if ($register) {
            return response()->json( [
               'status' => 'success',
               'label' => [
                   "msg" => __('device.register_success', [], $lang),
               ],
           ]);
        }
        else {
           return response()->json( [
               'status' => 'error',
               'label' => [
                   "msg" => __('device.failed', [], $lang),
               ],
           ]);
       }
    }

    public function destroy(Request $request)
    {
        // 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (! $payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';
        elseif (empty(request()->lang))
            $return_data['msg'] = 'Missing lang';
        elseif (empty(request()->device) || !in_array(request()->device, ['android', 'ios']))
            $return_data['msg'] = 'Missing or invalid device.';
        elseif (empty(request()->device_id))
            $return_data['msg'] = 'Missing parameter. Please specify device_id.';
        elseif (empty(request()->device_token))
            $return_data['msg'] = 'Missing parameter. Please specify device_token.';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $data_where = [
            "device" => $request->device,
            "device_id" => $request->device_id,
            "device_token" => $request->device_token,
            "user_id" => $payload->id,
            "status" => 1,
        ];
        DeviceRegister::where($data_where)->update(['status' => 0]);

        $lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
        return response()->json([
			'status' => 'success',
			'label' => [
                "msg" => __('device.remove_success', [], $lang),
            ]
		]);
    }
}
