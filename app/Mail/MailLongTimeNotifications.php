<?php


namespace App\Mail;


use App\Location;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailLongTimeNotifications extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array|Location[]
     */
    protected $locations;

    /**
     * @var string
     */
    public $subject;

    /**
     * MailNotifications constructor.
     *
     * @param User             $user
     * @param Location[]|array $locations
     * @param string           $subject
     */
    public function __construct(User $user, $locations, string $subject)
    {
        $this->user      = $user;
        $this->locations = $locations;
        $this->subject   = $subject;
    }


    /**
     * Build the message.
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function build()
    {
        return $this->from('notifications@surfscribe.com', 'Surf Sniper')
            ->subject($this->subject)
            ->view('emails.notificationlongtime')
            ->with(
                [
                    'locations' => $this->locations,
                    'user'      => $this->user,
                ]
            );
    }

}
