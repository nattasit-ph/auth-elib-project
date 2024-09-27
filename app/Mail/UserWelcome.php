<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserWelcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $result;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $result)
    {
        $this->result = $result;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        return $this->subject('Welcome to '.config('bookdose.app.name').'')
            ->from(config('bookdose.mail.from_address'), config('bookdose.mail.from_name'))
            ->view('front.' . config('bookdose.theme_front') . '.mails.user.welcome');            
    }
}
