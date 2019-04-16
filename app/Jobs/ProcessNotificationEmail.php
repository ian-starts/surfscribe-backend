<?php


namespace App\Jobs;


use App\Location;
use App\Mail\MailNotifications;
use App\User;
use Illuminate\Support\Facades\Mail;

class ProcessNotificationEmail extends Job
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array|Location[]
     */
    protected $locations;

    /**
     * ProcessNotificationEmail constructor.
     *
     * @param User             $user
     * @param Location[]|array $locations
     */
    public function __construct(User $user, $locations)
    {
        $this->user      = $user;
        $this->locations = $locations;
    }


    public function handle()
    {
        Mail::to('hello@yonikok.com')->send(
            new MailNotifications($this->user, $this->locations, 'The weather is looking good these days!')
        );
    }

}
