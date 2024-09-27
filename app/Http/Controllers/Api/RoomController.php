<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\RoomBooking;
use App\Models\RewardEarningHistory;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\RoomCancelReservation;
use Illuminate\Support\Facades\Mail;

class RoomController extends ApiController
{
    public function cancelReserve(Request $request)
    {
        $token = request()->token ?? '';
		$token_api = request()->token_api ?? '';
		$booking_id = request()->booking_id ?? '';
		$username = request()->username ?? '';

		if (empty($token) && empty($token_api)) {
			$err['msg'] = 'Missing token.';
			return response()->json($err, 500);
		}
		if (empty($booking_id)) {
			$err['msg'] = 'Missing booking_id';
			return response()->json($err, 500);
		}

		if (!empty($token))
			$user = User::where('temp_token', $token)->first();
		else
			$user = User::where('id', $token_api->id)->first();
        

        $room_booking = RoomBooking::with(['room','user'])->where('id', $booking_id)->first();
		if ($user && $room_booking) {
            $q = RoomBooking::where('id', $room_booking->id)->update(['status' => 0]);

            // 1. Send email to sender
            if ($user && !empty($user->email)) {
                Mail::to($user->email)->send(new RoomCancelReservation($room_booking));
            }
            //--- End send email Registration ---//
            
			if ($q) {
				return response()->json([
					'status' => 'success',
					'results' => 'Updated avater successfully.',
				]);
			} else {
				return response()->json([
					'status' => 'error',
					'results' => 'Oops! Something went wrong, please refresh this page and then try again.',
				]);
			}
		}

		return response()->json([
				'status' => 'error',
				'results' => 'User & Booking not found.',
		]);
    }

}
