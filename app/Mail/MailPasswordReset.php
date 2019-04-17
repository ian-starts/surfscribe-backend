<?php


namespace App\Mail;


use App\Location;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $url;

    /**
     * MailPasswordReset constructor.
     *
     * @param User   $user
     * @param string $url
     */
    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url  = $url;
    }


    /**
     * Build the message.
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function build()
    {
        return $this->from('passwordresets@surfscribe.com', 'Surfscribe passwords dude')
            ->subject('Reset your password')
            ->view('emails.passwordreset')
            ->with(
                [
                    'user' => $this->user,
                    'url'  => $this->url,
                ]
            );
    }

}
