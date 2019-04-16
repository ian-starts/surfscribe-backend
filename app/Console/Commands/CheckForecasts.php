<?php


namespace App\Console\Commands;


use App\Jobs\ProcessNotificationEmail;
use App\Location;
use App\Notification;
use App\Repositories\ForecastRepositoryAdapter;
use App\User;
use IanKok\MSWSDK\Forecasts\ForecastRepository;
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
     * @param ForecastRepositoryAdapter $repository
     */
    public function handle(ForecastRepositoryAdapter $repository)
    {
        $locations       = Location::query()->whereHas('notifications', null)->get();
        $notifications   = $locations->map(
            function ($location) use ($repository) {
                $forecasts               = $repository->getBySlugAsync($location->msw_wave_break_slug)->wait();
                $location->notifications = $location->notifications->filter(
                    function ($notification) use ($forecasts, $repository) {
                        $notification->forecasts = $repository->findNotifiableForecasts($notification, $forecasts);
                        return (count($notification->forecasts) !== 0);
                    }
                );
                return $location;
            }
        )->filter(
            function ($location) {
                return (!$location->notifications->isEmpty());
            }
        )->reduce(
            function (Collection $carry, Location $location) {
                return $carry->merge($location->notifications);
            },
            new Collection()
        );
        $notificationIds = $notifications->map(
            function ($notification) {
                return $notification->id;
            }
        );
        $users           = User::query()->whereHas(
            'notifications',
            function (Builder $q) use ($notificationIds) {
                $q->whereIn('id', $notificationIds);
            }
        )->get()->map(
            function (User $user) use ($notifications) {
                $user->notifications = $notifications->filter(
                    function (Notification $notification) use ($user) {
                        return $notification->user_id === $user->id;
                    }
                );
                return $user;
            }
        );
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
