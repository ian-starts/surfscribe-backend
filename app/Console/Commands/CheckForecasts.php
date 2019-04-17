<?php


namespace App\Console\Commands;


use App\Jobs\ProcessNotificationEmail;
use App\Location;
use App\Notification;
use App\Repositories\ForecastRepositoryAdapter;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CheckForecasts extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'surfscribe:check_forecasts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all the required locations and dispatch to email-sender Job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param NotificationRepository $notificationRepository
     * @param UserRepository         $userRepository
     */
    public function handle(NotificationRepository $notificationRepository, UserRepository $userRepository)
    {
        $locations = Location::query()->whereHas('notifications', null)->get();

        // Get all the notifications that have a forecast that matches the parameters
        $notifications   = $locations->map(
            function ($location) use ($notificationRepository) {
                return $notificationRepository->getPredictedByLocation($location);
            }
        )->reduce(
            function (Collection $carry, Collection $notifications) {
                return $carry->merge($notifications);
            },
            new Collection()
        );

        $users = $userRepository->findByNotifications($notifications);
        // Dispatch all emails
        foreach ($users as $user) {
            dispatch(
                new ProcessNotificationEmail(
                    $user, $user->notifications->map(
                    function ($notification) {
                        return $notification->location;
                    }
                )
                )
            );
        }
    }
}
