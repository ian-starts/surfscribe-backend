<?php


namespace App\Repositories;


use App\Notification;
use GuzzleHttp\Promise\PromiseInterface;
use IanKok\MSWSDK\Forecasts\ForecastRepository;

class ForecastRepositoryAdapter
{
    /**
     * @var ForecastRepository
     */
    protected $repository;

    /**
     * ForecastRepositoryAdapter constructor.
     *
     * @param ForecastRepository $repository
     */
    public function __construct(ForecastRepository $repository) { $this->repository = $repository; }

    /**
     * @param Notification $notification
     * @param array        $forecasts
     *
     * @return array
     */
    public function findNotifiableForecasts(Notification $notification, array $forecasts)
    {
        return array_filter(
            $forecasts,
            function ($forecast) use ($notification) {
                return (($forecast->swell->absHeight >= $notification->swell_height_min
                        && $forecast->swell->absHeight <= $notification->swell_height_max)
                    && ($forecast->wind->speed >= $notification->wind_speed_min
                        && $forecast->wind->speed <= $notification->wind_speed_max)
                    && (($forecast->wind->stringDirection === ucwords($notification->wind_direction))
                        || (!$notification->wind_direction_exact_match && strpos(
                                str_replace('-', '', str_replace('/', 'shore', $forecast->wind->stringDirection)),
                                ucwords($notification->wind_direction)
                            )))
                    && ($forecast->swell->period >= $notification->swell_period_min
                        && $forecast->swell->period <= $notification->swell_period_max)
                );
            }
        );
    }

    /**
     * @param $slug
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getBySlugAsync($slug): PromiseInterface
    {
        return $this->repository->getBySlugAsync($slug);
    }

    /**
     * @param $slug
     *
     * @return array|mixed
     */
    public function getBySlug($slug): array
    {
        return $this->repository->getBySlug($slug);
    }
}
