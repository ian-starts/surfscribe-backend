<?php


namespace App\Http\Controllers\Forecasts;

use App\Location;
use Laravel\Lumen\Routing\Controller as BaseController;
use IanKok\MSWSDK\Forecasts\ForecastRepository;

class ForecastsController extends BaseController
{
    public function location($id, ForecastRepository $repository)
    {
        return response()->json(
            $repository->getBySlugAsync(
                Location::query()->where('slug', '=', $id)->firstOrFail()->msw_wave_break_slug
            )->wait()
        )->setTtl(40);
    }
}
