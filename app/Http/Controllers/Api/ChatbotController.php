<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use Session;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Chatbot\ChatbotExport;
use App\Models\LogChatSession;
use App\Models\LogChatbot;
use App\Models\LogChatadmin;

class ChatbotController extends ApiController
{
     public function ajax_register_session(Request $request){
		$session_id = $request->input('sessionID');
		$email = $request->input('email');
		$gender = $request->input('gender');
		$country = $request->input('country');
		$chat_type = $request->input('chatType');

		$chat = new LogChatSession();
		$chat->session_id = $session_id;
		$chat->email = $email;
		$chat->gender = $gender;
		$chat->country = $country;
		$chat->chat_type = $chat_type;
		$chat->ip = request()->ip();
		$chat->save();

		return response()->json([
			'status' => 'success',
			'results' => $chat ?? []
		]);
	}

	public function ajax_save_log_chatbot(Request $request)
	{
        $data['session_id'] = $request->sessionID;
        $data['user_type'] = $request->from;
        $data['message'] = $request->id;
        $data['intent_name'] = $request->name;
        $data['action'] = $request->action;
        $data['data_type'] = $request->dataType;
        $data['data'] = $request->data;
        $data['ip'] = request()->ip();

        $log = LogChatadmin::create($data);
        
        return response()->json( [
            'status' => 'success',
            'msg' => '',
            'result' => $data,
        ]);
 	}
    
	public function ajax_save_log_chatadmin(Request $request)
	{
        $data['session_id'] = $request->sessionID;
        $data['user_type'] = $request->from;
        $data['message'] = $request->data['text'];

        $log = LogChatadmin::create($data);
        
        return response()->json( [
            'status' => 'success',
            'msg' => '',
            'result' => $data,
        ]);
 	}

    public function ajax_get_msg_chatadmin(Request $request)
    {
        $data['session_id'] = $request->sessionID;
        $data['user_type'] = $request->from;
        $data['is_read'] = 0;
        
		$result = array();
		$result = LogChatadmin::where($data)
			->orderBy('updated_at','ASC')
			->get();
        $result->each(function ($item) {
            $item->update(['is_read'=>1]);
        });

        return response()->json( [
            'status' => 'success',
            'msg' => '',
            'result' => $result,
        ]);
    }
}
