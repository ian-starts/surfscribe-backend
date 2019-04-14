<?php


namespace App\Jobs;


use App\Location;
use App\Mail\MailLongTimeNotifications;
use App\Mail\MailShortTimeNotifications;
use App\User;
use Illuminate\Support\Facades\Mail;

class ProcessNotificationShortTimeEmail extends Job
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
    public function __construct(User $user, array $locations)
    {
        $this->user      = $user;
        $this->locations = $locations;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(
            new MailShortTimeNotifications($this->user, $this->locations, 'It\'s go time tomorrow!')
        );
    }

}
