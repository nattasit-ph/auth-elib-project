<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\SendEventInvitationEmail;
use Mail;
use Illuminate\Support\Facades\Notification;

class SendEventInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public $retryAfter = 3;
	public $user;
	public $event;
	public $invitation_code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Event $event, $invitation_code='')
    {
   	  $this->user = $user;
   	  $this->event = $event;
   	  $this->invitation_code = $invitation_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    		Notification::route('mail', [$this->user->email => $this->user->name_th])
       			->notify(new SendEventInvitationEmail($this->user, $this->event, $this->invitation_code));
    }
}
