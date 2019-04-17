<?php


namespace App\Repositories;


use App\Location;
use Litipk\BigNumbers\Decimal;

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

    public function convertData($requestData)
    {
        if ($requestData['wind']['speed']['unit'] === 'kts') {
            $requestData['wind']['speed']['min']  = Decimal::fromFloat(
                $requestData['wind']['speed']['min'] * 1.85
            )->floor(2)->asFloat();
            $requestData['wind']['speed']['max']  = Decimal::fromFloat(
                $requestData['wind']['speed']['max'] * 1.85
            )->ceil(2)->asFloat();
            $requestData['wind']['speed']['unit'] = 'kph';
        }
        if ($requestData['wind']['speed']['unit'] === 'mph') {
            $requestData['wind']['speed']['min']  = Decimal::fromFloat(
                $requestData['wind']['speed']['min'] * 1.609344
            )->floor(2)->asFloat();
            $requestData['wind']['speed']['max']  = Decimal::fromFloat(
                $requestData['wind']['speed']['max'] * 1.609344
            )->ceil(2)->asFloat();
            $requestData['wind']['speed']['unit'] = 'kph';
        }
        if ($requestData['swell']['height']['unit'] === 'ft') {
            $requestData['swell']['height']['min']  = Decimal::fromFloat(
                $requestData['swell']['height']['min'] / 3.2808
            )->floor(2)->asFloat();
            $requestData['swell']['height']['max']  = Decimal::fromFloat(
                $requestData['swell']['height']['max'] / 3.2808
            )->ceil(2)->asFloat();
            $requestData['swell']['height']['unit'] = 'm';
        }

        return $requestData;
    }
}
