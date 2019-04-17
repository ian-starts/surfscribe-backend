<?php


namespace App\Repositories;


use App\Location;

class NotificationRepository
{
    /**
     * @var ForecastRepositoryAdapter
     */
    protected $repository;

    /**
     * NotificationRepository constructor.
     *
     * @param ForecastRepositoryAdapter $repository
     */
    public function __construct(ForecastRepositoryAdapter $repository) { $this->repository = $repository; }


    public function getPredictedByLocation(Location $location)
    {
        $forecasts = $this->repository->getBySlugAsync($location->msw_wave_break_slug)->wait();
        return $location->notifications->filter(
            function ($notification) use ($forecasts) {
                $notification->forecasts = $this->repository->findNotifiableForecasts($notification, $forecasts);
                return (count($notification->forecasts) !== 0);
            }
        );
    }
}
