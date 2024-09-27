<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\InformAdminFormSubmit;
use App\Notifications\InformSenderFormSubmit;
use Illuminate\Support\Facades\Notification;

class SendInformFormSubmit implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public $retryAfter = 3;
	public $form_contact_email;
	public $user;
	public $form_name;
	public $form_data;
	public $created_at;
	public $subject;
	public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
	public function __construct($form_contact_email, User $user, $form_name, $form_data='', $created_at, $subject='', $body='')
	{
	  	$this->form_contact_email = $form_contact_email;
	  	$this->user = $user;
	  	$this->form_name = $form_name;
	  	$this->form_data = $form_data;
	  	$this->created_at = $created_at;
	  	$this->subject = $subject;
	  	$this->body = $body;
	}

	public function handle()
	{
		Notification::route('mail', [$this->form_contact_email])
       			->notify(new InformAdminFormSubmit($this->user, $this->form_name, $this->form_data, $this->created_at));

 	}
}
