<?php


namespace App\Http\Controllers\Locations;


use App\Filters\LocationsFilter;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class LocationsController extends BaseController
{
    public function index(Request $request, LocationsFilter $filter)
    {
        return response()->json($filter->paginate($request->get('pagesize', 50)));
    }

    public function location ($id, LocationsFilter $filter)
    {
        return response()->json($filter->query()->where('slug','=', $id)->firstOrFail());
    }
}
