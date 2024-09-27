<?php

namespace App\Mail;

use App\Models\RoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomCancelReservation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $result;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RoomBooking $result)
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
        return $this->subject(config('bookdose.app.name').' | Cancel room reservation')
            ->from(config('bookdose.mail.from_address'), config('bookdose.mail.from_name'))
            ->view('front.' . config('bookdose.theme_front') . '.mails.room.cancel_reservation');
    }
}
