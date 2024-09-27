<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendEventInvitationEmail extends Notification
{
    use Queueable;

    public $user;
    public $event;
    public $invitation_code;

    /**
     * Create a new notification instance.
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
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(config('bookdose.app.name').' - You\'re invited!')
                    ->greeting('Hi '.$this->user->name.',')
                    ->line('You are invited to join the event, '.$this->event->title.'. Please click the link below to join.')
                    ->line(new HtmlString('<a href="'.route('event.invitation.accept', $this->invitation_code).'">Accept invitation '.$this->event->title.'</button>'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
