<?php

namespace App\Http\Controllers\Front\Event;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventJoin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Front\FrontController;
use App\Models\RewardRedemptionHistory;
use Illuminate\Support\Facades\Redirect;

class EventController extends FrontController
{
	public function acceptInvitation(Request $request, $code) {
		$code = $request->code ?? '';
		$invitation = EventJoin::where('invitation_code', $code)->first();
		if ($invitation) {
			if ($invitation->user_id == Auth::user()->id) {
				$event = Event::active()->findOrFail($invitation->event_id);
				EventJoin::where('invitation_code', $code)->update([
					'joined_at' => now(),
				]);
				
				// session(['invitation_accepted' => true, 'invitation_accepted_event_title' => $event->title]);
				$request->session()->put('invitation_accepted', true);
				$request->session()->put('invitation_accepted_event_title', $event->title);
				if(!empty(config('bookdose.app.km_url'))){
					return Redirect::to(config('bookdose.app.km_url').'/event');
				}else{
					return redirect(route('belib.event.index'));
				}
				

				// return view('front.'.config('bookdose.theme_front').'.modules.event.invitation_accepted', compact('event'));
			}
			abort('403', 'Invalid user');
		}
		abort('403', 'Invalid invitation code');
	}

}
