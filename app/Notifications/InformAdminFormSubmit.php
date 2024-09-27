<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\FormField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InformAdminFormSubmit extends Notification implements ShouldQueue
{
 	use Queueable;
 	public $user;
	public $form_name;
	public $form_data;
	public $created_at;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $form_name, $form_data, $created_at)
    {
      	$this->user = $user;
      	$this->form_name = $form_name;
      	$this->form_data = $form_data;
      	$this->created_at = $created_at;
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
       	$message = (new MailMessage)
     					->subject(config('app.name').' - ได้รับการตอบแบบฟอร์มเข้ามาใหม่ ')
                 	->greeting('เรียน ผู้ที่เกี่ยวข้อง,')
                 	->line( new HtmlString('สมาชิก <span style="color:#1B458A;">'.$this->user->name.' ('.$this->user->email.')</span> ได้ทำการส่งแบบฟอร์ม <span style="color:#1B458A;">'.$this->form_name.'</span> เข้ามา') );
                 	// ->line( new HtmlString('<hr style="border:1px solid #ECF2F7;">') )
                 	// ->line( new HtmlString('<strong>วันที่ส่งแบบฟอร์ม</strong> : '.date_format($this->created_at, 'd/m/Y H:i')) );
                 	// ->action('ไปยัง'.$this->comment->post->content_type->title, $url);

         /*
        	if (is_array($this->form_data)) {
        		foreach ($this->form_data as $key => $value) {
        			if (is_array($value)) {
        				$message->line( new HtmlString('<strong>'.$key.'</strong>') );
        				foreach ($value as $k => $doc_value) {
        					if (is_numeric($k))
        						$msg = 'รายการที่ '.($k+1) .' : ';
        					else
        						$msg = $k.' : ';
        					
        					if (is_array($doc_value)) {
        						foreach ($doc_value as $x => $v) {
        							if (is_array($v))
        								$msg .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;'.$x.' : '.implode(', ', $v);
        							else
        								$msg .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;'.$x.' : '.$v;
        						}
        					}
        					else {
        						$msg .= $doc_value;
        					}
        					$message->line( new HtmlString($msg) );
        				}
        			}
        			else {
        				$message->line( new HtmlString('<strong>'.$key.'</strong> : '.($value)) );
        			}
        		}
        	}
        	$message->line( new HtmlString('<hr style="border:1px solid #ECF2F7;">') );
        	*/
     		return $message;
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
