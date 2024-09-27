<?php

namespace App\Listeners;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserLoggedIn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
    	// echo '<pre>'; print_r($event->user); echo '</pre>'; exit;
    	$event->user->last_login_at = Carbon::now();
    	$event->user->last_login_ip = request()->ip();
    	$event->user->save();

    	$user = User::with('roles')->where('id', $event->user->id)->first();
		if ($user->roles->isEmpty()) {
			$default_role = Role::where('is_default', 1)->first();
			if (isset($default_role->name)) {
				$user->assignRole($default_role->name);
				
				$event->user->user_role_id = $default_role->id;
    			$event->user->save();
			}
		}
    }
}
